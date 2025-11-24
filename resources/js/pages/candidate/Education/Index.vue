<template>
    <CandidateLayout>
        <Head title="Học vấn" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700 shadow-xl">
                    <div class="relative px-8 py-10 sm:px-12">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-white sm:text-4xl">
                                    Học vấn
                                </h1>
                                <p class="mt-3 text-lg text-emerald-100">
                                    Quản lý bằng cấp, chứng chỉ và quá trình học tập của bạn
                                </p>
                            </div>
                            <div class="hidden sm:block">
                                <GraduationCap class="h-16 w-16 text-white/30" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mb-6 flex justify-end">
                    <Link
                        :href="create.url()"
                        class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:outline-none shadow-sm"
                    >
                        <Plus class="mr-2 h-5 w-5" />
                        Thêm học vấn
                    </Link>
                </div>

                <!-- Content -->
                <div v-if="educations.length === 0" class="rounded-xl border-2 border-dashed border-gray-300 bg-white/50 p-12 text-center shadow-sm">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50">
                        <GraduationCap class="h-8 w-8 text-emerald-500" />
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">
                        Chưa có thông tin học vấn
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">
                        Thêm thông tin học vấn giúp nhà tuyển dụng đánh giá đúng năng lực của bạn
                    </p>
                    <div class="mt-6">
                        <Link
                            :href="create.url()"
                            class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 shadow-sm"
                        >
                            <Plus class="mr-2 h-4 w-4" />
                            Thêm học vấn ngay
                        </Link>
                    </div>
                </div>

                <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="responsive-table-wrapper">
                        <table class="w-full text-sm text-left mobile-card-view">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Bằng cấp / Chứng chỉ</th>
                                    <th class="px-6 py-4 font-semibold">Trường / Tổ chức</th>
                                    <th class="px-6 py-4 font-semibold">Thời gian</th>
                                    <th class="px-6 py-4 font-semibold">GPA</th>
                                    <th class="px-6 py-4 font-semibold text-right">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="education in educations"
                                    :key="education.id"
                                    class="bg-white border-b border-gray-50 hover:bg-gray-50/50 transition-colors"
                                >
                                    <td class="px-6 py-4" data-label="Bằng cấp / Chứng chỉ">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 flex-shrink-0 border border-emerald-100">
                                                <GraduationCap class="h-6 w-6 text-emerald-600" />
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900 text-base">
                                                    {{ education.degree }}
                                                </div>
                                                <div v-if="education.field_of_study" class="text-sm text-gray-600 mt-0.5">
                                                    {{ education.field_of_study }}
                                                </div>
                                                <Badge
                                                    v-if="!education.is_completed"
                                                    variant="default"
                                                    class="mt-2 bg-blue-100 text-blue-700 hover:bg-blue-100 border-blue-200 shadow-none font-normal"
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
                                        <div v-if="education.description" class="text-sm text-gray-500 mt-1 line-clamp-2 max-w-xs">
                                            {{ education.description }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4" data-label="Thời gian">
                                        <div class="flex items-center text-gray-700 font-medium">
                                            <Calendar class="mr-2 h-4 w-4 text-gray-400" />
                                            {{ education.period }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4" data-label="GPA">
                                        <div v-if="education.gpa" class="flex items-center text-gray-700 font-medium">
                                            <Award class="mr-2 h-4 w-4 text-yellow-500" />
                                            {{ education.gpa }}/4.0
                                        </div>
                                        <span v-else class="text-gray-400 italic">-</span>
                                    </td>
                                    <td class="px-6 py-4 text-right" data-label="Hành động">
                                        <div class="flex items-center justify-end gap-2">
                                            <Button
                                                as-child
                                                variant="ghost"
                                                size="icon"
                                                class="h-8 w-8 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50"
                                            >
                                                <Link :href="edit.url(education.id)">
                                                    <Edit class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                @click="deleteEducation(education.id)"
                                                class="h-8 w-8 text-gray-500 hover:text-red-600 hover:bg-red-50"
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

