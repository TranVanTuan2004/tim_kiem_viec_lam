<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Search, Eye, Trash2, Briefcase } from 'lucide-vue-next';

interface Application {
    id: number;
    status: string;
    created_at: string;
    notes?: string;
    interview_date?: string;
    candidate: {
        id: number;
        avatar?: string;
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
    { value: 'pending', label: 'Chờ xử lý', variant: 'default', class: 'bg-yellow-100 text-yellow-800' },
    { value: 'reviewing', label: 'Đang xem xét', variant: 'default', class: 'bg-blue-100 text-blue-800' },
    { value: 'interview', label: 'Phỏng vấn', variant: 'default', class: 'bg-purple-100 text-purple-800' },
    { value: 'accepted', label: 'Chấp nhận', variant: 'default', class: 'bg-green-100 text-green-800' },
    { value: 'rejected', label: 'Từ chối', variant: 'default', class: 'bg-red-100 text-red-800' },
];

const getStatusBadgeClass = (status: string) => {
    return statusOptions.find(s => s.value === status)?.class || 'bg-gray-100 text-gray-800';
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

const breadcrumbs = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Quản lý ứng tuyển', href: '/admin/applications' }
];
</script>

<template>
    <Head title="Quản lý ứng tuyển - Admin" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 m-[20px]">
            
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
                            <Briefcase class="h-6 w-6 text-primary" />
                        </div>
                        Quản lý ứng tuyển
                    </h1>
                    <p class="text-muted-foreground mt-2 ml-[52px]">
                        Theo dõi và quản lý tất cả đơn ứng tuyển trong hệ thống
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Search class="h-5 w-5" />
                        Bộ lọc
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-4">
                        <Input
                            v-model="filterForm.search"
                            placeholder="Tìm kiếm ứng viên..."
                            @keyup.enter="applyFilters"
                        />
                        <select
                            v-model="filterForm.company_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="">Tất cả công ty</option>
                            <option v-for="company in companies" :key="company.id" :value="company.id">
                                {{ company.company_name }}
                            </option>
                        </select>
                        <select
                            v-model="filterForm.job_posting_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="">Tất cả vị trí</option>
                            <option v-for="job in jobPostings" :key="job.id" :value="job.id">
                                {{ job.title }}
                                <template v-if="job.company?.company_name">
                                    - {{ job.company.company_name }}
                                </template>
                            </option>
                        </select>
                        <select
                            v-model="filterForm.status"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="">Tất cả trạng thái</option>
                            <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                {{ status.label }}
                            </option>
                        </select>
                    </div>
                    <div class="flex gap-2 mt-4">
                        <Button @click="applyFilters" class="flex-1">Tìm</Button>
                        <Button variant="outline" @click="clearFilters">Xóa</Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Applications Table -->
            <div class="bg-white rounded-md shadow overflow-hidden">
                <div class="responsive-table-wrapper">
                    <table class="w-full text-sm text-left mobile-card-view">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3">Ứng viên</th>
                                <th class="px-4 py-3">Công ty</th>
                                <th class="px-4 py-3">Vị trí</th>
                                <th class="px-4 py-3">Ngày ứng tuyển</th>
                                <th class="px-4 py-3">Trạng thái</th>
                                <th class="px-4 py-3 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="app in applications.data"
                                :key="app.id"
                                class="border-b hover:bg-gray-50"
                            >
                                <td class="px-4 py-3 font-medium text-gray-900" data-label="Ứng viên">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center overflow-hidden">
                                            <img
                                                v-if="app.candidate.avatar"
                                                :src="`/storage/${app.candidate.avatar}`"
                                                :alt="app.candidate.user.name"
                                                class="w-full h-full object-cover"
                                            />
                                            <span v-else class="text-sm font-semibold text-white">
                                                {{ app.candidate.user.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-semibold">
                                                {{ app.candidate.user.name }}
                                            </p>
                                            <p class="text-xs text-muted-foreground">
                                                {{ app.candidate.user.email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3" data-label="Công ty">
                                    {{ app.jobPosting?.company?.company_name || 'Chưa cập nhật' }}
                                </td>
                                <td class="px-4 py-3" data-label="Vị trí">
                                    {{ app.jobPosting?.title || 'Chưa cập nhật' }}
                                </td>
                                <td class="px-4 py-3" data-label="Ngày ứng tuyển">
                                    {{ new Date(app.created_at).toLocaleDateString('vi-VN') }}
                                </td>
                                <td class="px-4 py-3" data-label="Trạng thái">
                                    <Badge 
                                        variant="default" 
                                        :class="getStatusBadgeClass(app.status)"
                                    >
                                        {{ getStatusLabel(app.status) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-right" data-label="Hành động">
                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                        <Button 
                                            size="sm" 
                                            variant="outline"
                                            @click="router.get(`/admin/applications/${app.id}`)"
                                        >
                                            <Eye class="h-4 w-4 mr-1" />
                                            Xem
                                        </Button>
                                        <Button 
                                            size="sm" 
                                            :variant="deleteConfirm === app.id ? 'destructive' : 'outline'"
                                            @click="deleteApplication(app.id)"
                                        >
                                            <Trash2 class="h-4 w-4 mr-1" />
                                            {{ deleteConfirm === app.id ? 'Xác nhận' : 'Xóa' }}
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="applications.data.length === 0" class="text-center py-12 text-muted-foreground">
                <Briefcase class="h-16 w-16 mx-auto mb-4 text-muted-foreground/30" />
                <p>Không tìm thấy đơn ứng tuyển nào</p>
            </div>

            <!-- Pagination -->
            <div v-if="applications.last_page > 1" class="flex items-center justify-center gap-2 pt-6 mt-6 border-t">
                <Button
                    :disabled="applications.current_page === 1"
                    variant="outline"
                    size="sm"
                    @click="router.get(`/admin/applications?page=${applications.current_page - 1}`)"
                >
                    Trước
                </Button>
                <span class="text-sm text-muted-foreground">
                    Trang {{ applications.current_page }} / {{ applications.last_page }}
                </span>
                <Button
                    :disabled="applications.current_page === applications.last_page"
                    variant="outline"
                    size="sm"
                    @click="router.get(`/admin/applications?page=${applications.current_page + 1}`)"
                >
                    Sau
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
