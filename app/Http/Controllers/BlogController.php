<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Blog::query()->orderByDesc('created_at');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->boolean('featured')) {
            $query->where('featured', true);
        }

        $blogs = $query->limit($request->get('limit', 100))->get();

        return response()->json(['success' => true, 'data' => $blogs]);
    }

    public function show(int $id): JsonResponse
    {
        $blog = Blog::findOrFail($id);
        $blog->incrementViews();
        return response()->json(['success' => true, 'data' => $blog]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:1000',
            'excerpt'    => 'nullable|string',
            'body'       => 'nullable|string',
            'author'     => 'nullable|string|max:200',
            'author_bio' => 'nullable|string|max:500',
            'author_img' => 'nullable|string|max:1000',
            'image_url'  => 'nullable|string|max:1000',
            'tags'       => 'nullable|string|max:500',
            'featured'   => 'nullable|boolean',
            'status'     => 'nullable|in:published,draft',
        ]);

        $validated['author'] = $validated['author'] ?? 'فريق التحرير';
        $validated['status'] = $validated['status'] ?? 'published';

        $blog = Blog::create($validated);

        return response()->json(['success' => true, 'data' => ['id' => $blog->id]], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $blog = Blog::findOrFail($id);

        $validated = $request->validate([
            'title'      => 'required|string|max:1000',
            'excerpt'    => 'nullable|string',
            'body'       => 'nullable|string',
            'author'     => 'nullable|string|max:200',
            'author_bio' => 'nullable|string|max:500',
            'author_img' => 'nullable|string|max:1000',
            'image_url'  => 'nullable|string|max:1000',
            'tags'       => 'nullable|string|max:500',
            'featured'   => 'nullable|boolean',
            'status'     => 'nullable|in:published,draft',
        ]);

        $blog->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy(int $id): JsonResponse
    {
        Blog::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
