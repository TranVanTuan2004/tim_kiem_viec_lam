<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import CandidateLayout from '@/layouts/CandidateLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { 
    Calendar, 
    Clock, 
    MapPin, 
    Video, 
    Phone, 
    Building2, 
    Briefcase, 
    Search,
    MessageSquare
} from 'lucide-vue-next';
import { reactive } from 'vue';

interface Interview {
    id: number;
    title: string;
    type: string;
    type_label: string;
    status: string;
    status_label: string;
    scheduled_at: string;
    duration: number;
    location: string;
    company: {
        name: string;
        logo: string | null;
    };
    job_posting: {
        title: string;
    };
}

interface Props {
    upcoming: Interview[];
    past: Interview[];
    filters: {
        status: string;
    };
}

const props = defineProps<Props>();

const localFilters = reactive({ ...props.filters });

const getStatusBadgeClass = (status: string) => {
    const classes: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
        confirmed: 'bg-green-100 text-green-800 border-green-200',
        declined: 'bg-red-100 text-red-800 border-red-200',
        completed: 'bg-blue-100 text-blue-800 border-blue-200',
        cancelled: 'bg-gray-100 text-gray-800 border-gray-200',
        rescheduled: 'bg-purple-100 text-purple-800 border-purple-200',
    };
    return classes[status] || 'bg-gray-100 text-gray-800 border-gray-200';
};

const getTypeIcon = (type: string) => {
    if (type === 'video') return Video;
    if (type === 'phone') return Phone;
    return Building2;
};

const formatDateTime = (dateString: string) => {
    const date = new Date(dateString);
    return {
        date: date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' }),
        time: date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }),
        full: date.toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }),
    };
};

