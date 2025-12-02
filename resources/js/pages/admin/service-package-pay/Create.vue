<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

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
  });
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý gói dịch vụ', href: '/admin/service-packages' },
  { title: 'Tạo gói mới', href: '/admin/service-packages/create' },
];
</script>

<template>
  <Head title="Tạo gói dịch vụ" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-5 bg-indigo-50 p-6 rounded-xl">
      <!-- Header -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 md:gap-0">
        <h1 class="text-3xl font-bold text-gray-800">Tạo gói dịch vụ mới</h1>
        <Button variant="outline" class="flex items-center gap-2" @click="router.visit('/admin/service-packages')">
          <ArrowLeft class="h-4 w-4"/> Quay lại
        </Button>
      </div>

      <!-- Form + Submit Card -->
      <div class="grid lg:grid-cols-3 gap-6">
        <!-- Form Card: chiếm 2 cột -->
        <div class="lg:col-span-2 space-y-6">
          <Card class="shadow-lg rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200">
            <CardHeader>
              <CardTitle class="text-indigo-700">Thông tin gói</CardTitle>
              <CardDescription>Điền thông tin chi tiết cho gói dịch vụ</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4 text-gray-700">
              <div class="grid lg:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label for="name">Tên gói <span class="text-red-500">*</span></Label>
                  <Input id="name" v-model="form.name" placeholder="Nhập tên gói" :class="{ 'border-red-500': form.errors.name }" class="border-indigo-300 bg-indigo-50"/>
                  <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
                </div>

                <div class="space-y-2">
                  <Label for="slug">Slug (tùy chọn)</Label>
                  <Input id="slug" v-model="form.slug" placeholder="Nhập slug" class="border-indigo-300 bg-indigo-50"/>
                  <p v-if="form.errors.slug" class="text-sm text-red-500">{{ form.errors.slug }}</p>
                </div>
              </div>

              <div class="space-y-2">
                <Label for="description">Mô tả <span class="text-red-500">*</span></Label>
                <Textarea id="description" v-model="form.description" placeholder="Mô tả gói dịch vụ" class="border-indigo-300 bg-indigo-50"/>
                <p v-if="form.errors.description" class="text-sm text-red-500">{{ form.errors.description }}</p>
              </div>

              <div class="grid lg:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label for="price">Giá (VNĐ) <span class="text-red-500">*</span></Label>
                  <Input id="price" v-model="form.price" type="number" placeholder="Nhập giá" :class="{ 'border-red-500': form.errors.price }" class="border-indigo-300 bg-indigo-50"/>
                  <p v-if="form.errors.price" class="text-sm text-red-500">{{ form.errors.price }}</p>
                </div>

                <div class="space-y-2">
                  <Label for="duration_days">Số ngày sử dụng <span class="text-red-500">*</span></Label>
                  <Input id="duration_days" v-model="form.duration_days" type="number" placeholder="Số ngày" :class="{ 'border-red-500': form.errors.duration_days }" class="border-indigo-300 bg-indigo-50"/>
                  <p v-if="form.errors.duration_days" class="text-sm text-red-500">{{ form.errors.duration_days }}</p>
                </div>
              </div>

              <div class="space-y-2">
                <Label for="features">Tính năng (text hoặc JSON)<span class="text-red-500">*</span></Label>
                <Textarea id="features" v-model="form.features" placeholder='["feature1","feature2"] hoặc "text"...' class="border-indigo-300 bg-indigo-50"/>
                <p v-if="form.errors.features" class="text-sm text-red-500">{{ form.errors.features }}</p>
              </div>

              <div class="flex items-center gap-2">
                <input type="checkbox" id="is_active" v-model="form.is_active" class="h-4 w-4 rounded border-indigo-300 bg-indigo-50"/>
                <Label for="is_active">Kích hoạt gói dịch vụ</Label>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Submit Card: chiếm 1 cột -->
        <div class="space-y-6">
          <Card class="shadow-lg rounded-xl bg-gradient-to-br from-pink-50 to-pink-100 border border-pink-200 flex flex-col justify-center items-center p-6 h-full">
            <CardContent class="w-full">
              <Button type="submit" class="w-full bg-pink-400 hover:bg-pink-500 text-white flex items-center justify-center gap-2" @click="submit" :disabled="form.processing">
                <Save class="h-4 w-4"/> {{ form.processing ? 'Đang lưu...' : 'Tạo gói' }}
              </Button>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
