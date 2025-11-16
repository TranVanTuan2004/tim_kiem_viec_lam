<script setup lang="ts">
import { usePermissions } from '@/composables/usePermissions';
import { Button } from '@/components/ui/button';

const { 
    can, 
    hasRole, 
    getUserRoles,
    getUserPermissions,
    currentUser
} = usePermissions();

// Debug function
const debugPermissions = () => {
    console.log('=== DEBUG PERMISSIONS ===');
    console.log('Current User:', currentUser.value);
    console.log('User Roles:', getUserRoles());
    console.log('User Permissions:', getUserPermissions());
    console.log('Can view users:', can('view users'));
    console.log('Can view users in sidebar:', can('view users in sidebar'));
    console.log('Can view messages:', can('view messages'));
    console.log('Is Admin:', hasRole('Admin'));
    console.log('========================');
};
</script>

<template>
    <div class="p-4 space-y-4 border rounded-lg bg-yellow-50">
        <h2 class="text-xl font-bold text-yellow-800">🔍 Permission Debug</h2>
        
        <div class="space-y-2">
            <Button @click="debugPermissions" variant="outline">
                Debug Permissions (Check Console)
            </Button>
        </div>
        
        <div class="bg-white p-4 rounded border">
            <h3 class="font-semibold mb-2">Current User Info:</h3>
            <p><strong>Name:</strong> {{ currentUser?.name || 'Not logged in' }}</p>
            <p><strong>Email:</strong> {{ currentUser?.email || 'N/A' }}</p>
            <p><strong>Roles:</strong> {{ getUserRoles().join(', ') || 'None' }}</p>
        </div>
        
        <div class="bg-white p-4 rounded border">
            <h3 class="font-semibold mb-2">Permission Checks:</h3>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div>view users: {{ can('view users') ? '✅' : '❌' }}</div>
                <div>view users in sidebar: {{ can('view users in sidebar') ? '✅' : '❌' }}</div>
                <div>view messages: {{ can('view messages') ? '✅' : '❌' }}</div>
                <div>Is Admin: {{ hasRole('Admin') ? '✅' : '❌' }}</div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded border">
            <h3 class="font-semibold mb-2">All Permissions ({{ getUserPermissions().length }}):</h3>
            <div class="text-xs max-h-32 overflow-y-auto">
                <div v-for="permission in getUserPermissions()" :key="permission" class="mb-1">
                    {{ permission }}
                </div>
            </div>
        </div>
    </div>
</template>
