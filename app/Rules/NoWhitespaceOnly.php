<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoWhitespaceOnly implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Nếu value là null hoặc empty string, cho phép (sẽ được xử lý bởi 'nullable' rule)
        if ($value === null || $value === '') {
            return true;
        }

        // Loại bỏ tất cả các loại khoảng trắng (ASCII và full-width)
        // \s: ASCII whitespace
        // \x{3000}: Full-width space (　)
        // \x{00A0}: Non-breaking space
        $trimmed = preg_replace('/[\s\x{3000}\x{00A0}]+/u', '', $value);

        // Nếu sau khi loại bỏ khoảng trắng mà string rỗng -> fail
        return $trimmed !== '';
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Trường :attribute không được chỉ chứa khoảng trắng.';
    }
}
