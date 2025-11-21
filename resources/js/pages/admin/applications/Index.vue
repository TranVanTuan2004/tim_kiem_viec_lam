<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Trash2, Eye, Filter, X, Search } from 'lucide-vue-next';

interface Application {
    id: number;
    status: string;
    created_at: string;
    notes?: string;
    interview_date?: string;
    candidate: {
        id: number;
        user: {
            name: string;
            email: string;
            avatar?: string;
        };
    };
    jobPosting: {
        id: number;
        title: string;
        company: {
            id: number;
            company_name: string;
        };
    };
}

interface Props {
    applications: {
        data: Application[];
        links: any[];
        current_page: number;
        last_page: number;
    };
    statistics: {
        total: number;
        pending: number;
        reviewing: number;
        interview: number;
        accepted: number;
        rejected: number;
    };
    companies: Array<{ id: number; company_name: string }>;
    jobPostings: Array<{ 
        id: number; 
        title: string;
        company: { id: number; company_name: string };
    }>;
    filters: {
        status?: string;
        company_id?: number;
        job_posting_id?: number;
        search?: string;
        date_from?: string;
        date_to?: string;
    };
}

const props = defineProps<Props>();

// Filter state
const filterForm = ref({
    status: props.filters.status || '',
    company_id: props.filters.company_id || '',
    job_posting_id: props.filters.job_posting_id || '',
    search: props.filters.search || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

// Delete confirmation
const deleteConfirm = ref<number | null>(null);

const statusOptions = [
    { value: 'pending', label: 'Chờ xử lý', color: 'bg-yellow-100 text-yellow-800' },
    { value: 'reviewing', label: 'Đang xem xét', color: 'bg-blue-100 text-blue-800' },
    { value: 'interview', label: 'Phỏng vấn', color: 'bg-purple-100 text-purple-800' },
    { value: 'accepted', label: 'Chấp nhận', color: 'bg-green-100 text-green-800' },
    { value: 'rejected', label: 'Từ chối', color: 'bg-red-100 text-red-800' },
];

const getStatusColor = (status: string) => {
    return statusOptions.find(s => s.value === status)?.color || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status: string) => {
    return statusOptions.find(s => s.value === status)?.label || status;
};

const applyFilters = () => {
    router.get('/admin/applications', filterForm.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filterForm.value = {
        status: '',
        company_id: '',
        job_posting_id: '',
        search: '',
        date_from: '',
        date_to: '',
    };
    applyFilters();
};

const updateStatus = (applicationId: number, newStatus: string) => {
    router.patch(`/admin/applications/${applicationId}/status`, {
        status: newStatus,
    }, {
        preserveScroll: true,
    });
};

const deleteApplication = (id: number) => {
    if (deleteConfirm.value === id) {
        router.delete(`/admin/applications/${id}`, {
            preserveScroll: true,
            onSuccess: () => {
                deleteConfirm.value = null;
            },
        });
    } else {
        deleteConfirm.value = id;
        setTimeout(() => {
            deleteConfirm.value = null;
        }, 3000);
    }
};

const hasActiveFilters = computed(() => {
    return filterForm.value.status || 
           filterForm.value.company_id || 
           filterForm.value.job_posting_id || 
           filterForm.value.search || 
           filterForm.value.date_from || 
           filterForm.value.date_to;
});
</script>

<template>
    <Head title="Quản lý ứng tuyển - Admin" />
    
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50 py-8">
            <div class="container mx-auto px-4">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">
                        Quản lý ứng tuyển
                    </h1>
                    <p class="text-gray-600 mt-2">Quản lý tất cả đơn ứng tuyển trong hệ thống</p>
                </div>

                <!-- Statistics Dashboard -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-8">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow">
                        <div class="text-sm font-medium opacity-90">Tổng số</div>
                        <div class="text-3xl font-bold mt-2">{{ statistics.total }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow">
                        <div class="text-sm font-medium opacity-90">Chờ xử lý</div>
                        <div class="text-3xl font-bold mt-2">{{ statistics.pending }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow">
                        <div class="text-sm font-medium opacity-90">Đang xem xét</div>
                        <div class="text-3xl font-bold mt-2">{{ statistics.reviewing }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow">
                        <div class="text-sm font-medium opacity-90">Phỏng vấn</div>
                        <div class="text-3xl font-bold mt-2">{{ statistics.interview }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow">
                        <div class="text-sm font-medium opacity-90">Chấp nhận</div>
                        <div class="text-3xl font-bold mt-2">{{ statistics.accepted }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow">
                        <div class="text-sm font-medium opacity-90">Từ chối</div>
                        <div class="text-3xl font-bold mt-2">{{ statistics.rejected }}</div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <Filter class="h-5 w-5 text-red-600" />
                            Bộ lọc
                        </h2>
                        <button
                            v-if="hasActiveFilters"
                            @click="clearFilters"
                            class="text-sm text-red-600 hover:text-red-700 flex items-center gap-1"
                        >
                            <X class="h-4 w-4" />
                            Xóa bộ lọc
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tìm kiếm ứng viên
                            </label>
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                <input
                                    v-model="filterForm.search"
                                    type="text"
                                    placeholder="Tên hoặc email..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                        </div>

                        <!-- Company -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Công ty
                            </label>
                            <select
                                v-model="filterForm.company_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            >
                                <option value="">Tất cả công ty</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">
                                    {{ company.company_name }}
                                </option>
                            </select>
                        </div>

                        <!-- Job Posting -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Vị trí tuyển dụng
                            </label>
                            <select
                                v-model="filterForm.job_posting_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            >
                                <option value="">Tất cả vị trí</option>
                                <option v-for="job in jobPostings" :key="job.id" :value="job.id">
                                    {{ job.title }} - {{ job.company.company_name }}
                                </option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Trạng thái
                            </label>
                            <select
                                v-model="filterForm.status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            >
                                <option value="">Tất cả trạng thái</option>
                                <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                    {{ status.label }}
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
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
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
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button
                            @click="applyFilters"
                            class="px-6 py-2 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-lg hover:from-red-700 hover:to-orange-700 transition-all shadow-md hover:shadow-lg"
                        >
                            Áp dụng bộ lọc
                        </button>
                    </div>
                </div>

                <!-- Applications Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Ứng viên</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Công ty</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Vị trí</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Ngày ứng tuyển</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Trạng thái</th>
                                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="app in applications.data" :key="app.id" class="hover:bg-gray-50 transition-colors">
                                    <!-- Candidate -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                                                {{ app.candidate.user.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ app.candidate.user.name }}</div>
                                                <div class="text-sm text-gray-500">{{ app.candidate.user.email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Company -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ app.jobPosting.company.company_name }}
                                        </div>
                                    </td>

                                    <!-- Job Title -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ app.jobPosting.title }}</div>
                                    </td>

                                    <!-- Date -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600">
                                            {{ new Date(app.created_at).toLocaleDateString('vi-VN') }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        <select
                                            :value="app.status"
                                            @change="updateStatus(app.id, ($event.target as HTMLSelectElement).value)"
                                            :class="[
                                                'px-3 py-1 rounded-full text-xs font-medium border-0 cursor-pointer',
                                                getStatusColor(app.status)
                                            ]"
                                        >
                                            <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                                {{ status.label }}
                                            </option>
                                        </select>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="`/employer/applications/${app.id}`"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Xem chi tiết"
                                            >
                                                <Eye class="h-5 w-5" />
                                            </Link>
                                            <button
                                                @click="deleteApplication(app.id)"
                                                :class="[
                                                    'p-2 rounded-lg transition-all',
                                                    deleteConfirm === app.id
                                                        ? 'bg-red-600 text-white'
                                                        : 'text-red-600 hover:bg-red-50'
                                                ]"
                                                :title="deleteConfirm === app.id ? 'Click lại để xác nhận xóa' : 'Xóa'"
                                            >
                                                <Trash2 class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="applications.data.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        Không tìm thấy đơn ứng tuyển nào
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="applications.data.length > 0" class="px-6 py-4 border-t bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                Trang {{ applications.current_page }} / {{ applications.last_page }}
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    v-for="(link, index) in applications.links"
                                    :key="index"
                                    :href="link.url || '#'"
                                    :class="[
                                        'px-4 py-2 rounded-lg text-sm font-medium transition-all',
                                        link.active
                                            ? 'bg-gradient-to-r from-red-600 to-orange-600 text-white'
                                            : link.url
                                            ? 'bg-white text-gray-700 hover:bg-gray-100 border'
                                            : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    ]"
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
