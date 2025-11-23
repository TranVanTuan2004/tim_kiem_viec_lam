<script setup lang="ts">
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Calendar, Clock, Filter, Trash2 } from 'lucide-vue-next';

interface Interview {
    id: number;
    title: string;
    type: string;
    type_label: string;
    status: string;
    status_label: string;
    scheduled_at: string;
    duration: number;
    candidate: {
        id: number;
        name: string;
        email: string;
    };
    company: {
        id: number;
        name: string;
    };
    job_posting: {
        id: number;
        title: string;
    };
    created_at: string;
}

interface Props {
    interviews: {
        data: Interview[];
        links: any;
        meta: any;
    };
    stats: {
        total: number;
        pending: number;
        confirmed: number;
        rescheduled: number;
        completed: number;
        cancelled: number;
    };
    companies: Array<{ id: number; company_name: string }>;
    filters: {
        status?: string;
        company_id?: number;
        candidate_id?: number;
        date_from?: string;
        date_to?: string;
    };
}

const props = defineProps<Props>();

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-green-100 text-green-800',
    declined: 'bg-red-100 text-red-800',
    rescheduled: 'bg-blue-100 text-blue-800',
    completed: 'bg-gray-100 text-gray-800',
    cancelled: 'bg-gray-100 text-gray-600',
};

const filterStatus = ref(props.filters.status);
const filterCompanyId = ref(props.filters.company_id);
const filterDateFrom = ref(props.filters.date_from);
const filterDateTo = ref(props.filters.date_to);

const applyFilter = () => {
    router.get(route('admin.interviews.index'), {
        status: filterStatus.value,
        company_id: filterCompanyId.value,
        date_from: filterDateFrom.value,
        date_to: filterDateTo.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatDateTime = (dateString: string) => {
    const date = new Date(dateString);
    return {
        date: date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' }),
        time: date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }),
    };
};
</script>

<template>
    <AuthenticatedLayout title="Quản Lý Lịch Phỏng Vấn">
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold">Quản Lý Lịch Phỏng Vấn</h1>
                <p class="text-gray-600 mt-1">Quản lý tất cả lịch phỏng vấn trong hệ thống</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
                <Card>
                    <CardContent class="pt-6">
                        <div class="text-2xl font-bold">{{ stats.total }}</div>
                        <p class="text-sm text-gray-600">Tổng số</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6">
                        <div class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</div>
                        <p class="text-sm text-gray-600">Chờ xác nhận</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6">
                        <div class="text-2xl font-bold text-green-600">{{ stats.confirmed }}</div>
                        <p class="text-sm text-gray-600">Đã xác nhận</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6">
                        <div class="text-2xl font-bold text-blue-600">{{ stats.rescheduled }}</div>
                        <p class="text-sm text-gray-600">Đã đổi lịch</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6">
                        <div class="text-2xl font-bold text-gray-600">{{ stats.completed }}</div>
                        <p class="text-sm text-gray-600">Hoàn thành</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6">
                        <div class="text-2xl font-bold text-gray-500">{{ stats.cancelled }}</div>
                        <p class="text-sm text-gray-600">Đã hủy</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Filters -->
            <Card class="mb-6">
                <CardContent class="pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="text-sm font-medium">Trạng thái</label>
                            <select
                                v-model="filterStatus"
                                class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="">Tất cả</option>
                                <option value="pending">Chờ xác nhận</option>
                                <option value="confirmed">Đã xác nhận</option>
                                <option value="rescheduled">Đã đổi lịch</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Công ty</label>
                            <select
                                v-model="filterCompanyId"
                                class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option :value="undefined">Tất cả</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">
                                    {{ company.company_name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Từ ngày</label>
                            <input
                                v-model="filterDateFrom"
                                type="date"
                                class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Đến ngày</label>
                            <input
                                v-model="filterDateTo"
                                type="date"
                                class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            />
                        </div>
                    </div>
                    <div class="mt-4">
                        <Button @click="applyFilter">
                            <Filter class="h-4 w-4 mr-2" />
                            Lọc
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Interviews Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Danh Sách Lịch Phỏng Vấn</CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="interviews.data.length === 0" class="text-center py-12">
                        <Calendar class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                        <p class="text-gray-600">Không tìm thấy lịch phỏng vấn</p>
                    </div>

                    <!-- Desktop Table View -->
                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left p-4">Tiêu đề</th>
                                    <th class="text-left p-4">Ứng viên</th>
                                    <th class="text-left p-4">Công ty</th>
                                    <th class="text-left p-4">Thời gian</th>
                                    <th class="text-left p-4">Trạng thái</th>
                                    <th class="text-left p-4">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="interview in interviews.data"
                                    :key="interview.id"
                                    class="border-b hover:bg-gray-50 transition-colors"
                                >
                                    <td class="p-4">
                                        <p class="font-medium">{{ interview.title }}</p>
                                        <p class="text-sm text-gray-600">{{ interview.job_posting.title }}</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium">{{ interview.candidate.name }}</p>
                                        <p class="text-sm text-gray-600">{{ interview.candidate.email }}</p>
                                    </td>
                                    <td class="p-4">
                                        <p>{{ interview.company.name }}</p>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2 text-sm">
                                            <Calendar class="h-4 w-4 text-gray-400" />
                                            {{ formatDateTime(interview.scheduled_at).date }}
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-600 mt-1">
                                            <Clock class="h-4 w-4" />
                                            {{ formatDateTime(interview.scheduled_at).time }}
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <Badge :class="statusColors[interview.status]">
                                            {{ interview.status_label }}
                                        </Badge>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex gap-2">
                                            <Link :href="route('admin.interviews.show', interview.id)">
                                                <Button size="sm" variant="outline">Xem</Button>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="interviews.data.length > 0 && interviews.links.length > 3" class="mt-6 flex items-center justify-between border-t pt-4">
                        <div class="text-sm text-gray-600">
                            Hiển thị {{ interviews.meta.from }} - {{ interviews.meta.to }} trong tổng số {{ interviews.meta.total }} lịch phỏng vấn
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="(link, index) in interviews.links"
                                :key="index"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-1 rounded border text-sm',
                                    link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                :disabled="!link.url"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
