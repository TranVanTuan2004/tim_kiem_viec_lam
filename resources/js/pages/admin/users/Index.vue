<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Search, User as UserIcon } from 'lucide-vue-next';

interface User {
  id: number;
  name: string;
  email: string;
  roles: { name: string }[];
  is_active: boolean;
  created_at: string;
}

interface Props {
  users: {
    data: User[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  filters: {
    search?: string;
  };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');

function applyFilters() {
  router.get('/admin/users', { search: search.value || undefined }, { preserveState: false });
}

function clearFilters() {
  search.value = '';
  applyFilters();
}

function toggleActive(id: number) {
  router.post(`/admin/users/${id}/toggle-active`, {}, { preserveScroll: true });
}

function resetPassword(id: number) {
  router.post(`/admin/users/${id}/reset-password`, {}, { preserveScroll: true });
}

function sendResetLink(id: number) {
  router.post(`/admin/users/${id}/send-reset-link`, {}, { preserveScroll: true });
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý người dùng', href: '/admin/users' }
];
</script>

<template>
  <Head title="Quản lý người dùng" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-[20px]">
      
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
        <div>
          <h1 class="text-3xl font-bold tracking-tight flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <UserIcon class="h-6 w-6 text-primary" />
            </div>
            Quản lý người dùng
          </h1>
          <p class="text-muted-foreground mt-2 ml-[52px]">
            Quản lý danh sách người dùng và vai trò
          </p>
        </div>
      </div>

      <!-- Filters -->
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <Search class="h-5 w-5" />
            Bộ lọc
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid gap-4 md:grid-cols-3 mb-4">
            <Input
              v-model="search"
              placeholder="Tìm kiếm tên hoặc email..."
              @keyup.enter="applyFilters"
            />
            <div class="flex gap-2">
              <Button @click="applyFilters" class="flex-1">Tìm</Button>
              <Button variant="outline" @click="clearFilters">Xóa</Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Users List -->
      <div class="space-y-4">
        <div
          v-for="user in users.data"
          :key="user.id"
          class="border rounded-lg p-4 hover:bg-muted/50 transition-colors flex justify-between items-center"
        >
          <div>
            <div class="font-bold text-lg">{{ user.name }}</div>
            <div class="text-sm text-muted-foreground">{{ user.email }}</div>
            <Badge 
              variant="default" 
              :class="user.roles?.[0]?.name === 'Admin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'"
            >
              {{ user.roles?.[0]?.name ?? '-' }}
            </Badge>

            <Badge 
              variant="default" 
              :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
            >
              {{ user.is_active ? 'Hoạt động' : 'Bị khóa' }}
            </Badge>

            <div class="flex gap-2 mt-2">
              <Button size="sm" variant="outline" @click="toggleActive(user.id)">
                {{ user.is_active ? 'Khóa' : 'Mở khóa' }}
              </Button>

              <Button size="sm" variant="outline" @click="resetPassword(user.id)">
                Reset password
              </Button>

              <Button size="sm" variant="outline" @click="sendResetLink(user.id)">
                Email Reset
              </Button>

              <Button size="sm" variant="outline" @click="router.get(`/admin/users/${user.id}/activity-logs`)">
                Logs
              </Button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="users.data.length === 0" class="text-center py-12 text-muted-foreground">
          <UserIcon class="h-16 w-16 mx-auto mb-4 text-muted-foreground/30" />
          <p>Không tìm thấy người dùng nào</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="users.last_page > 1" class="flex items-center justify-center gap-2 pt-6 mt-6 border-t">
        <Button
          :disabled="users.current_page === 1"
          variant="outline"
          size="sm"
          @click="router.get(`/admin/users?page=${users.current_page - 1}`)"
        >
          Trước
        </Button>
        <span class="text-sm text-muted-foreground">
          Trang {{ users.current_page }} / {{ users.last_page }}
        </span>
        <Button
          :disabled="users.current_page === users.last_page"
          variant="outline"
          size="sm"
          @click="router.get(`/admin/users?page=${users.current_page + 1}`)"
        >
          Sau
        </Button>
      </div>
    </div>
  </AppLayout>
</template>
