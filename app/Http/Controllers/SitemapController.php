<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Blog;
use App\Models\Interview;
use App\Models\MagazineIssue;
use App\Models\Person;
use App\Support\HomeSections;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([
            $this->entry(route('home'), now(), 'daily', '1.0'),
            $this->entry(route('about'), now(), 'monthly', '0.5'),
            $this->entry(route('editorial'), now(), 'monthly', '0.5'),
            $this->entry(route('privacy'), now(), 'monthly', '0.4'),
            $this->entry(route('terms'), now(), 'monthly', '0.4'),
            $this->entry(route('contact'), now(), 'monthly', '0.5'),
            $this->entry(route('advertise'), now(), 'monthly', '0.4'),
            $this->entry(route('news.index'), now(), 'daily', '0.9'),
            $this->entry(route('blogs.index'), now(), 'daily', '0.9'),
            $this->entry(route('doctors.index'), now(), 'weekly', '0.8'),
            $this->entry(route('influencers.index'), now(), 'weekly', '0.8'),
            $this->entry(route('artists.index'), now(), 'weekly', '0.8'),
            $this->entry(route('business.index'), now(), 'weekly', '0.8'),
            $this->entry(route('fashion.index'), now(), 'weekly', '0.8'),
            $this->entry(route('magazine.index'), now(), 'weekly', '0.9'),
        ]);

        if (HomeSections::hasInterviews()) {
            $urls->push($this->entry(route('interviews.index'), now(), 'weekly', '0.9'));
        }

        Article::query()
            ->published()
            ->select(['id', 'slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->each(function (Article $article) use ($urls) {
                $urls->push($this->entry(
                    route('news.show', $article),
                    $article->updated_at,
                    'weekly',
                    '0.7',
                ));
            });

        Blog::query()
            ->published()
            ->select(['id', 'slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->each(function (Blog $blog) use ($urls) {
                $urls->push($this->entry(route('blogs.show', $blog), $blog->updated_at, 'weekly', '0.7'));
            });

        MagazineIssue::query()
            ->published()
            ->select(['slug', 'updated_at'])
            ->orderByDesc('sort_order')
            ->orderByDesc('updated_at')
            ->each(function (MagazineIssue $issue) use ($urls) {
                $urls->push($this->entry(route('magazine.show', $issue), $issue->updated_at, 'monthly', '0.7'));
            });

        if (HomeSections::hasInterviews()) {
            Interview::query()
                ->published()
                ->select(['slug', 'updated_at'])
                ->orderByDesc('updated_at')
                ->each(function (Interview $interview) use ($urls) {
                    $urls->push($this->entry(route('interviews.show', $interview), $interview->updated_at, 'weekly', '0.7'));
                });
        }

        foreach (['doctor' => 'doctors.show', 'influencer' => 'influencers.show', 'artist' => 'artists.show', 'business' => 'business.show'] as $category => $routeName) {
            Person::query()
                ->where('category', $category)
                ->select(['id', 'updated_at'])
                ->orderByDesc('updated_at')
                ->each(function (Person $person) use ($urls, $routeName) {
                    $urls->push($this->entry(route($routeName, $person), $person->updated_at, 'monthly', '0.6'));
                });
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /** @return array{loc: string, lastmod: string, changefreq: string, priority: string} */
    protected function entry(string $loc, $lastmod, string $changefreq, string $priority): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod->toAtomString(),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
