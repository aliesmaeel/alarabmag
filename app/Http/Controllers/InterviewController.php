<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Interview::query()->orderByDesc('created_at');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->boolean('featured')) {
            $query->where('featured', true);
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $interviews = $query->limit($request->integer('limit', 100))->get();

        return response()->json(['success' => true, 'data' => $interviews]);
    }

    public function show(string $slug): JsonResponse
    {
        $interview = Interview::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $interview->incrementViews();

        return response()->json(['success' => true, 'data' => $interview]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:1000',
            'slug'          => 'nullable|string|max:255|unique:interviews,slug',
            'description'   => 'nullable|string',
            'video_url'     => 'required|string|max:1000|url',
            'category'      => 'nullable|string|max:100',
            'thumbnail_url' => 'nullable|string|max:1000',
            'featured'      => 'nullable|boolean',
            'status'        => 'nullable|in:published,draft',
        ]);

        $validated['category'] = $validated['category'] ?? 'عام';
        $validated['status'] = $validated['status'] ?? 'published';

        $interview = Interview::create($validated);

        return response()->json(['success' => true, 'data' => ['id' => $interview->id, 'slug' => $interview->slug]], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $interview = Interview::findOrFail($id);

        $validated = $request->validate([
            'title'         => 'required|string|max:1000',
            'slug'          => 'nullable|string|max:255|unique:interviews,slug,' . $id,
            'description'   => 'nullable|string',
            'video_url'     => 'required|string|max:1000|url',
            'category'      => 'nullable|string|max:100',
            'thumbnail_url' => 'nullable|string|max:1000',
            'featured'      => 'nullable|boolean',
            'status'        => 'nullable|in:published,draft',
        ]);

        $interview->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy(int $id): JsonResponse
    {
        Interview::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
