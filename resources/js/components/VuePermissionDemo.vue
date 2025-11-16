<script setup lang="ts">
import { usePermissions } from '@/composables/usePermissions';
import { Button } from '@/components/ui/button';

const { can } = usePermissions();
</script>

<template>
    <div class="p-4 space-y-4">
        <h2 class="text-xl font-bold">Vue Permission Demo (giống @can trong Blade)</h2>
        
        <!-- Giống @can('view users') trong Blade -->
        <div v-if="can('view users')">
            <Button>Quản lý Users</Button>
        </div>
        
        <!-- Giống @can('view messages') trong Blade -->
        <div v-if="can('view messages')">
            <Button>Xem Chat</Button>
        </div>
        
        <!-- Giống @can('delete users') trong Blade -->
        <div v-if="can('delete users')">
            <Button variant="destructive">Xóa Users</Button>
        </div>
        
        <!-- Không có permission = ai cũng xem được -->
        <div>
            <Button variant="outline">Dashboard (không cần permission)</Button>
        </div>
        
        <!-- Hiển thị thông tin -->
        <div class="bg-gray-100 p-4 rounded">
            <h3 class="font-semibold">Permission Status:</h3>
            <p>Can view users: {{ can('view users') ? '✅ Yes' : '❌ No' }}</p>
            <p>Can view messages: {{ can('view messages') ? '✅ Yes' : '❌ No' }}</p>
            <p>Can delete users: {{ can('delete users') ? '✅ Yes' : '❌ No' }}</p>
        </div>
    </div>
</template>
