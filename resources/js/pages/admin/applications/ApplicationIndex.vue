<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Search, Eye, Trash2 } from 'lucide-vue-next';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';

interface Application {
  id: number;
  status: string;
  created_at: string;
  candidate: {
    id: number;
    user: {
      name: string;
      email: string;
      avatar?: string;
    };
  };
  jobPosting: {
    id: number;
    title: string;
    company?: { company_name: string } | null;
  } | null;
}

interface Props {
  applications: {
    data: Application[];
    current_page: number;
    last_page: number;
  };
  filters?: {
    search?: string;
    status?: string;
    company_id?: number;
    job_posting_id?: number;
  };
  companies: { id: number; company_name: string }[];
  jobPostings: { id: number; title: string; company?: { company_name: string } }[];
}

const props = defineProps<Props>();

// Filter form
const filterForm = ref({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  company_id: props.filters?.company_id || '',
  job_posting_id: props.filters?.job_posting_id || '',
});

// Delete modal
const isDeleteDialogOpen = ref(false);
const applicationToDelete = ref<Application | null>(null);
const successMessage = ref('');

// Status options
const statusOptions = [
  { value: 'pending', label: 'Chờ xử lý', class: 'bg-yellow-100 text-yellow-800' },
  { value: 'reviewing', label: 'Đang xem xét', class: 'bg-blue-100 text-blue-800' },
  { value: 'interview', label: 'Phỏng vấn', class: 'bg-purple-100 text-purple-800' },
  { value: 'accepted', label: 'Chấp nhận', class: 'bg-green-100 text-green-800' },
  { value: 'rejected', label: 'Từ chối', class: 'bg-red-100 text-red-800' },
];

const getStatusBadgeClass = (status: string) =>
  statusOptions.find(s => s.value === status)?.class || 'bg-gray-100 text-gray-800';

const getStatusLabel = (status: string) =>
  statusOptions.find(s => s.value === status)?.label || status;

// Áp dụng filter
const applyFilters = () => {
  router.get(
    '/admin/applications',
    filterForm.value,
    { preserveState: true, preserveScroll: true }
  );
};

// Xóa filter
const clearFilters = () => {
  filterForm.value = { search: '', status: '', company_id: '', job_posting_id: '' };
  applyFilters();
};

// Mở modal xóa
const openDeleteDialog = (app: Application) => {
  applicationToDelete.value = app;
  isDeleteDialogOpen.value = true;
};

// Xác nhận xóa
const confirmDelete = () => {
  if (!applicationToDelete.value) return;

  router.delete(`/admin/applications/${applicationToDelete.value.id}`, {
    preserveScroll: true,
    onSuccess: (page) => {
      // Cập nhật table sau khi xóa
      const index = props.applications.data.findIndex(a => a.id === applicationToDelete.value?.id);
      if (index !== -1) {
        props.applications.data.splice(index, 1);
      }
      successMessage.value = 'Xóa ứng tuyển thành công!';
      isDeleteDialogOpen.value = false;
      applicationToDelete.value = null;
      setTimeout(() => successMessage.value = '', 3000);
    }
  });
};

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý ứng tuyển', href: '/admin/applications' },
];
</script>

<template>
  <Head title="Quản lý ứng tuyển - Admin" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-5">

      <!-- Filters -->
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <Search class="w-5 h-5" /> Bộ lọc
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid gap-4 md:grid-cols-4 mb-4">
            <Input v-model="filterForm.search" placeholder="Tìm kiếm ứng viên..." @keyup.enter="applyFilters" />
            
            <select v-model="filterForm.status" class="border rounded px-2 py-2">
              <option value="">Tất cả trạng thái</option>
              <option v-for="s in statusOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
            </select>

            <select v-model="filterForm.company_id" class="border rounded px-2 py-2">
              <option value="">Tất cả công ty</option>
              <option v-for="c in props.companies" :key="c.id" :value="c.id">{{ c.company_name }}</option>
            </select>

            <select v-model="filterForm.job_posting_id" class="border rounded px-2 py-2">
              <option value="">Tất cả vị trí</option>
              <option v-for="j in props.jobPostings" :key="j.id" :value="j.id">{{ j.title }}</option>
            </select>

            <div class="flex gap-2 md:col-span-4">
              <Button @click="applyFilters" class="flex-1">Tìm</Button>
              <Button variant="outline" @click="clearFilters">Xóa</Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Applications Table -->
      <div class="bg-white rounded-md shadow overflow-hidden">
        <div class="responsive-table-wrapper">
          <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
              <tr>
                <th class="px-4 py-3">Ứng viên</th>
                <th class="px-4 py-3">Công ty</th>
                <th class="px-4 py-3">Vị trí</th>
                <th class="px-4 py-3">Ngày ứng tuyển</th>
                <th class="px-4 py-3">Trạng thái</th>
                <th class="px-4 py-3 text-right">Hành động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="app in props.applications.data" :key="app.id" class="border-b hover:bg-gray-50">
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                      <img v-if="app.candidate.user.avatar" :src="`/storage/${app.candidate.user.avatar}`" :alt="app.candidate.user.name" class="w-full h-full object-cover"/>
                      <span v-else class="text-white font-bold">{{ app.candidate.user.name.charAt(0).toUpperCase() }}</span>
                    </div>
                    <div>
                      <p>{{ app.candidate.user.name }}</p>
                      <p class="text-xs text-gray-500">{{ app.candidate.user.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3">{{ app.jobPosting?.company?.company_name || '-' }}</td>
                <td class="px-4 py-3">{{ app.jobPosting?.title || '-' }}</td>
                <td class="px-4 py-3">{{ new Date(app.created_at).toLocaleDateString('vi-VN') }}</td>
                <td class="px-4 py-3">
                  <Badge :class="getStatusBadgeClass(app.status)">{{ getStatusLabel(app.status) }}</Badge>
                </td>
                <td class="px-4 py-3 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <Link :href="`/admin/applications/${app.id}`">
                      <Button size="sm" variant="outline">
                        <Eye class="w-4 h-4 mr-1"/> Xem
                      </Button>
                    </Link>
                    <Button size="sm" variant="destructive" @click="openDeleteDialog(app)">
                      <Trash2 class="w-4 h-4 mr-1"/> Xóa
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent class="sm:max-w-md">
          <DialogHeader>
            <div class="flex items-center gap-3 mb-2">
              <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                <Trash2 class="h-6 w-6 text-red-600" />
              </div>
              <div>
                <DialogTitle class="text-xl">Xóa Ứng tuyển</DialogTitle>
                <DialogDescription>Hành động này không thể hoàn tác</DialogDescription>
              </div>
            </div>
          </DialogHeader>
          <div class="space-y-4">
            <p class="text-sm text-red-700">
              Bạn có chắc muốn xóa ứng tuyển <strong>{{ applicationToDelete?.candidate.user.name }}</strong> cho vị trí <strong>{{ applicationToDelete?.jobPosting?.title }}</strong>?
            </p>
            <div class="flex justify-end gap-2 pt-2">
              <Button type="button" variant="outline" @click="isDeleteDialogOpen = false">Hủy</Button>
              <Button variant="destructive" @click="confirmDelete">
                <Trash2 class="h-4 w-4 mr-2" /> Xác nhận xóa
              </Button>
            </div>
          </div>
        </DialogContent>
      </Dialog>

      <!-- Success Toast -->
      <div v-if="successMessage" class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow">
        {{ successMessage }}
      </div>

    </div>
  </AppLayout>
</template>
