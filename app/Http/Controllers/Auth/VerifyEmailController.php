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
     * ✅ Redirect về trang xác thực với thông báo lỗi nếu link không hợp lệ
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Nếu không có user (chưa đăng nhập), redirect về trang xác thực
        if (!$user) {
            return redirect()->route('verification.notice')
                ->with('error', 'Vui lòng đăng nhập để xác thực email');
        }

        // Kiểm tra tính hợp lệ của signature
        if (!URL::hasValidSignature($request)) {
            Log::warning('Email verification failed: Invalid or expired signature', [
                'user_id' => $user->id,
                'url' => $request->fullUrl()
            ]);

            return redirect()->route('verification.notice')
                ->with('error', 'Liên kết xác thực không hợp lệ hoặc đã hết hạn');
        }

        // Kiểm tra hash có khớp với email không
        if (!hash_equals(sha1($user->email), (string) $request->route('hash'))) {
            Log::warning('Email verification failed: Hash mismatch', [
                'user_id' => $user->id
            ]);

            return redirect()->route('verification.notice')
                ->with('error', 'Liên kết xác thực không hợp lệ hoặc đã hết hạn');
        }

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

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Redirect based on user role after verification
        if ($user->hasRole('Candidate')) {
            return redirect()->route('candidate.dashboard', ['verified' => 1])
                ->with('success', 'Xác thực tài khoản thành công');
        } elseif ($user->hasRole('Employer')) {
            return redirect()->route('employer.dashboard', ['verified' => 1])
                ->with('success', 'Xác thực tài khoản thành công');
        } elseif ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard', ['verified' => 1])
                ->with('success', 'Xác thực tài khoản thành công');
        }
        return redirect()->route('candidate.dashboard', ['verified' => 1])
            ->with('success', 'Xác thực tài khoản thành công');
    }

    /**
     * ✅ Xử lý xác thực tài khoản khi người dùng nhấn vào link trong email
     */
    public function verifyEmail(Request $request)
    {
        $user = User::find($request->route('id'));

        // Kiểm tra user tồn tại
        if (!$user) {
            Log::warning('Email verification failed: User not found', ['id' => $request->route('id')]);
            return response()->json([
                'success' => false,
                'message' => 'Liên kết xác thực không hợp lệ hoặc đã hết hạn'
            ], 404);
        }

        // Nếu người dùng đã xác thực trước đó
        if ($user->hasVerifiedEmail()) {
            Log::info('Email verification skipped: Already verified', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản này đã được xác thực'
            ], 400);
        }

        // Kiểm tra tính hợp lệ của link (sử dụng signed route)
        if (!URL::hasValidSignature($request)) {
            Log::warning('Email verification failed: Invalid signature', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'Liên kết xác thực không hợp lệ hoặc đã hết hạn'
            ], 403);
        }

        // Cập nhật DB
        try {
            $user->markEmailAsVerified();
            event(new Verified($user));

            Log::info('Email verified successfully', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Xác thực tài khoản thành công, bạn có thể đăng nhập ngay bây giờ'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Email verification error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Xác thực không thành công, vui lòng thử lại sau'
            ], 500);
        }
    }

    /**
     * ✅ Gửi lại email xác thực (hỗ trợ cả người dùng đã đăng nhập và chưa đăng nhập)
     */
    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:150']
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Sai định dạng email',
            'email.max' => 'Email không dài quá 150 ký tự',
        ]);

        $user = User::where('email', $request->email)->first();

        // Email không tồn tại
        if (!$user) {
            Log::warning('Resend verification failed: Email not found', ['email' => $request->email]);
            return response()->json([
                'success' => false,
                'message' => 'Email không tồn tại trong hệ thống'
            ], 404);
        }

        // Đã xác thực rồi
        if ($user->hasVerifiedEmail()) {
            Log::info('Resend verification skipped: Already verified', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản này đã được xác thực'
            ], 400);
        }

        try {
            // Gửi lại email xác thực
            event(new Registered($user));

            Log::info('Verification email resent successfully', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Email xác thực đã được gửi lại'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Resend verification email failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gửi email xác thực thất bại, vui lòng thử lại sau'
            ], 500);
        }
    }
}
