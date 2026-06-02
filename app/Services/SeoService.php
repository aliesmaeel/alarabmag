<?php

namespace App\Services;

use App\Filament\Support\ImageUpload;
use App\Models\Article;
use App\Models\Blog;
use App\Models\Person;
use App\Models\Setting;
use App\Support\SeoMeta;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SeoService
{
    protected array $settings;

    public function __construct()
    {
        $this->settings = Cache::remember('site_settings', 3600, fn () => Setting::getAllAsArray());
    }

    public static function forgetCache(): void
    {
        Cache::forget('site_settings');
    }

    public function page(string $pageKey, ?string $canonical = null): SeoMeta
    {
        $siteName = $this->setting('site_name', 'مجلة العرب');
        $title = $this->setting("seo_{$pageKey}_title")
            ?: $this->setting('seo_title')
            ?: $siteName;
        $description = $this->setting("seo_{$pageKey}_description")
            ?: $this->setting('seo_description')
            ?: $this->setting('site_description');
        $keywords = $this->setting("seo_{$pageKey}_keywords")
            ?: $this->setting('seo_keywords');

        return $this->makeMeta(
            title: $this->suffixSiteName($title, $siteName, $pageKey === 'home'),
            description: $description,
            keywords: $keywords,
            canonical: $canonical ?? $this->pageUrl($pageKey),
            ogTitle: $this->setting("og_{$pageKey}_title") ?: $title,
            ogDescription: $this->setting("og_{$pageKey}_description") ?: $description,
            ogImage: $this->resolveImage($this->setting("og_{$pageKey}_image")),
            ogType: 'website',
        );
    }

    public function fromArticle(Article $article, string $routeName = 'news.show'): SeoMeta
    {
        $siteName = $this->setting('site_name', 'مجلة العرب');
        $title = $article->meta_title ?: $article->title;
        $description = $article->meta_description ?: $article->excerpt ?: Str::limit(strip_tags($article->body ?? ''), 160);
        $canonical = route($routeName, $article);

        return $this->makeMeta(
            title: $this->suffixSiteName($title, $siteName),
            description: $description,
            keywords: $article->meta_keywords ?: $this->setting('seo_keywords'),
            canonical: $canonical,
            ogTitle: $article->og_title ?: $title,
            ogDescription: $article->og_description ?: $description,
            ogImage: $this->resolveImage($article->og_image ?: $article->image_url),
            ogType: 'article',
        );
    }

    public function fromBlog(Blog $blog): SeoMeta
    {
        $siteName = $this->setting('site_name', 'مجلة العرب');
        $title = $blog->meta_title ?: $blog->title;
        $description = $blog->meta_description ?: $blog->excerpt ?: Str::limit(strip_tags($blog->body ?? ''), 160);
        $canonical = route('blogs.show', $blog);

        return $this->makeMeta(
            title: $this->suffixSiteName($title, $siteName),
            description: $description,
            keywords: $blog->meta_keywords ?: $blog->tags ?: $this->setting('seo_keywords'),
            canonical: $canonical,
            ogTitle: $blog->og_title ?: $title,
            ogDescription: $blog->og_description ?: $description,
            ogImage: $this->resolveImage($blog->og_image ?: $blog->image_url),
            ogType: 'article',
        );
    }

    public function fromPerson(Person $person, string $routeName): SeoMeta
    {
        $siteName = $this->setting('site_name', 'مجلة العرب');
        $title = $person->meta_title ?: $person->name;
        $description = $person->meta_description ?: $person->excerpt ?: Str::limit(strip_tags($person->bio ?? ''), 160);
        $canonical = route($routeName, $person);

        return $this->makeMeta(
            title: $this->suffixSiteName($title, $siteName),
            description: $description,
            keywords: $person->meta_keywords ?: $this->setting('seo_keywords'),
            canonical: $canonical,
            ogTitle: $person->og_title ?: $title,
            ogDescription: $person->og_description ?: $description,
            ogImage: $this->resolveImage($person->og_image ?: $person->image_url),
            ogType: 'profile',
        );
    }

    protected function makeMeta(
        string $title,
        ?string $description,
        ?string $keywords,
        string $canonical,
        ?string $ogTitle,
        ?string $ogDescription,
        ?string $ogImage,
        string $ogType,
    ): SeoMeta {
        return new SeoMeta(
            title: $title,
            description: $description,
            keywords: $keywords,
            canonical: $canonical,
            ogTitle: $ogTitle,
            ogDescription: $ogDescription,
            ogImage: $ogImage ?: $this->defaultOgImage(),
            ogUrl: $canonical,
            ogType: $ogType,
            ogSiteName: $this->setting('og_site_name') ?: $this->setting('site_name', 'مجلة العرب'),
            twitterCard: $this->setting('twitter_card', 'summary_large_image') ?: 'summary_large_image',
        );
    }

    protected function suffixSiteName(string $title, string $siteName, bool $isHome = false): string
    {
        if ($isHome) {
            return $title;
        }

        if (Str::contains($title, $siteName)) {
            return $title;
        }

        return "{$title} — {$siteName}";
    }

    protected function setting(string $key, ?string $default = null): ?string
    {
        $value = $this->settings[$key] ?? null;

        return filled($value) ? $value : $default;
    }

    protected function defaultOgImage(): ?string
    {
        return $this->resolveImage($this->setting('og_default_image')) ?: asset('logo.png');
    }

    protected function resolveImage(?string $path): ?string
    {
        return ImageUpload::resolveUrl($path);
    }

    protected function pageUrl(string $pageKey): string
    {
        return match ($pageKey) {
            'home' => route('home'),
            'news' => route('news.index'),
            'blogs' => route('blogs.index'),
            'doctors' => route('doctors.index'),
            'influencers' => route('influencers.index'),
            'artists' => route('artists.index'),
            'business' => route('business.index'),
            'fashion' => route('fashion.index'),
            default => url('/'),
        };
    }
}
