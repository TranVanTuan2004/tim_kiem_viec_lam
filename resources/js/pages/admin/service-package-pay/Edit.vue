<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';

const props = defineProps({
  package: Object
});

// Form chuẩn Inertia
const form = useForm({
  name: props.package.name,
  description: props.package.description,
  price: props.package.price,
  duration_days: props.package.duration_days,
  max_jobs: props.package.max_jobs,
  features: props.package.features.join(', '), // nhập dạng string
});

// Flash message
const flash = usePage().props.flash ?? {};

const submit = () => {
  form.transform((data) => ({
    ...data,
    features: data.features.split(',').map(f => f.trim()), // convert sang array
  }))
  .put(route('admin.service-packages.update', props.package.id));
};
</script>

<template>
  <Head title="Chỉnh sửa gói dịch vụ" />
  <AppLayout>
    <Card>
      <CardHeader>
        <CardTitle>Chỉnh sửa gói dịch vụ</CardTitle>
      </CardHeader>

      <CardContent>

        <!-- Thông báo -->
        <div v-if="flash?.success" class="bg-green-100 text-green-800 p-2 rounded mb-2">
          {{ flash.success }}
        </div>

        <!-- Form -->
        <div class="space-y-4">

          <div>
            <label class="font-semibold">Tên gói</label>
            <Input v-model="form.name" />
            <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
          </div>

          <div>
            <label class="font-semibold">Mô tả</label>
            <Textarea v-model="form.description" />
            <div v-if="form.errors.description" class="text-red-500 text-sm">{{ form.errors.description }}</div>
          </div>

          <div>
            <label class="font-semibold">Giá (VNĐ)</label>
            <Input v-model="form.price" type="number" />
            <div v-if="form.errors.price" class="text-red-500 text-sm">{{ form.errors.price }}</div>
          </div>

          <div>
            <label class="font-semibold">Thời gian (ngày)</label>
            <Input v-model="form.duration_days" type="number" />
            <div v-if="form.errors.duration_days" class="text-red-500 text-sm">
              {{ form.errors.duration_days }}
            </div>
          </div>

          <div>
            <label class="font-semibold">Tối đa bài đăng</label>
            <Input v-model="form.max_jobs" type="number" />
            <div v-if="form.errors.max_jobs" class="text-red-500 text-sm">{{ form.errors.max_jobs }}</div>
          </div>

          <div>
            <label class="font-semibold">Tính năng (ngăn cách bởi dấu , )</label>
            <Input v-model="form.features" placeholder="Feature1, Feature2" />
            <div v-if="form.errors.features" class="text-red-500 text-sm">{{ form.errors.features }}</div>
          </div>

          <div class="flex space-x-2">
            <Button :disabled="form.processing" @click="submit">Lưu</Button>

            <Button variant="outline"
              @click="$inertia.get(route('admin.service-packages.index'))"
            >
              Hủy
            </Button>
          </div>

        </div>
      </CardContent>
    </Card>
  </AppLayout>
</template>
