<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class LoginController extends Controller
{
    /**
     * Hiển thị trang đăng nhập
     */
    public function create(): Response
    {
        return Inertia::render('auth/Login', [
            'title' => 'Đăng nhập',
        ]);
    }

    /**
     * Xử lý login
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validate và kiểm tra credentials (có message tiếng Việt trong LoginRequest)
        $user = $request->validateCredentials();

        // Đăng nhập người dùng
        Auth::login($user);

        // Regenerate session
        $request->session()->regenerate();

        // Redirect tới dashboard
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Logout
     */
    public function destroy(\Illuminate\Http\Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
