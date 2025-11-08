<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có được phép thực hiện request này không.
     */
    public function authorize(): bool
{
    return true;
}

    /**
     * Lấy các quy tắc validation áp dụng cho request.
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            
            // Các trường địa lý, ngành nghề, quy mô
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:50'], 
            'industry' => ['required', 'string', 'max:255'],

            // Validation cho input file 'logo_file'
            'logo_file' => ['nullable', 'image', 'max:2048'], 
            'remove_logo' => ['nullable', 'boolean'], 
        ];
    }
    
    /**
     * Lấy các thông báo lỗi tùy chỉnh.
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'Tên công ty là bắt buộc.',
            'address.required' => 'Địa chỉ chi tiết là bắt buộc.',
            'province.required' => 'Tỉnh/Thành phố là bắt buộc.',
            'city.required' => 'Quận/Huyện là bắt buộc.',
            'industry.required' => 'Ngành nghề là bắt buộc.',
            'logo_file.image' => 'Logo phải là file hình ảnh (jpg, png, v.v.).',
            'logo_file.max' => 'Logo không được vượt quá 2MB.',
        ];
    }
}