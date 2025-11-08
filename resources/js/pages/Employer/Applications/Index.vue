<template>
  <AppLayout>
    <Head title="Danh sách ứng viên" />

    <div class="p-6">
      <h1 class="text-2xl font-semibold mb-6">Danh sách ứng viên đã ứng tuyển</h1>

      <div v-if="applications.length === 0" class="text-gray-500">
        Hiện chưa có ứng viên nào ứng tuyển.
      </div>

      <div v-else class="grid gap-4">
        <div
          v-for="app in applications"
          :key="app.id"
          class="border rounded-xl p-4 shadow-sm hover:shadow-md transition"
        >
          <div class="flex justify-between items-center">
            <div>
              <h2 class="text-lg font-medium">{{ app.candidate.user.full_name }}</h2>
              <p class="text-sm text-gray-600">{{ app.candidate.user.email }}</p>
              <p class="text-sm text-gray-500 mt-1">
                Ứng tuyển: <strong>{{ app.job_posting.title }}</strong>
              </p>
              <p class="text-sm text-blue-600 mt-1">Trạng thái: {{ app.status_label }}</p>
            </div>

            <Link
              :href="route('employer.applications.show', app.id)"
              class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
            >
              Xem chi tiết
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps({
  applications: Array,
})
</script>
