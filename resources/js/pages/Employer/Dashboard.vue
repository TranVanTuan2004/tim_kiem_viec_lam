<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  Briefcase,
  FileText,
  Users,
  Clock,
  Plus,
  Search,
  Settings,
  TrendingUp,
  MapPin,
  Calendar,
  ChevronRight,
  Building2
} from 'lucide-vue-next'

const props = defineProps({
  stats: Object,
  recentJobs: Array,
  recentApplications: Array,
  company: Object,
})

const getStatusBadgeClass = (status) => {
  const classes = {
    open: 'bg-green-100 text-green-700 border-green-200',
    closed: 'bg-red-100 text-red-700 border-red-200',
    draft: 'bg-gray-100 text-gray-700 border-gray-200',
  }
  return classes[status] || 'bg-gray-100 text-gray-700 border-gray-200'
}

const getStatusLabel = (status) => {
  const labels = {
    open: 'Đang tuyển',
    closed: 'Đã đóng',
    draft: 'Nháp',
  }
  return labels[status] || status
}

const getInitials = (name) => {
  if (!name) return ''
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
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  })
}
</script>

<template>
  <AppLayout>
    <Head title="Dashboard Nhà tuyển dụng" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 pb-12">
      <!-- Header Section -->
      <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-30">
        <div class="container mx-auto px-4 py-6">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-200">
                <Building2 class="w-6 h-6" />
              </div>
              <div>
                <h1 class="text-2xl font-bold text-gray-900">
                  Xin chào, {{ company?.name || 'Nhà tuyển dụng' }}
                </h1>
                <p class="text-gray-500 text-sm flex items-center gap-2">
                  <span class="flex items-center gap-1">
                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                    Gói đăng ký: {{ company?.subscription?.status ?? 'Cơ bản' }}
                  </span>
                </p>
              </div>
            </div>
            
            <Link
              href="/employer/posting/create"
              class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-lg shadow-blue-200 hover:shadow-blue-300"
            >
              <Plus class="w-5 h-5" />
              Đăng tin tuyển dụng
            </Link>
          </div>
        </div>
      </div>

      <div class="container mx-auto px-4 py-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Jobs -->
          <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-4">
              <div class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition">
                <Briefcase class="w-6 h-6" />
              </div>
              <span class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">Tổng quan</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ stats.total_jobs }}</h3>
            <p class="text-gray-500 text-sm">Tổng tin tuyển dụng</p>
          </div>

          <!-- Active Jobs -->
          <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-4">
              <div class="p-3 bg-green-50 text-green-600 rounded-xl group-hover:bg-green-600 group-hover:text-white transition">
                <TrendingUp class="w-6 h-6" />
              </div>
              <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-lg">Đang chạy</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ stats.active_jobs }}</h3>
            <p class="text-gray-500 text-sm">Tin đang hoạt động</p>
          </div>

          <!-- Total Applications -->
          <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-4">
              <div class="p-3 bg-purple-50 text-purple-600 rounded-xl group-hover:bg-purple-600 group-hover:text-white transition">
                <Users class="w-6 h-6" />
              </div>
              <span class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">Tổng quan</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ stats.total_applications }}</h3>
            <p class="text-gray-500 text-sm">Lượt ứng tuyển</p>
          </div>

          <!-- Pending Applications -->
          <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-4">
              <div class="p-3 bg-yellow-50 text-yellow-600 rounded-xl group-hover:bg-yellow-600 group-hover:text-white transition">
                <Clock class="w-6 h-6" />
              </div>
              <span class="text-xs font-medium text-yellow-600 bg-yellow-50 px-2 py-1 rounded-lg">Cần xử lý</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ stats.pending_applications }}</h3>
            <p class="text-gray-500 text-sm">Hồ sơ chờ duyệt</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column: Recent Jobs & Applications -->
          <div class="lg:col-span-2 space-y-8">
            
            <!-- Recent Jobs -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
              <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                  <Briefcase class="w-5 h-5 text-blue-600" />
                  Tin tuyển dụng gần đây
                </h2>
                <Link href="/employer/posting" class="text-sm font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">
                  Xem tất cả <ChevronRight class="w-4 h-4" />
                </Link>
              </div>
              
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead class="bg-gray-50/50">
                    <tr>
                      <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vị trí</th>
                      <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                      <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Ứng viên</th>
                      <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ngày đăng</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100">
                    <tr v-for="job in recentJobs" :key="job.id" class="hover:bg-gray-50/50 transition">
                      <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ job.title }}</div>
                        <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                          <MapPin class="w-3 h-3" /> {{ job.location }}
                        </div>
                      </td>
                      <td class="px-6 py-4">
                        <span :class="['px-2.5 py-1 rounded-full text-xs font-medium border', getStatusBadgeClass(job.status)]">
                          {{ getStatusLabel(job.status) }}
                        </span>
                      </td>
                      <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-1 text-gray-700 font-medium">
                          <Users class="w-4 h-4 text-gray-400" />
                          {{ job.applications_count }}
                        </div>
                      </td>
                      <td class="px-6 py-4 text-right text-sm text-gray-500">
                        {{ formatDate(job.created_at) }}
                      </td>
                    </tr>
                    <tr v-if="!recentJobs.length">
                      <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                        Chưa có tin tuyển dụng nào.
                        <Link href="/employer/posting/create" class="text-blue-600 font-medium hover:underline ml-1">Đăng tin ngay</Link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Recent Applications -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
              <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                  <Users class="w-5 h-5 text-purple-600" />
                  Ứng viên mới nhất
                </h2>
                <Link href="/admin/applications" class="text-sm font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">
                  Xem tất cả <ChevronRight class="w-4 h-4" />
                </Link>
              </div>

              <div class="divide-y divide-gray-100">
                <div v-for="app in recentApplications" :key="app.id" class="p-4 hover:bg-gray-50 transition flex items-center gap-4">
                  <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm overflow-hidden">
                      <img
                        v-if="app.candidate.avatar"
                        :src="`/storage/${app.candidate.avatar}`"
                        :alt="app.candidate.name"
                        class="w-full h-full object-cover"
                      />
                      <span v-else>
                        {{ getInitials(app.candidate.name) }}
                      </span>
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                      <h4 class="text-sm font-bold text-gray-900 truncate">{{ app.candidate.name }}</h4>
                      <span class="text-xs text-gray-500">{{ formatDate(app.applied_at) }}</span>
                    </div>
                    <p class="text-sm text-gray-600 truncate mb-1">
                      Ứng tuyển: <span class="font-medium text-blue-600">{{ app.job_posting.title }}</span>
                    </p>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <span class="flex items-center gap-1">
                        <Briefcase class="w-3 h-3" /> {{ app.candidate.current_position || 'Chưa cập nhật' }}
                      </span>
                    </div>
                  </div>
                  <Link
                    :href="`/employer/applications/${app.id}`"
                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                  >
                    <ChevronRight class="w-5 h-5" />
                  </Link>
                </div>
                
                <div v-if="!recentApplications.length" class="p-8 text-center text-gray-500">
                  Chưa có ứng viên nào ứng tuyển.
                </div>
              </div>
            </div>

          </div>

          <!-- Right Column: Quick Actions & Info -->
          <div class="space-y-6">
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
              <h2 class="text-lg font-bold text-gray-900 mb-4">Truy cập nhanh</h2>
              <div class="space-y-3">
                <Link
                  href="/employer/posting/create"
                  class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 text-gray-700 hover:text-blue-700 transition group"
                >
                  <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition">
                    <Plus class="w-5 h-5" />
                  </div>
                  <span class="font-medium">Đăng tin mới</span>
                </Link>

                <Link
                  href="/admin/applications"
                  class="flex items-center gap-3 p-3 rounded-xl hover:bg-purple-50 text-gray-700 hover:text-purple-700 transition group"
                >
                  <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition">
                    <Search class="w-5 h-5" />
                  </div>
                  <span class="font-medium">Tìm kiếm hồ sơ</span>
                </Link>

                <Link
                  href="/employer/settings/company"
                  class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 text-gray-700 hover:text-gray-900 transition group"
                >
                  <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center group-hover:bg-gray-800 group-hover:text-white transition">
                    <Settings class="w-5 h-5" />
                  </div>
                  <span class="font-medium">Cài đặt công ty</span>
                </Link>
              </div>
            </div>

            <!-- Company Info Card -->
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl shadow-lg p-6 text-white">
              <div class="flex items-center gap-4 mb-6">
                <img
                  v-if="company?.logo"
                  :src="`/storage/${company.logo}`"
                  alt="Logo"
                  class="w-16 h-16 object-cover rounded-xl bg-white p-1"
                />
                <div v-else class="w-16 h-16 rounded-xl bg-white/10 flex items-center justify-center">
                  <Building2 class="w-8 h-8 text-white/50" />
                </div>
                <div>
                  <h3 class="font-bold text-lg">{{ company?.name || 'Công ty của bạn' }}</h3>
                  <p class="text-slate-400 text-sm">Nhà tuyển dụng</p>
                </div>
              </div>
              
              <div class="space-y-3 text-sm text-slate-300">
                <div class="flex justify-between py-2 border-b border-white/10">
                  <span>Gói dịch vụ</span>
                  <span class="text-white font-medium">{{ company?.subscription?.status ?? 'Miễn phí' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-white/10">
                  <span>Thành viên từ</span>
                  <span class="text-white font-medium">{{ formatDate(company?.created_at) }}</span>
                </div>
              </div>

              <Link
                href="/employer/settings/company"
                class="mt-6 block w-full py-2.5 bg-white/10 hover:bg-white/20 text-center rounded-lg transition text-sm font-medium"
              >
                Quản lý hồ sơ
              </Link>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
