<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardLocaleController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, ['ar', 'en'], true), 404);

        $request->session()->put('dashboard_locale', $locale);

        $fallback = url('/dashboard');
        $referer = $request->headers->get('referer');

        if (! $referer || str_contains($referer, '/dashboard/locale/')) {
            return redirect()->to($fallback);
        }

        return redirect()->to($referer);
    }
}
