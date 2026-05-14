<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $username = $request->input('username');
        $password = $request->input('password');

        $adminUser = env('ADMIN_USER', 'admin');
        $adminPass = env('ADMIN_PASS', 'alarab2026');

        if ($username === $adminUser && $password === $adminPass) {
            $token = base64_encode("{$adminUser}:{$adminPass}");
            return response()->json([
                'success' => true,
                'token'   => $token,
                'user'    => [
                    'username' => $adminUser,
                    'role'     => 'admin',
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'error'   => 'بيانات الدخول غير صحيحة',
        ], 401);
    }
}
