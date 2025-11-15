<script setup lang="ts">
import { usePermissions } from '@/composables/usePermissions';
import { Button } from '@/components/ui/button';

const { 
    hasPermission, 
    hasRole, 
    hasAnyRole, 
    hasAllRoles,
    roleHasPermission,
    getUserRoles,
    getUserPermissions,
    hasPermissionWithRole,
    currentUser
} = usePermissions();
</script>

<template>
    <div class="p-4 space-y-4">
        <h2 class="text-xl font-bold">Permission & Role Demo</h2>
        
        <!-- 1. Kiểm tra permission đơn giản (tự động bao gồm permissions từ roles) -->
        <div class="space-y-2">
            <h3 class="font-semibold">1. Kiểm tra Permission (bao gồm từ roles):</h3>
            <div v-if="hasPermission('view users')" class="flex gap-2">
                <Button>Quản lý Users</Button>
            </div>
            <div v-if="hasPermission('view messages')" class="flex gap-2">
                <Button>Xem Chat</Button>
            </div>
            <div v-if="hasPermission('delete users')" class="flex gap-2">
                <Button variant="destructive">Xóa Users</Button>
            </div>
        </div>
        
        <!-- 2. Kiểm tra role -->
        <div class="space-y-2">
            <h3 class="font-semibold">2. Kiểm tra Role:</h3>
            <div v-if="hasRole('Admin')" class="flex gap-2">
                <Button variant="destructive">Admin Panel</Button>
            </div>
            <div v-if="hasRole('Employer')" class="flex gap-2">
                <Button>Employer Tools</Button>
            </div>
            <div v-if="hasRole('Candidate')" class="flex gap-2">
                <Button>Candidate Tools</Button>
            </div>
        </div>
        
        <!-- 3. Kiểm tra multiple roles -->
        <div class="space-y-2">
            <h3 class="font-semibold">3. Kiểm tra Multiple Roles:</h3>
            <div v-if="hasAnyRole(['Admin', 'Employer'])" class="flex gap-2">
                <Button>Admin hoặc Employer</Button>
            </div>
            <div v-if="hasAllRoles(['Admin', 'SuperAdmin'])" class="flex gap-2">
                <Button variant="destructive">Super Admin</Button>
            </div>
        </div>
        
        <!-- 4. Kiểm tra permission với role cụ thể -->
        <div class="space-y-2">
            <h3 class="font-semibold">4. Permission với Role cụ thể:</h3>
            <div v-if="hasPermissionWithRole('view analytics', 'Admin')" class="flex gap-2">
                <Button>Analytics (chỉ Admin)</Button>
            </div>
            <div v-if="roleHasPermission('Admin', 'delete users')" class="flex gap-2">
                <Button variant="destructive">Admin có thể xóa users</Button>
            </div>
        </div>
        
        <!-- 5. Hiển thị thông tin chi tiết -->
        <div class="bg-gray-100 p-4 rounded space-y-2">
            <h3 class="font-semibold">Thông tin User:</h3>
            <p><strong>Name:</strong> {{ currentUser?.name }}</p>
            <p><strong>Email:</strong> {{ currentUser?.email }}</p>
            
            <div>
                <strong>Roles:</strong>
                <ul class="ml-4 list-disc">
                    <li v-for="role in getUserRoles()" :key="role">{{ role }}</li>
                </ul>
            </div>
            
            <div>
                <strong>Permissions:</strong>
                <ul class="ml-4 list-disc">
                    <li v-for="permission in getUserPermissions()" :key="permission">{{ permission }}</li>
                </ul>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <strong>Permission Checks:</strong>
                    <ul class="ml-4 list-disc">
                        <li>view users: {{ hasPermission('view users') ? '✅' : '❌' }}</li>
                        <li>view messages: {{ hasPermission('view messages') ? '✅' : '❌' }}</li>
                        <li>delete users: {{ hasPermission('delete users') ? '✅' : '❌' }}</li>
                    </ul>
                </div>
                
                <div>
                    <strong>Role Checks:</strong>
                    <ul class="ml-4 list-disc">
                        <li>Admin: {{ hasRole('Admin') ? '✅' : '❌' }}</li>
                        <li>Employer: {{ hasRole('Employer') ? '✅' : '❌' }}</li>
                        <li>Candidate: {{ hasRole('Candidate') ? '✅' : '❌' }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
