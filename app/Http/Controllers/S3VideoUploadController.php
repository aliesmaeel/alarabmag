<?php

namespace App\Http\Controllers;

use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class S3VideoUploadController extends Controller
{
    public function __construct(
        protected FileUploadService $files,
    ) {}

    public function presign(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'filename' => 'required|string|max:255',
            'content_type' => 'required|string|in:'.implode(',', FileUploadService::VIDEO_MIME_TYPES),
            'size' => 'required|integer|min:1|max:'.FileUploadService::VIDEO_MAX_BYTES,
            'origin' => 'nullable|string|max:255',
        ]);

        $requestOrigin = $request->header('Origin')
            ?: ($validated['origin'] ?? null);

        try {
            $result = $this->files->createPresignedVideoUpload(
                $validated['filename'],
                $validated['content_type'],
                $requestOrigin,
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            ...$result,
        ]);
    }

    public function confirm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'path' => 'required|string|max:1000',
        ]);

        if (! $this->files->confirmVideoUpload($validated['path'])) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على الملف على S3.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'path' => $validated['path'],
            'url' => $this->files->resolveUrl($validated['path']),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        set_time_limit(600);

        $validated = $request->validate([
            'video' => 'required|file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo|max:512000',
        ]);

        try {
            $path = $this->files->uploadFile($validated['video'], 'interviews/videos');
        } catch (RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => $this->files->resolveUrl($path),
        ]);
    }
}
