<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép tất cả người dùng gửi request
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:150',
                'regex:/^[\pL\s]+$/u', // chỉ chữ và khoảng trắng
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
            ],
            'phone' => [
                'required',
                'regex:/^0\d{9}$/', // bắt đầu bằng 0 và đúng 10 số
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed',
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

            'phone.required' => 'Vui lòng nhập số điện thoại',
            'phone.regex' => 'Số điện thoại phải bắt đầu bằng số 0 và có 10 số',

            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.max' => 'Mật khẩu không quá 255 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->only(['name', 'email', 'phone', 'password']);

            // Nếu tất cả các trường trống
            if (empty($data['name']) && empty($data['email']) && empty($data['phone']) && empty($data['password'])) {
                // Xóa lỗi riêng lẻ
                $validator->errors()->forget('name');
                $validator->errors()->forget('email');
                $validator->errors()->forget('phone');
                $validator->errors()->forget('password');

                // Thêm lỗi tổng quát
                $validator->errors()->add('general', 'Vui lòng nhập đầy đủ thông tin');
            }
        });
    }

}
