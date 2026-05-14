<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Blog;
use App\Models\Person;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'articles'    => Article::count(),
            'blogs'       => Blog::count(),
            'people'      => Person::count(),
            'published'   => Article::where('status', 'published')->count(),
            'drafts'      => Article::where('status', 'draft')->count(),
            'influencers' => Person::where('category', 'influencer')->count(),
            'artists'     => Person::where('category', 'artist')->count(),
            'doctors'     => Person::where('category', 'doctor')->count(),
            'business'    => Person::where('category', 'business')->count(),
            'featured_articles' => Article::where('featured', true)->count(),
            'total_views' => Article::sum('views') + Blog::sum('views'),
        ]);
    }
}
