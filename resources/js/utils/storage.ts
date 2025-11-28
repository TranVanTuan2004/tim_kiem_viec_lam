/**
 * Get the URL for a file, handling both local storage and external URLs
 * @param path - The file path or URL
 * @returns The full URL or undefined
 */
export function storageUrl(path: string | null | undefined): string | undefined {
    if (!path) {
        return undefined;
    }

    // If it's already a full URL (http:// or https://), return as is
    if (path.startsWith('http://') || path.startsWith('https://')) {
        return path;
    }

    // Otherwise, it's a local storage path
    return `/storage/${path}`;
}

/**
 * Generate a default company logo URL using ui-avatars.com
 * @param companyName - The company name
 * @returns The ui-avatars URL
 */
export function getDefaultCompanyLogo(companyName: string): string {
    // Get initials from company name (max 2 characters)
    const initials = companyName
        .split(' ')
        .map((word) => word.charAt(0).toUpperCase())
        .join('')
        .substring(0, 2);
    return `https://ui-avatars.com/api/?name=${initials}&background=ef4444&color=ffffff&size=128&bold=true`;
}

/**
 * Get company logo URL - returns custom logo if exists, otherwise generates default
 * @param logo - The logo path from database(can be null)
 * @param companyName - The company name for generating default logo
 * @returns The logo URL
 */
export function getCompanyLogoUrl(logo: string | null | undefined, companyName: string): string {
    // If company has custom logo, use storageUrl to handle it
    if (logo) {
        return storageUrl(logo) || getDefaultCompanyLogo(companyName);
    }

    // Otherwise, generate default logo
    return getDefaultCompanyLogo(companyName);
}
