<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, Edit, Star } from 'lucide-vue-next';

interface Props {
  package: {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    duration_days: number;
    is_active: boolean;
    features: string | string[] | null;
    created_at: string;
  }
}

const props = defineProps<Props>();

// Chuyển features về array an toàn
const normalizeFeatures = (features: string | string[] | null) => {
  if (!features) return [];
  if (Array.isArray(features)) return features;
  try {
    const parsed = JSON.parse(features);
    if (Array.isArray(parsed)) return parsed;
  } catch (e) {}
  return (features as string).split(',').map(f => f.trim());
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý gói dịch vụ', href: '/admin/service-packages' },
  { title: props.package.name, href: `/admin/service-packages/${props.package.slug}` },
];
</script>

<template>
  <Head title="Chi tiết gói dịch vụ" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-5">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">{{ props.package.name }}</h1>
        <div class="flex gap-2">
          <Link href="/admin/service-packages">
            <Button variant="outline">
              <ArrowLeft class="h-4 w-4 mr-2"/> Quay lại
            </Button>
          </Link>
          <Link :href="`/admin/service-packages/${props.package.slug}/edit`">
            <Button variant="secondary">
              <Edit class="h-4 w-4 mr-2"/> Chỉnh sửa
            </Button>
          </Link>
        </div>
      </div>

      <!-- Info Cards -->
      <div class="grid md:grid-cols-2 gap-6">
        <!-- Thông tin cơ bản -->
        <Card class="shadow-md hover:shadow-lg transition-all rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100">
          <CardHeader>
            <CardTitle>Thông tin cơ bản</CardTitle>
            <CardDescription>Chi tiết gói dịch vụ</CardDescription>
          </CardHeader>
          <CardContent class="space-y-3 text-gray-700">
            <div class="flex justify-between"><span class="font-semibold">Tên gói:</span><span class="text-indigo-600 font-semibold">{{ props.package.name }}</span></div>
            <div class="flex justify-between"><span class="font-semibold">Slug:</span><span class="text-gray-600">{{ props.package.slug || '-' }}</span></div>
            <div class="flex justify-between"><span class="font-semibold">Mô tả:</span><span class="text-gray-700">{{ props.package.description || '-' }}</span></div>
            <div class="flex justify-between items-center gap-2">
              <span class="font-semibold">Giá:</span>
              <span class="text-indigo-700 font-bold text-lg">{{ props.package.price.toLocaleString() }} VNĐ</span>
            </div>
            <div class="flex justify-between"><span class="font-semibold">Số ngày:</span><span>{{ props.package.duration_days }}</span></div>
            <div class="flex justify-between items-center">
              <span class="font-semibold">Trạng thái:</span>
              <Badge :class="props.package.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                {{ props.package.is_active ? 'Hoạt động' : 'Đã tắt' }}
              </Badge>
            </div>
            <div class="flex justify-between"><span class="font-semibold">Ngày tạo:</span><span class="text-gray-500">{{ props.package.created_at }}</span></div>
          </CardContent>
        </Card>

        <!-- Tính năng -->
        <Card class="shadow-md hover:shadow-lg transition-all rounded-xl bg-gradient-to-br from-yellow-50 to-yellow-100">
          <CardHeader>
            <CardTitle>Tính năng</CardTitle>
            <CardDescription>Những điểm nổi bật của gói dịch vụ</CardDescription>
          </CardHeader>
          <CardContent>
            <ul class="space-y-2">
              <li v-for="(feat, idx) in normalizeFeatures(props.package.features)" :key="idx"
                  class="flex items-center gap-2 p-2 rounded-lg hover:bg-yellow-200 transition-all">
                <Star class="h-4 w-4 text-yellow-400"/>
                <span class="text-gray-700">{{ feat }}</span>
              </li>
              <li v-if="normalizeFeatures(props.package.features).length === 0" class="text-gray-400 italic">Chưa có tính năng nào</li>
            </ul>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
