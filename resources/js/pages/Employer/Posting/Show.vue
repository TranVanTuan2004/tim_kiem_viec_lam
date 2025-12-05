<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ArrowLeft, MapPin, Briefcase, Wallet, Layers } from 'lucide-vue-next'

const props = defineProps({
  job: Object
})
</script>

<template>
  <Head :title="job.title" />

  <AppLayout>
    <div class="max-w-5xl mx-auto py-10 px-6">

      <!-- Back Button -->
      <div class="mb-6">
        <Link 
          href="/employer/posting"
          class="flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium"
        >
          <ArrowLeft class="w-5 h-5" /> Quay lại danh sách
        </Link>
      </div>

      <!-- Job Title -->
      <div class="bg-white shadow-md p-8 rounded-xl border border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900 mb-3">
          {{ job.title }}
        </h1>

        <!-- Meta Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
          <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-200">
            <div class="flex items-center gap-2 text-indigo-700 font-semibold">
              <Wallet class="w-5 h-5" /> Mức lương
            </div>
            <p class="mt-1 text-gray-900">
              <span v-if="job.min_salary && job.max_salary">
                {{ job.min_salary.toLocaleString() }}₫ - {{ job.max_salary.toLocaleString() }}₫
              </span>
              <span v-else>Thỏa thuận</span>
            </p>
          </div>

          <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-200">
            <div class="flex items-center gap-2 text-indigo-700 font-semibold">
              <Briefcase class="w-5 h-5" /> Hình thức làm việc
            </div>
            <p class="mt-1 text-gray-900 capitalize">
              {{ job.employment_type.replace('_', ' ') }}
            </p>
          </div>

          <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-200">
            <div class="flex items-center gap-2 text-indigo-700 font-semibold">
              <Layers class="w-5 h-5" /> Cấp bậc
            </div>
            <p class="mt-1 text-gray-900">
              {{ job.experience_level }}
            </p>
          </div>
        </div>

        <!-- Location -->
        <div class="mt-4 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
          <div class="flex items-center gap-2 text-indigo-700 font-semibold">
            <MapPin class="w-5 h-5" /> Địa điểm làm việc
          </div>
          <p class="mt-1 text-gray-900">
            {{ job.location || `${job.city}, ${job.province}` }}
          </p>
        </div>
      </div>

      <!-- Sections -->
      <div class="mt-10 space-y-8">

        <!-- Mô tả -->
        <section class="bg-white p-8 rounded-xl shadow-md border border-gray-200">
          <h2 class="text-2xl font-bold text-indigo-700 mb-4">Mô tả công việc</h2>
          <p class="text-gray-800 whitespace-pre-line leading-relaxed">
            {{ job.description }}
          </p>
        </section>

        <!-- Yêu cầu -->
        <section class="bg-white p-8 rounded-xl shadow-md border border-gray-200">
          <h2 class="text-2xl font-bold text-indigo-700 mb-4">Yêu cầu ứng viên</h2>
          <p class="text-gray-800 whitespace-pre-line leading-relaxed">
            {{ job.requirements }}
          </p>
        </section>

        <!-- Quyền lợi -->
        <section v-if="job.benefits" class="bg-white p-8 rounded-xl shadow-md border border-gray-200">
          <h2 class="text-2xl font-bold text-indigo-700 mb-4">Quyền lợi</h2>
          <p class="text-gray-800 whitespace-pre-line leading-relaxed">
            {{ job.benefits }}
          </p>
        </section>

      </div>

    </div>
  </AppLayout>
</template>
