<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Blog;
use App\Models\Person;
use Illuminate\Support\Collection;

class SiteContentService
{
    public function __construct(
        protected FileUploadService $files,
    ) {}

    public function resolveArticleImage(?Article $article): ?string
    {
        if (! $article) {
            return null;
        }

        return $this->files->resolveUrl($article->getAttributes()['image_url'] ?? null) ?? $article->image_url;
    }

    public function resolvePersonImage(?Person $person): ?string
    {
        if (! $person) {
            return null;
        }

        return $this->files->resolveUrl($person->getAttributes()['image_url'] ?? null) ?? $person->image_url;
    }

    public function resolveBlogImage(?Blog $blog): ?string
    {
        if (! $blog) {
            return null;
        }

        return $this->files->resolveUrl($blog->getAttributes()['image_url'] ?? null) ?? $blog->image_url;
    }

    /** @return array<string, mixed> */
    public function homeData(): array
    {
        $hero = Article::query()
            ->published()
            ->where('featured', true)
            ->orderByDesc('created_at')
            ->first()
            ?? Article::query()->published()->orderByDesc('created_at')->first();

        if ($hero) {
            $hero->setAttribute('image_url', $this->resolveArticleImage($hero));
        }

        $sidebarArticles = Article::query()
            ->published()
            ->when($hero, fn ($q) => $q->where('id', '!=', $hero->id))
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->each(fn (Article $a) => $a->setAttribute('image_url', $this->resolveArticleImage($a)));

        $mapPeople = fn (string $category, int $limit = 4) => Person::query()
            ->where('category', $category)
            ->orderByDesc('featured')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get()
            ->each(fn (Person $p) => $p->setAttribute('image_url', $this->resolvePersonImage($p)));

        $businessArticles = Article::query()
            ->published()
            ->where('category', 'أعمال')
            ->orderByDesc('featured')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->each(fn (Article $a) => $a->setAttribute('image_url', $this->resolveArticleImage($a)));

        $fashionArticles = Article::query()
            ->published()
            ->where('category', 'موضة')
            ->orderByDesc('featured')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->each(fn (Article $a) => $a->setAttribute('image_url', $this->resolveArticleImage($a)));

        $newsArticles = Article::query()
            ->published()
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->each(fn (Article $a) => $a->setAttribute('image_url', $this->resolveArticleImage($a)));

        $blogs = Blog::query()
            ->published()
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->each(fn (Blog $b) => $b->setAttribute('image_url', $this->resolveBlogImage($b)));

        return [
            'hero' => $hero,
            'sidebarArticles' => $sidebarArticles,
            'influencers' => $mapPeople('influencer'),
            'artists' => $mapPeople('artist', 6),
            'doctors' => $mapPeople('doctor'),
            'businessPeople' => $mapPeople('business', 2),
            'businessArticles' => $businessArticles,
            'fashionArticles' => $fashionArticles,
            'newsArticles' => $newsArticles,
            'blogs' => $blogs,
            'counts' => [
                'influencers' => Person::where('category', 'influencer')->count(),
                'artists' => Person::where('category', 'artist')->count(),
                'doctors' => Person::where('category', 'doctor')->count(),
                'business' => Person::where('category', 'business')->count(),
                'articles' => Article::published()->count(),
                'blogs' => Blog::published()->count(),
                'fashion' => Article::published()->where('category', 'موضة')->count(),
            ],
        ];
    }

    /** @return Collection<int, Article> */
    public function initialArticles(?string $category = null, int $limit = 12): Collection
    {
        return Article::query()
            ->published()
            ->when(filled($category) && $category !== 'all', fn ($q) => $q->where('category', $category))
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->each(fn (Article $a) => $a->setAttribute('image_url', $this->resolveArticleImage($a)));
    }

    /** @return Collection<int, Blog> */
    public function initialBlogs(int $limit = 12): Collection
    {
        return Blog::query()
            ->published()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->each(fn (Blog $b) => $b->setAttribute('image_url', $this->resolveBlogImage($b)));
    }

    /** @return Collection<int, Person> */
    public function initialPeople(string $category, int $limit = 12): Collection
    {
        return Person::query()
            ->where('category', $category)
            ->orderByDesc('featured')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get()
            ->each(fn (Person $p) => $p->setAttribute('image_url', $this->resolvePersonImage($p)));
    }

    public function preparePerson(Person $person): Person
    {
        $person->setAttribute('image_url', $this->resolvePersonImage($person));

        return $person;
    }

    /** @return Collection<int, Person> */
    public function relatedPeople(Person $person, int $limit = 4): Collection
    {
        return Person::query()
            ->where('category', $person->category)
            ->where('id', '!=', $person->id)
            ->orderByDesc('featured')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get()
            ->each(fn (Person $p) => $p->setAttribute('image_url', $this->resolvePersonImage($p)));
    }

    public function personShowRoute(Person $person): string
    {
        return match ($person->category) {
            'doctor' => 'doctors.show',
            'influencer' => 'influencers.show',
            'artist' => 'artists.show',
            'business' => 'business.show',
            default => 'home',
        };
    }
}
