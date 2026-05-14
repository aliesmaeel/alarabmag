<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminUser = env('ADMIN_USER', 'admin');
        $adminPass = env('ADMIN_PASS', 'alarab2026');
        $expected  = base64_encode("{$adminUser}:{$adminPass}");

        $token = $request->header('x-admin-token');

        if ($token !== $expected) {
            return response()->json([
                'error'   => 'Unauthorized',
                'message' => 'رمز المصادقة غير صحيح',
            ], 401);
        }

        return $next($request);
    }
}
