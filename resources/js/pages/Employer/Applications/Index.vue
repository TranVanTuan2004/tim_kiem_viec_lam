<template>
  <AppLayout>
    <Head title="Lịch sử ứng tuyển" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100">
      <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">
            Lịch sử ứng tuyển
          </h1>
          <p class="text-gray-600">
            Quản lý và theo dõi tất cả đơn ứng tuyển vào công ty của bạn
          </p>
        </div>

        <!-- Statistics Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
          <!-- Total Applications -->
          <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Tổng số</p>
                <p class="text-3xl font-bold text-gray-900">
                  {{ statistics.total }}
                </p>
              </div>
              <div class="p-3 bg-blue-100 rounded-lg">
                <svg
                  class="w-6 h-6 text-blue-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </svg>
              </div>
            </div>
          </div>

          <!-- Pending -->
          <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Chờ xem</p>
                <p class="text-3xl font-bold text-gray-900">
                  {{ statistics.pending }}
                </p>
              </div>
              <div class="p-3 bg-yellow-100 rounded-lg">
                <svg
                  class="w-6 h-6 text-yellow-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
            </div>
          </div>

          <!-- Reviewing -->
          <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Đang xem</p>
                <p class="text-3xl font-bold text-gray-900">
                  {{ statistics.reviewing }}
                </p>
              </div>
              <div class="p-3 bg-purple-100 rounded-lg">
                <svg
                  class="w-6 h-6 text-purple-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>
              </div>
            </div>
          </div>

          <!-- Interview -->
          <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Phỏng vấn</p>
                <p class="text-3xl font-bold text-gray-900">
                  {{ statistics.interview }}
                </p>
              </div>
              <div class="p-3 bg-indigo-100 rounded-lg">
                <svg
                  class="w-6 h-6 text-indigo-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
              </div>
            </div>
          </div>

          <!-- Accepted -->
          <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Chấp nhận</p>
                <p class="text-3xl font-bold text-gray-900">
                  {{ statistics.accepted }}
                </p>
              </div>
              <div class="p-3 bg-green-100 rounded-lg">
                <svg
                  class="w-6 h-6 text-green-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
            </div>
          </div>

          <!-- Rejected -->
          <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Từ chối</p>
                <p class="text-3xl font-bold text-gray-900">
                  {{ statistics.rejected }}
                </p>
              </div>
              <div class="p-3 bg-red-100 rounded-lg">
                <svg
                  class="w-6 h-6 text-red-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Tìm kiếm ứng viên
              </label>
              <div class="relative">
                <input
                  v-model="filterForm.search"
                  type="text"
                  placeholder="Tên hoặc email..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                  @input="applyFilters"
                />
                <svg
                  class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                  />
                </svg>
              </div>
            </div>

            <!-- Status Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Trạng thái
              </label>
              <select
                v-model="filterForm.status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                @change="applyFilters"
              >
                <option value="">Tất cả trạng thái</option>
                <option value="pending">Chờ xem xét</option>
                <option value="reviewing">Đang xem xét</option>
                <option value="interview">Phỏng vấn</option>
                <option value="accepted">Được chấp nhận</option>
                <option value="rejected">Bị từ chối</option>
              </select>
            </div>

            <!-- Job Posting Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Vị trí tuyển dụng
              </label>
              <select
                v-model="filterForm.job_posting_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                @change="applyFilters"
              >
                <option value="">Tất cả vị trí</option>
                <option
                  v-for="job in jobPostings"
                  :key="job.id"
                  :value="job.id"
                >
                  {{ job.title }}
                </option>
              </select>
            </div>

            <!-- Date From -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Từ ngày
              </label>
              <input
                v-model="filterForm.date_from"
                type="date"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                @change="applyFilters"
              />
            </div>

            <!-- Date To -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Đến ngày
              </label>
              <input
                v-model="filterForm.date_to"
                type="date"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                @change="applyFilters"
              />
            </div>

            <!-- Clear Filters Button -->
            <div class="flex items-end">
              <button
                @click="clearFilters"
                class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium"
              >
                Xóa bộ lọc
              </button>
            </div>
          </div>
        </div>

        <!-- Applications List -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
          <div v-if="applications.data && applications.data.length === 0" class="text-center py-12">
            <svg
              class="mx-auto h-16 w-16 text-gray-400 mb-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
            <p class="text-xl font-medium text-gray-600">Không có ứng viên nào</p>
            <p class="text-gray-500 mt-2">Chưa có ứng viên nào ứng tuyển phù hợp với bộ lọc của bạn</p>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Ứng viên
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Vị trí ứng tuyển
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Ngày ứng tuyển
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Trạng thái
                  </th>
                  <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Hành động
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="app in applications.data"
                  :key="app.id"
                  class="hover:bg-gray-50 transition"
                >
                  <td class="px-6 py-4">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold">
                          {{ getInitials(app.candidate.user.full_name) }}
                        </span>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ app.candidate.user.full_name }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ app.candidate.user.email }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ app.job_posting.title }}
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">
                      {{ formatDate(app.created_at) }}
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <select
                      :value="app.status"
                      class="px-3 py-1 rounded-full text-xs font-semibold border-0 focus:ring-2 focus:ring-offset-1 transition"
                      :class="getStatusClass(app.status)"
                      @change="updateStatus(app.id, $event.target.value)"
                    >
                      <option value="pending">Chờ xem xét</option>
                      <option value="reviewing">Đang xem xét</option>
                      <option value="interview">Phỏng vấn</option>
                      <option value="accepted">Chấp nhận</option>
                      <option value="rejected">Từ chối</option>
                    </select>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <Link
                      :href="route('employer.applications.show', app.id)"
                      class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition shadow-sm hover:shadow-md"
                    >
                      <svg
                        class="w-4 h-4 mr-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                        />
                      </svg>
                      Xem chi tiết
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="applications.data && applications.data.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Hiển thị
                <span class="font-medium">{{ applications.from }}</span>
                đến
                <span class="font-medium">{{ applications.to }}</span>
                trong tổng số
                <span class="font-medium">{{ applications.total }}</span>
                kết quả
              </div>

              <div class="flex gap-2">
                <Link
                  v-for="link in applications.links"
                  :key="link.label"
                  :href="link.url"
                  :class="[
                    'px-4 py-2 text-sm font-medium rounded-lg transition',
                    link.active
                      ? 'bg-blue-600 text-white'
                      : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300',
                    !link.url && 'opacity-50 cursor-not-allowed',
                  ]"
                  :disabled="!link.url"
                  v-html="link.label"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { reactive } from 'vue'

