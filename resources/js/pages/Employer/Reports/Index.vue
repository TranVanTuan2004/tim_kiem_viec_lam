<template>
  <AppLayout>
    <Head title="Theo dõi báo cáo ứng viên" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 shadow-xl">
          <div class="relative px-8 py-10 sm:px-12 flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-white sm:text-4xl">Theo dõi báo cáo ứng viên</h1>
              <p class="mt-3 text-lg text-blue-100">Xem lại các báo cáo bạn (Employer) đã gửi và tình trạng xử lý</p>
            </div>
            <div class="hidden sm:block">
              <FileText class="h-16 w-16 text-white/30" />
            </div>
          </div>
        </div>

        <Card class="mb-6">
          <CardHeader>
            <CardTitle class="text-lg">Bộ lọc</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Trạng thái</label>
                <select
                  v-model="localFilters.status"
                  @change="applyFilters"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="all">Tất cả</option>
                  <option value="pending">Chờ xử lý</option>
                  <option value="reviewing">Đang xem xét</option>
                  <option value="resolved">Đã xử lý</option>
                  <option value="dismissed">Bỏ qua</option>
                </select>
              </div>

              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Tìm kiếm</label>
                <div class="relative">
                  <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                  <input
                    v-model="localFilters.search"
                    @input="debounceSearch"
                    type="text"
                    placeholder="Tìm báo cáo... (tên ứng viên / lý do)"
                    class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  />
                </div>
              </div>

              <div class="flex items-end gap-2">
                <button @click="applyFilters" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-white">Tìm</button>
                <button @click="resetFilters" class="inline-flex items-center justify-center rounded-md border px-4 py-2">Xóa</button>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Danh sách báo cáo</CardTitle>
          </CardHeader>
          <CardContent class="p-0">
            <div v-if="reports.data.length === 0" class="p-12 text-center">
              <FileText class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-4 text-sm font-medium text-gray-900">Không tìm thấy báo cáo</h3>
              <p class="mt-2 text-sm text-gray-500">Bạn chưa gửi báo cáo nào hoặc bộ lọc không khớp.</p>
            </div>

            <div v-else class="responsive-table-wrapper">
              <table class="w-full text-sm text-left mobile-card-view">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                  <tr>
                    <th class="px-6 py-3">Đối tượng</th>
                    <th class="px-6 py-3">Lý do</th>
                    <th class="px-6 py-3">Trạng thái</th>
                    <th class="px-6 py-3">Thời gian</th>
                    <th class="px-6 py-3 text-right">Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="report in reports.data"
                    :key="report.id"
                    class="bg-white border-b hover:bg-gray-50"
                  >
                    <td class="px-6 py-4" data-label="Đối tượng">{{ report.candidate?.name ?? report.reportable?.title ?? '(Đã xóa)' }}</td>
                    <td class="px-6 py-4" data-label="Lý do">{{ report.reason }}</td>
                    <td class="px-6 py-4" data-label="Trạng thái">
                      <Badge :variant="getStatusVariant(report.status)">{{ report.status_label }}</Badge>
                    </td>
                    <td class="px-6 py-4" data-label="Thời gian">{{ formatDate(report.created_at) }}</td>
                    <td class="px-6 py-4 text-right" data-label="Hành động">
                      <Link
                        :href="`/employer/reports/${report.id}`"
                        class="inline-flex items-center justify-center rounded-md bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-800"
                      >
                        Xem
                      </Link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </CardContent>

          <div v-if="reports.links && reports.links.length > 0" class="border-t border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Hiển thị {{ reports.from }} đến {{ reports.to }} trong tổng số {{ reports.total }} báo cáo
              </div>
              <div class="flex space-x-2">
                <Link
                  v-for="link in reports.links"
                  :key="link.label"
                  :href="link.url || '#'"
                  v-html="link.label"
                  class="rounded-md border border-gray-300 px-3 py-2 text-sm"
                  :class="link.active ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-50'"
                ></Link>
              </div>
            </div>
          </div>
        </Card>

      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue'; // nếu bạn dùng component này
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { FileText, Search } from 'lucide-vue-next';

interface Reports {
  data: any[];
  links?: any[];
  from?: number;
  to?: number;
  total?: number;
}

interface Props {
  reports: Reports;
  filters: Record<string, any>;
}

const props = defineProps<Props>();
const reports = props.reports || { data: [], links: [] };

// local reactive copy of filters
const localFilters = reactive({ ...props.filters });

// debounce helper
let searchTimeout: number | undefined = undefined;
const debounceSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = window.setTimeout(() => applyFilters(), 450);
};

function applyFilters() {
  // request to same route, keeps state/scroll
  router.get('/employer/reports', localFilters, {
    preserveState: true,
    preserveScroll: true,
  });
}

function resetFilters() {
  localFilters.status = 'all';
  localFilters.search = '';
  applyFilters();
}

const formatDate = (date: string | null) => {
  if (!date) return '—';
  try {
    return new Date(date).toLocaleString('vi-VN', { year: 'numeric', month: 'long', day: 'numeric' });
  } catch {
    return date;
  }
};

const getStatusVariant = (status: string) => {
  const map: Record<string, string> = {
    pending: 'secondary',
    reviewing: 'outline',
    resolved: 'default',
    dismissed: 'destructive',
  };
  return map[status] ?? 'secondary';
};
</script>

<style scoped>
/* small responsive tweaks if needed */
.responsive-table-wrapper {
  overflow-x: auto;
}
.mobile-card-view td[data-label]::before {
  /* if you use mobile-card-view styles elsewhere */
}
</style>
