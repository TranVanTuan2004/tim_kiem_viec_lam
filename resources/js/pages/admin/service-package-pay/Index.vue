<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Trash2, Edit, Eye, EyeOff, Plus } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { toast } from 'vue3-toastify';

interface ServicePackage {
  id: number;
  name: string;
  slug: string;
  price: number;
  duration_days: number;
  is_active: boolean;
  features: string | null;
  created_at: string;
}

interface Props {
  packages: {
    data: ServicePackage[];
    current_page: number;
    last_page: number;
    total: number;
  };
  filters: {
    search?: string;
    status?: string;
  };
}

const props = defineProps<Props>();
const page = usePage<any>();

// Flash toast
watch(
  () => page.props.flash,
  (flash) => {
    if (!flash) return;
    if (flash.success) toast.success(flash.success);
    if (flash.error) toast.error(flash.error);
  },
  { immediate: true }
);

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const isToggleDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const packageToToggle = ref<ServicePackage | null>(null);
const packageToDelete = ref<ServicePackage | null>(null);

function applyFilters() {
  router.get('/admin/service-packages', { 
    search: search.value || undefined,
    status: status.value || undefined,
  }, { preserveState: false });
}

function clearFilters() {
  search.value = '';
  status.value = '';
  applyFilters();
}

function openToggleDialog(pkg: ServicePackage) {
  packageToToggle.value = pkg;
  isToggleDialogOpen.value = true;
}

function confirmToggle() {
  if (!packageToToggle.value) return;
  
  router.post(`/admin/service-packages/${packageToToggle.value.slug}/toggle`, {}, { 
    preserveScroll: true,
    preserveState: false, // <--- tránh lặp flash
    onSuccess: () => {
      isToggleDialogOpen.value = false;
      packageToToggle.value = null;
    }
  });
}

function openDeleteDialog(pkg: ServicePackage) {
  packageToDelete.value = pkg;
  isDeleteDialogOpen.value = true;
}

function confirmDelete() {
  if (!packageToDelete.value) return;
  
  router.delete(`/admin/service-packages/${packageToDelete.value.slug}`, { 
    preserveScroll: true,
    preserveState: false, // <--- tránh lặp flash
    onSuccess: () => {
      isDeleteDialogOpen.value = false;
      packageToDelete.value = null;
    }
  });
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý gói dịch vụ', href: '/admin/service-packages' }
];
</script>

<template>
  <Head title="Quản lý gói dịch vụ" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-5">
      <!-- Page Header -->
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Quản lý gói dịch vụ</h1>
        <Link href="/admin/service-packages/create">
          <Button class="flex items-center gap-2">
            <Plus class="h-4 w-4" /> Tạo gói mới
          </Button>
        </Link>
      </div>

      <!-- Filters -->
      <Card>
        <CardHeader>
          <CardTitle>Bộ lọc</CardTitle>
        </CardHeader>
        <CardContent class="grid md:grid-cols-3 gap-4">
          <input v-model="search" type="text" placeholder="Tìm theo tên gói" class="border rounded px-2 py-1" @keyup.enter="applyFilters"/>
          <select v-model="status" class="border rounded px-2 py-1">
            <option value="">Tất cả</option>
            <option value="active">Đang hoạt động</option>
            <option value="inactive">Đã tắt</option>
          </select>
          <div class="flex gap-2">
            <Button @click="applyFilters">Tìm</Button>
            <Button variant="outline" @click="clearFilters">Xóa</Button>
          </div>
        </CardContent>
      </Card>

      <!-- Table -->
      <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-4 py-2">Tên gói</th>
              <th class="px-4 py-2">Giá</th>
              <th class="px-4 py-2">Số ngày</th>
              <th class="px-4 py-2">Trạng thái</th>
              <th class="px-4 py-2 text-right">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="pkg in packages.data" :key="pkg.id" class="border-b hover:bg-gray-50">
              <td class="px-4 py-2">{{ pkg.name }}</td>
              <td class="px-4 py-2">{{ pkg.price.toLocaleString() }} VNĐ</td>
              <td class="px-4 py-2">{{ pkg.duration_days }}</td>
              <td class="px-4 py-2">
                <Badge :class="pkg.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ pkg.is_active ? 'Hoạt động' : 'Đã tắt' }}
                </Badge>
              </td>
              <td class="px-4 py-2 text-right flex justify-end gap-2">
                <Link :href="`/admin/service-packages/${pkg.slug}`">
                  <Button size="sm" variant="outline">
                    <Eye class="h-4 w-4" /> Xem
                  </Button>
                </Link>
                <Link :href="`/admin/service-packages/${pkg.slug}/edit`">
                  <Button size="sm" variant="outline">
                    <Edit class="h-4 w-4" /> Sửa
                  </Button>
                </Link>
                <Button size="sm" variant="outline" @click="openToggleDialog(pkg)">
                  <component :is="pkg.is_active ? EyeOff : Eye" class="h-4 w-4" />
                  {{ pkg.is_active ? 'Tắt' : 'Bật' }}
                </Button>
                <Button size="sm" variant="destructive" @click="openDeleteDialog(pkg)">
                  <Trash2 class="h-4 w-4" /> Xóa
                </Button>
                
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="packages.last_page > 1" class="flex justify-center gap-2 pt-4">
        <Button @click="router.get(`/admin/service-packages?page=${packages.current_page - 1}`)" :disabled="packages.current_page === 1">Trước</Button>
        <span>Trang {{ packages.current_page }} / {{ packages.last_page }}</span>
        <Button @click="router.get(`/admin/service-packages?page=${packages.current_page + 1}`)" :disabled="packages.current_page === packages.last_page">Sau</Button>
      </div>

      <!-- Toggle Modal -->
      <Dialog v-model:open="isToggleDialogOpen">
        <DialogContent class="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>Xác nhận {{ packageToToggle?.is_active ? 'tắt' : 'bật' }} gói</DialogTitle>
            <DialogDescription>Bạn có chắc muốn {{ packageToToggle?.is_active ? 'tắt' : 'bật' }} gói dịch vụ này không?</DialogDescription>
          </DialogHeader>
          <div class="flex justify-end gap-2 mt-4">
            <Button variant="outline" @click="isToggleDialogOpen = false">Hủy</Button>
            <Button @click="confirmToggle" :class="packageToToggle?.is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700'">
              Xác nhận
            </Button>
          </div>
        </DialogContent>
      </Dialog>

      <!-- Delete Modal -->
      <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent class="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>Xóa gói dịch vụ</DialogTitle>
            <DialogDescription>Hành động này không thể hoàn tác</DialogDescription>
          </DialogHeader>
          <div class="flex justify-end gap-2 mt-4">
            <Button variant="outline" @click="isDeleteDialogOpen = false">Hủy</Button>
            <Button variant="destructive" @click="confirmDelete">Xác nhận xóa</Button>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  </AppLayout>
</template>
