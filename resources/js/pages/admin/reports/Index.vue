<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import Echo from 'laravel-echo';

const props = defineProps<{
  reports: {
    data: Array<{
      id: number;
      user: { id: number; name: string; email: string };
      reason: string;
      reason_label: string;
      description: string;
      status: string;
      status_label: string;
      reportable_type: string;
      reportable_type_label: string;
      reportable: {
        id: number;
        title: string;
        slug: string;
      } | null;
      admin_notes: string | null;
      reviewer: { id: number; name: string } | null;
      created_at: string;
      reviewed_at: string | null;
    }>;
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  stats: {
    total: number;
    pending: number;
    reviewing: number;
    resolved: number;
    dismissed: number;
  };
  filters: {
    status?: string;
    type?: string;
    reason?: string;
    search?: string;
  };
}>();

// Tạm tắt Echo để tránh lỗi thiếu Pusher key
// Bật lại khi đã cấu hình Reverb server
/*
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST ?? '127.0.0.1',
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: false,
    encrypted: false,
    enabledTransports: ['ws'],
});
*/

const reportsList = ref([...props.reports.data]);
const reportsStats = ref({ ...props.stats });

onMounted(() => {
  // Tắt realtime listening
  /*
  if (window.Echo) {
    window.Echo.channel('admin-reports')
      .listen('NewReportCreated', (e: { report: any }) => {
        if (!e?.report) return;
        console.log('Báo cáo mới realtime:', e.report);

        if (!e.report.reportable) {
          e.report.reportable = { id: null, title: 'Đã xóa', slug: '#' };
        }
        reportsList.value.unshift(e.report);
        reportsStats.value.total += 1;
        if (e.report.status === 'pending') reportsStats.value.pending += 1;
      });
  }
  */
});

const filters = ref({ ...props.filters });

function applyFilters() {
  router.get('/admin/reports', filters.value, { preserveState: false });
}

function deleteReport(id: number) {
  if (!confirm('Bạn có chắc chắn muốn xóa báo cáo này?')) return;
  router.delete(`/admin/reports/${id}`);
}

function statusColor(status: string) {
  switch (status) {
    case 'pending': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
    case 'reviewing': return 'bg-blue-100 text-blue-800 border-blue-200';
    case 'resolved': return 'bg-green-100 text-green-800 border-green-200';
    case 'dismissed': return 'bg-gray-100 text-gray-800 border-gray-200';
    default: return 'bg-gray-100 text-gray-800 border-gray-200';
  }
}

function getTypeIcon(type: string) {
  switch (type) {
    case 'App\\Models\\JobPosting': return '💼';
    case 'App\\Models\\Company': return '🏢';
    case 'App\\Models\\CandidateProfile': return '👤';
    default: return '📄';
  }
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý báo cáo', href: '/admin/reports' }
];
</script>

<template>
  <Head title="Quản lý báo cáo vi phạm" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center shadow-lg">
              <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-3xl font-bold tracking-tight text-gray-900">Quản lý báo cáo vi phạm</h1>
              <p class="text-sm text-gray-500 mt-1">Theo dõi và xử lý các báo cáo từ người dùng</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
        <!-- Total -->
        <Card class="border-l-4 border-l-purple-500 hover:shadow-lg transition-shadow">
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Tổng báo cáo</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ reportsStats.total }}</p>
              </div>
              <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                <svg class="h-6 w-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                  <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Pending -->
        <Card class="border-l-4 border-l-yellow-500 hover:shadow-lg transition-shadow">
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Chờ xử lý</p>
                <p class="text-3xl font-bold text-yellow-600 mt-2">{{ reportsStats.pending }}</p>
              </div>
              <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center">
                <svg class="h-6 w-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Reviewing -->
        <Card class="border-l-4 border-l-blue-500 hover:shadow-lg transition-shadow">
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Đang xem xét</p>
                <p class="text-3xl font-bold text-blue-600 mt-2">{{ reportsStats.reviewing }}</p>
              </div>
              <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                  <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Resolved -->
        <Card class="border-l-4 border-l-green-500 hover:shadow-lg transition-shadow">
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Đã xử lý</p>
                <p class="text-3xl font-bold text-green-600 mt-2">{{ reportsStats.resolved }}</p>
              </div>
              <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Dismissed -->
        <Card class="border-l-4 border-l-gray-500 hover:shadow-lg transition-shadow">
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Đã bỏ qua</p>
                <p class="text-3xl font-bold text-gray-600 mt-2">{{ reportsStats.dismissed }}</p>
              </div>
              <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center">
                <svg class="h-6 w-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Filters -->
      <Card>
        <CardHeader class="border-b bg-gray-50/50">
          <CardTitle class="text-lg flex items-center gap-2">
            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Bộ lọc tìm kiếm
          </CardTitle>
        </CardHeader>
        <CardContent class="p-6">
          <div class="grid gap-4 md:grid-cols-4">
            <div>
              <label class="text-sm font-medium text-gray-700 mb-2 block">Trạng thái</label>
              <select v-model="filters.status" @change="applyFilters" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="all">Tất cả trạng thái</option>
                <option value="pending">Chờ xử lý</option>
                <option value="reviewing">Đang xem xét</option>
                <option value="resolved">Đã xử lý</option>
                <option value="dismissed">Đã bỏ qua</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <label class="text-sm font-medium text-gray-700 mb-2 block">Tìm kiếm</label>
              <input
                v-model="filters.search"
                @keyup.enter="applyFilters"
                type="text"
                placeholder="Tìm kiếm theo tên, email, nội dung..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div class="flex items-end gap-2">
              <Button @click="applyFilters" class="flex-1">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Tìm kiếm
              </Button>
              <Button variant="outline" @click="filters.search=''; filters.status='all'; applyFilters()">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Reports Table -->
      <Card>
        <CardHeader class="border-b bg-gray-50/50">
          <CardTitle class="text-lg">Danh sách báo cáo ({{ reportsList.length }})</CardTitle>
        </CardHeader>
        <CardContent class="p-0">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 border-b">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Đối tượng</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Người báo cáo</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lý do</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Trạng thái</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ngày tạo</th>
                  <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Thao tác</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="report in reportsList" :key="report.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <span class="text-2xl">{{ getTypeIcon(report.reportable_type) }}</span>
                      <div>
                        <Link v-if="report.reportable" :href="report.reportable_type === 'App\\Models\\JobPosting' ? `/jobs/${report.reportable.slug}` : `/companies/${report.reportable.slug}`" class="font-medium text-gray-900 hover:text-blue-600">
                          {{ report.reportable.title }}
                        </Link>
                        <span v-else class="text-gray-400 italic">Đã xóa</span>
                        <p class="text-xs text-gray-500 mt-1">{{ report.reportable_type_label }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div>
                      <p class="font-medium text-gray-900">{{ report.user.name }}</p>
                      <p class="text-xs text-gray-500">{{ report.user.email }}</p>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-sm text-gray-700">{{ report.reason_label }}</span>
                  </td>
                  <td class="px-6 py-4">
                    <Badge :class="statusColor(report.status)" class="font-medium">
                      {{ report.status_label }}
                    </Badge>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-sm text-gray-600">{{ new Date(report.created_at).toLocaleDateString('vi-VN') }}</span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                      <Button size="sm" variant="outline" @click="router.get(`/admin/reports/${report.id}`)">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Xem
                      </Button>
                      <Button size="sm" variant="destructive" @click="deleteReport(report.id)">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </Button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="reportsList.length === 0" class="text-center py-16">
            <svg class="h-20 w-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 text-lg font-medium">Không tìm thấy báo cáo nào</p>
            <p class="text-gray-400 text-sm mt-1">Thử thay đổi bộ lọc hoặc tìm kiếm khác</p>
          </div>
        </CardContent>
      </Card>

      <!-- Pagination -->
      <div v-if="props.reports?.last_page > 1" class="flex items-center justify-between border-t pt-6">
        <div class="text-sm text-gray-600">
          Hiển thị <span class="font-medium">{{ (props.reports.current_page - 1) * props.reports.per_page + 1 }}</span> - 
          <span class="font-medium">{{ Math.min(props.reports.current_page * props.reports.per_page, props.reports.total) }}</span> 
          trong tổng số <span class="font-medium">{{ props.reports.total }}</span> báo cáo
        </div>
        <div class="flex items-center gap-2">
          <Button
            :disabled="props.reports?.current_page === 1"
            variant="outline"
            size="sm"
            @click="router.get(`/admin/reports?page=${props.reports?.current_page - 1}`)"
          >
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Trước
          </Button>
          <span class="text-sm text-gray-600 px-4">
            Trang {{ props.reports?.current_page }} / {{ props.reports?.last_page }}
          </span>
          <Button
            :disabled="props.reports?.current_page === props.reports?.last_page"
            variant="outline"
            size="sm"
            @click="router.get(`/admin/reports?page=${props.reports?.current_page + 1}`)"
          >
            Sau
            <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </Button>
        </div>
      </div>

    </div>
  </AppLayout>
</template>
