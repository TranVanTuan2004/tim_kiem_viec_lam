<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyReviewRequest extends FormRequest
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
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['required', 'string', 'max:255'],
            'review' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'rating.integer' => 'Đánh giá phải là số nguyên.',
            'rating.min' => 'Đánh giá tối thiểu là 1 sao.',
            'rating.max' => 'Đánh giá tối đa là 5 sao.',
            'title.required' => 'Vui lòng nhập tiêu đề đánh giá.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'review.required' => 'Vui lòng nhập nội dung đánh giá.',
            'review.min' => 'Nội dung đánh giá phải có ít nhất 10 ký tự.',
            'review.max' => 'Nội dung đánh giá không được vượt quá 2000 ký tự.',
        ];
    }
}

