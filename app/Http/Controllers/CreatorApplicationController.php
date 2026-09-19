<?php

namespace App\Http\Controllers;

use App\Models\CreatorApplication;
use App\Services\SeoService;
use App\Support\CreatorApplicationOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreatorApplicationController extends Controller
{
    public function index(SeoService $seo): View
    {
        return view('site.creators', [
            'seo' => $seo->page('creators'),
            'countries' => CreatorApplicationOptions::countries(),
            'dialCodes' => CreatorApplicationOptions::dialCodes(),
            'niches' => CreatorApplicationOptions::niches(),
            'showTicker' => true,
            'activeNav' => 'creators',
            'newsletterHeadline' => 'كن أول من يعرف<br><em>فرص صنّاع المحتوى</em>',
            'newsletterSub' => 'اشترك لتصلك المبادرات والفرص الجديدة من مجلة العرب أولاً.',
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'country' => ['required', 'string', 'max:60'],
            'dial_code' => ['nullable', 'string', 'max:8'],
            'phone' => ['required', 'string', 'max:32'],
            'niche' => ['required', 'string', 'max:80'],
            'followers' => ['nullable', 'string', 'max:64'],
            'social_links' => ['required', 'array', 'min:1', 'max:8'],
            'social_links.*' => ['required', 'url', 'max:500'],
            'bio' => ['required', 'string', 'min:20', 'max:2000'],
        ], [
            'name.required' => 'أدخل اسمك الكامل.',
            'email.required' => 'أدخل بريدك الإلكتروني.',
            'email.email' => 'أدخل بريداً إلكترونياً صالحاً.',
            'country.required' => 'اختر الدولة.',
            'phone.required' => 'أدخل رقم التواصل.',
            'niche.required' => 'اختر مجال المحتوى.',
            'social_links.required' => 'أضف رابط حساب واحد على الأقل.',
            'social_links.min' => 'أضف رابط حساب واحد على الأقل.',
            'social_links.max' => 'يمكنك إضافة 8 روابط كحد أقصى.',
            'social_links.*.required' => 'أدخل رابط الحساب أو احذف الحقل.',
            'social_links.*.url' => 'أدخل رابطاً صالحاً يبدأ بـ https://',
            'bio.required' => 'اكتب نبذة عنك وعن محتواك.',
            'bio.min' => 'النبذة قصيرة جداً — اكتب 20 حرفاً على الأقل.',
        ]);

        $email = strtolower(trim($data['email']));

        $duplicate = CreatorApplication::query()
            ->where('email', $email)
            ->where('created_at', '>=', now()->subDays(30))
            ->exists();

        if ($duplicate) {
            $message = 'لديك طلب مسجّل بهذا البريد — فريق مجلة العرب سيتواصل معك.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'duplicate' => true, 'message' => $message], 409);
            }

            return back()->withInput()->with('creator_error', $message);
        }

        $socialLinks = collect($data['social_links'])
            ->map(fn (string $link): string => trim($link))
            ->filter()
            ->unique()
            ->values()
            ->all();

        CreatorApplication::query()->create([
            'name' => trim($data['name']),
            'email' => $email,
            'country' => $data['country'],
            'dial_code' => $data['dial_code'] ?? null,
            'phone' => trim($data['phone']),
            'niche' => $data['niche'],
            'followers' => $data['followers'] ?? null,
            'social_links' => $socialLinks,
            'bio' => trim($data['bio']),
            'status' => 'new',
            'ip_hash' => hash('sha256', (string) $request->ip()),
        ]);

        $message = 'تم إرسال طلبك بنجاح. سيتواصل معك فريق مجلة العرب قريباً.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'message' => $message]);
        }

        return back()->with('creator_success', $message);
    }
}
