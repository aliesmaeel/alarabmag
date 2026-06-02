<?php

namespace App\Services;

use App\Support\OutboundHttp;
use Illuminate\Support\Str;
use RuntimeException;

class WebSearchService
{
    public function isAvailable(): bool
    {
        return $this->resolveProvider() !== null;
    }

    public function configurationMessage(): string
    {
        if ($this->hasTavily()) {
            return 'البحث عبر Tavily (مفعّل)';
        }

        if ($this->hasBrave()) {
            return 'البحث عبر Brave Search (مدفوع — رصيد شهري محدود)';
        }

        return 'البحث عبر DuckDuckGo (بدون مفتاح — للدقة الأفضل أضف TAVILY_API_KEY مجاناً من tavily.com)';
    }

    /**
     * @return list<array{title: string, url: string, snippet: string}>
     */
    public function search(string $query, ?int $maxResults = null): array
    {
        $query = trim($query);
        if ($query === '') {
            return [];
        }

        $maxResults = $maxResults ?? config('ai.web_search.max_results', 6);
        $provider = $this->resolveProvider();

        $results = match ($provider) {
            'tavily' => $this->searchTavily($query, $maxResults),
            'brave' => $this->searchBrave($query, $maxResults),
            'duckduckgo' => $this->searchDuckDuckGo($query, $maxResults),
            default => [],
        };

        return array_values(array_filter($results, fn (array $r) => filled($r['title'] ?? null)));
    }

    /**
     * @param  list<array{title: string, url: string, snippet: string}>  $results
     */
    public function formatForPrompt(array $results): string
    {
        if ($results === []) {
            return 'لم تُعثر على نتائج بحث حية — اعتمد على معرفتك العامة مع الإشارة إلى أن المعلومات قد تحتاج تحققاً.';
        }

        $blocks = [];
        foreach ($results as $i => $item) {
            $n = $i + 1;
            $blocks[] = "[{$n}] {$item['title']}\nالمصدر: {$item['url']}\n{$item['snippet']}";
        }

        return "نتائج بحث من الإنترنت (استخدمها كمصادر، لا تنسخ حرفياً):\n\n".implode("\n\n", $blocks);
    }

    protected function resolveProvider(): ?string
    {
        $configured = config('ai.web_search.provider', 'auto');

        if ($configured === 'tavily' && $this->hasTavily()) {
            return 'tavily';
        }

        if ($configured === 'brave' && $this->hasBrave()) {
            return 'brave';
        }

        if ($configured === 'duckduckgo') {
            return 'duckduckgo';
        }

        if ($configured === 'auto') {
            if ($this->hasTavily()) {
                return 'tavily';
            }
            if ($this->hasBrave()) {
                return 'brave';
            }

            return 'duckduckgo';
        }

        return null;
    }

    protected function hasTavily(): bool
    {
        return filled(config('ai.web_search.tavily.api_key'));
    }

    protected function hasBrave(): bool
    {
        return filled(config('ai.web_search.brave.api_key'));
    }

    /**
     * @return list<array{title: string, url: string, snippet: string}>
     */
    protected function searchTavily(string $query, int $maxResults): array
    {
        $response = OutboundHttp::client(25)
            ->post('https://api.tavily.com/search', [
                'api_key' => config('ai.web_search.tavily.api_key'),
                'query' => $query,
                'search_depth' => 'basic',
                'max_results' => $maxResults,
                'include_answer' => false,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Tavily: '.$response->json('error', $response->body()));
        }

        $results = [];
        foreach ($response->json('results', []) as $row) {
            $results[] = [
                'title' => (string) ($row['title'] ?? ''),
                'url' => (string) ($row['url'] ?? ''),
                'snippet' => Str::limit((string) ($row['content'] ?? $row['snippet'] ?? ''), 500),
            ];
        }

        return $results;
    }

    /**
     * @return list<array{title: string, url: string, snippet: string}>
     */
    protected function searchBrave(string $query, int $maxResults): array
    {
        $response = OutboundHttp::client(25)
            ->withHeaders([
                'Accept' => 'application/json',
                'X-Subscription-Token' => config('ai.web_search.brave.api_key'),
            ])
            ->get('https://api.search.brave.com/res/v1/web/search', [
                'q' => $query,
                'count' => $maxResults,
                'search_lang' => 'ar',
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Brave Search: '.$response->body());
        }

        $results = [];
        foreach ($response->json('web.results', []) as $row) {
            $results[] = [
                'title' => (string) ($row['title'] ?? ''),
                'url' => (string) ($row['url'] ?? ''),
                'snippet' => Str::limit((string) ($row['description'] ?? ''), 500),
            ];
        }

        return $results;
    }

    /**
     * @return list<array{title: string, url: string, snippet: string}>
     */
    protected function searchDuckDuckGo(string $query, int $maxResults): array
    {
        $response = OutboundHttp::client(20)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; AlArabMagazine/1.0)',
            ])
            ->asForm()
            ->post('https://html.duckduckgo.com/html/', [
                'q' => $query,
                'kl' => 'ar-ar',
            ]);

        if (! $response->successful()) {
            return [];
        }

        $html = $response->body();
        $results = [];

        if (preg_match_all(
            '/<a[^>]*class="result__a"[^>]*href="([^"]+)"[^>]*>(.*?)<\/a>/si',
            $html,
            $titleMatches,
            PREG_SET_ORDER
        )) {
            preg_match_all(
                '/<a[^>]*class="result__snippet"[^>]*>(.*?)<\/a>/si',
                $html,
                $snippetMatches
            );

            foreach (array_slice($titleMatches, 0, $maxResults) as $i => $match) {
                $url = $this->normalizeDuckDuckGoUrl(html_entity_decode($match[1]));
                $title = trim(strip_tags(html_entity_decode($match[2])));
                $snippet = isset($snippetMatches[1][$i])
                    ? trim(strip_tags(html_entity_decode($snippetMatches[1][$i])))
                    : '';

                if ($title !== '' && $url !== '') {
                    $results[] = [
                        'title' => $title,
                        'url' => $url,
                        'snippet' => Str::limit($snippet, 500),
                    ];
                }
            }
        }

        return $results;
    }

    protected function normalizeDuckDuckGoUrl(string $url): string
    {
        if (str_starts_with($url, '//')) {
            $url = 'https:'.$url;
        }

        if (str_contains($url, 'duckduckgo.com/l/')) {
            parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $params);

            if (! empty($params['uddg'])) {
                return urldecode($params['uddg']);
            }
        }

        return $url;
    }
}
