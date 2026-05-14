<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,webp,gif|max:15360', // 15MB
        ]);

        $file      = $request->file('image');
        $filename  = time() . '-' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                     . '.' . $file->getClientOriginalExtension();

        // Save directly to public/uploads so it's accessible immediately
        $file->move(public_path('uploads'), $filename);

        return response()->json([
            'success' => true,
            'url'     => '/uploads/' . $filename,
        ]);
    }
}
