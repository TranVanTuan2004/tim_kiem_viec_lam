<template>
    <ClientLayout>
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

                <div v-else class="space-y-4">
                    <div
                        v-for="education in educations"
                        :key="education.id"
                        class="group rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100"
                                    >
                                        <GraduationCap
                                            class="h-6 w-6 text-green-600"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h3
                                                class="text-xl font-semibold text-gray-900"
                                            >
                                                {{ education.degree }}
                                            </h3>
                                            <Badge
                                                v-if="!education.is_completed"
                                                variant="default"
                                                class="bg-blue-100 text-blue-800"
                                            >
                                                Đang học
                                            </Badge>
                                        </div>
                                        <p
                                            class="mt-1 text-lg font-medium text-gray-700"
                                        >
                                            {{ education.institution }}
                                        </p>
                                        <p
                                            v-if="education.field_of_study"
                                            class="mt-1 text-sm text-gray-600"
                                        >
                                            {{ education.field_of_study }}
                                        </p>
                                        <div
                                            class="mt-3 flex flex-wrap items-center gap-4 text-sm text-gray-600"
                                        >
                                            <div class="flex items-center gap-1">
                                                <Calendar class="h-4 w-4" />
                                                <span>{{ education.period }}</span>
                                            </div>
                                            <div
                                                v-if="education.gpa"
                                                class="flex items-center gap-1"
                                            >
                                                <Award class="h-4 w-4" />
                                                <span>GPA: {{ education.gpa }}/4.0</span>
                                            </div>
                                        </div>
                                        <p
                                            v-if="education.description"
                                            class="mt-4 text-sm leading-relaxed text-gray-700"
                                        >
                                            {{ education.description }}
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
                                    <Link :href="edit.url(education.id)">
                                        <Edit class="h-4 w-4" />
                                    </Link>
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    @click="deleteEducation(education.id)"
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
        router.delete(destroy(id).url());
    }
};
</script>
