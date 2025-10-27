<?php

namespace App\Http\Requests\Portfolio;

use Illuminate\Foundation\Http\FormRequest;

class StorePortfolioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isCandidate();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'project_url' => ['nullable', 'url', 'max:500'],
            'github_url' => ['nullable', 'url', 'max:500'],
            'demo_url' => ['nullable', 'url', 'max:500'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5MB max per image
            'technologies' => ['nullable', 'array', 'max:50'],
            'technologies.*' => ['string', 'max:100'],
            'start_date' => ['nullable', 'date', 'before_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_ongoing' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_public' => ['boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Project title is required.',
            'title.max' => 'Project title cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 5000 characters.',
            'project_url.url' => 'Project URL must be a valid URL.',
            'github_url.url' => 'GitHub URL must be a valid URL.',
            'demo_url.url' => 'Demo URL must be a valid URL.',
            'images.max' => 'You can upload maximum 10 images.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be in JPG, JPEG, PNG, or WEBP format.',
            'images.*.max' => 'Each image must not exceed 5MB.',
            'technologies.max' => 'You can add maximum 50 technologies.',
            'start_date.before_or_equal' => 'Start date cannot be in the future.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string booleans to actual booleans
        $this->merge([
            'is_ongoing' => filter_var($this->is_ongoing ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_featured' => filter_var($this->is_featured ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_public' => filter_var($this->is_public ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}