const props = defineProps({
  applications: Object,
  statistics: Object,
  jobPostings: Array,
  filters: Object,
})

const filterForm = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
  job_posting_id: props.filters.job_posting_id || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
})

const applyFilters = () => {
  router.get(
    route('employer.applications.index'),
    {
      search: filterForm.search,
      status: filterForm.status,
      job_posting_id: filterForm.job_posting_id,
      date_from: filterForm.date_from,
      date_to: filterForm.date_to,
    },
    {
      preserveState: true,
      preserveScroll: true,
    }
  )
}

const clearFilters = () => {
  filterForm.search = ''
  filterForm.status = ''
  filterForm.job_posting_id = ''
  filterForm.date_from = ''
  filterForm.date_to = ''
  applyFilters()
}

const updateStatus = (applicationId, newStatus) => {
  if (confirm('Bạn có chắc muốn cập nhật trạng thái?')) {
    router.patch(
      route('employer.applications.update-status', applicationId),
      { status: newStatus },
      {
        preserveScroll: true,
        onSuccess: () => {
          // Show success message
        },
      }
    )
  }
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 focus:ring-yellow-500',
    reviewing: 'bg-purple-100 text-purple-800 focus:ring-purple-500',
    interview: 'bg-indigo-100 text-indigo-800 focus:ring-indigo-500',
    accepted: 'bg-green-100 text-green-800 focus:ring-green-500',
    rejected: 'bg-red-100 text-red-800 focus:ring-red-500',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
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
</script>
