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
            // Nếu người dùng hủy đăng nhập
            if (request()->has('error')) {
                return redirect('/login')->with('error', 'Bạn đã hủy đăng nhập. Vui lòng thử lại.');
            }

            $googleUser = Socialite::driver('google')->stateless()->user();

            // Trường hợp không có email (ẩn email)
            if (!$googleUser->getEmail()) {
                return redirect('/login')->with('error', 'Không thể lấy thông tin tài khoản. Vui lòng thử lại hoặc sử dụng email thông thường.');
            }

            // Tạo hoặc cập nhật user
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'password' => Hash::make('social_login_default_password'),
                    'email_verified_at' => now(),
                    'is_verified' => true,
                ]
            );

            Auth::login($user);

            // ✅ Truyền flash message đúng chuẩn Laravel
            return redirect()->intended('dashboard')->with('success', 'Đăng nhập Google thành công!');
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect('/login')->with('error', 'Đăng nhập thất bại. Vui lòng thử lại sau.');
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return redirect('/login')->with('error', 'Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng và thử lại.');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Có lỗi xảy ra khi đăng nhập. Vui lòng thử lại sau.');
        }
    }

    // // Facebook
    // public function redirectToFacebook()
    // {
    //     return Socialite::driver('facebook')->redirect();
    // }

    // public function handleFacebookCallback()
    // {
    //     try {
    //         $facebookUser = Socialite::driver('facebook')->stateless()->user();

    //         $user = User::updateOrCreate(
    //             ['email' => $facebookUser->getEmail()],
    //             [
    //                 'name' => $facebookUser->getName(),
    //                 'password' => Hash::make('social_login_default_password'),
    //                 'email_verified_at' => now(),
    //                 'is_verified' => true,
    //             ]
    //         );

    //         Auth::login($user);

    //         return redirect()->intended('dashboard')->with('success', 'Đăng nhập Facebook thành công!');
    //     } catch (\Exception $e) {
    //         return redirect('/login')->with('error', 'Đăng nhập Facebook thất bại.');
    //     }
    // }
}
