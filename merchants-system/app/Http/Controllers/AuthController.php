<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // يفضّل أمنيًا عدم الاعتماد على remember me
        $remember = true;

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = auth()->user();

            LoginLog::create([
                'user_id' => $user->id,
                'session_id' => $request->session()->getId(),
                'login_at' => now(),
                'logout_at' => null,
                'ended_by' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            ActivityLogService::log(
                action: 'login',
                target: $user,
                description: 'تم تسجيل الدخول إلى النظام'
            );

            if ($user->can('view dashboard')) {
                return redirect()->route('dashboard');
            }

            return redirect()->route('merchants.index');
        }

        return back()
            ->withErrors([
                'email' => 'بيانات الدخول غير صحيحة.',
            ])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        $endedBy = $request->input('ended_by', 'manual');

        if (!in_array($endedBy, ['manual', 'timeout', 'forced'], true)) {
            $endedBy = 'manual';
        }

        if ($user) {
            $loginLog = LoginLog::where('user_id', $user->id)
                ->where('session_id', $request->session()->getId())
                ->whereNull('logout_at')
                ->latest()
                ->first();

            if ($loginLog) {
                $loginLog->update([
                    'logout_at' => now(),
                    'ended_by' => $endedBy,
                ]);
            }

            ActivityLogService::log(
                action: 'logout',
                target: $user,
                description: match ($endedBy) {
                    'timeout' => 'تم تسجيل الخروج بسبب عدم النشاط',
                    'forced' => 'تم تسجيل الخروج الإجباري',
                    default => 'تم تسجيل الخروج من النظام',
                }
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
            ]);
        }

        return redirect()->route('login');
    }
}
