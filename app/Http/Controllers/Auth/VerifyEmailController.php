<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Events\Registered;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            // Redirect based on user role
            if ($user->hasRole('Candidate')) {
                return redirect()->route('candidate.dashboard', ['verified' => 1]);
            } elseif ($user->hasRole('Employer')) {
                return redirect()->route('employer.dashboard', ['verified' => 1]);
            } elseif ($user->hasRole('Admin')) {
                return redirect()->route('admin.dashboard', ['verified' => 1]);
            }
            return redirect()->route('candidate.dashboard', ['verified' => 1]);
        }

        $request->fulfill();

        // Redirect based on user role after verification
        if ($user->hasRole('Candidate')) {
            return redirect()->route('candidate.dashboard', ['verified' => 1]);
        } elseif ($user->hasRole('Employer')) {
            return redirect()->route('employer.dashboard', ['verified' => 1]);
        } elseif ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard', ['verified' => 1]);
        }
        return redirect()->route('candidate.dashboard', ['verified' => 1]);
    }

    /**
     * ✅ Xử lý xác thực tài khoản khi người dùng nhấn vào link trong email
     */
    public function verifyEmail(Request $request)
    {
        $user = User::find($request->route('id'));

        if (! $user) {
            return response()->json(['message' => 'Liên kết xác thực không hợp lệ hoặc đã hết hạn'], 400);
        }

        // Nếu người dùng đã xác thực trước đó
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Tài khoản này đã được xác thực'], 400);
        }

        // Kiểm tra tính hợp lệ của link (sử dụng signed route)
        if (! URL::hasValidSignature($request)) {
            return response()->json(['message' => 'Liên kết xác thực không hợp lệ hoặc đã hết hạn'], 400);
        }

        // Cập nhật DB
        try {
            $user->markEmailAsVerified();
            event(new Verified($user));
        } catch (\Exception $e) {
            Log::error('Lỗi xác thực tài khoản: '.$e->getMessage());
            return response()->json(['message' => 'Xác thực không thành công, vui lòng thử lại sau'], 500);
        }

        return response()->json(['message' => 'Xác thực tài khoản thành công, bạn có thể đăng nhập ngay bây giờ']);
    }

    /**
     * ✅ Gửi lại email xác thực
     */
    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $user = User::where('email', $request->email)->first();

        // Email không tồn tại
        if (! $user) {
            return response()->json(['message' => 'Email không tồn tại trong hệ thống'], 404);
        }

        // Đã xác thực rồi
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Tài khoản này đã được xác thực'], 400);
        }

        try {
            // Gửi lại email xác thực
            event(new Registered($user));
            return response()->json(['message' => 'Email xác thực đã được gửi lại']);
        } catch (\Exception $e) {
            Log::error('Gửi lại email xác thực thất bại: '.$e->getMessage());
            return response()->json(['message' => 'Gửi email xác thực thất bại, vui lòng thử lại sau'], 500);
        }
    }
}
