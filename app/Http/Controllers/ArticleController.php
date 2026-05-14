<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Article::query()->orderByDesc('created_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->boolean('featured')) {
            $query->where('featured', true);
        }
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        $limit  = (int) $request->get('limit', 100);
        $offset = (int) $request->get('offset', 0);

        $articles = $query->limit($limit)->offset($offset)->get();

        return response()->json(['success' => true, 'data' => $articles]);
    }

    public function show(int $id): JsonResponse
    {
        $article = Article::findOrFail($id);
        $article->incrementViews();
        return response()->json(['success' => true, 'data' => $article]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:1000',
            'subtitle'  => 'nullable|string|max:500',
            'excerpt'   => 'nullable|string',
            'body'      => 'nullable|string',
            'category'  => 'nullable|string|max:100',
            'author'    => 'nullable|string|max:200',
            'image_url' => 'nullable|string|max:1000',
            'read_time' => 'nullable|string|max:50',
            'featured'  => 'nullable|boolean',
            'status'    => 'nullable|in:published,draft',
            'region'    => 'nullable|string|max:100',
        ]);

        $validated['category'] = $validated['category'] ?? 'عام';
        $validated['author']   = $validated['author']   ?? 'فريق التحرير';
        $validated['read_time']= $validated['read_time'] ?? '5 دقائق';
        $validated['status']   = $validated['status']   ?? 'published';

        $article = Article::create($validated);

        return response()->json(['success' => true, 'data' => ['id' => $article->id]], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title'     => 'required|string|max:1000',
            'subtitle'  => 'nullable|string|max:500',
            'excerpt'   => 'nullable|string',
            'body'      => 'nullable|string',
            'category'  => 'nullable|string|max:100',
            'author'    => 'nullable|string|max:200',
            'image_url' => 'nullable|string|max:1000',
            'read_time' => 'nullable|string|max:50',
            'featured'  => 'nullable|boolean',
            'status'    => 'nullable|in:published,draft',
            'region'    => 'nullable|string|max:100',
        ]);

        $article->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy(int $id): JsonResponse
    {
        Article::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
