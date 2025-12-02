<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';
import { Search, Briefcase, CheckCircle, XCircle, Trash2, AlertCircle, Eye } from 'lucide-vue-next';
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
  slug: string;
  description: string;
  requirements: string;
  benefits?: string;
  location: string;
  city: string;
  province: string;
  employment_type: string;
  experience_level: string;
  min_salary?: number;
  max_salary?: number;
  salary_type: string;
  application_deadline?: string;
  quantity: number;
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
const showRejectModal = ref(false);
const showDeleteModal = ref(false);
const showViewModal = ref(false);
const selectedJobId = ref<number | null>(null);
const selectedJob = ref<JobPosting | null>(null);
const rejectionReason = ref('');
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

function viewJob(job: JobPosting) {
  selectedJob.value = job;
  showViewModal.value = true;
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
  selectedJobId.value = id;
  rejectionReason.value = '';
  showRejectModal.value = true;
}

function confirmReject() {
  if (!selectedJobId.value) return;
  
  isProcessing.value = true;
  router.post(`/admin/job-postings/${selectedJobId.value}/reject`, {
    reason: rejectionReason.value
  }, { 
    preserveScroll: true,
    onSuccess: () => {
      showRejectModal.value = false;
      selectedJobId.value = null;
      rejectionReason.value = '';
      isProcessing.value = false;
    },
    onError: () => {
      isProcessing.value = false;
    }
  });
}

function deleteJob(id: number) {
  selectedJobId.value = id;
  showDeleteModal.value = true;
}

function confirmDelete() {
  if (!selectedJobId.value) return;
  
  isProcessing.value = true;
  router.delete(`/admin/job-postings/${selectedJobId.value}`, { 
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      selectedJobId.value = null;
      isProcessing.value = false;
    },
    onError: () => {
      isProcessing.value = false;
    }
  });
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
                        size="sm" 
                        variant="outline"
                        class="text-blue-600 hover:text-blue-700 hover:bg-blue-50 border-blue-200"
                        @click="viewJob(job)"
                        title="Xem chi tiết"
                    >
                      <Eye class="w-4 h-4 mr-1" /> Xem
                    </Button>

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

    <!-- Reject Confirmation Modal -->
    <Dialog :open="showRejectModal" @update:open="showRejectModal = $event">
      <DialogContent class="sm:max-w-[500px]">
        <DialogHeader>
          <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
            <XCircle class="h-6 w-6 text-red-600" />
          </div>
          <DialogTitle class="text-center text-xl">Từ chối tin tuyển dụng</DialogTitle>
          <DialogDescription class="text-center">
            Vui lòng nhập lý do từ chối tin tuyển dụng này. Nhà tuyển dụng sẽ nhận được thông báo kèm lý do.
          </DialogDescription>
        </DialogHeader>
        <div class="py-4">
          <Textarea
            v-model="rejectionReason"
            placeholder="Nhập lý do từ chối (ví dụ: Nội dung vi phạm chính sách, thông tin không rõ ràng...)"
            :rows="4"
            class="resize-none"
          />
        </div>
        <DialogFooter class="sm:justify-center gap-2">
          <Button variant="outline" @click="showRejectModal = false" :disabled="isProcessing">
            Hủy bỏ
          </Button>
          <Button 
            variant="destructive" 
            @click="confirmReject" 
            :disabled="isProcessing || !rejectionReason.trim()"
          >
            <span v-if="isProcessing">Đang xử lý...</span>
            <span v-else>Xác nhận từ chối</span>
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Delete Confirmation Modal -->
    <Dialog :open="showDeleteModal" @update:open="showDeleteModal = $event">
      <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
          <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
            <AlertCircle class="h-6 w-6 text-red-600" />
          </div>
          <DialogTitle class="text-center text-xl">Xác nhận xóa tin</DialogTitle>
          <DialogDescription class="text-center">
            Bạn có chắc chắn muốn xóa tin tuyển dụng này? 
            <span class="block mt-2 font-semibold text-red-600">Hành động này không thể hoàn tác!</span>
          </DialogDescription>
        </DialogHeader>
        <DialogFooter class="sm:justify-center gap-2 mt-4">
          <Button variant="outline" @click="showDeleteModal = false" :disabled="isProcessing">
            Hủy bỏ
          </Button>
          <Button 
            variant="destructive" 
            class="bg-red-600 hover:bg-red-700" 
            @click="confirmDelete" 
            :disabled="isProcessing"
          >
            <Trash2 class="w-4 h-4 mr-1" />
            <span v-if="isProcessing">Đang xóa...</span>
            <span v-else>Xác nhận xóa</span>
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- View Job Details Modal -->
    <Dialog :open="showViewModal" @update:open="showViewModal = $event">
      <DialogContent class="sm:max-w-[800px] max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle class="text-2xl font-bold">Chi tiết tin tuyển dụng</DialogTitle>
        </DialogHeader>
        
        <div v-if="selectedJob" class="space-y-6 py-4">
          <!-- Job Title & Company -->
          <div class="border-b pb-4">
            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ selectedJob.title }}</h2>
            <p class="text-gray-600">{{ selectedJob.company?.company_name }}</p>
          </div>

          <!-- Job Info Grid -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <h3 class="text-sm font-semibold text-gray-500 mb-1">Địa điểm</h3>
              <p class="text-gray-900">{{ selectedJob.city }}, {{ selectedJob.province }}</p>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-500 mb-1">Loại hình</h3>
              <p class="text-gray-900">{{ selectedJob.employment_type }}</p>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-500 mb-1">Kinh nghiệm</h3>
              <p class="text-gray-900">{{ selectedJob.experience_level }}</p>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-500 mb-1">Số lượng</h3>
              <p class="text-gray-900">{{ selectedJob.quantity }} người</p>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-500 mb-1">Mức lương</h3>
              <p class="text-gray-900">
                <template v-if="selectedJob.min_salary && selectedJob.max_salary">
                  {{ selectedJob.min_salary.toLocaleString() }} - {{ selectedJob.max_salary.toLocaleString() }} VNĐ
                </template>
                <template v-else>Thỏa thuận</template>
              </p>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-500 mb-1">Hạn nộp</h3>
              <p class="text-gray-900">
                {{ selectedJob.application_deadline ? new Date(selectedJob.application_deadline).toLocaleDateString('vi-VN') : 'Không giới hạn' }}
              </p>
            </div>
          </div>

          <!-- Description -->
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Mô tả công việc</h3>
            <div class="prose prose-sm max-w-none text-gray-700" v-html="selectedJob.description"></div>
          </div>

          <!-- Requirements -->
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Yêu cầu công việc</h3>
            <div class="prose prose-sm max-w-none text-gray-700" v-html="selectedJob.requirements"></div>
          </div>

          <!-- Benefits -->
          <div v-if="selectedJob.benefits">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Quyền lợi</h3>
            <div class="prose prose-sm max-w-none text-gray-700" v-html="selectedJob.benefits"></div>
          </div>

          <!-- Status Badge -->
          <div class="flex items-center gap-2 pt-4 border-t">
            <span class="text-sm font-semibold text-gray-500">Trạng thái:</span>
            <Badge :class="getStatusBadge(selectedJob.status).class">
              {{ getStatusBadge(selectedJob.status).label }}
            </Badge>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="showViewModal = false">
            Đóng
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
