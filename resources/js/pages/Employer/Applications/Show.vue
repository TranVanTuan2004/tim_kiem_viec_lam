<template>
  <AppLayout>
    <Head title="Chi tiết ứng viên" />

    <div class="p-6">
      <Link :href="route('employer.applications.index')" class="text-blue-600 hover:underline mb-4 inline-block">
        ← Quay lại danh sách
      </Link>

      <div class="bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-semibold mb-2">{{ application.candidate.user.full_name }}</h1>
        <p class="text-gray-600">{{ application.candidate.user.email }}</p>

        <div class="mt-4">
          <h2 class="text-lg font-semibold">Giới thiệu bản thân</h2>
          <p class="text-gray-700 mt-2">{{ application.candidate.summary || 'Không có mô tả' }}</p>
        </div>

        <div class="mt-4">
          <h2 class="text-lg font-semibold">Kinh nghiệm làm việc</h2>
          <ul class="list-disc ml-5 text-gray-700">
            <li v-for="exp in application.candidate.work_experiences" :key="exp.id">
              {{ exp.position }} tại {{ exp.company_name }} ({{ exp.start_date }} - {{ exp.end_date || 'Hiện tại' }})
            </li>
          </ul>
        </div>

        <div class="mt-4">
          <h2 class="text-lg font-semibold">Kỹ năng</h2>
          <ul class="flex flex-wrap gap-2 mt-2">
            <li
              v-for="skill in application.candidate.skills"
              :key="skill.id"
              class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm"
            >
              {{ skill.name }}
            </li>
          </ul>
        </div>

        <div class="mt-4">
          <a
            v-if="application.cv_file"
            :href="`/storage/${application.cv_file}`"
            target="_blank"
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 inline-block"
          >
            Xem CV
          </a>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps({
  application: Object,
})
</script>
