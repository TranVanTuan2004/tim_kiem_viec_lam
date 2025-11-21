<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { 
  Image as ImageIcon, 
  Upload,
  ArrowLeft,
  Save
} from 'lucide-vue-next';

const form = useForm({
  title: '',
  description: '',
  image: null as File | null,
  link_url: '',
  button_text: '',
  type: 'hero',
  is_active: true,
  start_date: '',
  end_date: '',
});

const imagePreview = ref<string | null>(null);

function handleImageChange(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  
  if (file) {
    form.image = file;
    
    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
}

function submit() {
  form.post('/admin/banners', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      imagePreview.value = null;
    },
  });
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý Banner', href: '/admin/banners' },
  { title: 'Tạo Banner Mới', href: '/admin/banners/create' }
];
</script>

<template>
  <Head title="Tạo Banner Mới" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-[20px]">
      
      <!-- Page Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <ImageIcon class="h-6 w-6 text-primary" />
            </div>
            Tạo Banner Mới
          </h1>
          <p class="text-muted-foreground mt-2 ml-[52px]">
            Thêm banner mới cho trang chủ
          </p>
        </div>
        <Button variant="outline" @click="router.visit('/admin/banners')">
          <ArrowLeft class="h-4 w-4 mr-2" />
          Quay lại
        </Button>
      </div>

      <form @submit.prevent="submit">
        <div class="grid gap-6 lg:grid-cols-3">
          <!-- Main Form -->
          <div class="lg:col-span-2 space-y-6">
            <Card>
              <CardHeader>
                <CardTitle>Thông tin cơ bản</CardTitle>
                <CardDescription>Nhập thông tin chi tiết cho banner</CardDescription>
              </CardHeader>
              <CardContent class="space-y-4">
                <div class="space-y-2">
                  <Label for="title">Tiêu đề <span class="text-red-500">*</span></Label>
                  <Input
                    id="title"
                    v-model="form.title"
                    placeholder="Nhập tiêu đề banner"
                    :class="{ 'border-red-500': form.errors.title }"
                  />
                  <p v-if="form.errors.title" class="text-sm text-red-500">{{ form.errors.title }}</p>
                </div>

                <div class="space-y-2">
                  <Label for="description">Mô tả</Label>
                  <Textarea
                  id="description"
                  v-model="form.description"
                  :rows="4"
                  placeholder="Nhập mô tả cho banner..."
                  :class="form.errors.description ? 'border-red-500' : ''"
                />
                  <p v-if="form.errors.description" class="text-sm text-red-500">{{ form.errors.description }}</p>
                </div>

                <div class="space-y-2">
                  <Label for="type">Loại Banner <span class="text-red-500">*</span></Label>
                  <select v-model="form.type" id="type" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    <option value="hero">Hero Banner</option>
                    <option value="promotional">Khuyến mãi</option>
                    <option value="announcement">Thông báo</option>
                  </select>
                  <p v-if="form.errors.type" class="text-sm text-red-500">{{ form.errors.type }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <Label for="link_url">Link URL</Label>
                    <Input
                      id="link_url"
                      v-model="form.link_url"
                      type="url"
                      placeholder="https://example.com"
                      :class="{ 'border-red-500': form.errors.link_url }"
                    />
                    <p v-if="form.errors.link_url" class="text-sm text-red-500">{{ form.errors.link_url }}</p>
                  </div>

                  <div class="space-y-2">
                    <Label for="button_text">Văn bản nút</Label>
                    <Input
                      id="button_text"
                      v-model="form.button_text"
                      placeholder="Xem thêm"
                      :class="{ 'border-red-500': form.errors.button_text }"
                    />
                    <p v-if="form.errors.button_text" class="text-sm text-red-500">{{ form.errors.button_text }}</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Hình ảnh Banner</CardTitle>
                <CardDescription>Upload hình ảnh cho banner (tối đa 2MB)</CardDescription>
              </CardHeader>
              <CardContent class="space-y-4">
                <div class="space-y-2">
                  <Label for="image">Hình ảnh <span class="text-red-500">*</span></Label>
                  <div class="flex items-center gap-4">
                    <Input
                      id="image"
                      type="file"
                      accept="image/*"
                      @change="handleImageChange"
                      :class="{ 'border-red-500': form.errors.image }"
                    />
                    <Upload class="h-5 w-5 text-muted-foreground" />
                  </div>
                  <p v-if="form.errors.image" class="text-sm text-red-500">{{ form.errors.image }}</p>
                </div>

                <!-- Image Preview -->
                <div v-if="imagePreview" class="mt-4">
                  <Label>Xem trước</Label>
                  <div class="mt-2 border rounded-lg overflow-hidden">
                    <img :src="imagePreview" alt="Preview" class="w-full h-auto" />
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle>Cài đặt</CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <div class="flex items-center gap-2">
                  <input
                    id="is_active"
                    type="checkbox"
                    v-model="form.is_active"
                    class="h-4 w-4 rounded border-gray-300"
                  />
                  <Label for="is_active">Kích hoạt ngay</Label>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Lịch hiển thị</CardTitle>
                <CardDescription>Tùy chọn thời gian hiển thị banner</CardDescription>
              </CardHeader>
              <CardContent class="space-y-4">
                <div class="space-y-2">
                  <Label for="start_date">Ngày bắt đầu</Label>
                  <Input
                    id="start_date"
                    v-model="form.start_date"
                    type="datetime-local"
                  />
                </div>

                <div class="space-y-2">
                  <Label for="end_date">Ngày kết thúc</Label>
                  <Input
                    id="end_date"
                    v-model="form.end_date"
                    type="datetime-local"
                  />
                  <p v-if="form.errors.end_date" class="text-sm text-red-500">{{ form.errors.end_date }}</p>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardContent class="pt-6">
                <Button 
                  type="submit" 
                  class="w-full"
                  :disabled="form.processing"
                >
                  <Save class="h-4 w-4 mr-2" />
                  {{ form.processing ? 'Đang lưu...' : 'Tạo Banner' }}
                </Button>
              </CardContent>
            </Card>
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
