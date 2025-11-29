<?php

namespace App\Http\Requests\Admin;

use App\Rules\NoWhitespaceOnly;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization được xử lý bởi middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:pending,reviewing,resolved,dismissed'],
            'admin_notes' => ['nullable', 'string', 'max:1000', new NoWhitespaceOnly()],
            'updated_at' => ['nullable', 'date'], // Để check optimistic locking
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ. Vui lòng chọn một trong các trạng thái: Chờ xử lý, Đang xem xét, Đã xử lý, Đã bỏ qua.',
            'admin_notes.max' => 'Ghi chú quá dài. Tối đa 1000 ký tự.',
            'admin_notes.string' => 'Ghi chú phải là chuỗi văn bản.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'status' => 'trạng thái',
            'admin_notes' => 'ghi chú',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim admin_notes nếu có
        if ($this->has('admin_notes') && is_string($this->admin_notes)) {
            $this->merge([
                'admin_notes' => trim($this->admin_notes),
            ]);
        }
    }
}
