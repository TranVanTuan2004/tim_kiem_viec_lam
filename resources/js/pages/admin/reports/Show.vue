<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

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

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý báo cáo', href: '/admin/reports' },
  { title: 'Chi tiết báo cáo', href: '#' }
];
</script>

<template>
  <Head title="Chi tiết báo cáo" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-[20px]">

      <div>
        <h1 class="text-3xl font-bold">Chi tiết báo cáo</h1>
        <p class="text-muted-foreground mt-1">Xem và cập nhật trạng thái báo cáo</p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Thông tin báo cáo</CardTitle>
        </CardHeader>

        <CardContent class="space-y-4">
          <p><strong>Người báo cáo:</strong> {{ props.report.user.name }} ({{ props.report.user.email }})</p>

          <p><strong>Đối tượng:</strong> {{ props.report.reportable_type_label }}</p>

          <p>
            <strong>Tiêu đề:</strong>
            <span v-if="props.report.reportable">
              {{ props.report.reportable.title }}
            </span>
            <span v-else class="text-red-500">Đã bị xoá</span>
          </p>

          <p><strong>Lý do:</strong> {{ props.report.reason_label }}</p>
          <p><strong>Trạng thái:</strong> {{ props.report.status_label }}</p>
          <p><strong>Ngày báo cáo:</strong> {{ props.report.created_at }}</p>

          <form @submit.prevent="updateReport" class="mt-4 space-y-4">
            
            <div>
              <label class="font-medium block mb-1">Trạng thái</label>
              <select v-model="form.status" class="border px-3 py-2 rounded w-60">
                <option value="pending">Chờ xử lý</option>
                <option value="reviewing">Đang xem xét</option>
                <option value="resolved">Đã xử lý</option>
                <option value="dismissed">Đã bỏ qua</option>
              </select>
            </div>

            <div>
              <label class="font-medium block mb-1">Ghi chú admin</label>
              <textarea
                v-model="form.admin_notes"
                class="border px-3 py-2 rounded w-full h-28"
              ></textarea>
            </div>

            <div v-if="successMessage" class="rounded-md bg-green-50 p-3 mb-4">
            <p class="text-sm text-green-800">{{ successMessage }}</p>
            </div>

            <div class="flex gap-2 mt-4">
                <Button type="submit">Cập nhật</Button>
                <Button 
                    type="button" 
                    variant="destructive" 
                    @click="router.get('/admin/reports')"
                >
                    Thoát
                </Button>
            </div>
          </form>

        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
