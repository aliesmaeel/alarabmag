<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Blog;
use App\Models\Interview;
use App\Models\Person;
use App\Support\ExpandedContent;
use Illuminate\Database\Seeder;

class ContentExpansionSeeder extends Seeder
{
    public function run(): void
    {
        $this->expandArticles();
        $this->expandBlogs();
        $this->expandPeopleBios();
        $this->seedAdditionalBlogs();
        $this->seedAdditionalArticles();
        $this->seedInterviews();

        $this->command?->info('✅ Content expansion complete.');
    }

    private function expandArticles(): void
    {
        Article::query()->each(function (Article $article) {
            $body = ExpandedContent::articleBody(
                $article->title,
                $article->excerpt ?? '',
                $article->body,
            );
            $article->update(['body' => $body]);
        });

        $this->command?->line('  → Expanded ' . Article::count() . ' article bodies');
    }

    private function expandBlogs(): void
    {
        Blog::query()->each(function (Blog $blog) {
            $body = ExpandedContent::articleBody(
                $blog->title,
                $blog->excerpt ?? '',
                $blog->body,
            );
            $blog->update(['body' => $body]);
        });

        $this->command?->line('  → Expanded ' . Blog::count() . ' blog bodies');
    }

    private function expandPeopleBios(): void
    {
        Person::query()->each(function (Person $person) {
            $bio = ExpandedContent::bioBody(
                $person->name,
                $person->role ?? 'مجاله',
                $person->excerpt ?? '',
                $person->bio,
            );
            $person->update(['bio' => $bio]);
        });

        $this->command?->line('  → Expanded ' . Person::count() . ' profile bios');
    }

    private function seedAdditionalBlogs(): void
    {
        $blogs = [
            ['title' => 'لماذا يعود المغتربون العرب إلى ريادة الأعمال في أوطانهم؟', 'author' => 'عمر الفيصل', 'tags' => 'ريادة,اغتراب,اقتصاد', 'excerpt' => 'موجة جديدة من المؤسسين يختارون العودة وبناء مشاريع من داخل المنطقة.'],
            ['title' => 'السينما العربية: من الجمهور المحلي إلى مهرجانات العالم', 'author' => 'زينة الخوري', 'tags' => 'سينما,فن,ثقافة', 'excerpt' => 'كيف أصبحت الأفلام العربية تنافس على جوائز دولية.'],
            ['title' => 'التعليم الطبي العربي: بين التميز والهجرة', 'author' => 'د. رانيا مصطفى', 'tags' => 'طب,تعليم,هجرة', 'excerpt' => 'تحليل لمسار الأطباء العرب في الجامعات العالمية.'],
            ['title' => 'الموضة المستدامة في الخليج: اتجاه أم موضة؟', 'author' => 'أميرة سعيد', 'tags' => 'موضة,استدامة,خليج', 'excerpt' => 'مصممون شباب يمزجون التراث بالإنتاج الأخلاقي.'],
            ['title' => 'كيف تبني ثقافة ابتكار في شركتك الناشئة العربية', 'author' => 'سارة خليل', 'tags' => 'ابتكار,شركات,تقنية', 'excerpt' => 'دروس عملية من مؤسسين نجحوا في بناء فرق إبداعية.'],
            ['title' => 'الذكاء الاصطناعي والإعلام العربي: فرص ومخاطر', 'author' => 'خالد الشمراني', 'tags' => 'تقنية,إعلام,ذكاء اصطناعي', 'excerpt' => 'كيف يغيّر الذكاء الاصطناعي صناعة المحتوى في العالم العربي.'],
            ['title' => 'الرياضة العربية واقتصاد النجوم: أين تذهب المليارات؟', 'author' => 'فريق التحرير', 'tags' => 'رياضة,اقتصاد,خليج', 'excerpt' => 'تحليل لسوق الانتقالات والاستثمار في الكرة العربية.'],
        ];

        foreach ($blogs as $data) {
            $blog = Blog::updateOrCreate(
                ['title' => $data['title']],
                [
                    'excerpt' => $data['excerpt'],
                    'author' => $data['author'],
                    'tags' => $data['tags'],
                    'image_url' => 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=1200&q=85',
                    'featured' => false,
                    'status' => 'published',
                ],
            );
            $blog->update([
                'body' => ExpandedContent::articleBody($blog->title, $blog->excerpt ?? ''),
            ]);
        }

        $this->command?->line('  → Added/updated ' . count($blogs) . ' additional blogs');
    }

