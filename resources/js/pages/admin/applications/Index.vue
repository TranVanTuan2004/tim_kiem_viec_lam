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
    jobPosting?: {
        id: number;
        title: string;
        company?: {
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
        company?: { id: number; company_name: string } | null;
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
        <div class="min-h-screen bg-slate-50 py-8">
            <div class="mx-auto w-full max-w-6xl space-y-8 px-4">

                <!-- Header + actions -->
                <div class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-500 uppercase tracking-wide">Admin</p>
                        <h1 class="mt-2 text-3xl font-bold text-slate-900">Quản lý lịch sử ứng tuyển</h1>
                        <p class="mt-1 text-sm text-slate-500">
                            Theo dõi trạng thái đơn ứng tuyển, quản lý phỏng vấn và xử lý các yêu cầu của ứng viên.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:border-slate-300 hover:bg-slate-50"
                            @click="clearFilters"
                        >
                            <X class="h-4 w-4" />
                            Làm mới bộ lọc
                        </button>
                        <button
                            class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-red-600 to-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-red-500/30 hover:shadow-lg"
                            @click="applyFilters"
                        >
                            <Filter class="h-4 w-4" />
                            Áp dụng bộ lọc
                        </button>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-6">
                    <div
                        v-for="stat in [
                            { label: 'Tổng số', value: statistics.total, color: 'from-slate-900 to-slate-800' },
                            { label: 'Chờ xử lý', value: statistics.pending, color: 'from-amber-500 to-amber-600' },
                            { label: 'Đang xem xét', value: statistics.reviewing, color: 'from-blue-500 to-blue-600' },
                            { label: 'Phỏng vấn', value: statistics.interview, color: 'from-violet-500 to-violet-600' },
                            { label: 'Chấp nhận', value: statistics.accepted, color: 'from-emerald-500 to-emerald-600' },
                            { label: 'Từ chối', value: statistics.rejected, color: 'from-rose-500 to-rose-600' },
                        ]"
                        :key="stat.label"
                        class="rounded-2xl bg-gradient-to-br p-5 text-white shadow-sm shadow-slate-200/50"
                        :class="stat.color"
                    >
                        <p class="text-sm font-medium text-white/80">{{ stat.label }}</p>
                        <p class="mt-2 text-3xl font-semibold tracking-tight">{{ stat.value }}</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Bộ lọc nâng cao</p>
                            <p class="text-xs text-slate-500">
                                {{ hasActiveFilters ? 'Đã áp dụng bộ lọc' : 'Không có bộ lọc nào' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-slate-300"
                                @click="clearFilters"
                            >
                                <X class="h-3.5 w-3.5" />
                                Xóa tất cả
                            </button>
                        </div>
                    </div>
                    <div class="grid gap-4 border-t border-slate-100 px-6 py-6 md:grid-cols-2 lg:grid-cols-3">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tìm kiếm ứng viên</label>
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                <input
                                    v-model="filterForm.search"
                                    type="text"
                                    placeholder="Tên hoặc email..."
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-100"
                                />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Công ty</label>
                            <select
                                v-model="filterForm.company_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-sm text-slate-700 focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-100"
                            >
                                <option value="">Tất cả công ty</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">
                                    {{ company.company_name }}
                                </option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Vị trí tuyển dụng</label>
                            <select
                                v-model="filterForm.job_posting_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-sm text-slate-700 focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-100"
                            >
                                <option value="">Tất cả vị trí</option>
                                <option v-for="job in jobPostings" :key="job.id" :value="job.id">
                                    {{ job.title }}
                                    <template v-if="job.company?.company_name">
                                        - {{ job.company.company_name }}
                                    </template>
                                </option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Trạng thái</label>
                            <select
                                v-model="filterForm.status"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-sm text-slate-700 focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-100"
                            >
                                <option value="">Tất cả trạng thái</option>
                                <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                    {{ status.label }}
                                </option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Từ ngày</label>
                            <input
                                v-model="filterForm.date_from"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-sm text-slate-700 focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-100"
                            />
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Đến ngày</label>
                            <input
                                v-model="filterForm.date_to"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-sm text-slate-700 focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-100"
                            />
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="responsive-table-wrapper">
                        <table class="w-full text-sm text-left mobile-card-view">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-6 py-4">Ứng viên</th>
                                    <th class="px-6 py-4">Công ty</th>
                                    <th class="px-6 py-4">Vị trí</th>
                                    <th class="px-6 py-4">Ngày ứng tuyển</th>
                                    <th class="px-6 py-4">Trạng thái</th>
                                    <th class="px-6 py-4 text-right">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr
                                    v-for="app in applications.data"
                                    :key="app.id"
                                    class="hover:bg-slate-50/60 transition-colors"
                                >
                                    <td class="px-6 py-4" data-label="Ứng viên">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-sm font-semibold text-white">
                                                {{ app.candidate.user.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-900">
                                                    {{ app.candidate.user.name }}
                                                </p>
                                                <p class="text-xs text-slate-500">
                                                    {{ app.candidate.user.email }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4" data-label="Công ty">
                                        <p class="font-medium text-slate-900">
                                            {{
                                                app.jobPosting?.company?.company_name
                                                    || 'Chưa cập nhật'
                                            }}
                                        </p>
                                        <p v-if="app.jobPosting?.company?.id" class="text-xs text-slate-500">
                                            ID: {{ app.jobPosting.company.id }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4" data-label="Vị trí">
                                        <p class="font-medium">
                                            {{ app.jobPosting?.title || 'Chưa cập nhật' }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4" data-label="Ngày ứng tuyển">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                            {{ new Date(app.created_at).toLocaleDateString('vi-VN') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4" data-label="Trạng thái">
                                        <select
                                            :value="app.status"
                                            class="rounded-full border border-transparent px-3 py-1 text-xs font-semibold text-white shadow-sm"
                                            :class="getStatusColor(app.status)"
                                            @change="updateStatus(app.id, ($event.target as HTMLSelectElement).value)"
                                        >
                                            <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                                {{ status.label }}
                                            </option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 text-right" data-label="Hành động">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="`/employer/applications/${app.id}`"
                                                class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:bg-slate-50"
                                            >
                                                <Eye class="h-4 w-4" />
                                                Xem
                                            </Link>
                                            <button
                                                @click="deleteApplication(app.id)"
                                                :class="[
                                                    'inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-semibold transition-colors',
                                                    deleteConfirm === app.id
                                                        ? 'bg-red-600 text-white'
                                                        : 'border border-red-200 text-red-600 hover:bg-red-50'
                                                ]"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                                <span v-if="deleteConfirm !== app.id">Xóa</span>
                                                <span v-else>Xác nhận</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="applications.data.length === 0">
                                    <td colspan="6" class="px-6 py-16 text-center text-sm text-slate-500">
                                        Không tìm thấy đơn ứng tuyển nào trùng khớp
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="applications.data.length > 0" class="border-t border-slate-100 bg-slate-50 px-6 py-4">
                        <div class="flex flex-col gap-4 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
                            <p>Trang {{ applications.current_page }} / {{ applications.last_page }}</p>
                            <div class="flex flex-wrap gap-2">
                                <Link
                                    v-for="(link, index) in applications.links"
                                    :key="index"
                                    :href="link.url || '#'"
                                    v-html="link.label"
                                    :class="[
                                        'inline-flex min-w-[40px] items-center justify-center rounded-full px-3 py-1 text-xs font-semibold transition-all',
                                        link.active
                                            ? 'bg-gradient-to-r from-red-600 to-orange-600 text-white shadow-md'
                                            : link.url
                                            ? 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'
                                            : 'bg-slate-100 text-slate-400 cursor-not-allowed'
                                    ]"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
