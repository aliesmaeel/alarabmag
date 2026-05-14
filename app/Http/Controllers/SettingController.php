<?php
// ── SettingController ─────────────────────────────────────
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => Setting::getAllAsArray(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->all();

        // Only allow known setting keys
        $allowed = [
            'site_name', 'site_name_en', 'site_tagline', 'site_description',
            'current_issue', 'editor_email', 'instagram', 'twitter',
            'youtube', 'tiktok', 'whatsapp', 'facebook',
        ];

        $filtered = array_filter($data, fn($k) => in_array($k, $allowed), ARRAY_FILTER_USE_KEY);

        Setting::setMany($filtered);

        return response()->json(['success' => true]);
    }
}
