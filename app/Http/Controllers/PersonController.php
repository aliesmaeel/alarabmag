<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PersonController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Person::query()->orderByDesc('featured')->orderByDesc('created_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->boolean('featured')) {
            $query->where('featured', true);
        }

        $people = $query->limit($request->get('limit', 100))->get();

        return response()->json(['success' => true, 'data' => $people]);
    }

    public function show(int $id): JsonResponse
    {
        $person = Person::findOrFail($id);
        return response()->json(['success' => true, 'data' => $person]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:200',
            'name_en'    => 'nullable|string|max:200',
            'role'       => 'nullable|string|max:200',
            'category'   => 'required|in:influencer,artist,doctor,business',
            'country'    => 'nullable|string|max:100',
            'flag'       => 'nullable|string|max:10',
            'image_url'  => 'nullable|string|max:1000',
            'excerpt'    => 'nullable|string|max:1000',
            'bio'        => 'nullable|string',
            'stat'       => 'nullable|string|max:100',
            'stat_label' => 'nullable|string|max:200',
            'handle'     => 'nullable|string|max:100',
            'platform'   => 'nullable|string|max:100',
            'followers'  => 'nullable|string|max:50',
            'hospital'   => 'nullable|string|max:300',
            'specialty'  => 'nullable|string|max:200',
            'badge'      => 'nullable|string|max:200',
            'company'    => 'nullable|string|max:200',
            'net_worth'  => 'nullable|string|max:100',
            'featured'   => 'nullable|boolean',
        ]);

        $person = Person::create($validated);

        return response()->json(['success' => true, 'data' => ['id' => $person->id]], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $person = Person::findOrFail($id);

        $validated = $request->validate([
            'name'       => 'required|string|max:200',
            'name_en'    => 'nullable|string|max:200',
            'role'       => 'nullable|string|max:200',
            'category'   => 'required|in:influencer,artist,doctor,business',
            'country'    => 'nullable|string|max:100',
            'flag'       => 'nullable|string|max:10',
            'image_url'  => 'nullable|string|max:1000',
            'excerpt'    => 'nullable|string|max:1000',
            'bio'        => 'nullable|string',
            'stat'       => 'nullable|string|max:100',
            'stat_label' => 'nullable|string|max:200',
            'handle'     => 'nullable|string|max:100',
            'platform'   => 'nullable|string|max:100',
            'followers'  => 'nullable|string|max:50',
            'hospital'   => 'nullable|string|max:300',
            'specialty'  => 'nullable|string|max:200',
            'badge'      => 'nullable|string|max:200',
            'company'    => 'nullable|string|max:200',
            'net_worth'  => 'nullable|string|max:100',
            'featured'   => 'nullable|boolean',
        ]);

        $person->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy(int $id): JsonResponse
    {
        Person::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
