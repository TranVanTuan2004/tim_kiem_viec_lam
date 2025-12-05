<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Form, Head } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import DeleteUser from '@/components/DeleteUser.vue';


const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Cài đặt hồ sơ', href: '#' },
];

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
  <Head title="Cài đặt hồ sơ" />

  <SettingsLayout>
    <div class="flex flex-col space-y-6">

  <!-- Heading -->
  <HeadingSmall
    title="Thông tin hồ sơ"
    description="Cập nhật tên và địa chỉ email của bạn"
  />

  <!-- Form profile -->
  <Form v-bind="ProfileController.update.form()" v-slot="{ errors, processing, recentlySuccessful }" class="space-y-6">
    <div class="grid gap-2">
      <Label for="name">Họ và tên <span class="text-red-500">*</span></Label>
      <Input id="name" name="name" :default-value="user.name" />
      <InputError :message="errors.name" />
    </div>

    <div class="grid gap-2">
      <Label for="email">Email <span class="text-red-500">*</span></Label>
      <Input id="email" name="email" type="email" :default-value="user.email" />
      <InputError :message="errors.email" />
    </div>

    <div class="flex items-center gap-4">
      <Button :disabled="processing">Lưu</Button>
      <p v-show="recentlySuccessful" class="text-green-600 font-semibold">Lưu thành công.</p>
    </div>
  </Form>

  <!-- Delete user -->
  <DeleteUser />
</div>

  </SettingsLayout>
</AppLayout>

</template>
