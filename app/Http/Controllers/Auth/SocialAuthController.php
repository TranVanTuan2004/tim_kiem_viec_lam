<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;

class SocialAuthController extends Controller
{
    // Redirect người dùng đến trang xác thực của Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Xử lý callback sau khi xác thực Google thành công
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'password' => Hash::make('social_login_default_password'),
                    'email_verified_at' => now(),
                    'is_verified' => true, // nếu bạn có cột này
                ]
            );

            Auth::login($user);
            return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Đăng nhập Google thất bại.');
        }
    }

    // Facebook
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            $user = User::updateOrCreate(
                ['email' => $facebookUser->getEmail()],
                [
                    'name' => $facebookUser->getName(),
                    'password' => Hash::make('social_login_default_password'),
                    'email_verified_at' => now(),
                    'is_verified' => true,
                ]
            );

            Auth::login($user);
            return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Đăng nhập Facebook thất bại.');
        }
    }
}
