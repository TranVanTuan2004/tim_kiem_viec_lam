<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Eye, ArrowLeft } from 'lucide-vue-next'

interface Props {
  application: {
    id: number
    status: string
    created_at: string
    notes?: string
    candidate: {
      id: number
      name: string
      email: string
      avatar?: string | null
      skills: Array<{ name: string }>
      educations: Array<{ school: string; degree: string; year: string }>
      workExperiences: Array<{ company: string; position: string; from: string; to?: string }>
    } | null
    jobPosting: {
      id: number
      title: string
      company?: { id: number; company_name: string } | null
    } | null
  }
}

const props = defineProps<Props>()

const statusOptions = [
  { value: 'pending', label: 'Chờ xử lý', class: 'bg-yellow-100 text-yellow-800' },
  { value: 'reviewing', label: 'Đang xem xét', class: 'bg-blue-100 text-blue-800' },
  { value: 'interview', label: 'Phỏng vấn', class: 'bg-purple-100 text-purple-800' },
  { value: 'accepted', label: 'Chấp nhận', class: 'bg-green-100 text-green-800' },
  { value: 'rejected', label: 'Từ chối', class: 'bg-red-100 text-red-800' },
]

const getStatusBadgeClass = (status: string) =>
  statusOptions.find(s => s.value === status)?.class || 'bg-gray-100 text-gray-800'

const getStatusLabel = (status: string) =>
  statusOptions.find(s => s.value === status)?.label || status

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý ứng tuyển', href: '/admin/applications' },
  { title: 'Chi tiết', href: '' },
]

// Hàm quay lại
const goBack = () => {
  router.get('/admin/applications')
}
</script>

<template>
  <Head title="Chi tiết ứng viên - Admin" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-5">
      <Card>
        <CardHeader class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <div class="flex items-center gap-2">
            <Eye class="w-5 h-5" />
            <CardTitle>Thông tin ứng viên</CardTitle>
          </div>
          <Button size="sm" variant="outline" class="flex items-center gap-1" @click="goBack">
            <ArrowLeft class="w-4 h-4" /> Quay lại
          </Button>
        </CardHeader>

        <CardContent class="space-y-4">
          <!-- Thông tin cơ bản -->
          <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
              <img v-if="props.application.candidate?.avatar" :src="`/storage/${props.application.candidate.avatar}`" class="w-full h-full object-cover"/>
              <span v-else class="text-white font-bold text-xl">{{ props.application.candidate?.name?.charAt(0).toUpperCase() || '?' }}</span>
            </div>
            <div>
              <p class="text-lg font-semibold">{{ props.application.candidate?.name || 'Chưa cập nhật' }}</p>
              <p class="text-sm text-gray-500">{{ props.application.candidate?.email || '-' }}</p>
            </div>
          </div>

          <!-- Thông tin công việc -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <h4 class="font-semibold mb-1">Công ty</h4>
              <p>{{ props.application.jobPosting?.company?.company_name || 'Chưa cập nhật' }}</p>
            </div>
            <div>
              <h4 class="font-semibold mb-1">Vị trí</h4>
              <p>{{ props.application.jobPosting?.title || 'Chưa cập nhật' }}</p>
            </div>
            <div>
              <h4 class="font-semibold mb-1">Trạng thái</h4>
              <Badge :class="getStatusBadgeClass(props.application.status)">
                {{ getStatusLabel(props.application.status) }}
              </Badge>
            </div>
            <div>
              <h4 class="font-semibold mb-1">Ngày ứng tuyển</h4>
              <p>{{ new Date(props.application.created_at).toLocaleDateString('vi-VN') }}</p>
            </div>
          </div>

          <!-- Kỹ năng -->
          <div v-if="props.application.candidate?.skills.length">
            <h4 class="font-semibold mb-1">Kỹ năng</h4>
            <div class="flex flex-wrap gap-2">
              <Badge v-for="skill in props.application.candidate.skills" :key="skill.name">{{ skill.name }}</Badge>
            </div>
          </div>

          <!-- Học vấn -->
          <div v-if="props.application.candidate?.educations.length">
            <h4 class="font-semibold mb-1">Học vấn</h4>
            <ul class="list-disc list-inside">
              <li v-for="edu in props.application.candidate.educations" :key="edu.school">
                {{ edu.degree }} - {{ edu.school }} ({{ edu.year }})
              </li>
            </ul>
          </div>

          <!-- Kinh nghiệm làm việc -->
          <div v-if="props.application.candidate?.workExperiences.length">
            <h4 class="font-semibold mb-1">Kinh nghiệm làm việc</h4>
            <ul class="list-disc list-inside">
              <li v-for="work in props.application.candidate.workExperiences" :key="work.company + work.position">
                {{ work.position }} tại {{ work.company }} ({{ work.from }} - {{ work.to || 'Hiện tại' }})
              </li>
            </ul>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