    private function seedAdditionalArticles(): void
    {
        $articles = [
            ['title' => 'قمة الرياض الاقتصادية 2026: ما الذي تغيّر في المنطقة؟', 'category' => 'أعمال', 'region' => 'السعودية', 'author' => 'عمر الفيصل', 'excerpt' => 'قراءة في أبرز مخرجات القمة وتأثيرها على الاستثمار الخليجي.'],
            ['title' => 'ثقافة ريادة الأعمال في بيروت: صمود رغم الأزمات', 'category' => 'أعمال', 'region' => 'لبنان', 'author' => 'سارة خليل', 'excerpt' => 'كيف يواصل المؤسسون اللبنانيون البناء رغم التحديات.'],
            ['title' => 'مهرجان الجونة السينمائي: نافذة جديدة للفن العربي', 'category' => 'فن', 'region' => 'مصر', 'author' => 'زينة الخوري', 'excerpt' => 'تقرير من قلب المهرجان عن الأفلام الأكثر تأثيراً.'],
            ['title' => 'الطب النانوي: ثورة قادمة من مختبرات عربية', 'category' => 'صحة', 'region' => 'الإمارات', 'author' => 'فريق التحرير', 'excerpt' => 'باحثون عرب يقودون أبحاثاً طبية واعدة.'],
        ];

        foreach ($articles as $data) {
            $article = Article::updateOrCreate(
                ['title' => $data['title']],
                [
                    'subtitle' => 'تقرير · ' . $data['region'],
                    'excerpt' => $data['excerpt'],
                    'category' => $data['category'],
                    'author' => $data['author'],
                    'region' => $data['region'],
                    'image_url' => 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=1200&q=85',
                    'read_time' => '8 دقائق',
                    'featured' => false,
                    'status' => 'published',
                ],
            );
            $article->update([
                'body' => ExpandedContent::articleBody($article->title, $article->excerpt ?? ''),
            ]);
        }

        $this->command?->line('  → Added/updated ' . count($articles) . ' additional articles');
    }

    private function seedInterviews(): void
    {
        $interviews = [
            [
                'title' => 'حوار حصري مع أحمد الراشدي: مستقبل المال الرقمي في العرب',
                'description' => "في هذا الحوار الموسع، يتحدث أحمد الراشدي عن رؤيته للاقتصاد الرقمي العربي، وتحديات التنظيم، وفرص النمو في منطقة MENA.\n\nيستعرض الراشدي مسيرته من شركة ناشئة إلى ثلاث شركات بمليار دولار، ويشارك نصائحه للمؤسسين الشباب.",
                'category' => 'أعمال',
                'video_url' => 'https://sample-videos.com/video321/mp4/720/big_buck_bunny_720p_1mb.mp4',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=1200&q=85',
                'featured' => true,
            ],
            [
                'title' => 'ريما الحسن: السينما العربية تستحق مكانها في العالم',
                'description' => "المخرجة الأردنية ريما الحسن تتحدث عن تجربتها في مهرجان كان، وكيف تبني أفلامها على الهوية العربية.\n\n«القصة العربية غنية بما يكفي — نحن فقط نحتاج إلى شجاعة روايتها».",
                'category' => 'فن',
                'video_url' => 'https://sample-videos.com/video321/mp4/720/big_buck_bunny_720p_1mb.mp4',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=1200&q=85',
                'featured' => false,
            ],
            [
                'title' => 'د. خالد العمري: الطبيب العربي بين الإنجاز والمسؤولية',
                'description' => "د. خالد العمري يتحدث عن مسيرته في Mayo Clinic، وكيف يرى دور الطبيب العربي في العالم.\n\nحوار عميق عن الطب والإنسانية والاغتراب والعودة إلى الجذور.",
                'category' => 'صحة',
                'video_url' => 'https://sample-videos.com/video321/mp4/720/big_buck_bunny_720p_1mb.mp4',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=1200&q=85',
                'featured' => false,
            ],
        ];

        foreach ($interviews as $data) {
            Interview::updateOrCreate(
                ['title' => $data['title']],
                array_merge($data, ['status' => 'published']),
            );
        }

        $this->command?->line('  → Seeded ' . count($interviews) . ' interviews');
    }
}
