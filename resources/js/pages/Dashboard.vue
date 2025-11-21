<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import type { PageProps } from '@inertiajs/core';

interface FlashProps {
  success?: string;
  error?: string;
}

type ExtendedPageProps = PageProps & {
  flash?: FlashProps;
  stats?: any;
  roleStats?: any;
  jobStats?: any;
  applicationStats?: any;
  revenue?: any;
  topJobs?: any;
  topCompanies?: any;
  newUsers?: any;
  newJobs?: any;
  last30Days?: any;
  recentActivities?: any;
};

const page = usePage<ExtendedPageProps>();
const flashMessage = ref<FlashProps>(page.props.flash || {});

onMounted(() => {
  if (flashMessage.value?.success || flashMessage.value?.error) {
    setTimeout(() => {
      flashMessage.value = {};
    }, 5000);
  }
});

// Note: This Dashboard.vue is a generic dashboard that should not be accessed directly
// All users should be redirected to their role-specific dashboards via /dashboard route
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard', // This will redirect to role-specific dashboard
    },
];

const stats = computed(() => page.props.stats || {});
const roleStats = computed(() => page.props.roleStats || {});
const jobStats = computed(() => page.props.jobStats || {});
const applicationStats = computed(() => page.props.applicationStats || {});
const revenue = computed(() => page.props.revenue || {});
const topJobs = computed(() => page.props.topJobs || []);
const topCompanies = computed(() => page.props.topCompanies || []);
const last30Days = computed(() => page.props.last30Days || []);
const recentActivities = computed(() => page.props.recentActivities || {});

const formatNumber = (num: number) => {
  if (!num) return '0';
  return new Intl.NumberFormat('vi-VN').format(num);
};

const formatCurrency = (num: number) => {
  if (!num) return '0 ₫';
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(num);
};

