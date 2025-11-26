<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface Report {
  candidate: { name: string; email: string };
  reason: string;
  admin_notes?: string;
  status: string;
  status_label: string;
  created_at: string;
}

const props = defineProps<{ report: Report }>();
const report = props.report;

const formatDate = (date: string) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('vi-VN', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const getStatusVariant = (status: string) => {
  const map: Record<string, 'default' | 'secondary' | 'outline' | 'destructive'> = {
    pending: 'secondary',
    reviewing: 'outline',
    resolved: 'default',
    dismissed: 'destructive',
  };
  return map[status] ?? 'secondary';
};
</script>

<template>
  <AppLayout>
    <Head title="Chi tiết báo cáo" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 shadow-xl">
          <div class="relative px-8 py-10 sm:px-12">
            <h1 class="text-3xl font-bold text-white sm:text-4xl">Chi tiết báo cáo</h1>
            <p class="mt-3 text-lg text-blue-100">Thông tin chi tiết của báo cáo này</p>
          </div>
        </div>

        <Card class="mb-6">
          <CardHeader>
            <CardTitle>Trạng thái</CardTitle>
          </CardHeader>
          <CardContent>
            <Badge :variant="getStatusVariant(report.status)" class="text-lg px-4 py-2">
              {{ report.status_label }}
            </Badge>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Thông tin báo cáo</CardTitle>
          </CardHeader>

          <CardContent class="space-y-4">

            <div>
              <strong>Ứng viên:</strong>
              <p class="text-gray-700 mt-1">{{ report.candidate.name }} — {{ report.candidate.email }}</p>
            </div>

            <div>
              <strong>Lý do:</strong>
              <p class="text-gray-700 mt-1 whitespace-pre-line">{{ report.reason }}</p>
            </div>

            <div>
              <strong>Ghi chú từ Admin:</strong>
              <p class="text-gray-700 mt-1">{{ report.admin_notes ?? '—' }}</p>
            </div>

            <div>
              <strong>Ngày tạo:</strong>
              <p class="text-gray-700 mt-1">{{ formatDate(report.created_at) }}</p>
            </div>

          </CardContent>
        </Card>

        <div class="mt-6">
          <Link
            href="/employer/reports"
            class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
          >
            Quay lại
          </Link>
        </div>

      </div>
    </div>
  </AppLayout>
</template>
