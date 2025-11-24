<template>
  <aside class="space-y-3">
    <!-- Main Navigation Card -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
      <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3">
        <h2 class="text-sm font-semibold text-white">Quản lý hồ sơ</h2>
      </div>
      <nav class="divide-y divide-gray-100">
        <Link
          :href="profileIndex.url()"
          class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 transition-colors"
          :class="{ 'bg-blue-50 border-l-4 border-blue-600': isActive('index') }"
        >
          <Eye class="h-4 w-4 text-gray-600" />
          <div>
            <p class="text-sm font-medium text-gray-900">Xem hồ sơ</p>
            <p class="text-xs text-gray-500">Hồ sơ công khai</p>
          </div>
        </Link>

        <Link
          :href="profileCreate.url()"
          class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 transition-colors"
          :class="{ 'bg-blue-50 border-l-4 border-blue-600': isActive('create') }"
        >
          <Plus class="h-4 w-4 text-gray-600" />
          <div>
            <p class="text-sm font-medium text-gray-900">Tạo hồ sơ</p>
            <p class="text-xs text-gray-500">Hồ sơ mới</p>
          </div>
        </Link>

        <Link
          :href="profileEdit.url()"
          class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 transition-colors"
          :class="{ 'bg-blue-50 border-l-4 border-blue-600': isActive('edit') }"
        >
          <Edit2 class="h-4 w-4 text-gray-600" />
          <div>
            <p class="text-sm font-medium text-gray-900">Chỉnh sửa hồ sơ</p>
            <p class="text-xs text-gray-500">Cập nhật thông tin</p>
          </div>
        </Link>
      </nav>
    </div>

    <!-- Stats Section - Hoạt động của bạn -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
      <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-3">
        <h2 class="text-sm font-semibold text-white">Hoạt động của bạn</h2>
      </div>
      <div class="p-4 space-y-3">
        <!-- Work Experience -->
        <Link
          :href="workExperiencesIndex.url()"
          class="flex items-center justify-between p-3 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors"
        >
          <div class="flex items-center gap-2">
            <Briefcase class="h-4 w-4 text-blue-600" />
            <span class="text-sm text-gray-700">Kinh nghiệm làm việc</span>
          </div>
          <span class="text-lg font-bold text-blue-600">{{ profile?.work_experiences?.length || 0 }}</span>
        </Link>

        <!-- Education -->
        <Link
          :href="educationsIndex.url()"
          class="flex items-center justify-between p-3 rounded-lg bg-green-50 hover:bg-green-100 transition-colors"
        >
          <div class="flex items-center gap-2">
            <GraduationCap class="h-4 w-4 text-green-600" />
            <span class="text-sm text-gray-700">Học vấn</span>
          </div>
          <span class="text-lg font-bold text-green-600">{{ profile?.educations?.length || 0 }}</span>
        </Link>

        <!-- Skills -->
        <div class="flex items-center justify-between p-3 rounded-lg bg-purple-50">
          <div class="flex items-center gap-2">
            <Award class="h-4 w-4 text-purple-600" />
            <span class="text-sm text-gray-700">Kỹ năng</span>
          </div>
          <span class="text-lg font-bold text-purple-600">{{ profile?.skills?.length || 0 }}</span>
        </div>

        <!-- Portfolio -->
        <Link
          :href="portfoliosIndex.url()"
          class="flex items-center justify-between p-3 rounded-lg bg-orange-50 hover:bg-orange-100 transition-colors"
        >
          <div class="flex items-center gap-2">
            <Folder class="h-4 w-4 text-orange-600" />
            <span class="text-sm text-gray-700">Dự án</span>
          </div>
          <span class="text-lg font-bold text-orange-600">{{ profile?.portfolios?.length || 0 }}</span>
        </Link>
      </div>
    </div>

    <!-- Experience & Education -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
      <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-4 py-3">
        <h2 class="text-sm font-semibold text-white">Trải nghiệm &amp; Giáo dục</h2>
      </div>
      <nav class="divide-y divide-gray-100">
        <Link
          :href="workExperiencesIndex.url()"
          class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 transition-colors"
          :class="{ 'bg-green-50 border-l-4 border-green-600': isActive('work-experiences') }"
        >
          <Briefcase class="h-4 w-4 text-gray-600" />
          <div>
            <p class="text-sm font-medium text-gray-900">Kinh nghiệm làm việc</p>
            <p class="text-xs text-gray-500">Quản lý công việc</p>
          </div>
        </Link>

        <Link
          :href="educationsIndex.url()"
          class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 transition-colors"
          :class="{ 'bg-green-50 border-l-4 border-green-600': isActive('educations') }"
        >
          <GraduationCap class="h-4 w-4 text-gray-600" />
          <div>
            <p class="text-sm font-medium text-gray-900">Học vấn</p>
            <p class="text-xs text-gray-500">Quản lý học vấn</p>
          </div>
        </Link>
      </nav>
    </div>

    <!-- Portfolio & Skills -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
      <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-4 py-3">
        <h2 class="text-sm font-semibold text-white">Portfolio &amp; Kỹ năng</h2>
      </div>
      <nav class="divide-y divide-gray-100">
        <Link
          :href="portfoliosIndex.url()"
          class="flex items-center gap-3 px-4 py-3 hover:bg-purple-50 transition-colors"
          :class="{ 'bg-purple-50 border-l-4 border-purple-600': isActive('portfolios') }"
        >
          <Folder class="h-4 w-4 text-gray-600" />
          <div>
            <p class="text-sm font-medium text-gray-900">Portfolio</p>
            <p class="text-xs text-gray-500">Dự án của bạn</p>
          </div>
        </Link>

        <div class="flex items-center gap-3 px-4 py-3 hover:bg-purple-50 transition-colors cursor-not-allowed opacity-75">
          <Award class="h-4 w-4 text-gray-600" />
          <div>
            <p class="text-sm font-medium text-gray-900">Kỹ năng</p>
            <p class="text-xs text-gray-500">Được chỉnh sửa trong hồ sơ</p>
          </div>
        </div>
      </nav>
    </div>

    <!-- Help Card -->
    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
      <div class="flex gap-3">
        <Info class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
        <div>
          <p class="text-sm font-semibold text-blue-900">Mẹo hoàn thiện hồ sơ</p>
          <ul class="mt-2 text-xs text-blue-800 space-y-1">
            <li>✓ Cập nhật ảnh đại diện chuyên nghiệp</li>
            <li>✓ Thêm kinh nghiệm làm việc chi tiết</li>
            <li>✓ Tải lên CV/Resume</li>
            <li>✓ Thêm dự án portfolio</li>
          </ul>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
  Eye,
  Plus,
  Edit2,
  Briefcase,
  GraduationCap,
  Folder,
  Award,
  Info,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { index as profileIndex } from '@/routes/candidate/profile';
import { create as profileCreate } from '@/routes/candidate/profile';
import { edit as profileEdit } from '@/routes/candidate/profile';
import { index as workExperiencesIndex } from '@/routes/candidate/work-experiences';
import { index as educationsIndex } from '@/routes/candidate/educations';
import { index as portfoliosIndex } from '@/routes/candidate/portfolios';

interface Props {
  profile?: any;
}

defineProps<Props>();

const page = usePage();

const currentUrl = computed(() => page.url);

const isActive = (section: string) => {
  const url = currentUrl.value;
  return url.includes(section);
};
</script>
