<?php

namespace App\Services;

use App\Filament\Support\KeywordsInput;
use App\Support\OutboundHttp;
use Illuminate\Support\Str;
use RuntimeException;

class AiContentService
{
    public function isConfigured(): bool
    {
        return match (config('ai.provider')) {
            'groq' => filled(config('ai.groq.api_key')),
            'gemini' => filled(config('ai.gemini.api_key')),
            'ollama' => true,
            default => false,
        };
    }

    public function configurationMessage(): string
    {
        return match (config('ai.provider')) {
            'groq' => 'أضف GROQ_API_KEY في ملف .env — مفتاح مجاني من console.groq.com',
            'gemini' => 'أضف GEMINI_API_KEY في ملف .env — مفتاح مجاني من aistudio.google.com',
            'ollama' => 'شغّل Ollama محلياً: ollama run llama3.2',
            default => 'عيّن AI_PROVIDER في ملف .env',
        };
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function generateField(string $field, array $context, ?string $instruction = null): string|array
    {
        $this->ensureConfigured();

        $fieldGuide = match ($field) {
            'title' => 'عنوان جذاب بالعربية (60–90 حرفاً تقريباً)',
            'subtitle' => 'عنوان فرعي عربي يكمّل العنوان',
            'excerpt' => 'مقتطف عربي 2–3 جمل للقائمة والمعاينة',
            'meta_title' => 'عنوان SEO عربي (50–60 حرفاً)',
            'meta_description' => 'وصف meta عربي (140–160 حرفاً)',
            'meta_keywords' => '5–8 كلمات مفتاحية عربية كمصفوفة JSON',
            'og_title' => 'عنوان Open Graph عربي جذاب',
            'og_description' => 'وصف Open Graph عربي (حتى 200 حرف)',
            'name' => 'اسم الشخصية بالعربية',
            default => "محتوى عربي للحقل {$field}",
        };

        $prompt = "اكتب فقط قيمة الحقل «{$field}» بالعربية الفصحى.\n";
        $prompt .= "المطلوب: {$fieldGuide}.\n\n";
        $prompt .= "سياق المحتوى:\n".$this->contextBlock($context);

        if ($instruction) {
            $prompt .= "\n\nتعليمات المحرر: {$instruction}";
        }

        if ($field === 'meta_keywords') {
            $prompt .= "\n\nأجب بمصفوفة JSON فقط، مثال: [\"كلمة1\",\"كلمة2\"]";

            return KeywordsInput::toArray($this->extractJsonArray($this->chat($this->systemPrompt($context), $prompt)));
        }

        return trim($this->chat($this->systemPrompt($context), $prompt));
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, string|array<int, string>>
     */
    public function generateSeoBundle(array $context, ?string $instruction = null): array
    {
        $this->ensureConfigured();

        $prompt = "أنشئ حقول SEO كاملة بالعربية لمجلة العرب.\n\n";
        $prompt .= $this->contextBlock($context);

        if ($instruction) {
            $prompt .= "\n\nتعليمات إضافية: {$instruction}";
        }

        $prompt .= <<<'PROMPT'


أجب بـ JSON فقط بهذا الشكل (بدون markdown):
{
  "meta_title": "",
  "meta_description": "",
  "meta_keywords": ["", ""],
  "og_title": "",
  "og_description": ""
}
PROMPT;

        $decoded = $this->extractJsonObject($this->chat($this->systemPrompt($context), $prompt));

        return [
            'meta_title' => Str::limit((string) ($decoded['meta_title'] ?? ''), 500, ''),
            'meta_description' => Str::limit((string) ($decoded['meta_description'] ?? ''), 500, ''),
            'meta_keywords' => KeywordsInput::toArray($decoded['meta_keywords'] ?? []),
            'og_title' => Str::limit((string) ($decoded['og_title'] ?? ''), 500, ''),
            'og_description' => Str::limit((string) ($decoded['og_description'] ?? ''), 500, ''),
        ];
    }

    protected function systemPrompt(array $context): string
    {
        $type = $context['content_type'] ?? 'content';

        return <<<SYS
أنت محرر وخبير SEO في مجلة «العرب» العربية. اكتب بالعربية الفصحى السليمة.
نوع المحتوى: {$type}.
لا تضف شروحات إنجليزية ولا رموزاً زائدة. التزم بطول الحقول المطلوب.
SYS;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function contextBlock(array $context): string
    {
        $lines = [];

        foreach ([
            'title' => 'العنوان',
            'subtitle' => 'العنوان الفرعي',
            'name' => 'الاسم',
            'excerpt' => 'المقتطف',
            'category' => 'القسم',
            'author' => 'الكاتب',
            'role' => 'الصفة',
            'specialty' => 'التخصص',
            'region' => 'المنطقة',
            'tags' => 'الوسوم',
        ] as $key => $label) {
            if (! empty($context[$key])) {
                $lines[] = "{$label}: ".$context[$key];
            }
        }

        if (! empty($context['body_text'])) {
            $lines[] = 'النص: '.Str::limit($context['body_text'], 2500);
        }

        return $lines !== [] ? implode("\n", $lines) : 'لا يوجد سياق بعد — اكتب محتوى عاماً مناسباً للمجلة.';
    }

    /**
     * Generate a complete magazine article (optionally using web search).
     *
     * @param  array{use_web_search?: bool, category?: string, author?: string, instruction?: string}  $options
     * @return array<string, mixed>
     */
    public function generateFullArticle(string $subject, ?string $details = null, array $options = []): array
    {
        $this->ensureConfigured();

        $subject = trim($subject);
        if ($subject === '') {
            throw new RuntimeException('أدخل موضوع المقال أولاً.');
        }

        $useWebSearch = $options['use_web_search'] ?? true;
        $searchResults = [];
        $searchNote = '';

        if ($useWebSearch) {
            $search = app(WebSearchService::class);
            $searchQuery = $subject.($details ? ' '.Str::limit($details, 200) : '');

            try {
                $searchResults = $search->search($searchQuery);
                $searchNote = count($searchResults) > 0
                    ? 'تم العثور على '.count($searchResults).' مصدر من الإنترنت.'
                    : 'لم تُرجع محركات البحث نتائج — سيُكتب المقال من المعرفة العامة.';
            } catch (\Throwable $e) {
                $searchNote = 'تعذّر البحث: '.$e->getMessage();
            }
        } else {
            $searchNote = 'تم تخطي البحث في الإنترنت.';
        }

        $categoryHint = $options['category'] ?? null;
        $authorHint = $options['author'] ?? 'فريق التحرير';
        $instruction = $options['instruction'] ?? null;

        $userPrompt = "اكتب مقالاً صحفياً كاملاً لمجلة «العرب» بالعربية الفصحى.\n\n";
        $userPrompt .= "الموضوع: {$subject}\n";

        if ($details) {
            $userPrompt .= "تفاصيل وملاحظات المحرر:\n{$details}\n";
        }

        if ($categoryHint) {
            $userPrompt .= "القسم المطلوب: {$categoryHint}\n";
        }

        $userPrompt .= "الكاتب الافتراضي: {$authorHint}\n\n";
        $userPrompt .= app(WebSearchService::class)->formatForPrompt($searchResults);

        if ($instruction) {
            $userPrompt .= "\n\nتعليمات إضافية: {$instruction}";
        }

        $userPrompt .= <<<'PROMPT'


المطلوب في JSON فقط (بدون markdown خارج JSON):
{
  "title": "عنوان رئيسي جذاب",
  "subtitle": "عنوان فرعي",
  "excerpt": "مقتطف 2-3 جمل",
  "body": "<p>فقرة...</p><h2>عنوان فرعي</h2><p>...</p> (4-8 فقرات، HTML بسيط: p, h2, strong, em فقط)",
  "category": "قسم واحد مثل: أعمال، رياضة، فن، موضة، سياسة، تكنولوجيا، صحة، ثقافة، عام",
  "author": "اسم الكاتب",
  "region": "منطقة جغرافية عربية أو عام",
  "read_time": "مثال: 7 دقائق",
  "meta_title": "",
  "meta_description": "",
  "meta_keywords": ["", ""],
  "og_title": "",
  "og_description": ""
}

اكتب محتوى أصلي بأسلوب مجلة عربية راقية. لا تخترع اقتباسات أو إحصاءات دقيقة إن لم تكن في المصادر.
PROMPT;

        $system = <<<'SYS'
أنت محرر أول في مجلة «العرب» العربية. تكتب مقالات طويلة منسقة للنشر الرقمي.
لغة عربية فصحى واضحة. JSON صالح فقط في ردك النهائي.
SYS;

        $decoded = $this->extractJsonObject(
            $this->chat($system, $userPrompt, config('ai.article_timeout', 120))
        );

        $body = (string) ($decoded['body'] ?? '');
        if ($body !== '' && ! str_contains($body, '<')) {
            $body = '<p>'.nl2br(e($body)).'</p>';
        }

        return [
            '_search_note' => $searchNote,
            'title' => Str::limit((string) ($decoded['title'] ?? $subject), 1000, ''),
            'subtitle' => Str::limit((string) ($decoded['subtitle'] ?? ''), 500, ''),
            'excerpt' => (string) ($decoded['excerpt'] ?? ''),
            'body' => $body,
            'category' => Str::limit((string) ($decoded['category'] ?? $categoryHint ?? 'عام'), 100, ''),
            'author' => Str::limit((string) ($decoded['author'] ?? $authorHint), 200, ''),
            'region' => Str::limit((string) ($decoded['region'] ?? ''), 100, ''),
            'read_time' => Str::limit((string) ($decoded['read_time'] ?? '6 دقائق'), 50, ''),
            'meta_title' => Str::limit((string) ($decoded['meta_title'] ?? ''), 500, ''),
            'meta_description' => Str::limit((string) ($decoded['meta_description'] ?? ''), 500, ''),
            'meta_keywords' => KeywordsInput::toArray($decoded['meta_keywords'] ?? []),
            'og_title' => Str::limit((string) ($decoded['og_title'] ?? ''), 500, ''),
            'og_description' => Str::limit((string) ($decoded['og_description'] ?? ''), 500, ''),
        ];
    }

    protected function chat(string $system, string $user, ?int $timeout = null): string
    {
        $timeout ??= config('ai.timeout');

        return match (config('ai.provider')) {
            'groq' => $this->groqChat($system, $user, $timeout),
            'gemini' => $this->geminiChat($system, $user, $timeout),
            'ollama' => $this->ollamaChat($system, $user, $timeout),
            default => throw new RuntimeException('مزوّد AI غير معروف'),
        };
    }

    protected function groqChat(string $system, string $user, int $timeout): string
    {
        $response = OutboundHttp::client($timeout)
            ->withToken(config('ai.groq.api_key'))
            ->post(rtrim(config('ai.groq.base_url'), '/').'/chat/completions', [
                'model' => config('ai.groq.model'),
                'temperature' => 0.7,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $user],
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Groq: '.$response->json('error.message', $response->body()));
        }

        return (string) $response->json('choices.0.message.content', '');
    }

    protected function geminiChat(string $system, string $user, int $timeout): string
    {
        $model = config('ai.gemini.model');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $response = OutboundHttp::client($timeout)
            ->withQueryParameters(['key' => config('ai.gemini.api_key')])
            ->post($url, [
                'system_instruction' => ['parts' => [['text' => $system]]],
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $user]]],
                ],
                'generationConfig' => ['temperature' => 0.7],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Gemini: '.$response->json('error.message', $response->body()));
        }

        return (string) $response->json('candidates.0.content.parts.0.text', '');
    }

