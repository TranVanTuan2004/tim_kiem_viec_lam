<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { throttle } from 'lodash'; // THÊM: throttle (Cần cài đặt)
import { Eye, Plus, Users, AlertCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue'; // THÊM: ref, watch
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
// defineProps<{
//     jobs: {
//         data: any[];
//         links: any[];
//     };
// }>();
// CẬP NHẬT: Thêm filters vào Props
const props = defineProps<{
    jobs: {
        data: any[];
        links: any[];
    };
    filters: {
        // THÊM: Nhận prop filters (giả định Controller đã truyền)
        status: string;
        search: string | null;
    };
}>();

// === 1. BIẾN TRẠNG THÁI LỌC VÀ LOGIC TÌM KIẾM ===
const search = ref(props.filters.search ?? '');

const statusFilters = [
    { label: 'Tất cả', value: 'all' },
    { label: 'Đang hoạt động', value: 'active' },
    { label: 'Đang ẩn', value: 'inactive' },
    { label: 'Chờ duyệt', value: 'pending' },
    { label: 'Đã duyệt', value: 'approved' },
];

// Modal state for rejection reason
const showRejectionModal = ref(false);
const selectedRejectedJob = ref<any>(null);

function showRejectionReason(job: any) {
    selectedRejectedJob.value = job;
    showRejectionModal.value = true;
}

// === 2. LOGIC LỌC (WATCH + THROTTLE) ===
watch(
    search,
    throttle((value) => {
        // Sử dụng router.get với URL cứng và tham số search, giữ status hiện tại
        router.get(
            '/employer/posting', // <--- ROUTE TRỰC TIẾP
            {
                search: value,
                status: props.filters.status,
            },
            { preserveState: true, replace: true },
        );
    }, 300),
);

// Xóa tin tuyển dụng
const deleteJob = (id: number) => {
    if (!confirm('Bạn có chắc muốn xóa tin tuyển dụng này không?')) return;
    const form = useForm({});
    form.delete(`/employer/posting/${id}`, {
        onSuccess: () => {
            alert('Đã xóa tin tuyển dụng.');
            location.reload(); // hoặc dùng Inertia reload
        },
    });
};

// Ẩn/Hiện tin tuyển dụng
const toggleJob = (job: any) => {
    const form = useForm({});
    form.patch(`/employer/posting/${job.id}/toggle`, {
        onSuccess: () => {
            alert(`Tin tuyển dụng đã được ${job.is_active ? 'ẩn' : 'hiện'}.`);
            location.reload();
        },
    });
};
</script>

<template>
    <Head title="Danh sách tin tuyển dụng" />

    <AppLayout>
        <div class="space-y-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Tin tuyển dụng của bạn</h1>
                <Link href="/employer/posting/create">
                    <Button class="flex items-center gap-2">
                        <Plus class="h-4 w-4" /> Đăng tin mới
                    </Button>
                </Link>
            </div>

            <div
                class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
            >
                <input
                    type="text"
                    v-model="search"
                    placeholder="Tìm kiếm theo tiêu đề..."
                    class="rounded-lg border border-gray-300 p-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 md:w-1/3"
                />

                <div class="flex space-x-2 overflow-x-auto">
                    <Link
                        v-for="filter in statusFilters"
                        :key="filter.value"
                        :href="`/employer/posting?status=${filter.value}&search=${props.filters.search ?? ''}`"
                        preserve-scroll
                    >
                        <Button
                            :variant="
                                filter.value === props.filters.status
                                    ? 'default'
                                    : 'outline'
                            "
                            class="whitespace-nowrap"
                        >
                            {{ filter.label }}
                        </Button>
                    </Link>
                </div>
            </div>
            <div v-if="jobs.data.length > 0" class="bg-white rounded-md shadow overflow-hidden">
                <div class="responsive-table-wrapper">
                    <table class="w-full text-sm text-left mobile-card-view">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3">Tiêu đề</th>
                                <th class="px-4 py-3">Trạng thái duyệt</th>
                                <th class="px-4 py-3">Trạng thái hiển thị</th>
                                <th class="px-4 py-3 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="job in jobs.data" :key="job.id" class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900" data-label="Tiêu đề">
                                    {{ job.title }}
                                </td>
                                <td class="px-4 py-3" data-label="Trạng thái duyệt">
                                    <span
                                        :class="{
                                            'bg-green-100 text-green-600': job.status === 'approved',
                                            'bg-yellow-100 text-yellow-600': job.status === 'pending',
                                            'bg-red-100 text-red-600 cursor-pointer hover:bg-red-200': job.status === 'rejected',
                                        }"
                                        class="rounded-full px-3 py-1 text-xs font-semibold inline-flex items-center gap-1"
                                        @click="job.status === 'rejected' ? showRejectionReason(job) : null"
                                        :title="job.status === 'rejected' ? 'Click để xem lý do từ chối' : ''"
                                    >
                                        <AlertCircle v-if="job.status === 'rejected'" class="h-3 w-3" />
                                        {{
                                            job.status === 'approved'
                                                ? 'Đã duyệt'
                                                : job.status === 'pending'
                                                  ? 'Chờ duyệt'
                                                  : 'Bị từ chối'
                                        }}
                                    </span>
                                </td>
                                <td class="px-4 py-3" data-label="Trạng thái hiển thị">
                                    <span
                                        :class="job.is_active ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600'"
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                    >
                                        {{ job.is_active ? 'Đang hiện' : 'Đang ẩn' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right" data-label="Hành động">
                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                        <Link :href="`/employer/posting/${job.id}`">
                                            <Button variant="outline" size="sm" class="flex items-center gap-1">
                                                <Eye class="h-3 w-3" /> Xem
                                            </Button>
                                        </Link>

                                        <Link :href="`/employer/applications?job_posting_id=${job.id}`">
                                            <Button variant="outline" size="sm" class="flex items-center gap-1">
                                                <Users class="h-3 w-3" /> Ứng viên
                                            </Button>
                                        </Link>

                                        <Link :href="`/employer/posting/${job.id}/edit`">
                                            <Button variant="secondary" size="sm" class="flex items-center gap-1">
                                                ✏️ Sửa
                                            </Button>
                                        </Link>

                                        <Button
                                            :variant="job.is_active ? 'secondary' : 'default'"
                                            size="sm"
                                            @click="toggleJob(job)"
                                            :class="job.is_active ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : ''"
                                        >
                                            {{ job.is_active ? 'Ẩn' : 'Hiện' }}
                                        </Button>

                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="deleteJob(job.id)"
                                        >
                                            Xóa
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-else class="py-10 text-center text-gray-500">
                Bạn chưa có tin tuyển dụng nào phù hợp với bộ lọc hiện tại.
                <Link
                    href="/employer/posting/create"
                    class="text-blue-600 underline"
                    >Đăng tin ngay</Link
                >
            </div>

            <div v-if="jobs.links.length > 3" class="mt-6 flex justify-center">
                <nav class="flex space-x-1">
                    <Link
                        v-for="(link, key) in jobs.links"
                        :key="key"
                        :href="link.url ?? '#'"
                        v-html="link.label"
                        class="rounded-md px-3 py-2 text-sm leading-4 focus:outline-none"
                        :class="{
                            'bg-blue-500 text-white': link.active,
                            'text-gray-600 hover:bg-gray-100':
                                !link.active && link.url,
                            'cursor-default text-gray-400': !link.url,
                        }"
                        preserve-scroll
                    />
                </nav>
            </div>
        </div>

        <!-- Rejection Reason Modal -->
        <Dialog :open="showRejectionModal" @update:open="showRejectionModal = $event">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <AlertCircle class="h-6 w-6 text-red-600" />
                    </div>
                    <DialogTitle class="text-center text-xl text-red-600">Tin tuyển dụng bị từ chối</DialogTitle>
                    <DialogDescription class="text-center">
                        Tin tuyển dụng của bạn đã bị từ chối bởi quản trị viên. Vui lòng xem lý do và chỉnh sửa lại.
                    </DialogDescription>
                </DialogHeader>

                <div v-if="selectedRejectedJob" class="space-y-4 py-4">
                    <!-- Job Title -->
                    <div class="border-b pb-3">
                        <h3 class="font-semibold text-gray-900">{{ selectedRejectedJob.title }}</h3>
                    </div>

                    <!-- Rejection Reason -->
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                        <h4 class="font-semibold text-red-800 mb-2 flex items-center gap-2">
                            <AlertCircle class="h-4 w-4" />
                            Lý do từ chối:
                        </h4>
                        <p class="text-red-700 text-sm">
                            {{ selectedRejectedJob.rejection_reason || 'Không có lý do cụ thể được cung cấp.' }}
                        </p>
                    </div>

                    <!-- Rejection Time -->
                    <div class="text-sm text-gray-600">
                        <span class="font-semibold">Thời gian từ chối:</span>
                        {{ selectedRejectedJob.updated_at ? new Date(selectedRejectedJob.updated_at).toLocaleString('vi-VN') : 'N/A' }}
                    </div>

                    <!-- Edit Suggestions -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <h4 class="font-semibold text-blue-800 mb-2">💡 Gợi ý chỉnh sửa:</h4>
                        <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                            <li>Đọc kỹ lý do từ chối và chỉnh sửa nội dung phù hợp</li>
                            <li>Đảm bảo thông tin chính xác, rõ ràng và không vi phạm chính sách</li>
                            <li>Kiểm tra lại yêu cầu công việc và mức lương hợp lý</li>
                            <li>Sau khi chỉnh sửa, tin sẽ được gửi lại để duyệt</li>
                        </ul>
                    </div>
                </div>

                <DialogFooter class="sm:justify-center gap-2">
                    <Button variant="outline" @click="showRejectionModal = false">
                        Đóng
                    </Button>
                    <Link 
                        v-if="selectedRejectedJob" 
                        :href="`/employer/posting/${selectedRejectedJob.id}/edit`"
                    >
                        <Button class="bg-blue-600 hover:bg-blue-700">
                            Chỉnh sửa ngay
                        </Button>
                    </Link>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
