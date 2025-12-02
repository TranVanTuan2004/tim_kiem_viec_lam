<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoWhitespaceOnly;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id ?? $this->route('user');

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                'regex:/^[\pL\s]+$/u', // chỉ chữ và khoảng trắng
                new NoWhitespaceOnly(), // Test Case 6: Reject whitespace-only
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                "unique:users,email,{$userId}",
            ],
            'phone' => [
                'nullable',
                'regex:/^0[0-9]{9}$/', // Test Case 7: Only ASCII digits
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'max:255',
                'confirmed',
            ],
            'bio' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'roles' => [
                'required',
                'array',
                'min:1',
            ],
            'roles.*' => [
                'exists:roles,id',
            ],
            'updated_at' => [
                'required',
                'date',
            ],
        ];
    }



    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ và tên',
            'name.max' => 'Họ và tên không được quá 150 ký tự',
            'name.regex' => 'Họ và tên không có số, ký tự đặc biệt',

            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Sai định dạng email',
            'email.max' => 'Email không dài quá 150 ký tự',
            'email.unique' => 'Email đã được sử dụng',

            'phone.regex' => 'Số điện thoại phải bắt đầu bằng số 0 và có 10 số',

            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.max' => 'Mật khẩu không quá 255 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',

            'bio.max' => 'Tiểu sử không được quá 1000 ký tự',

            'roles.required' => 'Vui lòng chọn vai trò',
            'roles.array' => 'Vai trò không hợp lệ',
            'roles.min' => 'Vui lòng chọn ít nhất 1 vai trò',
            'roles.*.exists' => 'Vai trò được chọn không tồn tại trong hệ thống',
        ];
    }
}
