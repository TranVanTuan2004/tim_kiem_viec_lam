<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { ArrowLeft } from 'lucide-vue-next'


const props = defineProps({
  job: Object
})
</script>

<template>
  <Head :title="job.title" />

  <AppLayout>
    <div class="pmx-8 rounded-xl bg-white p-8 shadow">
      <div class="flex items-center mb-6">
        <Link href="/employer/posting" class="flex items-center text-sm text-gray-600 hover:text-gray-800">
          <ArrowLeft class="w-4 h-4 mr-1" /> Quay lại danh sách
        </Link>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>{{ job.title }}</CardTitle>
        </CardHeader>

        <CardContent class="space-y-4">
          <div>
            <h3 class="font-semibold">Mô tả công việc</h3>
            <p class="text-gray-700 whitespace-pre-line">{{ job.description }}</p>
          </div>

          <div>
            <h3 class="font-semibold">Yêu cầu ứng viên</h3>
            <p class="text-gray-700 whitespace-pre-line">{{ job.requirements }}</p>
          </div>

          <div v-if="job.benefits">
            <h3 class="font-semibold">Quyền lợi</h3>
            <p class="text-gray-700 whitespace-pre-line">{{ job.benefits }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div><strong>Ngành nghề:</strong> {{ job.industry_id }}</div>
            <div><strong>Hình thức:</strong> {{ job.employment_type }}</div>
            <div><strong>Kinh nghiệm:</strong> {{ job.experience_level }}</div>
            <div><strong>Thành phố:</strong> {{ job.city }}</div>
            <div><strong>Tỉnh:</strong> {{ job.province }}</div>
            <div><strong>Địa điểm:</strong> {{ job.location }}</div>
            <div><strong>Lương:</strong> {{ job.min_salary }} - {{ job.max_salary }}</div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>