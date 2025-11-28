<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }

    /**
     * Custom messages for validation
     */
    public function messages(): array
    {
        return [
            // Email
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Sai định dạng email',
            'email.max' => 'Email không dài quá 150 ký tự',

            // Password
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.max' => 'Mật khẩu không quá 255 ký tự',
        ];
    }

    /**
     * Add custom validation after the default rules
     * For example: cả email và password trống
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->only(['email', 'password']);

            // Nếu cả 2 trống → xóa lỗi riêng lẻ, thêm lỗi tổng quát
            if (empty($data['email']) && empty($data['password'])) {
                $validator->errors()->forget('email');
                $validator->errors()->forget('password');
                $validator->errors()->add('general', 'Vui lòng nhập email và password');
            }
        });
    }

    /**
     * Validate the request's credentials and return the user without logging them in.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateCredentials(): User
    {
        $this->ensureIsNotRateLimited();

        /** @var User|null $user */
        $user = Auth::getProvider()->retrieveByCredentials($this->only('email', 'password'));

        if (!$user || !Auth::getProvider()->validateCredentials($user, $this->only('password'))) {
            RateLimiter::hit($this->throttleKey());

            // Custom message khi credentials sai
            throw ValidationException::withMessages([
                'email' => 'Email hoặc mật khẩu không đúng',
            ]);
        }

        // ✅ Không chặn đăng nhập nếu chưa xác thực
        // Sẽ xử lý redirect trong AuthenticatedSessionController

        RateLimiter::clear($this->throttleKey());

        return $user;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => 'Quá nhiều lần đăng nhập. Vui lòng thử lại sau ' . ceil($seconds / 60) . ' phút.',
        ]);
    }

    /**
     * Get the rate-limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return $this->string('email')
            ->lower()
            ->append('|' . $this->ip())
            ->transliterate()
            ->value();
    }
}
