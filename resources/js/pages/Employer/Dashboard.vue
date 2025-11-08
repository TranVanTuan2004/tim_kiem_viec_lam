<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  stats: Object,
  recentJobs: Array,
  recentApplications: Array,
  company: Object,
})
</script>

<template>
  <AppLayout>
    <Head title="Dashboard Nhà tuyển dụng" />

    <div class="p-6">
      <h1 class="text-2xl font-bold mb-6">
        👔 Dashboard
      </h1>

      <!-- Thông tin công ty -->
      <div class="bg-white rounded-xl shadow p-4 mb-6 flex items-center gap-4">
        <img
          v-if="company?.logo"
          :src="`/storage/${company.logo}`"
          alt="Logo"
          class="w-16 h-16 object-cover rounded-lg"
        />
        <div>
          <h2 class="text-lg font-semibold">{{ company?.name }}</h2>
          <p class="text-gray-500 text-sm">
            Gói đăng ký:
            <span class="font-medium">
              {{ company?.subscription?.status ?? 'Chưa đăng ký' }}
            </span>
          </p>
        </div>
      </div>

      <!-- Thống kê tổng quan -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl">
          <p class="text-gray-500 text-sm">Tổng tin tuyển dụng</p>
          <h2 class="text-2xl font-bold text-blue-600">{{ stats.total_jobs }}</h2>
        </div>
        <div class="bg-green-50 border border-green-200 p-4 rounded-xl">
          <p class="text-gray-500 text-sm">Tin đang hoạt động</p>
          <h2 class="text-2xl font-bold text-green-600">{{ stats.active_jobs }}</h2>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl">
          <p class="text-gray-500 text-sm">Tổng ứng tuyển</p>
          <h2 class="text-2xl font-bold text-yellow-600">{{ stats.total_applications }}</h2>
        </div>
        <div class="bg-red-50 border border-red-200 p-4 rounded-xl">
          <p class="text-gray-500 text-sm">Ứng tuyển chờ duyệt</p>
          <h2 class="text-2xl font-bold text-red-600">{{ stats.pending_applications }}</h2>
        </div>
      </div>

      <!-- Tin tuyển dụng gần đây -->
      <div class="bg-white rounded-xl shadow p-4 mb-8">
        <div class="flex justify-between items-center mb-3">
          <h2 class="text-lg font-semibold">📰 Tin tuyển dụng gần đây</h2>
          <Link
            href="employer/jobs"
            class="text-sm text-blue-600 hover:underline"
            >Xem tất cả</Link
          >
        </div>

        <table class="w-full border-t border-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left py-2 px-3">Tiêu đề</th>
              <th class="text-left py-2 px-3">Trạng thái</th>
              <th class="text-left py-2 px-3">Vị trí</th>
              <th class="text-left py-2 px-3">Ứng tuyển</th>
              <th class="text-left py-2 px-3">Ngày tạo</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="job in recentJobs"
              :key="job.id"
              class="border-t hover:bg-gray-50"
            >
              <td class="py-2 px-3 font-medium text-gray-800">
                {{ job.title }}
              </td>
              <td class="py-2 px-3 capitalize">{{ job.status }}</td>
              <td class="py-2 px-3">{{ job.location }}</td>
              <td class="py-2 px-3">{{ job.applications_count }}</td>
              <td class="py-2 px-3">{{ job.created_at }}</td>
            </tr>
            <tr v-if="!recentJobs.length">
              <td colspan="5" class="text-center py-4 text-gray-500">
                Chưa có tin tuyển dụng nào
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Ứng viên gần đây -->
      <div class="bg-white rounded-xl shadow p-4">
        <div class="flex justify-between items-center mb-3">
          <h2 class="text-lg font-semibold">👤 Ứng viên gần đây</h2>
          <Link
            href="/employer/applications"
            class="text-sm text-blue-600 hover:underline"
            >Xem tất cả</Link
          >
        </div>

        <table class="w-full border-t border-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left py-2 px-3">Tên</th>
              <th class="text-left py-2 px-3">Email</th>
              <th class="text-left py-2 px-3">Vị trí hiện tại</th>
              <th class="text-left py-2 px-3">Tin đã ứng tuyển</th>
              <th class="text-left py-2 px-3">Ngày nộp</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="app in recentApplications"
              :key="app.id"
              class="border-t hover:bg-gray-50"
            >
              <td class="py-2 px-3">{{ app.candidate.name }}</td>
              <td class="py-2 px-3">{{ app.candidate.email }}</td>
              <td class="py-2 px-3">{{ app.candidate.current_position ?? '-' }}</td>
              <td class="py-2 px-3">{{ app.job_posting.title }}</td>
              <td class="py-2 px-3">{{ app.applied_at }}</td>
            </tr>
            <tr v-if="!recentApplications.length">
              <td colspan="5" class="text-center py-4 text-gray-500">
                Chưa có ứng viên nào ứng tuyển
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
