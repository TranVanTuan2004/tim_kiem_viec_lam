<script setup lang="ts">
import CandidateLayout from '@/layouts/CandidateLayout.vue';
import { reactive, ref, watch } from 'vue';
import { Link, router, Head, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { FileText, Search } from 'lucide-vue-next';

interface Reports {
  data: any[];
  links: any[];
  from?: number;
  to?: number;
  total?: number;
}

interface Props {
  reports: Reports;
  filters: Record<string, any>;
}

const props = defineProps<Props>();
const page = usePage();

// Toast message state
const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');

const localFilters = reactive({ ...props.filters });
let searchTimeout: number | null = null;

const applyFilters = () => {
  router.get('/candidate/reports', localFilters, { 
    preserveState: false,  // Changed to false so redirect works
    preserveScroll: true 
  });
};

const debounceSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => applyFilters(), 500);
};

// Define showToast BEFORE using it in watch
const showToast = (message: string, type: 'success' | 'error' = 'success') => {
  toastMessage.value = message;
  toastType.value = type;
  setTimeout(() => {
    toastMessage.value = '';
  }, 5000);
};

// Watch for flash messages (now showToast is already defined)
watch(() => page.props.flash, (flash: any) => {
  if (flash?.success) {
    showToast(flash.success, 'success');
  } else if (flash?.error) {
    showToast(flash.error, 'error');
  }
}, { deep: true, immediate: true });

const formatDate = (date: string) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('vi-VN', { year: 'numeric', month: 'long', day: 'numeric' });
};

const getStatusVariant = (
  status: string
): "default" | "secondary" | "outline" | "destructive" => {
  const map: Record<string, "default" | "secondary" | "outline" | "destructive"> = {
    pending: "secondary",
    reviewing: "outline",
    resolved: "default",
    dismissed: "destructive"
  };
  return map[status] ?? "secondary";
};
</script>
<template>
  <CandidateLayout>
    <Head title="Theo dõi báo cáo vi phạm" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <!-- Toast Notification -->
        <div
          v-if="toastMessage"
          :class="[
            'fixed top-4 right-4 z-50 max-w-md rounded-lg p-4 shadow-lg transition-all',
            toastType === 'error' ? 'bg-red-50 border border-red-200 text-red-800' : 'bg-green-50 border border-green-200 text-green-800'
          ]"
        >
          <div class="flex items-center gap-2">
            <svg v-if="toastType === 'error'" class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <svg v-else class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="font-medium">{{ toastMessage }}</p>
          </div>
        </div>

        <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 shadow-xl">
          <div class="relative px-8 py-10 sm:px-12 flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-white sm:text-4xl">Theo dõi báo cáo vi phạm</h1>
              <p class="mt-3 text-lg text-blue-100">Xem lại các báo cáo bạn đã gửi và tình trạng xử lý</p>
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
                  :value="localFilters.status"
                  @change="(e) => { localFilters.status = e.target.value; applyFilters(); }"
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
                    placeholder="Tìm báo cáo..."
                    class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  />
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Danh sách báo cáo</CardTitle>
          </CardHeader>
          <CardContent class="p-0">
            <div v-if="props.reports.data.length === 0" class="p-12 text-center">
              <FileText class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-4 text-sm font-medium text-gray-900">Không tìm thấy báo cáo</h3>
              <p class="mt-2 text-sm text-gray-500">Bạn chưa gửi báo cáo nào.</p>
            </div>

            <div v-else class="responsive-table-wrapper">
              <table class="w-full text-sm text-left mobile-card-view">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                  <tr>
                    <th class="px-6 py-3">Tiêu đề</th>
                    <th class="px-6 py-3">Loại vi phạm</th>
                    <th class="px-6 py-3">Trạng thái</th>
                    <th class="px-6 py-3">Thời gian</th>
                    <th class="px-6 py-3 text-right">Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="report in props.reports.data"
                    :key="report.id"
                    class="bg-white border-b hover:bg-gray-50"
                  >
                    <td class="px-6 py-4" data-label="Tiêu đề">{{ report.reportable?.title ?? '(Tin đã bị xóa)' }}</td>
                    <td class="px-6 py-4" data-label="Loại vi phạm">{{ report.type_label }}</td>
                    <td class="px-6 py-4" data-label="Trạng thái">
                      <Badge :variant="getStatusVariant(report.status)">{{ report.status_label }}</Badge>
                    </td>
                    <td class="px-6 py-4" data-label="Thời gian">{{ formatDate(report.created_at) }}</td>
                    <td class="px-6 py-4 text-right" data-label="Hành động">
                      <Link
                        :href="`/candidate/reports/${report.id}`"
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

          <div v-if="props.reports.links.length > 0" class="border-t border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Hiển thị {{ props.reports.from }} đến {{ props.reports.to }} trong tổng số {{ props.reports.total }} báo cáo
              </div>
              <div class="flex space-x-2">
                <Link
                  v-for="link in props.reports.links"
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
  </CandidateLayout>
</template>