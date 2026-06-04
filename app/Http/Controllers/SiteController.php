<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Blog;
use App\Models\Interview;
use App\Models\Person;
use App\Services\FileUploadService;
use App\Services\SeoService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiteController extends Controller
{
    public function __construct(
        protected SeoService $seo,
    ) {}

    public function home(): View
    {
        return view('site.home', [
            'seo' => $this->seo->page('home'),
            'showPreloader' => true,
            'showTicker' => true,
            'activeNav' => 'home',
            'footerVariant' => 'full',
        ]);
    }

    public function news(): View
    {
        return view('site.news', [
            'seo' => $this->seo->page('news'),
            'showTicker' => true,
            'activeNav' => 'news',
            'newsletterHeadline' => 'احصل على نشرة<br><em>الأخبار اليومية</em>',
            'newsletterSub' => 'ملخص يومي لأهم الأخبار العربية — مختار بعناية من فريق التحرير.',
        ]);
    }

    public function newsShow(int $id): View
    {
        $article = Article::query()->where('status', 'published')->findOrFail($id);

        return view('site.news-details', [
            'seo' => $this->seo->fromArticle($article),
            'activeNav' => 'news',
            'articleId' => $id,
            'footerVariant' => 'compact',
        ]);
    }

    public function blogs(): View
    {
        return view('site.blogs', [
            'seo' => $this->seo->page('blogs'),
            'showTicker' => true,
            'activeNav' => 'blogs',
            'newsletterHeadline' => 'مقالات جديدة<br><em>كل أسبوع</em>',
            'newsletterSub' => 'أفضل المدونات العربية — مختارة من فريق التحرير.',
        ]);
    }

    public function blogShow(int $id): View
    {
        $blog = Blog::query()->where('status', 'published')->findOrFail($id);

        return view('site.blog-details', [
            'seo' => $this->seo->fromBlog($blog),
            'activeNav' => 'blogs',
            'articleId' => $id,
            'footerVariant' => 'compact',
        ]);
    }

    public function doctors(): View
    {
        return view('site.doctors', [
            'seo' => $this->seo->page('doctors'),
            'activeNav' => 'doctors',
            'newsletterHeadline' => 'قصص الأطباء<br><em>في بريدك</em>',
            'newsletterSub' => 'ملفات حصرية عن الأطباء العرب الأكثر تأثيراً — أسبوعياً.',
        ]);
    }

    public function doctorShow(int $id): View
    {
        $person = Person::query()->where('category', 'doctor')->findOrFail($id);

        return view('site.doctor-details', [
            'seo' => $this->seo->fromPerson($person, 'doctors.show'),
            'activeNav' => 'doctors',
            'personId' => $id,
            'footerVariant' => 'compact',
        ]);
    }

    public function influencers(): View
    {
        return view('site.influencers', [
            'seo' => $this->seo->page('influencers'),
            'activeNav' => 'influencers',
            'newsletterHeadline' => 'المؤثرون الجدد<br><em>قبل الجميع</em>',
            'newsletterSub' => 'اكتشف نجوم السوشيال ميديا العرب الصاعدين أسبوعياً في بريدك.',
        ]);
    }

    public function influencerShow(int $id): View
    {
        $person = Person::query()->where('category', 'influencer')->findOrFail($id);

        return view('site.influencer-details', [
            'seo' => $this->seo->fromPerson($person, 'influencers.show'),
            'activeNav' => 'influencers',
            'personId' => $id,
            'footerVariant' => 'compact',
        ]);
    }

    public function artists(): View
    {
        return view('site.artists', [
            'seo' => $this->seo->page('artists'),
            'activeNav' => 'artists',
            'newsletterHeadline' => 'فنانون جدد<br><em>في بريدك</em>',
            'newsletterSub' => 'ملفات حصرية عن الفنانين العرب — أسبوعياً.',
        ]);
    }

    public function artistShow(int $id): View
    {
        $person = Person::query()->where('category', 'artist')->findOrFail($id);

        return view('site.artist-details', [
            'seo' => $this->seo->fromPerson($person, 'artists.show'),
            'activeNav' => 'artists',
            'personId' => $id,
            'footerVariant' => 'compact',
        ]);
    }

    public function business(): View
    {
        return view('site.business', [
            'seo' => $this->seo->page('business'),
            'activeNav' => 'business',
            'newsletterHeadline' => 'قصص الأعمال<br><em>في بريدك</em>',
            'newsletterSub' => 'ملفات حصرية عن رواد الأعمال العرب — أسبوعياً.',
        ]);
    }

    public function businessShow(int $id): View
    {
        $person = Person::query()->where('category', 'business')->findOrFail($id);

        return view('site.business-details', [
            'seo' => $this->seo->fromPerson($person, 'business.show'),
            'activeNav' => 'business',
            'personId' => $id,
            'footerVariant' => 'compact',
        ]);
    }

    public function fashion(): View
    {
        return view('site.fashion', [
            'seo' => $this->seo->page('fashion'),
            'activeNav' => 'fashion',
            'newsletterHeadline' => 'الموضة العربية<br><em>كل أسبوع</em>',
            'newsletterSub' => 'أحدث تقارير الموضة والتصميم من فريق التحرير.',
        ]);
    }

    public function fashionShow(int $id): View
    {
        $article = Article::query()
            ->where('category', 'موضة')
            ->where('status', 'published')
            ->findOrFail($id);

        return view('site.fashion-details', [
            'seo' => $this->seo->fromArticle($article, 'fashion.show'),
            'activeNav' => 'fashion',
            'articleId' => $id,
            'footerVariant' => 'compact',
        ]);
    }

    public function interviews(): View
    {
        return view('site.interviews', [
            'seo' => $this->seo->page('interviews'),
            'showTicker' => true,
            'activeNav' => 'interviews',
            'newsletterHeadline' => 'مقابلات حصرية<br><em>في بريدك</em>',
            'newsletterSub' => 'أبرز المقابلات مع شخصيات عربية مؤثرة — أسبوعياً.',
        ]);
    }

    public function interviewShow(Interview $interview, FileUploadService $files): View
    {
        abort_unless($interview->status === 'published', 404);

        $interview->incrementViews();

        $rawVideo = $interview->getAttributes()['video_url'] ?? null;
        $rawThumb = $interview->getAttributes()['thumbnail_url'] ?? null;

        $videoUrl = $files->playbackUrl($rawVideo, route('interviews.stream', $interview)) ?? $rawVideo;
        $thumbnailUrl = $files->resolveUrl($rawThumb) ?? $rawThumb;

        $latestArticles = Article::query()
            ->where('status', 'published')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(function (Article $article) use ($files) {
                $article->setAttribute(
                    'image_url',
                    $files->resolveUrl($article->getAttributes()['image_url'] ?? null) ?? $article->image_url
                );

                return $article;
            });

        return view('site.interview-details', [
            'seo' => $this->seo->fromInterview($interview),
            'activeNav' => 'interviews',
            'interview' => $interview,
            'videoUrl' => $videoUrl,
            'thumbnailUrl' => $thumbnailUrl,
            'latestArticles' => $latestArticles,
            'footerVariant' => 'compact',
            'skipGsap' => true,
        ]);
    }

    public function interviewStream(Interview $interview, FileUploadService $files): StreamedResponse
    {
        abort_unless($interview->status === 'published', 404);

        $key = $files->s3ObjectKey($interview->getAttributes()['video_url'] ?? null);

        abort_unless($key && Storage::disk('s3')->exists($key), 404);

        return Storage::disk('s3')->response($key);
    }
}
