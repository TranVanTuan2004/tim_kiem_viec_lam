<template>
    <AuthenticatedLayout>
        <Head title="My Work Experience" />

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">
                                    My Work Experience
                                </h1>
                                <p class="mt-2 text-gray-600">
                                    Manage your professional experience
                                </p>
                            </div>
                            <Link
                                :href="route('candidate.work-experiences.create')"
                                class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                            >
                                + Add Experience
                            </Link>
                        </div>
                    </div>

                    <div class="p-6">
                        <div v-if="workExperiences.length === 0" class="text-center py-12">
                            <p class="text-gray-500">No work experience added yet</p>
                            <Link
                                :href="route('candidate.work-experiences.create')"
                                class="mt-4 inline-block text-blue-600 hover:text-blue-700"
                            >
                                Add your first work experience
                            </Link>
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="experience in workExperiences"
                                :key="experience.id"
                                class="rounded-lg border border-gray-200 p-4 hover:shadow-md transition"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ experience.job_title }}
                                            </h3>
                                            <span v-if="experience.is_current" class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                                Current
                                            </span>
                                        </div>
                                        <p class="text-gray-600">
                                            {{ experience.company_name }}
                                        </p>
                                        <p class="mt-1 text-sm text-gray-500">
                                            <span class="font-medium">Type:</span>
                                            {{ formatEmploymentType(experience.employment_type) }}
                                        </p>
                                        <p class="mt-1 text-sm text-gray-600">
                                            <span class="font-medium">Period:</span>
                                            {{ experience.period }}
                                        </p>
                                        <p v-if="experience.duration_months" class="text-sm text-gray-600">
                                            <span class="font-medium">Duration:</span>
                                            {{ experience.duration_months }} months
                                        </p>
                                        <p v-if="experience.description" class="mt-2 text-sm text-gray-700">
                                            {{ experience.description }}
                                        </p>
                                    </div>
                                    <div class="ml-4 flex space-x-2">
                                        <Link
                                            :href="route('candidate.work-experiences.edit', experience.id)"
                                            class="text-blue-600 hover:text-blue-700"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </Link>
                                        <button
                                            @click="deleteExperience(experience.id)"
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
import { Head, Link, router } from '@inertiajs/vue3';

interface Props {
    workExperiences: Array<any>;
}

defineProps<Props>();

const formatEmploymentType = (type: string) => {
    const types: Record<string, string> = {
        full_time: 'Full Time',
        part_time: 'Part Time',
        contract: 'Contract',
        internship: 'Internship',
        freelance: 'Freelance',
    };
    return types[type] || type;
};

const deleteExperience = (id: number) => {
    if (confirm('Are you sure you want to delete this work experience?')) {
        router.delete(route('candidate.work-experiences.destroy', id));
    }
};
</script>
