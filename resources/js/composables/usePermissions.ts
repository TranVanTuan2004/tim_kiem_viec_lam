import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function usePermissions() {
    const page = usePage();

    const currentUser = computed(() => {
        return (page.props.auth as any)?.user;
    });

    /**
     * Kiểm tra user có permission không (giống @can trong Blade)
     * Tự động kiểm tra cả direct permissions và permissions từ roles
     * @param permission - Tên permission cần kiểm tra
     * @returns true nếu có permission, false nếu không
     */
    const can = (permission?: string): boolean => {
        if (!permission) return true; // Không có permission = ai cũng xem được

        const user = currentUser.value;
        if (!user) return false;

        // Kiểm tra trong permissions array từ backend
        return user.permissions?.includes(permission) || false;
    };

    /**
     * Kiểm tra user có role không
     * @param role - Tên role cần kiểm tra
     * @returns true nếu có role
     */
    const hasRole = (role: string): boolean => {
        const user = currentUser.value;
        if (!user || !user.roles) return false;

        // Support both formats:
        // 1. Array of strings: ['Employer', 'Admin']
        // 2. Array of objects: [{name: 'Employer'}, {name: 'Admin'}]
        return user.roles.some((r: any) => {
            // If r is a string, compare directly
            if (typeof r === 'string') {
                return r === role;
            }
            // If r is an object, compare r.name
            return r.name === role;
        });
    };

    /**
     * Kiểm tra user có ít nhất 1 trong các roles
     * @param roles - Mảng roles
     * @returns true nếu có ít nhất 1 role
     */
    const hasAnyRole = (roles: string[]): boolean => {
        return roles.some((role) => hasRole(role));
    };

    /**
     * Kiểm tra user có tất cả roles
     * @param roles - Mảng roles
     * @returns true nếu có tất cả roles
     */
    const hasAllRoles = (roles: string[]): boolean => {
        return roles.every((role) => hasRole(role));
    };

    /**
     * Lấy tất cả roles của user
     * @returns Mảng tên roles
     */
    const getUserRoles = (): string[] => {
        const user = currentUser.value;
        if (!user || !user.roles) return [];

        return user.roles.map((r: any) => r.name);
    };

    /**
     * Lấy tất cả permissions của user (bao gồm từ roles)
     * @returns Mảng tên permissions
     */
    const getUserPermissions = (): string[] => {
        const user = currentUser.value;
        if (!user) return [];

        return user.permissions || [];
    };

    /**
     * Kiểm tra user có permission thông qua role cụ thể (ví dụ: chỉ Admin mới có thể xem analytics)
     * @param permission - Tên permission
     * @param requiredRole - Role bắt buộc để có permission này
     * @returns true nếu có role và permission
     */
    const hasPermissionWithRole = (
        permission: string,
        requiredRole: string,
    ): boolean => {
        const user = currentUser.value;
        if (!user) return false;

        // Phải có role và permission
        return (
            user.roles?.some((r: any) => r.name === requiredRole) &&
            user.permissions?.includes(permission)
        );
    };

    return {
        currentUser,
        can,
        hasRole,
        hasAnyRole,
        hasAllRoles,
        getUserRoles,
        getUserPermissions,
        hasPermissionWithRole,
    };
}
