<template>
    <CandidateLayout>
        <Head title="Kinh nghiệm làm việc" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 shadow-xl">
                    <div class="relative px-8 py-10 sm:px-12">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-white sm:text-4xl">
                                    Kinh nghiệm làm việc
                                </h1>
                                <p class="mt-3 text-lg text-blue-100">
                                    Quản lý quá trình làm việc và phát triển sự nghiệp của bạn
                                </p>
                            </div>
                            <div class="hidden sm:block">
                                <Briefcase class="h-16 w-16 text-white/30" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mb-6 flex justify-end">
                    <Link
                        :href="create.url()"
                        class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none shadow-sm"
                    >
                        <Plus class="mr-2 h-5 w-5" />
                        Thêm kinh nghiệm
                    </Link>
                </div>

                <!-- Content -->
                <div v-if="workExperiences.length === 0" class="rounded-xl border-2 border-dashed border-gray-300 bg-white/50 p-12 text-center shadow-sm">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-50">
                        <Briefcase class="h-8 w-8 text-blue-500" />
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">
                        Chưa có kinh nghiệm làm việc
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">
                        Thêm kinh nghiệm làm việc giúp hồ sơ của bạn ấn tượng hơn với nhà tuyển dụng
                    </p>
                    <div class="mt-6">
                        <Link
                            :href="create.url()"
                            class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 shadow-sm"
                        >
                            <Plus class="mr-2 h-4 w-4" />
                            Thêm kinh nghiệm ngay
                        </Link>
                    </div>
                </div>

                <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="responsive-table-wrapper">
                        <table class="w-full text-sm text-left mobile-card-view">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Vị trí / Công ty</th>
                                    <th class="px-6 py-4 font-semibold">Thời gian</th>
                                    <th class="px-6 py-4 font-semibold">Mô tả</th>
                                    <th class="px-6 py-4 font-semibold text-right">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="experience in workExperiences"
                                    :key="experience.id"
                                    class="bg-white border-b border-gray-50 hover:bg-gray-50/50 transition-colors"
                                >
                                    <td class="px-6 py-4" data-label="Vị trí / Công ty">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 flex-shrink-0 border border-blue-100">
                                                <Briefcase class="h-6 w-6 text-blue-600" />
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900 text-base">
                                                    {{ experience.position }}
                                                </div>
                                                <div class="text-sm text-gray-600 mt-0.5">
                                                    {{ experience.company_name }}
                                                </div>
                                                <Badge
                                                    v-if="experience.is_current"
                                                    variant="default"
                                                    class="mt-2 bg-green-100 text-green-700 hover:bg-green-100 border-green-200 shadow-none font-normal"
                                                >
                                                    Đang làm việc
                                                </Badge>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4" data-label="Thời gian">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center text-gray-700 font-medium">
                                                <Calendar class="mr-2 h-4 w-4 text-gray-400" />
                                                {{ experience.period }}
                                            </div>
                                            <div v-if="experience.duration_formatted" class="flex items-center text-xs text-gray-500">
                                                <Clock class="mr-2 h-3.5 w-3.5 text-gray-400" />
                                                {{ experience.duration_formatted }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4" data-label="Mô tả">
                                        <div v-if="experience.description" class="text-sm text-gray-600 line-clamp-2 max-w-md leading-relaxed">
                                            {{ experience.description }}
                                        </div>
                                        <span v-else class="text-gray-400 italic">Không có mô tả</span>
                                    </td>
                                    <td class="px-6 py-4 text-right" data-label="Hành động">
                                        <div class="flex items-center justify-end gap-2">
                                            <Button
                                                as-child
                                                variant="ghost"
                                                size="icon"
                                                class="h-8 w-8 text-gray-500 hover:text-blue-600 hover:bg-blue-50"
                                            >
                                                <Link :href="edit.url(experience.id)">
                                                    <Edit class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                @click="deleteExperience(experience.id)"
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
} from '@/routes/candidate/work-experiences';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Briefcase,
    Calendar,
    Clock,
    Edit,
    Plus,
    Trash2,
} from 'lucide-vue-next';

interface Props {
    workExperiences: Array<any>;
}

defineProps<Props>();

const deleteExperience = (id: number) => {
    if (
        confirm(
            'Bạn có chắc chắn muốn xóa kinh nghiệm làm việc này không?',
        )
    ) {
        router.delete(destroy(id));
    }
};
</script>

