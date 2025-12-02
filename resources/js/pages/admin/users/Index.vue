<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Search, User as UserIcon, Plus, Edit, Trash2, X } from 'lucide-vue-next';

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
const page = usePage();

const search = ref(props.filters.search || '');

// Delete modal state
const showDeleteModal = ref(false);
const userToDelete = ref<User | null>(null);

// Toast state
const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');

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

function openDeleteModal(user: User) {
  userToDelete.value = user;
  showDeleteModal.value = true;
}

function closeDeleteModal() {
  showDeleteModal.value = false;
  userToDelete.value = null;
}

function confirmDelete() {
  if (!userToDelete.value) return;
  
  router.delete(`/admin/users/${userToDelete.value.id}`, {
    preserveScroll: true,
    onFinish: () => {
      closeDeleteModal();
    },
  });
}

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
  toastMessage.value = message;
  toastType.value = type;
  setTimeout(() => {
    toastMessage.value = '';
  }, 5000);
};

// Watch for flash messages
watch(() => page.props.flash, (flash: any) => {
  if (flash?.success) {
    showToast(flash.success, 'success');
  } else if (flash?.error) {
    showToast(flash.error, 'error');
  }
}, { deep: true, immediate: true });

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý người dùng', href: '/admin/users' }
];
</script>

<template>
  <Head title="Quản lý người dùng" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-[20px]">
      
      <!-- Toast Notification -->
      <div v-if="toastMessage" 
           :class="[
             'fixed top-4 right-4 z-50 rounded-lg p-4 shadow-lg border flex items-center gap-3 animate-in slide-in-from-top-5',
             toastType === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'
           ]">
        <svg v-if="toastType === 'success'" class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <svg v-else class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <p :class="[
          'text-sm font-medium',
          toastType === 'success' ? 'text-green-800' : 'text-red-800'
        ]">{{ toastMessage }}</p>
        <button @click="toastMessage = ''" class="ml-4">
          <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      
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
        
        <!-- Add User Button -->
        <Link href="/admin/users/create">
          <Button class="flex items-center gap-2">
            <Plus class="h-4 w-4" />
            Thêm người dùng
          </Button>
        </Link>
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

      <!-- Users Table -->
      <div class="bg-white rounded-md shadow overflow-hidden">
        <div class="responsive-table-wrapper">
          <table class="w-full text-sm text-left mobile-card-view">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
              <tr>
                <th class="px-4 py-3">Tên</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Vai trò</th>
                <th class="px-4 py-3">Trạng thái</th>
                <th class="px-4 py-3 text-right">Hành động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users.data" :key="user.id" class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900" data-label="Tên">
                  {{ user.name }}
                </td>
                <td class="px-4 py-3" data-label="Email">
                  {{ user.email }}
                </td>
                <td class="px-4 py-3" data-label="Vai trò">
                  <Badge 
                    variant="default" 
                    :class="user.roles?.[0]?.name === 'Admin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'"
                  >
                    {{ user.roles?.[0]?.name ?? '-' }}
                  </Badge>
                </td>
                <td class="px-4 py-3" data-label="Trạng thái">
                  <Badge 
                    variant="default" 
                    :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  >
                    {{ user.is_active ? 'Hoạt động' : 'Bị khóa' }}
                  </Badge>
                </td>
                <td class="px-4 py-3 text-right" data-label="Hành động">
                  <div class="flex items-center justify-end gap-2 flex-wrap">
                    <!-- Edit Button -->
                    <Link :href="`/admin/users/${user.id}/edit`">
                      <Button size="sm" variant="outline" class="flex items-center gap-1">
                        <Edit class="h-3 w-3" />
                        Sửa
                      </Button>
                    </Link>

                    <!-- Delete Button -->
                    <Button 
                      size="sm" 
                      variant="outline" 
                      class="flex items-center gap-1 text-red-600 hover:text-red-700 hover:bg-red-50"
                      @click="openDeleteModal(user)"
                    >
                      <Trash2 class="h-3 w-3" />
                      Xóa
                    </Button>

                    <Button size="sm" variant="outline" @click="toggleActive(user.id)">
                      {{ user.is_active ? 'Khóa' : 'Mở khóa' }}
                    </Button>

                    <Button size="sm" variant="outline" @click="resetPassword(user.id)">
                      Reset Pass
                    </Button>

                    <Button size="sm" variant="outline" @click="sendResetLink(user.id)">
                      Email Reset
                    </Button>

                    <Button size="sm" variant="outline" @click="router.get(`/admin/users/${user.id}/activity-logs`)">
                      Lịch sử hoạt động
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="users.data.length === 0" class="text-center py-12 text-muted-foreground">
        <UserIcon class="h-16 w-16 mx-auto mb-4 text-muted-foreground/30" />
        <p>Không tìm thấy người dùng nào</p>
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

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click.self="closeDeleteModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all scale-100">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <Trash2 class="h-6 w-6 text-red-600" />
            Xác nhận xóa người dùng
          </h3>
          <button
            @click="closeDeleteModal"
            class="p-2 hover:bg-gray-100 rounded-lg transition text-gray-500"
          >
            <X class="w-6 h-6" />
          </button>
        </div>

        <div class="mb-6">
          <p class="text-gray-600 mb-4">
            Bạn có chắc chắn muốn xóa người dùng này không?
          </p>
          <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="font-medium text-gray-900">{{ userToDelete?.name }}</p>
            <p class="text-sm text-gray-600">{{ userToDelete?.email }}</p>
            <p class="text-sm text-red-600 mt-2">
              ⚠️ Hành động này không thể hoàn tác!
            </p>
          </div>
        </div>

        <div class="flex gap-3">
          <Button
            variant="outline"
            @click="closeDeleteModal"
            class="flex-1"
          >
            Hủy
          </Button>
          <Button
            @click="confirmDelete"
            class="flex-1 bg-red-600 hover:bg-red-700 text-white"
          >
            Xóa người dùng
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