const getRevenueGrowth = () => {
  if (revenue.value.last_month === 0) return 0;
  const growth = ((revenue.value.this_month - revenue.value.last_month) / revenue.value.last_month) * 100;
  return Math.round(growth);
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <!-- Flash Messages -->
            <div v-if="flashMessage?.success"
                class="animate-fade-in text-center text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg border border-green-200 shadow-sm">
                {{ flashMessage.success }}
            </div>

            <div v-if="flashMessage?.error"
                class="animate-fade-in text-center text-sm font-medium text-red-600 bg-red-50 p-3 rounded-lg border border-red-200 shadow-sm">
                {{ flashMessage.error }}
            </div>

            <!-- Welcome Header with Animated Gradient -->
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-8 text-white shadow-2xl">
                <div class="absolute inset-0 bg-black opacity-10"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full opacity-5 transform translate-x-1/2 -translate-y-1/2"></div>
                <div class="relative z-10">
                    <h1 class="text-4xl font-bold mb-2 flex items-center gap-3">
                        <svg class="w-10 h-10 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                        Dashboard Tổng Quan
                    </h1>
                    <p class="text-indigo-100 text-lg">Chào mừng trở lại! Hệ thống hoạt động tốt.</p>
                </div>
            </div>

            <!-- Main Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Users -->
                <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-l-4 border-blue-500">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Người Dùng</p>
                                <p class="text-4xl font-bold text-gray-800 mt-1">{{ formatNumber(stats.total_users) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-green-600 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Tổng cộng
                        </div>
                    </div>
                </div>

                <!-- Total Companies -->
                <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-l-4 border-green-500">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Công Ty</p>
                                <p class="text-4xl font-bold text-gray-800 mt-1">{{ formatNumber(stats.total_companies) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-green-600 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Hoạt động
                        </div>
                    </div>
                </div>

                <!-- Total Jobs -->
                <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-l-4 border-purple-500">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-4 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Việc Làm</p>
                                <p class="text-4xl font-bold text-gray-800 mt-1">{{ formatNumber(stats.total_jobs) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-purple-600 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Đã đăng
                        </div>
                    </div>
                </div>

                <!-- Total Applications -->
                <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-l-4 border-orange-500">
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-4 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Đơn Ứng Tuyển</p>
                                <p class="text-4xl font-bold text-gray-800 mt-1">{{ formatNumber(stats.total_applications) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-orange-600 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Tổng số
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Stats with Gradient Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="group relative overflow-hidden bg-gradient-to-br from-emerald-500 via-green-500 to-teal-500 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                    <div class="relative p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-emerald-100 text-sm font-medium">Tổng Doanh Thu</p>
                                <p class="text-3xl font-bold">{{ formatCurrency(revenue.total) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center text-emerald-100 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                            Tất cả
                        </div>
                    </div>
                </div>

                <div class="group relative overflow-hidden bg-gradient-to-br from-blue-500 via-cyan-500 to-blue-600 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                    <div class="relative p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-blue-100 text-sm font-medium">Tháng Này</p>
                                <p class="text-3xl font-bold">{{ formatCurrency(revenue.this_month) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center text-blue-100 text-sm" :class="getRevenueGrowth() >= 0 ? 'text-green-200' : 'text-red-200'">
                            <svg v-if="getRevenueGrowth() >= 0" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <svg v-else class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ getRevenueGrowth() }}%
                        </div>
                    </div>
                </div>

                <div class="group relative overflow-hidden bg-gradient-to-br from-purple-500 via-pink-500 to-purple-600 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                    <div class="relative p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-purple-100 text-sm font-medium">Tháng Trước</p>
                                <p class="text-3xl font-bold">{{ formatCurrency(revenue.last_month) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center text-purple-100 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            So sánh
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Stats Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Role Stats -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <div class="w-1 h-8 bg-gradient-to-b from-blue-500 to-purple-500 rounded-full mr-3"></div>
                        Thống Kê Theo Vai Trò
                    </h3>
                    <div class="space-y-3">
                        <div class="group flex items-center justify-between p-4 bg-gradient-to-r from-red-50 to-red-100 rounded-xl hover:from-red-100 hover:to-red-200 transition-all duration-300">
                            <div class="flex items-center gap-3">
                                <div class="bg-red-500 rounded-full p-2 shadow-lg">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-red-700">Quản Trị Viên</span>
                            </div>
                            <span class="text-3xl font-bold text-red-600">{{ formatNumber(roleStats.admins) }}</span>
                        </div>

                        <div class="group flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl hover:from-blue-100 hover:to-blue-200 transition-all duration-300">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-500 rounded-full p-2 shadow-lg">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-blue-700">Nhà Tuyển Dụng</span>
                            </div>
                            <span class="text-3xl font-bold text-blue-600">{{ formatNumber(roleStats.employers) }}</span>
                        </div>

                        <div class="group flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-xl hover:from-green-100 hover:to-green-200 transition-all duration-300">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-500 rounded-full p-2 shadow-lg">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-green-700">Ứng Viên</span>
                            </div>
                            <span class="text-3xl font-bold text-green-600">{{ formatNumber(roleStats.candidates) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Job Stats -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <div class="w-1 h-8 bg-gradient-to-b from-purple-500 to-pink-500 rounded-full mr-3"></div>
                        Thống Kê Việc Làm
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <p class="text-green-600 text-xs font-semibold uppercase tracking-wider mb-2">Đã Duyệt</p>
                            <p class="text-3xl font-bold text-green-700">{{ formatNumber(jobStats.published) }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <p class="text-yellow-600 text-xs font-semibold uppercase tracking-wider mb-2">Chờ Duyệt</p>
                            <p class="text-3xl font-bold text-yellow-700">{{ formatNumber(jobStats.pending) }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <p class="text-red-600 text-xs font-semibold uppercase tracking-wider mb-2">Hết Hạn</p>
                            <p class="text-3xl font-bold text-red-700">{{ formatNumber(jobStats.expired) }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <p class="text-purple-600 text-xs font-semibold uppercase tracking-wider mb-2">Nổi Bật</p>
                            <p class="text-3xl font-bold text-purple-700">{{ formatNumber(jobStats.featured) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Stats -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <div class="w-1 h-8 bg-gradient-to-b from-orange-500 to-pink-500 rounded-full mr-3"></div>
                    Thống Kê Đơn Ứng Tuyển
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-6 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        <div class="inline-flex items-center justify-center w-14 h-14 bg-yellow-500 rounded-2xl mb-3 shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p class="text-yellow-600 text-sm font-semibold mb-1">Chờ Xem Xét</p>
                        <p class="text-3xl font-bold text-yellow-700">{{ formatNumber(applicationStats.pending) }}</p>
                    </div>

                    <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-500 rounded-2xl mb-3 shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                            </svg>
                        </div>
                        <p class="text-blue-600 text-sm font-semibold mb-1">Phỏng Vấn</p>
                        <p class="text-3xl font-bold text-blue-700">{{ formatNumber(applicationStats.interview) }}</p>
                    </div>

                    <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        <div class="inline-flex items-center justify-center w-14 h-14 bg-green-500 rounded-2xl mb-3 shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p class="text-green-600 text-sm font-semibold mb-1">Chấp Nhận</p>
                        <p class="text-3xl font-bold text-green-700">{{ formatNumber(applicationStats.accepted) }}</p>
                    </div>

                    <div class="text-center p-6 bg-gradient-to-br from-red-50 to-red-100 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        <div class="inline-flex items-center justify-center w-14 h-14 bg-red-500 rounded-2xl mb-3 shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p class="text-red-600 text-sm font-semibold mb-1">Từ Chối</p>
                        <p class="text-3xl font-bold text-red-700">{{ formatNumber(applicationStats.rejected) }}</p>
                    </div>
                </div>
            </div>

            <!-- Top Lists -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Jobs -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <div class="w-1 h-8 bg-gradient-to-b from-purple-500 to-indigo-500 rounded-full mr-3"></div>
                        Việc Làm Xem Nhiều Nhất
                    </h3>
                    <div class="space-y-3">
                        <div v-for="(job, index) in topJobs" :key="job.id" 
                             class="group flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gradient-to-r hover:from-purple-50 hover:to-indigo-50 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center gap-3">
                                <span class="bg-gradient-to-br from-purple-500 to-indigo-600 text-white font-bold w-10 h-10 rounded-xl flex items-center justify-center shadow-lg">
                                    {{ index + 1 }}
                                </span>
                                <div>
                                    <p class="font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">{{ job.title }}</p>
                                    <p class="text-sm text-gray-600">{{ job.company?.company_name }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-purple-600 bg-purple-100 px-3 py-1 rounded-full">{{ formatNumber(job.views) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Top Companies -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <div class="w-1 h-8 bg-gradient-to-b from-green-500 to-emerald-500 rounded-full mr-3"></div>
                        Công Ty Tuyển Dụng Nhiều Nhất
                    </h3>
                    <div class="space-y-3">
                        <div v-for="(company, index) in topCompanies" :key="company.id" 
                             class="group flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center gap-3">
                                <span class="bg-gradient-to-br from-green-500 to-emerald-600 text-white font-bold w-10 h-10 rounded-xl flex items-center justify-center shadow-lg">
                                    {{ index + 1 }}
                                </span>
                                <div>
                                    <p class="font-semibold text-gray-800 group-hover:text-green-600 transition-colors">{{ company.company_name }}</p>
                                    <p class="text-sm text-gray-600">{{ company.city }}, {{ company.province }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-green-600 bg-green-100 px-3 py-1 rounded-full">{{ company.job_postings_count }} việc</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <div class="w-1 h-8 bg-gradient-to-b from-orange-500 to-red-500 rounded-full mr-3"></div>
                    Hoạt Động Gần Đây
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Latest Users -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center text-blue-700">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                            Người Dùng Mới
                        </h4>
                        <div class="space-y-2">
                            <div v-for="user in recentActivities.latest_users" :key="user.id" 
                                 class="p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow border border-blue-100 hover:border-blue-200">
                                <p class="font-semibold text-gray-800">{{ user.name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Latest Jobs -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center text-purple-700">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                            </svg>
                            Việc Làm Mới
                        </h4>
                        <div class="space-y-2">
                            <div v-for="job in recentActivities.latest_jobs" :key="job.id" 
                                 class="p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow border border-purple-100 hover:border-purple-200">
                                <p class="font-semibold text-gray-800">{{ job.title }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ job.company?.company_name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Latest Applications -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center text-green-700">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            Đơn Ứng Tuyển Mới
                        </h4>
                        <div class="space-y-2">
                            <div v-for="app in recentActivities.latest_applications" :key="app.id" 
                                 class="p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow border border-green-100 hover:border-green-200">
                                <p class="font-semibold text-gray-800">{{ app.jobPosting?.title || 'N/A' }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ app.candidate?.user?.name || 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}
</style>