const applyFilters = () => {
    router.get('/candidate/interviews', localFilters, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <CandidateLayout>
        <Head title="Lịch Phỏng Vấn" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 shadow-xl">
                    <div class="relative px-8 py-10 sm:px-12">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-white sm:text-4xl">
                                    Lịch Phỏng Vấn
                                </h1>
                                <p class="mt-3 text-lg text-blue-100">
                                    Quản lý và theo dõi các buổi phỏng vấn của bạn
                                </p>
                            </div>
                            <div class="hidden sm:block">
                                <MessageSquare class="h-16 w-16 text-white/30" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <Card class="mb-6">
                    <CardHeader>
                        <CardTitle class="text-lg">Bộ lọc</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Trạng thái
                                </label>
                                <select
                                    v-model="localFilters.status"
                                    @change="applyFilters"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="all">Tất cả trạng thái</option>
                                    <option value="pending">Chờ xác nhận</option>
                                    <option value="confirmed">Đã xác nhận</option>
                                    <option value="completed">Đã hoàn thành</option>
                                    <option value="cancelled">Đã hủy</option>
                                    <option value="declined">Đã từ chối</option>
                                </select>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Upcoming Interviews -->
                <div v-if="upcoming.length > 0" class="mb-8">
                    <h2 class="mb-4 text-xl font-bold text-gray-900 flex items-center gap-2">
                        <Clock class="h-5 w-5 text-blue-600" />
                        Sắp Diễn Ra
                    </h2>
                    <div class="bg-white rounded-md shadow overflow-hidden">
                        <div class="responsive-table-wrapper">
                            <table class="w-full text-sm text-left mobile-card-view">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-6 py-3">Công ty / Vị trí</th>
                                        <th class="px-6 py-3">Thời gian</th>
                                        <th class="px-6 py-3">Hình thức</th>
                                        <th class="px-6 py-3">Trạng thái</th>
                                        <th class="px-6 py-3 text-right">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="interview in upcoming" :key="interview.id" class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4" data-label="Công ty / Vị trí">
                                            <div class="flex items-center gap-3">
                                                <img
                                                    v-if="interview.company.logo"
                                                    :src="interview.company.logo"
                                                    :alt="interview.company.name"
                                                    class="h-10 w-10 rounded-lg border border-gray-200 object-cover"
                                                />
                                                <div v-else class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100">
                                                    <Building2 class="h-5 w-5 text-gray-400" />
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ interview.job_posting.title }}</div>
                                                    <div class="text-xs text-gray-500">{{ interview.company.name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4" data-label="Thời gian">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-900">{{ formatDateTime(interview.scheduled_at).date }}</span>
                                                <span class="text-gray-500">{{ formatDateTime(interview.scheduled_at).time }} ({{ interview.duration }} phút)</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4" data-label="Hình thức">
                                            <div class="flex items-center gap-2">
                                                <component :is="getTypeIcon(interview.type)" class="h-4 w-4 text-gray-500" />
                                                <span>{{ interview.type_label }}</span>
                                            </div>
                                            <div v-if="interview.location" class="text-xs text-gray-500 mt-1 truncate max-w-[200px]" :title="interview.location">
                                                {{ interview.location }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4" data-label="Trạng thái">
                                            <Badge :class="getStatusBadgeClass(interview.status)">
                                                {{ interview.status_label }}
                                            </Badge>
                                        </td>
                                        <td class="px-6 py-4 text-right" data-label="Hành động">
                                            <Link :href="`/candidate/interviews/${interview.id}`">
                                                <Button variant="outline" size="sm">Xem Chi Tiết</Button>
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Past Interviews -->
                <div v-if="past.length > 0">
                    <h2 class="mb-4 text-xl font-bold text-gray-900 flex items-center gap-2">
                        <Calendar class="h-5 w-5 text-gray-600" />
                        Đã Diễn Ra
                    </h2>
                    <div class="bg-white rounded-md shadow overflow-hidden">
                        <div class="responsive-table-wrapper">
                            <table class="w-full text-sm text-left mobile-card-view">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-6 py-3">Công ty / Vị trí</th>
                                        <th class="px-6 py-3">Thời gian</th>
                                        <th class="px-6 py-3">Hình thức</th>
                                        <th class="px-6 py-3">Trạng thái</th>
                                        <th class="px-6 py-3 text-right">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="interview in past" :key="interview.id" class="bg-white border-b hover:bg-gray-50 opacity-75">
                                        <td class="px-6 py-4" data-label="Công ty / Vị trí">
                                            <div class="flex items-center gap-3">
                                                <img
                                                    v-if="interview.company.logo"
                                                    :src="interview.company.logo"
                                                    :alt="interview.company.name"
                                                    class="h-10 w-10 rounded-lg border border-gray-200 object-cover"
                                                />
                                                <div v-else class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100">
                                                    <Building2 class="h-5 w-5 text-gray-400" />
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ interview.job_posting.title }}</div>
                                                    <div class="text-xs text-gray-500">{{ interview.company.name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4" data-label="Thời gian">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-900">{{ formatDateTime(interview.scheduled_at).date }}</span>
                                                <span class="text-gray-500">{{ formatDateTime(interview.scheduled_at).time }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4" data-label="Hình thức">
                                            <div class="flex items-center gap-2">
                                                <component :is="getTypeIcon(interview.type)" class="h-4 w-4 text-gray-500" />
                                                <span>{{ interview.type_label }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4" data-label="Trạng thái">
                                            <Badge :class="getStatusBadgeClass(interview.status)">
                                                {{ interview.status_label }}
                                            </Badge>
                                        </td>
                                        <td class="px-6 py-4 text-right" data-label="Hành động">
                                            <Link :href="`/candidate/interviews/${interview.id}`">
                                                <Button variant="outline" size="sm">Xem Chi Tiết</Button>
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <Card v-if="upcoming.length === 0 && past.length === 0">
                    <CardContent class="py-16 text-center">
                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-blue-100">
                            <MessageSquare class="h-10 w-10 text-blue-600" />
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">
                            Chưa có lịch phỏng vấn nào
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Bạn sẽ nhận được thông báo khi nhà tuyển dụng mời phỏng vấn!
                        </p>
                        <div class="mt-6">
                            <Link
                                href="/jobs"
                                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                            >
                                <Search class="mr-2 h-4 w-4" />
                                Tìm việc làm
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </CandidateLayout>
</template>
