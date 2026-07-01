<?php

namespace App\Http\Controllers;

use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function __construct(
        protected FileUploadService $files,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,webp,gif|max:15360',
        ]);

        $file = $request->file('image');
        $path = $this->files->uploadFile($file, 'images');

        return response()->json([
            'success' => true,
            'url' => $this->files->resolveUrl($path),
            'path' => $path,
        ]);
    }

    public function storeVideo(Request $request): JsonResponse
    {
        set_time_limit(600);

        $request->validate([
            'video' => 'required|file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo|max:512000',
        ]);

        $path = $this->files->uploadFile($request->file('video'), 'interviews/videos');

        return response()->json([
            'success' => true,
            'url' => $this->files->resolveUrl($path),
            'path' => $path,
        ]);
    }
}
