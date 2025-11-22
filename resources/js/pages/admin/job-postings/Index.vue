<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Search, Briefcase, CheckCircle, XCircle, Trash2, AlertCircle } from 'lucide-vue-next';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';


interface JobPosting {
  id: number;
  title: string;
  company: {
    id: number;
    company_name: string;
  };
  status: string;
  created_at: string;
  published_at?: string;
  is_active: boolean;
}

interface Props {
  jobs: {
    data: JobPosting[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  filters: {
    search?: string;
    status?: string;
  };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');

// Modal state
const showApproveModal = ref(false);
const selectedJobId = ref<number | null>(null);
const isProcessing = ref(false);

function applyFilters() {
  router.get('/admin/job-postings', { 
    search: search.value || undefined,
    status: status.value !== 'all' ? status.value : undefined
  }, { preserveState: true, preserveScroll: true });
}

function clearFilters() {
  search.value = '';
  status.value = 'all';
  applyFilters();
}

function approveJob(id: number) {
  selectedJobId.value = id;
  showApproveModal.value = true;
}

function confirmApprove() {
  if (!selectedJobId.value) return;
  
  isProcessing.value = true;
  router.post(`/admin/job-postings/${selectedJobId.value}/approve`, {}, { 
    preserveScroll: true,
    onSuccess: () => {
      showApproveModal.value = false;
      selectedJobId.value = null;
      isProcessing.value = false;
    },
    onError: () => {
      isProcessing.value = false;
    }
  });
}

function rejectJob(id: number) {
  if (confirm('Bạn có chắc chắn muốn từ chối tin tuyển dụng này?')) {
    router.post(`/admin/job-postings/${id}/reject`, {}, { preserveScroll: true });
  }
}

function deleteJob(id: number) {
  if (confirm('Bạn có chắc chắn muốn xóa tin tuyển dụng này? Hành động này không thể hoàn tác.')) {
    router.delete(`/admin/job-postings/${id}`, { preserveScroll: true });
  }
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý tin tuyển dụng', href: '/admin/job-postings' }
];

const statusMap: Record<string, { label: string; class: string }> = {
  pending: { label: 'Chờ duyệt', class: 'bg-yellow-100 text-yellow-800' },
  approved: { label: 'Đã duyệt', class: 'bg-green-100 text-green-800' },
  rejected: { label: 'Từ chối', class: 'bg-red-100 text-red-800' },
  expired: { label: 'Hết hạn', class: 'bg-gray-100 text-gray-800' },
};

function getStatusBadge(status: string) {
  return statusMap[status] || { label: status, class: 'bg-gray-100 text-gray-800' };
}
</script>

<template>
  <Head title="Quản lý tin tuyển dụng" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-[20px]">
      
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
        <div>
          <h1 class="text-3xl font-bold tracking-tight flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <Briefcase class="h-6 w-6 text-primary" />
            </div>
            Quản lý tin tuyển dụng
          </h1>
          <p class="text-muted-foreground mt-2 ml-[52px]">
            Duyệt và quản lý các tin tuyển dụng từ nhà tuyển dụng
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
          <div class="grid gap-4 md:grid-cols-4 mb-4">
            <div class="md:col-span-2">
                <Input
                v-model="search"
                placeholder="Tìm kiếm tiêu đề hoặc tên công ty..."
                @keyup.enter="applyFilters"
                />
            </div>
            
            <div class="w-[200px]">
                <select
                    v-model="status"
                    class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    @change="applyFilters"
                >
                    <option value="all">Tất cả trạng thái</option>
                    <option value="pending">Chờ duyệt</option>
                    <option value="approved">Đã duyệt</option>
                    <option value="rejected">Từ chối</option>
                </select>
            </div>

            <div class="flex gap-2">
              <Button @click="applyFilters" class="flex-1">Tìm</Button>
              <Button variant="outline" @click="clearFilters">Xóa</Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Jobs Table -->
      <div class="bg-white rounded-md shadow overflow-hidden">
        <div class="responsive-table-wrapper">
          <table class="w-full text-sm text-left mobile-card-view">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
              <tr>
                <th class="px-4 py-3">Tiêu đề</th>
                <th class="px-4 py-3">Công ty</th>
                <th class="px-4 py-3">Ngày đăng</th>
                <th class="px-4 py-3">Trạng thái</th>
                <th class="px-4 py-3 text-right">Hành động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="job in jobs.data" :key="job.id" class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900" data-label="Tiêu đề">
                  <div class="font-semibold">{{ job.title }}</div>
                  <div class="text-xs text-gray-500 md:hidden">#{{ job.id }}</div>
                </td>
                <td class="px-4 py-3" data-label="Công ty">
                  {{ job.company?.company_name }}
                </td>
                <td class="px-4 py-3" data-label="Ngày đăng">
                  {{ new Date(job.created_at).toLocaleDateString('vi-VN') }}
                </td>
                <td class="px-4 py-3" data-label="Trạng thái">
                  <Badge 
                    variant="default" 
                    :class="getStatusBadge(job.status).class"
                  >
                    {{ getStatusBadge(job.status).label }}
                  </Badge>
                </td>
                <td class="px-4 py-3 text-right" data-label="Hành động">
                  <div class="flex items-center justify-end gap-2 flex-wrap">
                    <Button 
                        v-if="job.status === 'pending'"
                        size="sm" 
                        class="bg-green-600 hover:bg-green-700 text-white"
                        @click="approveJob(job.id)"
                        title="Duyệt tin"
                    >
                      <CheckCircle class="w-4 h-4 mr-1" /> Duyệt
                    </Button>

                    <Button 
                        v-if="job.status === 'pending' || job.status === 'approved'"
                        size="sm" 
                        variant="destructive"
                        @click="rejectJob(job.id)"
                        title="Từ chối tin"
                    >
                      <XCircle class="w-4 h-4 mr-1" /> Từ chối
                    </Button>

                    <Button 
                        size="sm" 
                        variant="outline" 
                        class="text-red-600 hover:text-red-700 hover:bg-red-50 border-red-200"
                        @click="deleteJob(job.id)"
                        title="Xóa tin"
                    >
                      <Trash2 class="w-4 h-4" />
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="jobs.data.length === 0" class="text-center py-12 text-muted-foreground">
        <Briefcase class="h-16 w-16 mx-auto mb-4 text-muted-foreground/30" />
        <p>Không tìm thấy tin tuyển dụng nào</p>
      </div>

      <!-- Pagination -->
      <div v-if="jobs.last_page > 1" class="flex items-center justify-center gap-2 pt-6 mt-6 border-t">
        <Button
          :disabled="jobs.current_page === 1"
          variant="outline"
          size="sm"
          @click="router.get(`/admin/job-postings?page=${jobs.current_page - 1}`)"
        >
          Trước
        </Button>
        <span class="text-sm text-muted-foreground">
          Trang {{ jobs.current_page }} / {{ jobs.last_page }}
        </span>
        <Button
          :disabled="jobs.current_page === jobs.last_page"
          variant="outline"
          size="sm"
          @click="router.get(`/admin/job-postings?page=${jobs.current_page + 1}`)"
        >
          Sau
        </Button>
      </div>
    </div>

    <!-- Approve Confirmation Modal -->
    <Dialog :open="showApproveModal" @update:open="showApproveModal = $event">
      <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
          <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
            <CheckCircle class="h-6 w-6 text-green-600" />
          </div>
          <DialogTitle class="text-center text-xl">Xác nhận duyệt tin</DialogTitle>
          <DialogDescription class="text-center">
            Bạn có chắc chắn muốn duyệt tin tuyển dụng này? Tin sẽ được hiển thị công khai cho các ứng viên.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter class="sm:justify-center gap-2 mt-4">
          <Button variant="outline" @click="showApproveModal = false" :disabled="isProcessing">
            Hủy bỏ
          </Button>
          <Button class="bg-green-600 hover:bg-green-700" @click="confirmApprove" :disabled="isProcessing">
            <span v-if="isProcessing">Đang xử lý...</span>
            <span v-else>Xác nhận duyệt</span>
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
