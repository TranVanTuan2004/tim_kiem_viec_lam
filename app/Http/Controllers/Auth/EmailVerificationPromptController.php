<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Show the email verification prompt page.
     * ✅ Hỗ trợ cả người dùng đã đăng nhập và chưa đăng nhập
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        $user = $request->user();

        // Nếu đã đăng nhập và đã xác thực email
        if ($user && $user->hasVerifiedEmail()) {
            // Redirect based on user role
            if ($user->hasRole('Candidate')) {
                return redirect()->route('candidate.dashboard');
            } elseif ($user->hasRole('Employer')) {
                return redirect()->route('employer.dashboard');
            } elseif ($user->hasRole('Admin')) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('candidate.dashboard');
        }

        // Hiển thị trang xác thực (cho cả người dùng đã đăng nhập và chưa đăng nhập)
        return Inertia::render('auth/VerifyEmail', [
            'status' => $request->session()->get('status'),
            'email' => $user ? $user->email : null, // Truyền email nếu đã đăng nhập
            'error' => $request->session()->get('error'), // Truyền error message nếu có
            'success' => $request->session()->get('success'), // Truyền success message nếu có
        ]);
    }
}