    protected function ollamaChat(string $system, string $user, int $timeout): string
    {
        $response = OutboundHttp::client($timeout)
            ->post(rtrim(config('ai.ollama.base_url'), '/').'/api/chat', [
                'model' => config('ai.ollama.model'),
                'stream' => false,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $user],
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Ollama: تأكد أن Ollama يعمل محلياً — '.$response->body());
        }

        return (string) $response->json('message.content', '');
    }

    protected function extractJsonObject(string $raw): array
    {
        $json = $this->extractJsonString($raw);
        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('لم يفهم النموذج الاستجابة. حاول مرة أخرى.');
        }

        return $decoded;
    }

    /** @return list<string> */
    protected function extractJsonArray(string $raw): array
    {
        $decoded = json_decode($this->extractJsonString($raw), true);

        if (is_array($decoded)) {
            return array_values(array_filter(array_map('strval', $decoded)));
        }

        return KeywordsInput::toArray(trim($raw));
    }

    protected function extractJsonString(string $raw): string
    {
        $raw = trim($raw);

        if (preg_match('/```(?:json)?\s*(.*?)```/s', $raw, $m)) {
            return trim($m[1]);
        }

        if (preg_match('/\{.*\}/s', $raw, $m)) {
            return $m[0];
        }

        if (preg_match('/\[.*\]/s', $raw, $m)) {
            return $m[0];
        }

        return $raw;
    }

    protected function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException($this->configurationMessage());
        }
    }
}
