<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import { User as UserIcon } from 'lucide-vue-next';

interface Log {
  id: number;
  description: string;
  created_at: string;
}

interface Props {
  user: {
    id: number;
    name: string;
  };
  logs: Log[];
}

const props = defineProps<Props>();

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý người dùng', href: '/admin/users' },
  { title: `Lịch sử hoạt động: ${props.user.name}`, href: '' }
];
</script>

<template>
  <Head title="Lịch sử hoạt động" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-[20px]">
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
        <div>
          <h1 class="text-3xl font-bold tracking-tight flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <UserIcon class="h-6 w-6 text-primary" />
            </div>
            Lịch sử hoạt động: {{ props.user.name }} (ID: {{ props.user.id }})
          </h1>
          <p class="text-muted-foreground mt-2 ml-[52px]">
            Danh sách các hoạt động gần đây của người dùng
          </p>
        </div>
      </div>

      <!-- Activity Logs -->
      <div v-if="logs.length > 0" class="bg-white rounded-md shadow overflow-hidden">
        <div class="responsive-table-wrapper">
          <table class="w-full text-sm text-left mobile-card-view">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
              <tr>
                <th class="px-4 py-3">Nội dung</th>
                <th class="px-4 py-3">Thời gian</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="log in logs" :key="log.id" class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900" data-label="Nội dung">
                  {{ log.description }}
                </td>
                <td class="px-4 py-3" data-label="Thời gian">
                  {{ new Date(log.created_at).toLocaleString() }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12 text-muted-foreground">
        <UserIcon class="h-16 w-16 mx-auto mb-4 text-muted-foreground/30" />
        <p>Chưa có hoạt động nào</p>
      </div>
    </div>
  </AppLayout>
</template>
