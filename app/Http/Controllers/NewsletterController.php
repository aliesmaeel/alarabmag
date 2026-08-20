<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
        ], [
            'email.required' => 'أدخل بريدك الإلكتروني.',
            'email.email' => 'أدخل بريداً إلكترونياً صالحاً.',
        ]);

        $email = strtolower(trim($data['email']));

        Subscriber::query()->firstOrCreate(
            ['email' => $email],
            [
                'source' => 'newsletter',
                'ip' => $request->ip(),
            ],
        );

        $message = 'أهلاً بك في مجلة العرب — تم تسجيل اشتراكك.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'message' => $message]);
        }

        return back()->with('newsletter_success', $message);
    }
}
