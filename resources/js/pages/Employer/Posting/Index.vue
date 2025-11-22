<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { throttle } from 'lodash'; // THÊM: throttle (Cần cài đặt)
import { Eye, Plus, Users } from 'lucide-vue-next';
import { ref, watch } from 'vue'; // THÊM: ref, watch
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
                                            'bg-red-100 text-red-600': job.status === 'rejected',
                                        }"
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                    >
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
    </AppLayout>
</template>
