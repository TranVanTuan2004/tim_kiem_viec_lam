<template>
    <AuthenticatedLayout>
        <Head title="My Education" />

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">
                                    My Education
                                </h1>
                                <p class="mt-2 text-gray-600">
                                    Manage your educational background
                                </p>
                            </div>
                            <Link
                                :href="route('candidate.educations.create')"
                                class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                            >
                                + Add Education
                            </Link>
                        </div>
                    </div>

                    <div class="p-6">
                        <div v-if="educations.length === 0" class="text-center py-12">
                            <p class="text-gray-500">No education added yet</p>
                            <Link
                                :href="route('candidate.educations.create')"
                                class="mt-4 inline-block text-blue-600 hover:text-blue-700"
                            >
                                Add your first education
                            </Link>
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="education in educations"
                                :key="education.id"
                                class="rounded-lg border border-gray-200 p-4 hover:shadow-md transition"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ education.degree }}
                                        </h3>
                                        <p class="text-gray-600">
                                            {{ education.institution }}
                                        </p>
                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ education.field_of_study }}
                                        </p>
                                        <p class="mt-2 text-sm text-gray-600">
                                            <span class="font-medium">Period:</span>
                                            {{ education.period }}
                                        </p>
                                        <p v-if="education.gpa" class="text-sm text-gray-600">
                                            <span class="font-medium">GPA:</span>
                                            {{ education.gpa }}/4.0
                                        </p>
                                        <p v-if="education.description" class="mt-2 text-sm text-gray-700">
                                            {{ education.description }}
                                        </p>
                                    </div>
                                    <div class="ml-4 flex space-x-2">
                                        <Link
                                            :href="route('candidate.educations.edit', education.id)"
                                            class="text-blue-600 hover:text-blue-700"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </Link>
                                        <button
                                            @click="deleteEducation(education.id)"
                                            class="text-red-600 hover:text-red-700"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const route = (name: string, params?: any) => {
    return window.route(name, params);
};

interface Props {
    educations: Array<any>;
}

defineProps<Props>();

const deleteEducation = (id: number) => {
    if (confirm('Are you sure you want to delete this education?')) {
        router.delete(route('candidate.educations.destroy', id));
    }
};
</script>
