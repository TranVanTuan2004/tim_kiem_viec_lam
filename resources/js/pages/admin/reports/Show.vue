<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';

const successMessage = ref('');

interface ReportUser {
  name: string;
  email: string;
}

interface Reportable {
  title: string;
  slug: string;
}

interface Report {
  id: number;
  user: ReportUser;
  status: string;
  status_label: string;
  reason_label: string;
  description?: string;
  admin_notes?: string;
  created_at: string;
  reportable?: Reportable;
  reportable_type: string;
  reportable_type_label: string;
}

const props = defineProps<{
  report: Report;
}>();

const form = ref({
  status: props.report?.status ?? 'pending',
  admin_notes: props.report?.admin_notes ?? '',
});

const updateReport = () => {
  router.patch(`/admin/reports/${props.report.id}`, form.value, {
    preserveScroll: true,
    onSuccess: () => {
      successMessage.value = 'Cập nhật trạng thái báo cáo thành công!';
      setTimeout(() => {
        successMessage.value = '';
      }, 3000);
    }
  });
};

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
  { title: 'Quản lý báo cáo', href: '/admin/reports' },
  { title: `Báo cáo #${props.report.id}`, href: '#' }
];
</script>

<template>
  <Head :title="`Chi tiết báo cáo #${report.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <Button variant="outline" size="sm" @click="router.get('/admin/reports')">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Quay lại
          </Button>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Báo cáo #{{ report.id }}</h1>
            <p class="text-sm text-gray-500 mt-1">Chi tiết và xử lý báo cáo vi phạm</p>
          </div>
        </div>
        <Badge :class="statusColor(report.status)" class="text-base px-4 py-2">
          {{ report.status_label }}
        </Badge>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="rounded-lg bg-green-50 border border-green-200 p-4 flex items-center gap-3">
        <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
      </div>

      <div class="grid gap-6 lg:grid-cols-3">
        <!-- Main Content - Left Side -->
        <div class="lg:col-span-2 space-y-6">
          
          <!-- Report Info Card -->
          <Card>
            <CardHeader class="border-b bg-gray-50/50">
              <CardTitle class="flex items-center gap-2">
                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Thông tin báo cáo
              </CardTitle>
            </CardHeader>
            <CardContent class="p-6 space-y-6">
              
              <!-- Reported Object -->
              <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border">
                <span class="text-4xl">{{ getTypeIcon(report.reportable_type) }}</span>
                <div class="flex-1">
                  <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Đối tượng bị báo cáo</p>
                  <p class="text-lg font-semibold text-gray-900">
                    <Link v-if="report.reportable" :href="report.reportable_type === 'App\\Models\\JobPosting' ? `/jobs/${report.reportable.slug}` : `/companies/${report.reportable.slug}`" class="hover:text-blue-600 hover:underline">
                      {{ report.reportable.title }}
                    </Link>
                    <span v-else class="text-red-500 italic">Đã bị xóa</span>
                  </p>
                  <p class="text-sm text-gray-600 mt-1">{{ report.reportable_type_label }}</p>
                </div>
              </div>

              <!-- Reporter Info -->
              <div class="grid md:grid-cols-2 gap-4">
                <div class="p-4 border rounded-lg">
                  <div class="flex items-center gap-2 mb-2">
                    <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs font-medium text-gray-500 uppercase">Người báo cáo</p>
                  </div>
                  <p class="font-semibold text-gray-900">{{ report.user.name }}</p>
                  <p class="text-sm text-gray-600">{{ report.user.email }}</p>
                </div>

                <div class="p-4 border rounded-lg">
                  <div class="flex items-center gap-2 mb-2">
                    <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs font-medium text-gray-500 uppercase">Ngày báo cáo</p>
                  </div>
                  <p class="font-semibold text-gray-900">{{ new Date(report.created_at).toLocaleDateString('vi-VN', { year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
                  <p class="text-sm text-gray-600">{{ new Date(report.created_at).toLocaleTimeString('vi-VN') }}</p>
                </div>
              </div>

              <!-- Reason -->
              <div class="p-4 border-l-4 border-l-red-500 bg-red-50 rounded">
                <div class="flex items-center gap-2 mb-2">
                  <svg class="h-4 w-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  <p class="text-xs font-semibold text-red-900 uppercase">Lý do vi phạm</p>
                </div>
                <p class="text-base font-medium text-red-900">{{ report.reason_label }}</p>
                <p v-if="report.description" class="text-sm text-red-800 mt-2">{{ report.description }}</p>
              </div>

            </CardContent>
          </Card>

          <!-- Admin Notes (if exists) -->
          <Card v-if="report.admin_notes">
            <CardHeader class="border-b bg-blue-50/50">
              <CardTitle class="flex items-center gap-2 text-blue-900">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                </svg>
                Ghi chú từ Admin
              </CardTitle>
            </CardHeader>
            <CardContent class="p-6">
              <p class="text-gray-700 whitespace-pre-wrap">{{ report.admin_notes }}</p>
            </CardContent>
          </Card>

        </div>

        <!-- Action Panel - Right Side -->
        <div class="space-y-6">
          
          <!-- Update Status Card -->
          <Card class="sticky top-6">
            <CardHeader class="border-b bg-gradient-to-r from-blue-50 to-indigo-50">
              <CardTitle class="flex items-center gap-2 text-blue-900">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                </svg>
                Xử lý báo cáo
              </CardTitle>
            </CardHeader>
            <CardContent class="p-6">
              <form @submit.prevent="updateReport" class="space-y-4">
                
                <!-- Status Select -->
                <div>
                  <label class="text-sm font-semibold text-gray-700 mb-2 block">Trạng thái</label>
                  <select v-model="form.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="pending">⏳ Chờ xử lý</option>
                    <option value="reviewing">👀 Đang xem xét</option>
                    <option value="resolved">✅ Đã xử lý</option>
                    <option value="dismissed">❌ Đã bỏ qua</option>
                  </select>
                </div>

                <!-- Admin Notes -->
                <div>
                  <label class="text-sm font-semibold text-gray-700 mb-2 block">Ghi chú xử lý</label>
                  <Textarea
                    v-model="form.admin_notes"
                    placeholder="Nhập ghi chú về cách xử lý báo cáo này..."
                    class="min-h-[120px]"
                  />
                  <p class="text-xs text-gray-500 mt-1">Ghi chú này sẽ được lưu lại để tham khảo sau</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-2 pt-2">
                  <Button type="submit" class="w-full">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Cập nhật báo cáo
                  </Button>
                  <Button type="button" variant="outline" class="w-full" @click="router.get('/admin/reports')">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Hủy
                  </Button>
                </div>
              </form>
            </CardContent>
          </Card>

          <!-- Quick Actions -->
          <Card>
            <CardHeader class="border-b bg-gray-50/50">
              <CardTitle class="text-sm">Thao tác nhanh</CardTitle>
            </CardHeader>
            <CardContent class="p-4 space-y-2">
              <Button variant="outline" size="sm" class="w-full justify-start" v-if="report.reportable" @click="window.open(report.reportable_type === 'App\\Models\\JobPosting' ? `/jobs/${report.reportable.slug}` : `/companies/${report.reportable.slug}`, '_blank')">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Xem đối tượng
              </Button>
              <Button variant="outline" size="sm" class="w-full justify-start text-red-600 hover:text-red-700 hover:bg-red-50" @click="router.delete(`/admin/reports/${report.id}`)">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Xóa báo cáo
              </Button>
            </CardContent>
          </Card>

        </div>
      </div>

    </div>
  </AppLayout>
</template>
