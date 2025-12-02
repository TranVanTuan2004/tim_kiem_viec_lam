<template>
  <AppLayout>
    <Head :title="`Chi tiết ứng viên - ${application.candidate.user.full_name}`" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 pb-12">
      <!-- Header Section -->
      <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-30">
        <div class="container mx-auto px-4 py-4">
          <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
              <Link
                href="/employer/applications"
                class="p-2 hover:bg-gray-100 rounded-full transition text-gray-500 hover:text-gray-700"
                title="Quay lại danh sách"
              >
                <ArrowLeft class="w-6 h-6" />
              </Link>
              <div>
                <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                  {{ application.candidate.user.full_name }}
                  <span :class="['px-3 py-0.5 rounded-full text-xs font-medium border', getStatusBadgeClass(application.status)]">
                    {{ getStatusLabel(application.status) }}
                  </span>
                </h1>
                <p class="text-sm text-gray-500 flex items-center gap-1">
                  Ứng tuyển: <span class="font-medium text-blue-600">{{ application.job_posting.title }}</span>
                  <span class="text-gray-300 mx-1">|</span>
                  {{ formatDate(application.created_at) }}
                </p>
              </div>
            </div>
            
            <div class="flex items-center gap-3">
              <a
                v-if="application.cv_file"
                :href="application.cv_file"
                target="_blank"
                class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition shadow-sm"
              >
                <FileText class="w-4 h-4" />
                Xem CV
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
          <!-- Left Column - Candidate Profile & Details -->
          <div class="lg:col-span-8 space-y-8">
            
            <!-- Candidate Summary Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
              <div class="h-32 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
              <div class="px-8 pb-8">
                <div class="relative flex justify-between items-end -mt-12 mb-6">
                  <div class="flex items-end gap-6">
                    <div class="w-32 h-32 rounded-2xl border-4 border-white shadow-lg bg-white overflow-hidden flex items-center justify-center">
                      <img
                        v-if="application.candidate.avatar"
                        :src="`/storage/${application.candidate.avatar}`"
                        :alt="application.candidate.user.full_name"
                        class="w-full h-full object-cover"
                      />
                      <div
                        v-else
                        class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center"
                      >
                        <span class="text-white font-bold text-4xl">
                          {{ getInitials(application.candidate.user.full_name) }}
                        </span>
                      </div>
                    </div>
                    <div class="mb-1">
                      <h2 class="text-2xl font-bold text-gray-900">{{ application.candidate.user.full_name }}</h2>
                      <p class="text-gray-500">{{ application.candidate.current_position || 'Chưa cập nhật chức danh' }}</p>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-3">
                    <div class="flex items-center gap-3 text-gray-600">
                      <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <Mail class="w-4 h-4" />
                      </div>
                      <span class="text-sm">{{ application.candidate.user.email }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600">
                      <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <Phone class="w-4 h-4" />
                      </div>
                      <span class="text-sm">{{ application.candidate.user.phone || 'Chưa cập nhật SĐT' }}</span>
                    </div>
                  </div>

                  <div class="space-y-3">
                    <div class="flex items-center gap-3 text-gray-600">
                      <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <MapPin class="w-4 h-4" />
                      </div>
                      <span class="text-sm">{{ formatLocation(application.candidate) }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600">
                      <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <Briefcase class="w-4 h-4" />
                      </div>
                      <span class="text-sm">{{ application.candidate.experience_level || 'Chưa cập nhật kinh nghiệm' }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600">
                      <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <DollarSign class="w-4 h-4" />
                      </div>
                      <span class="text-sm">{{ formatCurrency(application.candidate.expected_salary) }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600">
                      <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <User class="w-4 h-4" />
                      </div>
                      <span class="text-sm">
                        {{ application.candidate.gender === 'male' ? 'Nam' : (application.candidate.gender === 'female' ? 'Nữ' : 'Khác') }}
                        <span v-if="application.candidate.birth_date" class="mx-1">•</span>
                        <span v-if="application.candidate.birth_date">{{ formatDate(application.candidate.birth_date) }}</span>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Professional Summary -->
            <div v-if="application.candidate.summary" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
              <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <UserCheck class="w-5 h-5 text-blue-600" />
                Giới thiệu bản thân
              </h3>
              <div class="text-gray-700 leading-relaxed whitespace-pre-wrap">
                {{ application.candidate.summary }}
              </div>
            </div>

            <!-- Cover Letter -->
            <div v-if="application.cover_letter" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
              <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <FileText class="w-5 h-5 text-blue-600" />
                Thư xin việc
              </h3>
              <div class="bg-gray-50 rounded-xl p-6 text-gray-700 leading-relaxed whitespace-pre-wrap border border-gray-100 relative">
                <Quote class="w-8 h-8 text-gray-200 absolute top-4 left-4 -z-0" />
                <div class="relative z-10">
                  {{ application.cover_letter }}
                </div>
              </div>
            </div>

            <!-- Skills -->
            <div v-if="application.candidate.skills && application.candidate.skills.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
              <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <Zap class="w-5 h-5 text-yellow-500" />
                Kỹ năng chuyên môn
              </h3>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="skill in application.candidate.skills"
                  :key="skill.id"
                  class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium shadow-sm hover:border-blue-300 hover:text-blue-600 transition cursor-default"
                >
                  {{ skill.name }}
                </span>
              </div>
            </div>

            <!-- Work Experience Timeline -->
            <div v-if="application.candidate.work_experiences && application.candidate.work_experiences.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
              <h3 class="text-lg font-bold text-gray-900 mb-8 flex items-center gap-2">
                <Briefcase class="w-5 h-5 text-blue-600" />
                Kinh nghiệm làm việc
              </h3>
              <div class="relative border-l-2 border-gray-100 ml-3 space-y-10">
                <div
                  v-for="exp in application.candidate.work_experiences"
                  :key="exp.id"
                  class="relative pl-8"
                >
                  <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full border-2 border-white bg-blue-600 shadow-sm"></div>
                  <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 mb-2">
                    <h4 class="text-lg font-bold text-gray-900">{{ exp.job_title }}</h4>
                    <span class="text-sm font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                      {{ formatDateShort(exp.start_date) }} - {{ exp.is_current ? 'Hiện tại' : formatDateShort(exp.end_date) }}
                    </span>
                  </div>
                  <p class="text-blue-600 font-medium mb-2">{{ exp.company_name }}</p>
                  <p v-if="exp.description" class="text-gray-600 text-sm leading-relaxed">{{ exp.description }}</p>
                </div>
              </div>
            </div>

            <!-- Education Timeline -->
            <div v-if="application.candidate.educations && application.candidate.educations.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
              <h3 class="text-lg font-bold text-gray-900 mb-8 flex items-center gap-2">
                <GraduationCap class="w-5 h-5 text-blue-600" />
                Học vấn
              </h3>
              <div class="relative border-l-2 border-gray-100 ml-3 space-y-10">
                <div
                  v-for="edu in application.candidate.educations"
                  :key="edu.id"
                  class="relative pl-8"
                >
                  <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full border-2 border-white bg-indigo-500 shadow-sm"></div>
                  <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 mb-2">
                    <h4 class="text-lg font-bold text-gray-900">{{ edu.degree }}</h4>
                    <span class="text-sm font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                      {{ formatDateShort(edu.start_date) }} - {{ edu.is_current ? 'Hiện tại' : formatDateShort(edu.end_date) }}
                    </span>
                  </div>
                  <p class="text-indigo-600 font-medium mb-2">{{ edu.institution }}</p>
                  <p v-if="edu.description" class="text-gray-600 text-sm leading-relaxed">{{ edu.description }}</p>
                </div>
              </div>
            </div>

          </div>

          <!-- Right Column - Actions (Sticky) -->
          <div class="lg:col-span-4 space-y-6">

            <div v-if="successMessage" class="rounded-md bg-green-50 p-3 mb-4">
              <p class="text-sm text-green-800">{{ successMessage }}</p>
            </div>

            <div class="sticky top-24 space-y-6">
              
              <!-- Action Panel -->
              <div class="bg-white rounded-2xl shadow-lg ring-1 ring-black/5 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Quản lý hồ sơ</h3>
                
                <!-- Status Selector -->
                <div class="mb-6">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái hiện tại</label>
                  <div class="relative">
                    <select
                      v-model="selectedStatus"
                      @change="updateStatus(selectedStatus)"
                      class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition appearance-none font-medium text-gray-700"
                    >
                      <option value="pending">Chờ xem xét</option>
                      <option value="reviewing">Đang xem xét</option>
                      <option value="interview">Phỏng vấn</option>
                      <option value="accepted">Chấp nhận</option>
                      <option value="rejected">Từ chối</option>
                    </select>
                    <ChevronDown class="absolute right-3 top-3.5 w-5 h-5 text-gray-400 pointer-events-none" />
                  </div>
                </div>

                <!-- Primary Actions -->
                <div class="space-y-3">
                  <button
                    v-if="application.status !== 'accepted'"
                    @click="updateStatus('accepted')"
                    class="w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-sm hover:shadow-md flex items-center justify-center gap-2"
                  >
                    <CheckCircle class="w-5 h-5" />
                    Chấp nhận hồ sơ
                  </button>

                  <Link
                    :href="`/employer/interviews/create?application_id=${application.id}`"
                    class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-sm hover:shadow-md flex items-center justify-center gap-2"
                  >
                    <Calendar class="w-5 h-5" />
                    Đặt lịch phỏng vấn
                  </Link>

                  <button
                    v-if="application.status !== 'rejected'"
                    @click="updateStatus('rejected')"
                    class="w-full px-4 py-3 bg-white border border-red-200 text-red-600 font-bold rounded-xl hover:bg-red-50 transition flex items-center justify-center gap-2"
                  >
                    <XCircle class="w-5 h-5" />
                    Từ chối hồ sơ
                  </button>

                  <button
                    @click="openReportModal(application)"
                    class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition shadow-sm hover:shadow-md flex items-center justify-center gap-2"
                  >
                    <AlertCircle class="w-5 h-5" />
                    Báo cáo vi phạm
                  </button>

                </div>
              </div>

              <!-- Interview Info Card -->
              <div v-if="application.interview_date" class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-start gap-4">
                  <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                    <CalendarClock class="w-6 h-6 text-white" />
                  </div>
                  <div>
                    <h3 class="font-bold text-lg mb-1">Lịch phỏng vấn</h3>
                    <p class="text-blue-100 text-sm mb-3">Đã lên lịch hẹn với ứng viên</p>
                    <p class="font-mono text-xl font-bold tracking-wide">
                      {{ formatDateTime(application.interview_date) }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Notes Section -->
              <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                  <NotebookPen class="w-5 h-5 text-gray-500" />
                  Ghi chú nội bộ
                </h3>
                <form @submit.prevent="saveNotes">
                  <textarea
                    v-model="notesForm.notes"
                    rows="6"
                    placeholder="Ghi lại ấn tượng, câu hỏi phỏng vấn hoặc đánh giá..."
                    class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-xl focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition resize-none text-gray-700 placeholder-gray-400"
                  ></textarea>
                  <div class="mt-3 flex justify-end">
                    <button
                      type="submit"
                      :disabled="notesForm.processing"
                      class="px-6 py-2 bg-gray-900 text-white font-medium rounded-lg hover:bg-gray-800 transition shadow-sm disabled:opacity-50"
                    >
                      {{ notesForm.processing ? 'Đang lưu...' : 'Lưu ghi chú' }}
                    </button>
                  </div>
                </form>

                <!-- Display Current Notes -->
                <div v-if="application.notes && !notesForm.isDirty" class="mt-6 pt-6 border-t border-gray-100">
                  <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Ghi chú đã lưu</p>
                  <p class="text-gray-600 whitespace-pre-wrap text-sm">{{ application.notes }}</p>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Status Change Confirmation Modal -->
    <div
      v-if="showStatusModal"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click.self="cancelStatusChange"
    >
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all scale-100">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-gray-900">{{ getStatusModalContent().title }}</h3>
          <button
            @click="cancelStatusChange"
            class="p-2 hover:bg-gray-100 rounded-lg transition text-gray-500"
          >
            <X class="w-6 h-6" />
          </button>
        </div>

        <div class="mb-6">
          <p class="text-gray-600">{{ getStatusModalContent().message }}</p>
        </div>

        <div class="flex gap-3">
          <button
            @click="cancelStatusChange"
            class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition"
          >
            Hủy
          </button>
          <button
            @click="confirmStatusChange"
            class="flex-1 px-4 py-3 text-white font-bold rounded-xl transition shadow-lg"
            :class="{
              'bg-yellow-600 hover:bg-yellow-700': getStatusModalContent().color === 'yellow',
              'bg-purple-600 hover:bg-purple-700': getStatusModalContent().color === 'purple',
              'bg-blue-600 hover:bg-blue-700': getStatusModalContent().color === 'blue',
              'bg-green-600 hover:bg-green-700': getStatusModalContent().color === 'green',
              'bg-red-600 hover:bg-red-700': getStatusModalContent().color === 'red',
            }"
          >
            Xác nhận
          </button>
        </div>
      </div>
    </div>

  </AppLayout>
<EmployerReportModal
  :open="showReportModal"
  :applicationId="application.candidate.id"
  :candidateName="candidateName"
  :submitUrl="submitUrl"
  @update:open="showReportModal = $event"
  @report-success="successMessage = $event"
/>
</template>

<script setup>
import EmployerReportModal from '@/components/EmployerReportModal.vue';
import { AlertCircle } from 'lucide-vue-next';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import {
  ArrowLeft,
  Mail,
  Phone,
  MapPin,
  Briefcase,
  FileText,
  Quote,
  Zap,
  GraduationCap,
  CheckCircle,
  XCircle,
  Calendar,
  ChevronDown,
  NotebookPen,
  CalendarClock,
  DollarSign,
  User,
  UserCheck,
  X
} from 'lucide-vue-next';

const showReportModal = ref(false);
const applicationId = ref(0);
const candidateName = ref('');
const submitUrl = ref('');
const reportReason = ref('');
const successMessage = ref('');

const page = usePage();
const auth = computed(() => page.props.auth);

const companyName = computed(
    () =>
        jobData.value.company?.name ||
        jobData.value.company?.company_name ||
        'Công ty',
);

const openReportModal = (application) => {
  candidateName.value = application.candidate.user.email;
  submitUrl.value = '/employer/reports';
  showReportModal.value = true;
};

const formatCurrency = (value) => {
  if (!value) return 'Thỏa thuận'
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value)
}

const formatLocation = (candidate) => {
  const parts = []
  if (candidate.address) parts.push(candidate.address)
  if (candidate.city) parts.push(candidate.city)
  if (candidate.province && candidate.province !== candidate.city) parts.push(candidate.province)
  return parts.length > 0 ? parts.join(', ') : 'Chưa cập nhật địa chỉ'
}

const props = defineProps({
  application: Object,
})

const selectedStatus = ref(props.application.status)
const showStatusModal = ref(false)
const pendingStatus = ref('')

const notesForm = useForm({
  notes: props.application.notes || '',
})

const updateStatus = (newStatus) => {
  // Show confirmation modal for all statuses including interview
  pendingStatus.value = newStatus
  showStatusModal.value = true
}

const confirmStatusChange = () => {
  // Special handling for interview status - redirect to interview creation
  if (pendingStatus.value === 'interview') {
    router.visit(`/employer/interviews/create?application_id=${props.application.id}`)
    return
  }
  
  // For other statuses, update via API
  router.patch(
    `/employer/applications/${props.application.id}/status`,
    { status: pendingStatus.value },
    {
      preserveScroll: true,
      onSuccess: () => {
        selectedStatus.value = pendingStatus.value
        showStatusModal.value = false
      },
    }
  )
}

const cancelStatusChange = () => {
  selectedStatus.value = props.application.status
  showStatusModal.value = false
}

const getStatusModalContent = () => {
  const content = {
    pending: {
      title: 'Chuyển về Chờ Xem Xét',
      message: 'Bạn có chắc muốn chuyển hồ sơ này về trạng thái "Chờ xem xét"?',
      icon: 'warning',
      color: 'yellow'
    },
    reviewing: {
      title: 'Đang Xem Xét Hồ Sơ',
      message: 'Bạn có chắc muốn chuyển hồ sơ này sang trạng thái "Đang xem xét"?',
      icon: 'info',
      color: 'purple'
    },
    interview: {
      title: 'Đặt Lịch Phỏng Vấn',
      message: 'Bạn sẽ được chuyển đến trang tạo lịch phỏng vấn cho ứng viên này. Tiếp tục?',
      icon: 'info',
      color: 'blue'
    },
    accepted: {
      title: 'Chấp Nhận Ứng Viên',
      message: 'Bạn có chắc muốn chấp nhận ứng viên này? Hành động này sẽ gửi thông báo đến ứng viên.',
      icon: 'success',
      color: 'green'
    },
    rejected: {
      title: 'Từ Chối Hồ Sơ',
      message: 'Bạn có chắc muốn từ chối hồ sơ này? Hành động này sẽ gửi thông báo đến ứng viên.',
      icon: 'error',
      color: 'red'
    }
  }
  return content[pendingStatus.value] || content.pending
}

const saveNotes = () => {
  notesForm.patch(
    `/employer/applications/${props.application.id}/status`,
    {
      preserveScroll: true,
      onSuccess: () => {
        // Optional: show toast
      },
    }
  )
}



const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-50 text-yellow-700 border-yellow-200',
    reviewing: 'bg-purple-50 text-purple-700 border-purple-200',
    interview: 'bg-blue-50 text-blue-700 border-blue-200',
    accepted: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    rejected: 'bg-red-50 text-red-700 border-red-200',
  }
  return classes[status] || 'bg-gray-50 text-gray-700 border-gray-200'
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
