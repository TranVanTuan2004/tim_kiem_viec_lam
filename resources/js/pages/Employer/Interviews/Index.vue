<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Calendar, Clock, MapPin, Plus, Filter, ChevronLeft, ChevronRight, FileText, Trash2 } from 'lucide-vue-next';

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
    candidate: {
        id: number;
        name: string;
        email: string;
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
        upcoming: number;
        pending: number;
        confirmed: number;
    };
    filters: {
        status: string;
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
const showDeleteModal = ref(false);
const interviewToDelete = ref<number | null>(null);

const applyFilter = () => {
    router.get(route('employer.interviews.index'), {
        status: filterStatus.value,
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

const deleteInterview = (id: number) => {
    interviewToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (interviewToDelete.value) {
        router.delete(route('employer.interviews.destroy', interviewToDelete.value), {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteModal.value = false;
                interviewToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <AuthenticatedLayout title="Quản Lý Lịch Phỏng Vấn">
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-6">
                <div>
                    <h1 class="text-3xl font-bold">Lịch Phỏng Vấn</h1>
                    <p class="text-gray-600 mt-1">Quản lý tất cả các buổi phỏng vấn</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <Card>
                    <CardContent class="pt-6">
                        <div class="text-2xl font-bold">{{ stats.total }}</div>
                        <p class="text-sm text-gray-600">Tổng số</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6">
                        <div class="text-2xl font-bold text-blue-600">{{ stats.upcoming }}</div>
                        <p class="text-sm text-gray-600">Sắp diễn ra</p>
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
            </div>

            <!-- Filters -->
            <Card class="mb-6">
                <CardContent class="pt-6">
                    <div class="flex items-center gap-4">
                        <Filter class="h-5 w-5 text-gray-400" />
                        <select
                            v-model="filterStatus"
                            @change="applyFilter"
                            class="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="all">Tất cả trạng thái</option>
                            <option value="pending">Chờ xác nhận</option>
                            <option value="confirmed">Đã xác nhận</option>
                            <option value="declined">Đã từ chối</option>
                            <option value="rescheduled">Đã đổi lịch</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                </CardContent>
            </Card>

            <!-- Interviews Table/Cards -->
            <Card>
                <CardHeader>
                    <CardTitle>Danh Sách Lịch Phỏng Vấn</CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="interviews.data.length === 0" class="text-center py-12">
                        <Calendar class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                        <p class="text-gray-600 mb-2">Chưa có lịch phỏng vấn nào</p>
                        <p class="text-sm text-gray-500 mb-4">Tạo lịch phỏng vấn từ trang chi tiết ứng tuyển</p>
                        <Link :href="'/admin/applications'">
                            <Button>
                                <FileText class="h-4 w-4 mr-2" />
                                Xem Danh Sách Ứng Tuyển
                            </Button>
                        </Link>
                    </div>

                    <!-- Desktop Table View -->
                    <div v-else class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left p-4">Ứng Viên</th>
                                    <th class="text-left p-4">Vị Trí</th>
                                    <th class="text-left p-4">Thời Gian</th>
                                    <th class="text-left p-4">Hình Thức</th>
                                    <th class="text-left p-4">Trạng Thái</th>
                                    <th class="text-left p-4">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="interview in interviews.data"
                                    :key="interview.id"
                                    class="border-b hover:bg-gray-50 transition-colors"
                                >
                                    <td class="p-4">
                                        <div>
                                            <p class="font-medium">{{ interview.candidate.name }}</p>
                                            <p class="text-sm text-gray-600">{{ interview.candidate.email }}</p>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="text-sm">{{ interview.job_posting.title }}</p>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2 text-sm">
                                            <Calendar class="h-4 w-4 text-gray-400" />
                                            {{ formatDateTime(interview.scheduled_at).date }}
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-600 mt-1">
                                            <Clock class="h-4 w-4" />
                                            {{ formatDateTime(interview.scheduled_at).time }} ({{ interview.duration }}p)
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <Badge variant="outline">{{ interview.type_label }}</Badge>
                                    </td>
                                    <td class="p-4">
                                        <Badge :class="statusColors[interview.status]">
                                            {{ interview.status_label }}
                                        </Badge>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex gap-2">
                                            <Link :href="route('employer.interviews.show', interview.id)">
                                                <Button size="sm" variant="outline">Xem Chi Tiết</Button>
                                            </Link>
                                            <Button 
                                                size="sm" 
                                                variant="destructive" 
                                                @click="deleteInterview(interview.id)"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div v-if="interviews.data.length > 0" class="md:hidden space-y-4">
                        <Card
                            v-for="interview in interviews.data"
                            :key="interview.id"
                            class="hover:shadow-md transition-shadow"
                        >
                            <CardContent class="pt-6">
                                <div class="space-y-3">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="font-semibold">{{ interview.candidate.name }}</p>
                                            <p class="text-sm text-gray-600">{{ interview.candidate.email }}</p>
                                        </div>
                                        <Badge :class="statusColors[interview.status]" class="ml-2">
                                            {{ interview.status_label }}
                                        </Badge>
                                    </div>

                                    <div class="text-sm text-gray-700">
                                        <p class="font-medium">{{ interview.job_posting.title }}</p>
                                    </div>

                                    <div class="flex items-center gap-4 text-sm text-gray-600">
                                        <div class="flex items-center gap-1">
                                            <Calendar class="h-4 w-4" />
                                            {{ formatDateTime(interview.scheduled_at).date }}
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <Clock class="h-4 w-4" />
                                            {{ formatDateTime(interview.scheduled_at).time }}
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-2">
                                        <Badge variant="outline">{{ interview.type_label }}</Badge>
                                        <div class="flex gap-2">
                                            <Link :href="route('employer.interviews.show', interview.id)">
                                                <Button size="sm">Xem Chi Tiết</Button>
                                            </Link>
                                            <Button 
                                                size="sm" 
                                                variant="destructive" 
                                                @click="deleteInterview(interview.id)"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
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

        <!-- Delete Confirmation Modal -->
        <Dialog v-model:open="showDeleteModal">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Xác nhận hủy lịch</DialogTitle>
                    <DialogDescription>
                        Bạn có chắc chắn muốn hủy lịch phỏng vấn này không? Hành động này không thể hoàn tác.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showDeleteModal = false">Hủy</Button>
                    <Button variant="destructive" @click="confirmDelete">Xác nhận hủy</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>
