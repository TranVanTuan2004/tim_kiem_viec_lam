<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Save, ArrowLeft } from 'lucide-vue-next';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

interface Props {
  package: {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    duration_days: number;
    is_active: boolean;
    features: string | null;
  }
}

const props = defineProps<Props>();

const form = useForm({
  name: props.package.name,
  slug: props.package.slug,
  description: props.package.description || '',
  price: props.package.price,
  duration_days: props.package.duration_days,
  features: props.package.features || '',
  is_active: props.package.is_active,
});

function submit() {
  form.put(`/admin/service-packages/${props.package.slug}`);
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý gói dịch vụ', href: '/admin/service-packages' },
  { title: 'Chỉnh sửa gói', href: `/admin/service-packages/${props.package.slug}/edit` },
];
</script>

<template>
  <Head title="Chỉnh sửa gói dịch vụ" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-5">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Chỉnh sửa gói dịch vụ</h1>
        <Button variant="outline" @click="router.visit('/admin/service-packages')">
          <ArrowLeft class="h-4 w-4 mr-2"/> Quay lại
        </Button>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Card Form -->
        <Card class="shadow-lg rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 border border-gray-200">
          <CardHeader>
            <CardTitle>Thông tin gói</CardTitle>
            <CardDescription>Điền thông tin chi tiết cho gói dịch vụ</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4 text-gray-700">
            <div class="grid lg:grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label for="name">Tên gói <span class="text-red-500">*</span></Label>
                <Input
                  id="name"
                  v-model="form.name"
                  placeholder="Nhập tên gói"
                  class="bg-white"
                  :class="{ 'border-red-500': form.errors.name }"
                />
                <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
              </div>

              <div class="space-y-2">
                <Label for="slug">Slug (tùy chọn)</Label>
                <Input id="slug" v-model="form.slug" placeholder="Nhập slug" class="bg-white"/>
                <p v-if="form.errors.slug" class="text-sm text-red-500">{{ form.errors.slug }}</p>
              </div>
            </div>

            <div class="space-y-2">
              <Label for="description">Mô tả <span class="text-red-500">*</span></Label>
              <Textarea id="description" v-model="form.description" placeholder="Mô tả gói dịch vụ" class="bg-white"/>
              <p v-if="form.errors.description" class="text-sm text-red-500">{{ form.errors.description }}</p>
            </div>

            <div class="grid lg:grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label for="price">Giá (VNĐ) <span class="text-red-500">*</span></Label>
                <Input
                  id="price"
                  v-model="form.price"
                  type="number"
                  placeholder="Nhập giá"
                  class="bg-white"
                  :class="{ 'border-red-500': form.errors.price }"
                />
                <p v-if="form.errors.price" class="text-sm text-red-500">{{ form.errors.price }}</p>
              </div>

              <div class="space-y-2">
                <Label for="duration_days">Số ngày sử dụng <span class="text-red-500">*</span></Label>
                <Input
                  id="duration_days"
                  v-model="form.duration_days"
                  type="number"
                  placeholder="Số ngày"
                  class="bg-white"
                  :class="{ 'border-red-500': form.errors.duration_days }"
                />
                <p v-if="form.errors.duration_days" class="text-sm text-red-500">{{ form.errors.duration_days }}</p>
              </div>
            </div>

            <div class="space-y-2">
              <Label for="features">Tính năng (text hoặc JSON)<span class="text-red-500">*</span></Label>
              <Textarea
                id="features"
                v-model="form.features"
                placeholder='["feature1","feature2"] hoặc "text"...'
                class="bg-white"
              />
              <p v-if="form.errors.features" class="text-sm text-red-500">{{ form.errors.features }}</p>
            </div>

            <div class="flex items-center gap-2">
              <input type="checkbox" id="is_active" v-model="form.is_active" class="h-4 w-4 rounded border-gray-300"/>
              <Label for="is_active">Kích hoạt gói dịch vụ</Label>
            </div>
          </CardContent>
        </Card>

        <!-- Submit Button -->
        <div class="flex justify-end">
          <Button
            type="submit"
            class="flex items-center gap-2 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all"
            :disabled="form.processing"
          >
            <Save class="h-5 w-5"/> {{ form.processing ? 'Đang lưu...' : 'Cập nhật gói' }}
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

