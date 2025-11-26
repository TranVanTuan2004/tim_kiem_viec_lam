<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Save, ArrowLeft } from 'lucide-vue-next';

interface ServicePackage {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  price: number;
  duration_days: number;
  is_active: boolean;
  features: string | null;
}

interface Props {
  package: ServicePackage;
}

const props = defineProps<Props>();

const form = useForm({
  name: props.package.name,
  slug: props.package.slug,
  description: props.package.description || '',
  price: props.package.price,
  duration_days: props.package.duration_days,
  is_active: props.package.is_active,
  features: props.package.features || '',
  _method: 'PUT',
});

function submit() {
  router.post(`/admin/service-packages/${props.package.slug}`, form, {
    preserveScroll: true,
    forceFormData: true,
  });
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý Gói dịch vụ', href: '/admin/service-packages' },
  { title: 'Chỉnh sửa gói', href: `/admin/service-packages/${props.package.slug}/edit` }
];
</script>

<template>
  <Head title="Chỉnh sửa gói dịch vụ" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-5">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold">Chỉnh sửa gói dịch vụ</h1>
        </div>
        <Button variant="outline" @click="router.visit('/admin/service-packages')">
          <ArrowLeft class="h-4 w-4 mr-2" /> Quay lại
        </Button>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-2">

        <!-- Main -->
        <div class="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Thông tin cơ bản</CardTitle>
              <CardDescription>Điền thông tin chi tiết gói dịch vụ</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">

              <div class="space-y-2">
                <Label for="name">Tên gói <span class="text-red-500">*</span></Label>
                <Input id="name" v-model="form.name" placeholder="Nhập tên gói" :class="{ 'border-red-500': form.errors.name }" />
                <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
              </div>

              <div class="space-y-2">
                <Label for="slug">Slug</Label>
                <Input id="slug" v-model="form.slug" placeholder="Nhập slug (tự tạo nếu để trống)" />
              </div>

              <div class="space-y-2">
                <Label for="description">Mô tả</Label>
                <Textarea id="description" v-model="form.description" rows="4" placeholder="Mô tả gói dịch vụ..." />
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label for="price">Giá (VNĐ) <span class="text-red-500">*</span></Label>
                  <Input id="price" type="number" v-model="form.price" :class="{ 'border-red-500': form.errors.price }" />
                  <p v-if="form.errors.price" class="text-sm text-red-500">{{ form.errors.price }}</p>
                </div>

                <div class="space-y-2">
                  <Label for="duration_days">Số ngày <span class="text-red-500">*</span></Label>
                  <Input id="duration_days" type="number" v-model="form.duration_days" :class="{ 'border-red-500': form.errors.duration_days }" />
                  <p v-if="form.errors.duration_days" class="text-sm text-red-500">{{ form.errors.duration_days }}</p>
                </div>
              </div>

              <div class="space-y-2">
                <Label for="features">Features (JSON hoặc text)</Label>
                <Textarea id="features" v-model="form.features" rows="3" placeholder='["feature1","feature2"] hoặc "text"...' />
              </div>

            </CardContent>
          </Card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <Card>
            <CardHeader><CardTitle>Cài đặt</CardTitle></CardHeader>
            <CardContent>
              <div class="flex items-center gap-2">
                <input id="is_active" type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded border-gray-300" />
                <Label for="is_active">Hoạt động</Label>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="pt-6">
              <Button type="submit" class="w-full" :disabled="form.processing">
                <Save class="h-4 w-4 mr-2" /> {{ form.processing ? 'Đang lưu...' : 'Cập nhật gói' }}
              </Button>
            </CardContent>
          </Card>
        </div>

      </form>
    </div>
  </AppLayout>
</template>
