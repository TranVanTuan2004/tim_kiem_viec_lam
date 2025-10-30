<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    /**
     * Show the password reset page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'token' => 'required',
                'email' => 'required|email',
                'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
                'password_confirmation' => 'required',
            ],
            [
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Sai định dạng email',
                'password.required' => 'Vui lòng nhập mật khẩu mới',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
                'password.max' => 'Mật khẩu không quá 255 ký tự',
                'password.confirmed' => 'Mật khẩu nhập lại không khớp',
                'password_confirmation.required' => 'Vui lòng nhập lại mật khẩu mới',
            ]
        );

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? to_route('login')->with('success', __('Đặt lại mật khẩu thành công, vui lòng đăng nhập lại.'))
            : back()->withErrors(['email' => __('Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.')]);
    }

}
