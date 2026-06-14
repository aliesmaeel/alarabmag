<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Blog;
use App\Models\Interview;
use App\Models\Person;
use App\Services\FileUploadService;
use App\Services\SeoService;
use App\Services\SiteContentService;
use App\Support\HomeSections;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiteController extends Controller
{
    public function __construct(
        protected SeoService $seo,
        protected SiteContentService $content,
    ) {}

    public function home(): View
    {
        return view('site.home', [
            'seo' => $this->seo->page('home'),
            'home' => $this->content->homeData(),
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
            'initialArticles' => $this->content->initialArticles(null, 12),
            'showTicker' => true,
            'activeNav' => 'news',
            'newsletterHeadline' => 'احصل على نشرة<br><em>الأخبار اليومية</em>',
            'newsletterSub' => 'ملخص يومي لأهم الأخبار العربية — مختار بعناية من فريق التحرير.',
        ]);
    }

    public function newsShow(Article $article, FileUploadService $files): View
    {
        abort_unless($article->status === 'published', 404);

        $article->incrementViews();
        $article->setAttribute('image_url', $this->content->resolveArticleImage($article));

        $relatedArticles = Article::query()
            ->published()
            ->where('id', '!=', $article->id)
            ->when(filled($article->category), fn ($q) => $q->where('category', $article->category))
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->each(fn (Article $related) => $related->setAttribute('image_url', $this->content->resolveArticleImage($related)));

        return view('site.news-details', [
            'seo' => $this->seo->fromArticle($article),
            'jsonLdEntity' => $article,
            'activeNav' => 'news',
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'footerVariant' => 'compact',
        ]);
    }

    public function newsRedirectFromId(int $id): RedirectResponse
    {
        $article = Article::query()->published()->findOrFail($id);

        return redirect()->route('news.show', $article, 301);
    }

    public function blogs(): View
    {
        return view('site.blogs', [
            'seo' => $this->seo->page('blogs'),
            'initialBlogs' => $this->content->initialBlogs(12),
            'showTicker' => true,
            'activeNav' => 'blogs',
            'newsletterHeadline' => 'مقالات جديدة<br><em>كل أسبوع</em>',
            'newsletterSub' => 'أفضل المدونات العربية — مختارة من فريق التحرير.',
        ]);
    }

    public function blogShow(Blog $blog, FileUploadService $files): View
    {
        abort_unless($blog->status === 'published', 404);

        $blog->incrementViews();
        $blog->setAttribute('image_url', $this->content->resolveBlogImage($blog));
        $blog->setAttribute(
            'author_img',
            $files->resolveUrl($blog->getAttributes()['author_img'] ?? null) ?? $blog->author_img
        );

        $relatedBlogs = Blog::query()
            ->published()
            ->where('id', '!=', $blog->id)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->each(fn (Blog $related) => $related->setAttribute('image_url', $this->content->resolveBlogImage($related)));

        return view('site.blog-details', [
            'seo' => $this->seo->fromBlog($blog),
            'jsonLdEntity' => $blog,
            'activeNav' => 'blogs',
            'blog' => $blog,
            'relatedBlogs' => $relatedBlogs,
            'footerVariant' => 'compact',
        ]);
    }

    public function blogRedirectFromId(int $id): RedirectResponse
    {
        $blog = Blog::query()->published()->findOrFail($id);

        return redirect()->route('blogs.show', $blog, 301);
    }

    public function doctors(): View
    {
        return view('site.doctors', [
            'seo' => $this->seo->page('doctors'),
            'initialPeople' => $this->content->initialPeople('doctor', 12),
            'activeNav' => 'doctors',
            'newsletterHeadline' => 'قصص الأطباء<br><em>في بريدك</em>',
            'newsletterSub' => 'ملفات حصرية عن الأطباء العرب الأكثر تأثيراً — أسبوعياً.',
        ]);
    }

    public function doctorShow(int $id): View
    {
        return $this->personShow($id, 'doctor', 'doctors', 'site.doctor-details');
    }

    public function influencers(): View
    {
        return view('site.influencers', [
            'seo' => $this->seo->page('influencers'),
            'initialPeople' => $this->content->initialPeople('influencer', 12),
            'activeNav' => 'influencers',
            'newsletterHeadline' => 'المؤثرون الجدد<br><em>قبل الجميع</em>',
            'newsletterSub' => 'اكتشف نجوم السوشيال ميديا العرب الصاعدين أسبوعياً في بريدك.',
        ]);
    }

    public function influencerShow(int $id): View
    {
        return $this->personShow($id, 'influencer', 'influencers', 'site.influencer-details');
    }

    public function artists(): View
    {
        return view('site.artists', [
            'seo' => $this->seo->page('artists'),
            'initialPeople' => $this->content->initialPeople('artist', 12),
            'activeNav' => 'artists',
            'newsletterHeadline' => 'فنانون جدد<br><em>في بريدك</em>',
            'newsletterSub' => 'ملفات حصرية عن الفنانين العرب — أسبوعياً.',
        ]);
    }

    public function artistShow(int $id): View
    {
        return $this->personShow($id, 'artist', 'artists', 'site.artist-details');
    }

    public function business(): View
    {
        return view('site.business', [
            'seo' => $this->seo->page('business'),
            'initialPeople' => $this->content->initialPeople('business', 12),
            'initialArticles' => $this->content->initialArticles('أعمال', 6),
            'activeNav' => 'business',
            'newsletterHeadline' => 'قصص الأعمال<br><em>في بريدك</em>',
            'newsletterSub' => 'ملفات حصرية عن رواد الأعمال العرب — أسبوعياً.',
        ]);
    }

    public function businessShow(int $id): View
    {
        return $this->personShow($id, 'business', 'business', 'site.business-details');
    }

    public function fashion(): View
    {
        return view('site.fashion', [
            'seo' => $this->seo->page('fashion'),
            'initialArticles' => $this->content->initialArticles('موضة', 12),
            'activeNav' => 'fashion',
            'newsletterHeadline' => 'الموضة العربية<br><em>كل أسبوع</em>',
            'newsletterSub' => 'أحدث تقارير الموضة والتصميم من فريق التحرير.',
        ]);
    }

    public function fashionShow(int $id): RedirectResponse
    {
        $article = Article::query()
            ->where('category', 'موضة')
            ->published()
            ->findOrFail($id);

        return redirect()->route('news.show', $article, 301);
    }

    public function interviews(): View
    {
        abort_unless(HomeSections::hasInterviews(), 404);

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
            ->published()
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->each(fn (Article $article) => $article->setAttribute('image_url', $this->content->resolveArticleImage($article)));

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

    protected function personShow(int $id, string $category, string $navKey, string $view): View
    {
        $person = Person::query()->where('category', $category)->findOrFail($id);
        $person = $this->content->preparePerson($person);
        $routeName = match ($category) {
            'doctor' => 'doctors.show',
            'influencer' => 'influencers.show',
            'artist' => 'artists.show',
            'business' => 'business.show',
            default => 'home',
        };

        return view($view, [
            'seo' => $this->seo->fromPerson($person, $routeName),
            'jsonLdEntity' => $person,
            'activeNav' => $navKey,
            'person' => $person,
            'relatedPeople' => $this->content->relatedPeople($person),
            'footerVariant' => 'compact',
        ]);
    }
}
