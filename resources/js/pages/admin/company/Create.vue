<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

// Props interface
interface PageProps {
  users: { id: number; name: string }[];
}

// Lấy users từ props Inertia
const page = usePage<PageProps>();
const users = page.props.users;

// Form
const form = useForm({
  company_name: '',
  province: '',
  city: '',
  user_id: null,
  description: '',
  industry: '',
  is_active: true,
});

// Submit
function submit() {
  form.post('/admin/companies');
}

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý Công ty', href: '/admin/companies' },
  { title: 'Tạo công ty', href: '/admin/companies/create' },
];
</script>

<template>
  <Head title="Tạo công ty" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-5 bg-indigo-50 p-6 rounded-xl">
      <!-- Header -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 md:gap-0">
        <h1 class="text-3xl font-bold text-gray-800">Tạo công ty mới</h1>
        <Button variant="outline" class="flex items-center gap-2" @click="router.visit('/admin/companies')">
          <ArrowLeft class="h-4 w-4"/> Quay lại
        </Button>
      </div>


  <div class="grid lg:grid-cols-3 gap-6">
    <!-- Form Card -->
    <div class="lg:col-span-2 space-y-6">
      <Card class="shadow-lg rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200">
        <CardHeader>
          <CardTitle class="text-indigo-700">Thông tin công ty</CardTitle>
          <CardDescription>Điền thông tin chi tiết cho công ty</CardDescription>
        </CardHeader>
        <CardContent class="space-y-4 text-gray-700">
          <!-- Tên công ty -->
          <div class="space-y-2">
            <Label for="company_name">Tên công ty <span class="text-red-500">*</span></Label>
            <Input id="company_name" v-model="form.company_name" placeholder="Nhập tên công ty" :class="{ 'border-red-500': form.errors.company_name }" class="border-indigo-300 bg-indigo-50"/>
            <p v-if="form.errors.company_name" class="text-sm text-red-500">{{ form.errors.company_name }}</p>
          </div>

          <!-- Tỉnh / Quận -->
          <div class="grid lg:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="province">Tỉnh/Thành phố <span class="text-red-500">*</span></Label>
              <Input id="province" v-model="form.province" placeholder="Nhập tỉnh/TP" :class="{ 'border-red-500': form.errors.province }" class="border-indigo-300 bg-indigo-50"/>
              <p v-if="form.errors.province" class="text-sm text-red-500">{{ form.errors.province }}</p>
            </div>
            <div class="space-y-2">
              <Label for="city">Quận/Huyện <span class="text-red-500">*</span></Label>
              <Input id="city" v-model="form.city" placeholder="Nhập quận/huyện" :class="{ 'border-red-500': form.errors.city }" class="border-indigo-300 bg-indigo-50"/>
              <p v-if="form.errors.city" class="text-sm text-red-500">{{ form.errors.city }}</p>
            </div>
          </div>

          <!-- Người quản lý -->
          <div class="space-y-2">
            <Label for="user_id">Người quản lý <span class="text-red-500">*</span></Label>
            <select id="user_id" v-model="form.user_id" class="border border-indigo-300 bg-indigo-50 rounded-md w-full h-10 px-3 py-2">
              <option :value="null">Chọn người quản lý</option>
              <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
            </select>
            <p v-if="form.errors.user_id" class="text-sm text-red-500">{{ form.errors.user_id }}</p>
          </div>

          <!-- Mô tả -->
          <div class="space-y-2">
            <Label for="description">Mô tả <span class="text-red-500">*</span></Label>
            <Textarea id="description" v-model="form.description" placeholder="Mô tả công ty" class="border-indigo-300 bg-indigo-50"/>
            <p v-if="form.errors.description" class="text-sm text-red-500">{{ form.errors.description }}</p>
          </div>

          <!-- Ngành nghề -->
          <div class="space-y-2">
            <Label for="industry">Ngành nghề <span class="text-red-500">*</span></Label>
            <Input id="industry" v-model="form.industry" placeholder="Ngành nghề" class="border-indigo-300 bg-indigo-50"/>
            <p v-if="form.errors.industry" class="text-sm text-red-500">{{ form.errors.industry }}</p>
          </div>

          <!-- Kích hoạt -->
          <div class="flex items-center gap-2">
            <input type="checkbox" id="is_active" v-model="form.is_active" class="h-4 w-4 rounded border-indigo-300 bg-indigo-50"/>
            <Label for="is_active">Kích hoạt công ty</Label>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Submit Card -->
    <div class="space-y-6">
      <Card class="shadow-lg rounded-xl bg-gradient-to-br from-pink-50 to-pink-100 border border-pink-200 flex flex-col justify-center items-center p-6 h-full">
        <CardContent class="w-full">
          <Button type="submit" class="w-full bg-pink-400 hover:bg-pink-500 text-white flex items-center justify-center gap-2" @click="submit" :disabled="form.processing">
            <Save class="h-4 w-4"/> {{ form.processing ? 'Đang lưu...' : 'Tạo công ty' }}
          </Button>
        </CardContent>
      </Card>
    </div>
  </div>
</div>

  </AppLayout>
</template>