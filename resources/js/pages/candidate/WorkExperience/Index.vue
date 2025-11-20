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

                <div v-else class="space-y-4">
                    <div
                        v-for="experience in workExperiences"
                        :key="experience.id"
                        class="group rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                                        <Briefcase class="h-6 w-6 text-blue-600" />
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-xl font-semibold text-gray-900">
                                                {{ experience.position }}
                                            </h3>
                                            <Badge
                                                v-if="experience.is_current"
                                                variant="default"
                                                class="bg-green-100 text-green-800"
                                            >
                                                Đang làm việc
                                            </Badge>
                                        </div>
                                        <p class="mt-1 text-lg font-medium text-gray-700">
                                            {{ experience.company_name }}
                                        </p>
                                        <div class="mt-3 flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                            <div class="flex items-center gap-1">
                                                <Calendar class="h-4 w-4" />
                                                <span>{{ experience.period }}</span>
                                            </div>
                                            <div v-if="experience.duration_formatted" class="flex items-center gap-1">
                                                <Clock class="h-4 w-4" />
                                                <span>{{ experience.duration_formatted }}</span>
                                            </div>
                                        </div>
                                        <p
                                            v-if="experience.description"
                                            class="mt-4 text-sm leading-relaxed text-gray-700"
                                        >
                                            {{ experience.description }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4 flex gap-2">
                                <Button
                                    as-child
                                    variant="ghost"
                                    size="sm"
                                >
                                    <Link :href="edit.url(experience.id)">
                                        <Edit class="h-4 w-4" />
                                    </Link>
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    @click="deleteExperience(experience.id)"
                                    class="text-red-600 hover:text-red-700"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
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
        router.delete(destroy(id).url());
    }
};
</script>
