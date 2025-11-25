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

const reportsList = ref([...props.reports.data]);
const reportsStats = ref({ ...props.stats });

onMounted(() => {
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
});
console.log('Echo:', window.Echo);

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
    case 'pending': return 'bg-yellow-100 text-yellow-800';
    case 'reviewing': return 'bg-blue-100 text-blue-800';
    case 'resolved': return 'bg-green-100 text-green-800';
    case 'dismissed': return 'bg-gray-100 text-gray-800';
    default: return 'bg-gray-100 text-gray-800';
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
    <div class="space-y-6 m-[20px]">

      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
        <div>
          <h1 class="text-3xl font-bold tracking-tight flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <svg class="h-6 w-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L3 6v6c0 5 3 9 9 10s9-5 9-10V6l-9-4z"/></svg>
            </div>
            Quản lý báo cáo vi phạm
          </h1>
          <p class="text-muted-foreground mt-2 ml-[52px]">
            Danh sách các báo cáo vi phạm và trạng thái xử lý
          </p>
        </div>
      </div>

      <!-- Bộ lọc -->
      <Card>
        <CardHeader>
          <CardTitle>Bộ lọc</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid gap-4 md:grid-cols-3 mb-4">
            <select v-model="filters.status" @change="applyFilters" class="border px-2 py-1 rounded">
              <option value="all">Tất cả trạng thái</option>
              <option value="pending">Chờ xử lý</option>
              <option value="reviewing">Đang xem xét</option>
              <option value="resolved">Đã xử lý</option>
              <option value="dismissed">Đã bỏ qua</option>
            </select>
            <input
              v-model="filters.search"
              @keyup.enter="applyFilters"
              type="text"
              placeholder="Tìm kiếm tên công việc hoặc công ty"
              class="border px-2 py-1 rounded"
            />
            <div class="flex gap-2">
              <Button @click="applyFilters" class="flex-1">Tìm</Button>
              <Button variant="outline" @click="filters.search=''; filters.status='all'; applyFilters()">Xóa</Button>
            </div>
          </div>

          <div class="flex gap-2 flex-wrap">
            <Badge variant="outline">Tổng: {{ reportsStats.total }}</Badge>
            <Badge variant="outline" class="text-yellow-500">Chờ xử lý: {{ reportsStats.pending }}</Badge>
            <Badge variant="outline" class="text-blue-500">Đang xem xét: {{ reportsStats.reviewing }}</Badge>
            <Badge variant="outline" class="text-green-500">Đã xử lý: {{ reportsStats.resolved }}</Badge>
            <Badge variant="outline" class="text-gray-500">Đã bỏ qua: {{ reportsStats.dismissed }}</Badge>
          </div>
        </CardContent>
      </Card>

      <!-- Bảng báo cáo -->
      <div class="bg-white rounded-md shadow overflow-hidden">
        <div class="responsive-table-wrapper">
          <table class="w-full text-sm text-left mobile-card-view">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
              <tr>
                <th class="px-4 py-3">Tên công việc / Công ty</th>
                <th class="px-4 py-3">Lý do</th>
                <th class="px-4 py-3">Trạng thái</th>
                <th class="px-4 py-3">Ngày báo cáo</th>
                <th class="px-4 py-3 text-right">Hành động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="report in reportsList" :key="report.id" class="border-b hover:bg-gray-50">
                <td class="px-4 py-3" data-label="Tên công việc / Công ty">
                  <Link v-if="report.reportable" :href="report.reportable_type === 'App\\Models\\JobPosting' ? `/jobs/${report.reportable.slug}` : `/companies/${report.reportable.slug}`">
                    {{ report.reportable.title }}
                  </Link>
                  <span v-else>Đã xóa</span>
                </td>
                <td class="px-4 py-3" data-label="Lý do">{{ report.reason_label }}</td>
                <td class="px-4 py-3" data-label="Trạng thái">
                  <Badge :class="statusColor(report.status)">
                    {{ report.status_label }}
                  </Badge>
                </td>
                <td class="px-4 py-3" data-label="Ngày báo cáo">{{ report.created_at }}</td>
                <td class="px-4 py-3 text-right" data-label="Hành động">
                  <div class="flex items-center justify-end gap-2 flex-wrap">
                    <Button size="sm" @click="router.get(`/admin/reports/${report.id}`)">Xem</Button>
                    <Button size="sm" variant="destructive" @click="deleteReport(report.id)">Xóa</Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-if="reportsList.length === 0" class="text-center py-12 text-muted-foreground">
        <svg class="h-16 w-16 mx-auto mb-4 text-muted-foreground/30" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L3 6v6c0 5 3 9 9 10s9-5 9-10V6l-9-4z"/></svg>
        <p>Không tìm thấy báo cáo nào</p>
      </div>

      <!-- Pagination -->
      <div v-if="props.reports?.last_page > 1" class="flex items-center justify-center gap-2 pt-6 mt-6 border-t">
        <Button
          :disabled="props.reports?.current_page === 1"
          variant="outline"
          size="sm"
          @click="router.get(`/admin/reports?page=${props.reports?.current_page - 1}`)"
        >
          Trước
        </Button>
        <span class="text-sm text-muted-foreground">
          Trang {{ props.reports?.current_page }} / {{ props.reports?.last_page }}
        </span>
        <Button
          :disabled="props.reports?.current_page === props.reports?.last_page"
          variant="outline"
          size="sm"
          @click="router.get(`/admin/reports?page=${props.reports?.current_page + 1}`)"
        >
          Sau
        </Button>
      </div>

    </div>
  </AppLayout>
</template>
