<template>
    <ClientLayout>
        <Head title="Kinh nghiệm làm việc" />

        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            Kinh nghiệm làm việc
                        </h1>
                        <p class="mt-2 text-gray-600">
                            Quản lý kinh nghiệm làm việc của bạn
                        </p>
                    </div>
                    <Link
                        :href="create.url()"
                        class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 flex items-center gap-2"
                    >
                        <Plus class="h-5 w-5" />
                        Thêm kinh nghiệm
                    </Link>
                </div>

                <div v-if="workExperiences.length === 0" class="rounded-lg border-2 border-dashed border-gray-300 bg-white p-12 text-center">
                    <Briefcase class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-4 text-lg font-medium text-gray-900">
                        Chưa có kinh nghiệm làm việc
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Bắt đầu bằng cách thêm kinh nghiệm làm việc đầu tiên của bạn
                    </p>
                    <div class="mt-6">
                        <Link
                            :href="create.url()"
                            class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                        >
                            <Plus class="mr-2 h-4 w-4" />
                            Thêm kinh nghiệm
                        </Link>
                    </div>
                </div>

                <div v-else class="bg-white rounded-md shadow overflow-hidden">
                    <div class="responsive-table-wrapper">
                        <table class="w-full text-sm text-left mobile-card-view">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3">Vị trí / Công ty</th>
                                    <th class="px-6 py-3">Thời gian</th>
                                    <th class="px-6 py-3">Mô tả</th>
                                    <th class="px-6 py-3 text-right">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="experience in workExperiences"
                                    :key="experience.id"
                                    class="bg-white border-b hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4" data-label="Vị trí / Công ty">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 flex-shrink-0">
                                                <Briefcase class="h-5 w-5 text-blue-600" />
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    {{ experience.position }}
                                                </div>
                                                <div class="text-sm text-gray-600">
                                                    {{ experience.company_name }}
                                                </div>
                                                <Badge
                                                    v-if="experience.is_current"
                                                    variant="default"
                                                    class="mt-1 bg-green-100 text-green-800 text-[10px] px-1.5 py-0.5 h-auto"
                                                >
                                                    Đang làm việc
                                                </Badge>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4" data-label="Thời gian">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center text-gray-600">
                                                <Calendar class="mr-1 h-4 w-4" />
                                                {{ experience.period }}
                                            </div>
                                            <div v-if="experience.duration_formatted" class="flex items-center text-xs text-gray-500">
                                                <Clock class="mr-1 h-3 w-3" />
                                                {{ experience.duration_formatted }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4" data-label="Mô tả">
                                        <div v-if="experience.description" class="text-sm text-gray-600 line-clamp-2 max-w-xs">
                                            {{ experience.description }}
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
                                                <Link :href="edit.url(experience.id)">
                                                    <Edit class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                @click="deleteExperience(experience.id)"
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
    </ClientLayout>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import ClientLayout from '@/layouts/ClientLayout.vue';
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
