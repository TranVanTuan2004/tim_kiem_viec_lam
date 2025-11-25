<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Save } from 'lucide-vue-next';

const form = useForm({
  name: '',
  slug: '',
  description: '',
  price: 0,
  duration_days: 1,
  is_active: true,
  features: '',
});

function submit() {
  form.post('/admin/service-packages', {
    preserveScroll: true,
  });
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý gói dịch vụ', href: '/admin/service-packages' },
  { title: 'Tạo gói mới', href: '/admin/service-packages/create' }
];
</script>

<template>
  <Head title="Tạo gói dịch vụ" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-5">
      <h1 class="text-3xl font-bold">Tạo gói dịch vụ mới</h1>
      <Card>
        <CardHeader>
          <CardTitle>Thông tin gói dịch vụ</CardTitle>
          <CardDescription>Điền thông tin chi tiết cho gói dịch vụ</CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="space-y-2">
            <Label for="name">Tên gói <span class="text-red-500">*</span></Label>
            <Input id="name" v-model="form.name" placeholder="Nhập tên gói" />
            <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
          </div>

          <div class="space-y-2">
            <Label for="slug">Slug</Label>
            <Input id="slug" v-model="form.slug" placeholder="Nhập slug (tùy chọn)" />
            <p v-if="form.errors.slug" class="text-sm text-red-500">{{ form.errors.slug }}</p>
          </div>

          <div class="space-y-2">
            <Label for="description">Mô tả</Label>
            <Textarea id="description" v-model="form.description" placeholder="Mô tả gói dịch vụ" />
            <p v-if="form.errors.description" class="text-sm text-red-500">{{ form.errors.description }}</p>
          </div>

          <div class="grid md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="price">Giá <span class="text-red-500">*</span></Label>
              <Input id="price" v-model="form.price" type="number" placeholder="Nhập giá" />
              <p v-if="form.errors.price" class="text-sm text-red-500">{{ form.errors.price }}</p>
            </div>

            <div class="space-y-2">
              <Label for="duration_days">Số ngày sử dụng <span class="text-red-500">*</span></Label>
              <Input id="duration_days" v-model="form.duration_days" type="number" placeholder="Số ngày" />
              <p v-if="form.errors.duration_days" class="text-sm text-red-500">{{ form.errors.duration_days }}</p>
            </div>
          </div>

          <div class="space-y-2">
            <Label for="features">Tính năng (mô tả chi tiết)</Label>
            <Textarea id="features" v-model="form.features" placeholder="Liệt kê các tính năng" />
            <p v-if="form.errors.features" class="text-sm text-red-500">{{ form.errors.features }}</p>
          </div>

          <div class="flex items-center gap-2">
            <input type="checkbox" id="is_active" v-model="form.is_active" class="h-4 w-4 rounded border-gray-300" />
            <Label for="is_active">Kích hoạt gói dịch vụ</Label>
          </div>

          <Button type="button" @click="submit" :disabled="form.processing">
            <Save class="h-4 w-4 mr-2" />
            {{ form.processing ? 'Đang lưu...' : 'Tạo gói' }}
          </Button>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
