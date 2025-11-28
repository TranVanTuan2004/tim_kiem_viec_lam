<?php

if (!function_exists('storage_url')) {
    /**
     * Get the URL for a file, handling both local storage and external URLs
     *
     * @param string|null $path
     * @return string|null
     */
    function storage_url($path)
    {
        if (empty($path)) {
            return null;
        }

        // If it's already a full URL (http:// or https://), return as is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // If it starts with http:// or https://, return as is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Otherwise, it's a local storage path
        return asset('storage/' . $path);
    }
}
