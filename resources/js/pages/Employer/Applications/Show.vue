<template>
  <AppLayout>
    <Head :title="`Chi tiết ứng viên - ${application.candidate.user.full_name}`" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100">
      <div class="container mx-auto px-4 py-8">
        <!-- Header with Back Button -->
        <div class="mb-6 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <Link
              :href="route('employer.applications.index')"
              class="p-2 hover:bg-white rounded-lg transition"
            >
              <svg
                class="w-6 h-6 text-gray-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 19l-7-7 7-7"
                />
              </svg>
            </Link>
            <div>
              <h1 class="text-3xl font-bold text-gray-900">
                Chi tiết ứng viên
              </h1>
              <p class="text-gray-600 mt-1">
                Ứng tuyển vị trí: <strong>{{ application.job_posting.title }}</strong>
              </p>
            </div>
          </div>

          <!-- Current Status Badge -->
          <div class="px-6 py-3 rounded-xl font-semibold text-lg" :class="getStatusBadgeClass(application.status)">
            {{ getStatusLabel(application.status) }}
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Left Column - Candidate Profile -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Candidate Basic Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
              <div class="flex items-start gap-6">
                <div class="flex-shrink-0">
                  <div
                    v-if="application.candidate.user.avatar"
                    class="w-24 h-24 rounded-full overflow-hidden border-4 border-blue-100"
                  >
                    <img
                      :src="application.candidate.user.avatar"
                      :alt="application.candidate.user.full_name"
                      class="w-full h-full object-cover"
                    />
                  </div>
                  <div
                    v-else
                    class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center border-4 border-blue-100"
                  >
                    <span class="text-white font-bold text-3xl">
                      {{ getInitials(application.candidate.user.full_name) }}
                    </span>
                  </div>
                </div>

                <div class="flex-1">
                  <h2 class="text-2xl font-bold text-gray-900 mb-2">
                    {{ application.candidate.user.full_name }}
                  </h2>
                  <div class="space-y-2 text-gray-600">
                    <div class="flex items-center gap-2">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      <span>{{ application.candidate.user.email }}</span>
                    </div>
                    <div v-if="application.candidate.user.phone" class="flex items-center gap-2">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                      </svg>
                      <span>{{ application.candidate.user.phone }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <span>Ứng tuyển: {{ formatDate(application.created_at) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- CV File -->
            <div v-if="application.cv_file" class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                CV đính kèm
              </h3>
              <a
                :href="application.cv_file"
                target="_blank"
                class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition shadow-sm hover:shadow-md"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Tải xuống CV
              </a>
            </div>

            <!-- Cover Letter -->
            <div v-if="application.cover_letter" class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                Thư xin việc
              </h3>
              <p class="text-gray-700 whitespace-pre-wrap">{{ application.cover_letter }}</p>
            </div>

            <!-- Skills -->
            <div v-if="application.candidate.skills && application.candidate.skills.length > 0" class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                Kỹ năng
              </h3>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="skill in application.candidate.skills"
                  :key="skill.id"
                  class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium"
                >
                  {{ skill.name }}
                </span>
              </div>
            </div>

            <!-- Work Experience -->
            <div v-if="application.candidate.work_experiences && application.candidate.work_experiences.length > 0" class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Kinh nghiệm làm việc
              </h3>
              <div class="space-y-4">
                <div
                  v-for="exp in application.candidate.work_experiences"
                  :key="exp.id"
                  class="border-l-4 border-blue-500 pl-4 py-2"
                >
                  <h4 class="font-semibold text-gray-900">{{ exp.job_title }}</h4>
                  <p class="text-gray-700">{{ exp.company_name }}</p>
                  <p class="text-sm text-gray-500">
                    {{ formatDateShort(exp.start_date) }} - {{ exp.is_current ? 'Hiện tại' : formatDateShort(exp.end_date) }}
                  </p>
                  <p v-if="exp.description" class="mt-2 text-gray-600 text-sm">{{ exp.description }}</p>
                </div>
              </div>
            </div>

            <!-- Education -->
            <div v-if="application.candidate.educations && application.candidate.educations.length > 0" class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
                Học vấn
              </h3>
              <div class="space-y-4">
                <div
                  v-for="edu in application.candidate.educations"
                  :key="edu.id"
                  class="border-l-4 border-purple-500 pl-4 py-2"
                >
                  <h4 class="font-semibold text-gray-900">{{ edu.degree }}</h4>
                  <p class="text-gray-700">{{ edu.institution }}</p>
                  <p class="text-sm text-gray-500">
                    {{ formatDateShort(edu.start_date) }} - {{ edu.is_current ? 'Hiện tại' : formatDateShort(edu.end_date) }}
                  </p>
                  <p v-if="edu.description" class="mt-2 text-gray-600 text-sm">{{ edu.description }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Actions & Notes -->
          <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Hành động nhanh</h3>
              <div class="space-y-3">
                <button
                  v-if="application.status !== 'accepted'"
                  @click="updateStatus('accepted')"
                  class="w-full px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium rounded-lg hover:from-green-700 hover:to-emerald-700 transition shadow-sm hover:shadow-md flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Chấp nhận
                </button>

                <button
                  v-if="application.status !== 'rejected'"
                  @click="updateStatus('rejected')"
                  class="w-full px-4 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white font-medium rounded-lg hover:from-red-700 hover:to-rose-700 transition shadow-sm hover:shadow-md flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Từ chối
                </button>

                <button
                  @click="showInterviewModal = true"
                  class="w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-blue-700 transition shadow-sm hover:shadow-md flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  Đặt lịch phỏng vấn
                </button>
              </div>
            </div>

            <!-- Change Status -->
            <div class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Thay đổi trạng thái</h3>
              <select
                v-model="selectedStatus"
                @change="updateStatus(selectedStatus)"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-medium"
              >
                <option value="pending">Chờ xem xét</option>
                <option value="reviewing">Đang xem xét</option>
                <option value="interview">Phỏng vấn</option>
                <option value="accepted">Chấp nhận</option>
                <option value="rejected">Từ chối</option>
              </select>
            </div>

            <!-- Interview Info -->
            <div v-if="application.interview_date" class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl shadow-sm p-6 border border-indigo-100">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Lịch phỏng vấn
              </h3>
              <p class="text-gray-900 font-semibold text-lg">
                {{ formatDateTime(application.interview_date) }}
              </p>
            </div>

            <!-- Notes Section -->
            <div class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Ghi chú / Phản hồi</h3>
              <form @submit.prevent="saveNotes">
                <textarea
                  v-model="notesForm.notes"
                  rows="6"
                  placeholder="Thêm ghi chú về ứng viên này..."
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                ></textarea>
                <button
                  type="submit"
                  :disabled="notesForm.processing"
                  class="mt-3 w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition shadow-sm hover:shadow-md disabled:opacity-50"
                >
                  {{ notesForm.processing ? 'Đang lưu...' : 'Lưu ghi chú' }}
                </button>
              </form>

              <!-- Display Current Notes -->
              <div v-if="application.notes" class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-sm font-medium text-gray-700 mb-2">Ghi chú hiện tại:</p>
                <p class="text-gray-600 whitespace-pre-wrap">{{ application.notes }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Interview Modal -->
    <div
      v-if="showInterviewModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="showInterviewModal = false"
    >
      <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-gray-900">Đặt lịch phỏng vấn</h3>
          <button
            @click="showInterviewModal = false"
            class="p-2 hover:bg-gray-100 rounded-lg transition"
          >
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="scheduleInterview">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Ngày và giờ phỏng vấn
              </label>
              <input
                v-model="interviewForm.interview_date"
                type="datetime-local"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Ghi chú (tùy chọn)
              </label>
              <textarea
                v-model="interviewForm.notes"
                rows="3"
                placeholder="Ví dụ: Phỏng vấn tại văn phòng tầng 5..."
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
              ></textarea>
            </div>
          </div>

          <div class="mt-6 flex gap-3">
            <button
              type="button"
              @click="showInterviewModal = false"
              class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition"
            >
              Hủy
            </button>
            <button
              type="submit"
              :disabled="interviewForm.processing"
              class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-blue-700 transition disabled:opacity-50"
            >
              {{ interviewForm.processing ? 'Đang lưu...' : 'Xác nhận' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  application: Object,
})

const selectedStatus = ref(props.application.status)
const showInterviewModal = ref(false)

const notesForm = useForm({
  notes: props.application.notes || '',
})

const interviewForm = useForm({
  interview_date: '',
  notes: '',
})

const updateStatus = (newStatus) => {
  if (confirm('Bạn có chắc muốn cập nhật trạng thái?')) {
    router.patch(
      route('employer.applications.update-status', props.application.id),
      { status: newStatus },
      {
        preserveScroll: true,
        onSuccess: () => {
          selectedStatus.value = newStatus
        },
      }
    )
  }
}

const saveNotes = () => {
  notesForm.patch(
    route('employer.applications.update-status', props.application.id),
    {
      preserveScroll: true,
      onSuccess: () => {
        // Optional: show success message
      },
    }
  )
}

const scheduleInterview = () => {
  interviewForm
    .transform((data) => ({
      status: 'interview',
      interview_date: data.interview_date,
      notes: data.notes,
    }))
    .patch(route('employer.applications.update-status', props.application.id), {
      preserveScroll: true,
      onSuccess: () => {
        showInterviewModal.value = false
        interviewForm.reset()
      },
    })
}

const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 border border-yellow-300',
    reviewing: 'bg-purple-100 text-purple-800 border border-purple-300',
    interview: 'bg-indigo-100 text-indigo-800 border border-indigo-300',
    accepted: 'bg-green-100 text-green-800 border border-green-300',
    rejected: 'bg-red-100 text-red-800 border border-red-300',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Chờ xem xét',
    reviewing: 'Đang xem xét',
    interview: 'Phỏng vấn',
    accepted: 'Được chấp nhận',
    rejected: 'Bị từ chối',
  }
  return labels[status] || status
}

const getInitials = (name) => {
  return name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('vi-VN', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatDateShort = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('vi-VN', {
    year: 'numeric',
    month: '2-digit',
  })
}

const formatDateTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleString('vi-VN', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>
