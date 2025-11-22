<template>
    <CandidateLayout>
        <Head title="Học vấn" />

        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            Học vấn
                        </h1>
                        <p class="mt-2 text-gray-600">
                            Quản lý thông tin học vấn của bạn
                        </p>
                    </div>
                    <Link
                        :href="create.url()"
                        class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 flex items-center gap-2"
                    >
                        <Plus class="h-5 w-5" />
                        Thêm học vấn
                    </Link>
                </div>

                <div
                    v-if="educations.length === 0"
                    class="rounded-lg border-2 border-dashed border-gray-300 bg-white p-12 text-center"
                >
                    <GraduationCap class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-4 text-lg font-medium text-gray-900">
                        Chưa có thông tin học vấn
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Bắt đầu bằng cách thêm thông tin học vấn đầu tiên của
                        bạn
                    </p>
                    <div class="mt-6">
                        <Link
                            :href="create.url()"
                            class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                        >
                            <Plus class="mr-2 h-4 w-4" />
                            Thêm học vấn
                        </Link>
                    </div>
                </div>

                <div v-else class="bg-white rounded-md shadow overflow-hidden">
                    <div class="responsive-table-wrapper">
                        <table class="w-full text-sm text-left mobile-card-view">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3">Bằng cấp / Chứng chỉ</th>
                                    <th class="px-6 py-3">Trường / Tổ chức</th>
                                    <th class="px-6 py-3">Thời gian</th>
                                    <th class="px-6 py-3">GPA</th>
                                    <th class="px-6 py-3 text-right">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="education in educations"
                                    :key="education.id"
                                    class="bg-white border-b hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4" data-label="Bằng cấp / Chứng chỉ">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 flex-shrink-0">
                                                <GraduationCap class="h-5 w-5 text-green-600" />
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    {{ education.degree }}
                                                </div>
                                                <div v-if="education.field_of_study" class="text-xs text-gray-500">
                                                    {{ education.field_of_study }}
                                                </div>
                                                <Badge
                                                    v-if="!education.is_completed"
                                                    variant="default"
                                                    class="mt-1 bg-blue-100 text-blue-800 text-[10px] px-1.5 py-0.5 h-auto"
                                                >
                                                    Đang học
                                                </Badge>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4" data-label="Trường / Tổ chức">
                                        <div class="font-medium text-gray-900">
                                            {{ education.institution }}
                                        </div>
                                        <div v-if="education.description" class="text-xs text-gray-500 mt-1 line-clamp-2">
                                            {{ education.description }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4" data-label="Thời gian">
                                        <div class="flex items-center text-gray-600">
                                            <Calendar class="mr-1 h-4 w-4" />
                                            {{ education.period }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4" data-label="GPA">
                                        <div v-if="education.gpa" class="flex items-center text-gray-600">
                                            <Award class="mr-1 h-4 w-4" />
                                            {{ education.gpa }}/4.0
                                        </div>
                                        <span v-else class="text-gray-400">-</span>
                                    </td>
                                    <td class="px-6 py-4 text-right" data-label="Hành động">
                                        <div class="flex items-center justify-end gap-2">
                                            <Button
                                                as-child
                                                variant="ghost"
                                                size="sm"
                                                class="h-8 w-8 p-0"
                                            >
                                                <Link :href="edit.url(education.id)">
                                                    <Edit class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                @click="deleteEducation(education.id)"
                                                class="h-8 w-8 p-0 text-red-600 hover:text-red-700 hover:bg-red-50"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </CandidateLayout>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import CandidateLayout from '@/layouts/CandidateLayout.vue';
import {
    create,
    destroy,
    edit,
    index,
} from '@/routes/candidate/educations';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Award,
    Calendar,
    Edit,
    GraduationCap,
    Plus,
    Trash2,
} from 'lucide-vue-next';

interface Props {
    educations: Array<any>;
}

defineProps<Props>();

const deleteEducation = (id: number) => {
    if (confirm('Bạn có chắc chắn muốn xóa thông tin học vấn này không?')) {
        router.delete(destroy(id));
    }
};
</script>

