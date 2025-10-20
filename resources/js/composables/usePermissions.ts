import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

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
        if (!user) return false;
        
        return user.roles?.includes(role) || false;
    };
    
    /**
     * Kiểm tra user có ít nhất 1 trong các roles
     * @param roles - Mảng roles
     * @returns true nếu có ít nhất 1 role
     */
    const hasAnyRole = (roles: string[]): boolean => {
        const user = currentUser.value;
        if (!user) return false;
        
        return roles.some(role => user.roles?.includes(role)) || false;
    };
    
    /**
     * Kiểm tra user có tất cả roles
     * @param roles - Mảng roles
     * @returns true nếu có tất cả roles
     */
    const hasAllRoles = (roles: string[]): boolean => {
        const user = currentUser.value;
        if (!user) return false;
        
        return roles.every(role => user.roles?.includes(role)) || false;
    };
    
    /**
     * Lấy tất cả roles của user
     * @returns Mảng tên roles
     */
    const getUserRoles = (): string[] => {
        const user = currentUser.value;
        if (!user) return [];
        
        return user.roles || [];
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
    const hasPermissionWithRole = (permission: string, requiredRole: string): boolean => {
        const user = currentUser.value;
        if (!user) return false;
        
        // Phải có role và permission
        return user.roles?.includes(requiredRole) && user.permissions?.includes(permission);
    };
    
    return {
        currentUser,
        can, // Giống @can trong Blade
        hasRole,
        hasAnyRole,
        hasAllRoles,
        getUserRoles,
        getUserPermissions,
        hasPermissionWithRole,
    };
}
