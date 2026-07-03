<?php

namespace App\Services;

use App\Filament\Support\ImageUpload;
use App\Models\Article;
use App\Models\Blog;
use App\Models\Interview;
use App\Models\Person;
use App\Models\Setting;
use App\Support\SeoMeta;
use App\Support\SiteBrand;
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

    public function staticPage(string $pageKey, string $title, string $description): SeoMeta
    {
        $siteName = $this->setting('site_name', SiteBrand::NAME_AR);

        return $this->makeMeta(
            title: $this->suffixSiteName($title, $siteName),
            description: $description,
            keywords: $this->setting('seo_keywords'),
            canonical: $this->pageUrl($pageKey),
            ogTitle: $title,
            ogDescription: $description,
            ogImage: $this->defaultOgImage(),
            ogType: 'website',
        );
    }

    public function page(string $pageKey, ?string $canonical = null): SeoMeta
    {
        $siteName = $this->setting('site_name', SiteBrand::NAME_AR);
        $title = $this->setting("seo_{$pageKey}_title")
            ?: ($pageKey === 'home' ? SiteBrand::homeTitle() : $this->setting('seo_title'))
            ?: ($pageKey === 'home' ? SiteBrand::homeTitle() : SiteBrand::defaultTitle());
        $description = $this->setting("seo_{$pageKey}_description")
            ?: $this->setting('seo_description')
            ?: ($pageKey === 'home' ? SiteBrand::homeDescription() : SiteBrand::defaultDescription())
            ?: $this->setting('site_description');
        $keywords = $this->setting("seo_{$pageKey}_keywords")
            ?: $this->setting('seo_keywords')
            ?: SiteBrand::KEYWORDS;

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
        $siteName = $this->setting('site_name', SiteBrand::NAME_AR);
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
        $siteName = $this->setting('site_name', SiteBrand::NAME_AR);
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

    public function fromInterview(Interview $interview): SeoMeta
    {
        $siteName = $this->setting('site_name', SiteBrand::NAME_AR);
        $title = $interview->meta_title ?: $interview->title;
        $description = $interview->meta_description ?: $interview->description ?: Str::limit(strip_tags($interview->description ?? ''), 160);
        $canonical = route('interviews.show', $interview);

        return $this->makeMeta(
            title: $this->suffixSiteName($title, $siteName),
            description: $description,
            keywords: $interview->meta_keywords ?: $this->setting('seo_keywords'),
            canonical: $canonical,
            ogTitle: $interview->og_title ?: $title,
            ogDescription: $interview->og_description ?: $description,
            ogImage: $this->resolveImage($interview->og_image ?: $interview->thumbnail_url),
            ogType: 'video.other',
        );
    }

    public function fromPerson(Person $person, string $routeName): SeoMeta
    {
        $siteName = $this->setting('site_name', SiteBrand::NAME_AR);
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
            ogSiteName: $this->setting('og_site_name') ?: $this->setting('site_name', SiteBrand::NAME_AR),
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
            'interviews' => route('interviews.index'),
            'about' => route('about'),
            'editorial' => route('editorial'),
            'privacy' => route('privacy'),
            'terms' => route('terms'),
            'contact' => route('contact'),
            'advertise' => route('advertise'),
            default => url('/'),
        };
    }

    /** @return list<array<string, mixed>> */
    public function jsonLd(?SeoMeta $seo = null, mixed $entity = null): array
    {
        $graphs = [$this->organizationSchema()];

        if (request()->routeIs('home')) {
            $graphs[] = $this->websiteSchema();
            $graphs[] = $this->magazineSchema();
        }

        if ($seo) {
            $graphs[] = $this->webPageSchema($seo);
        }

        if ($entity instanceof Article) {
            $graphs[] = $this->newsArticleSchema($entity);
        } elseif ($entity instanceof Blog) {
            $graphs[] = $this->blogPostingSchema($entity);
        } elseif ($entity instanceof Person) {
            $graphs[] = $this->personSchema($entity);
        }

        return $graphs;
    }

    /** @return array<string, mixed> */
    protected function organizationSchema(): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsMediaOrganization',
            '@id' => url('/').'#organization',
            'name' => SiteBrand::NAME_AR,
            'alternateName' => SiteBrand::alternateNames(),
            'url' => url('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $this->defaultOgImage(),
            ],
            'description' => $this->setting('site_description', SiteBrand::defaultDescription()),
            'knowsAbout' => SiteBrand::knowsAbout(),
            'inLanguage' => 'ar',
        ];

        if ($email = $this->setting('editor_email')) {
            $schema['email'] = $email;
        }

        $schema['location'] = [
            '@type' => 'Place',
            'name' => 'مدينة دبي للإعلام',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Dubai',
                'addressCountry' => 'AE',
            ],
        ];

        $sameAs = array_values(array_filter([
            $this->socialUrl('instagram'),
            $this->socialUrl('twitter'),
            $this->socialUrl('youtube'),
            $this->socialUrl('facebook'),
            $this->socialUrl('tiktok'),
        ]));

        if ($sameAs !== []) {
            $schema['sameAs'] = $sameAs;
        }

        return $schema;
    }

    /** @return array<string, mixed> */
    protected function websiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'name' => SiteBrand::NAME_AR,
            'alternateName' => SiteBrand::alternateNames(),
            'url' => url('/'),
            'description' => SiteBrand::homeDescription(),
            'inLanguage' => 'ar',
            'publisher' => ['@id' => url('/').'#organization'],
            'about' => ['@id' => url('/').'#magazine'],
        ];
    }

    /** @return array<string, mixed> */
    protected function magazineSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Magazine',
            '@id' => url('/').'#magazine',
            'name' => SiteBrand::NAME_AR,
            'alternateName' => SiteBrand::alternateNames(),
            'url' => url('/'),
            'description' => SiteBrand::homeDescription(),
            'inLanguage' => 'ar',
            'publisher' => ['@id' => url('/').'#organization'],
            'isPartOf' => ['@id' => url('/').'#website'],
        ];
    }

    /** @return array<string, mixed> */
    protected function webPageSchema(SeoMeta $seo): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            '@id' => ($seo->canonical ?: url('/')).'#webpage',
            'url' => $seo->canonical ?: url('/'),
            'name' => $seo->title,
            'description' => $seo->description,
            'inLanguage' => 'ar',
            'isPartOf' => ['@id' => url('/').'#website'],
            'about' => ['@id' => url('/').'#organization'],
            'publisher' => ['@id' => url('/').'#organization'],
        ];
    }

    protected function socialUrl(string $platform): ?string
    {
        $value = $this->setting($platform);
        if (! filled($value)) {
            return null;
        }

        return match ($platform) {
            'instagram' => str_starts_with($value, 'http') ? $value : 'https://instagram.com/'.ltrim($value, '@'),
            'twitter' => str_starts_with($value, 'http') ? $value : 'https://x.com/'.ltrim($value, '@'),
            'youtube' => str_starts_with($value, 'http') ? $value : 'https://youtube.com/'.ltrim($value, '@'),
            'facebook' => str_starts_with($value, 'http') ? $value : 'https://facebook.com/'.ltrim($value, '@'),
            'tiktok' => str_starts_with($value, 'http') ? $value : 'https://tiktok.com/@'.ltrim($value, '@'),
            default => null,
        };
    }

    /** @return array<string, mixed> */
    protected function newsArticleSchema(Article $article): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $article->title,
            'description' => $article->excerpt ?: Str::limit(strip_tags($article->body ?? ''), 160),
            'image' => $this->resolveImage($article->image_url),
            'datePublished' => $article->created_at?->toIso8601String(),
            'dateModified' => $article->updated_at?->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $article->author ?: 'فريق التحرير',
            ],
            'publisher' => ['@id' => url('/').'#organization'],
            'mainEntityOfPage' => route('news.show', $article),
            'inLanguage' => 'ar',
        ];
    }

    /** @return array<string, mixed> */
    protected function blogPostingSchema(Blog $blog): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $blog->title,
            'description' => $blog->excerpt ?: Str::limit(strip_tags($blog->body ?? ''), 160),
            'image' => $this->resolveImage($blog->image_url),
            'datePublished' => $blog->created_at?->toIso8601String(),
            'dateModified' => $blog->updated_at?->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $blog->author ?: 'فريق التحرير',
            ],
            'publisher' => ['@id' => url('/').'#organization'],
            'mainEntityOfPage' => route('blogs.show', $blog),
            'inLanguage' => 'ar',
        ];
    }

    /** @return array<string, mixed> */
    protected function personSchema(Person $person): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $person->name,
            'alternateName' => $person->name_en,
            'description' => $person->excerpt ?: Str::limit(strip_tags($person->bio ?? ''), 160),
            'image' => $this->resolveImage($person->image_url),
            'jobTitle' => $person->role,
            'nationality' => $person->country,
        ];
    }
}
