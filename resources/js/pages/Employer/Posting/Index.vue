<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Eye, Plus } from 'lucide-vue-next';

defineProps<{
    jobs: {
        data: any[];
        links: any[];
    };
}>();

// Xóa tin tuyển dụng
const deleteJob = (id: number) => {
    if (!confirm('Bạn có chắc muốn xóa tin tuyển dụng này không?')) return;
    const form = useForm();
    form.delete(`/employer/posting/${id}`, {
        onSuccess: () => {
            alert('Đã xóa tin tuyển dụng.');
            location.reload(); // hoặc dùng Inertia reload
        },
    });
};

// Ẩn/Hiện tin tuyển dụng
const toggleJob = (job: any) => {
    const form = useForm();
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

            <div v-if="jobs.data.length > 0" class="grid grid-cols-1 gap-4">
                <Card
                    v-for="job in jobs.data"
                    :key="job.id"
                    class="transition hover:shadow-md"
                >
                    <CardHeader>
                        <CardTitle class="text-lg font-medium">{{
                            job.title
                        }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="mb-2 text-gray-600">
                            <strong>Địa điểm:</strong> {{ job.city }},
                            {{ job.province }}
                        </p>
                        <p class="mb-2 text-gray-600">
                            <strong>Mức lương:</strong>
                            <span v-if="job.min_salary || job.max_salary">
                                {{ job.min_salary ?? 0 }} -
                                {{ job.max_salary ?? 0 }} {{ job.salary_type }}
                            </span>
                            <span v-else>Thỏa thuận</span>
                        </p>
                        <div class="mt-4 flex justify-end gap-2">
                            <!-- Xem chi tiết -->
                            <Link :href="`/employer/posting/${job.id}`">
                                <Button
                                    variant="outline"
                                    class="flex items-center gap-2"
                                >
                                    <Eye class="h-4 w-4" /> Xem chi tiết
                                </Button>
                            </Link>

                            <!-- Sửa -->
                            <Link :href="`/employer/posting/${job.id}/edit`">
                                <Button
                                    variant="secondary"
                                    class="flex items-center gap-2"
                                    >✏️ Sửa</Button
                                >
                            </Link>

                            <!-- Ẩn/Hiện -->
                            <Button variant="outline" @click="toggleJob(job)">
                                {{ job.is_active ? 'Ẩn' : 'Hiện' }}
                            </Button>

                            <!-- Xóa -->
                            <Button
                                variant="destructive"
                                @click="deleteJob(job.id)"
                                >Xóa</Button
                            >
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-else class="py-10 text-center text-gray-500">
                Bạn chưa có tin tuyển dụng nào.
                <Link
                    href="/employer/posting/create"
                    class="text-blue-600 underline"
                    >Đăng tin ngay</Link
                >
            </div>
        </div>
    </AppLayout>
</template>
