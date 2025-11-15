<template>
    <AuthenticatedLayout>
        <Head title="Edit Work Experience" />

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 p-6">
                        <h1 class="text-2xl font-bold text-gray-900">
                            Edit Work Experience
                        </h1>
                        <p class="mt-2 text-gray-600">
                            Update your professional experience
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Company Name <span class="text-red-600">*</span>
                                </label>
                                <input
                                    v-model="form.company_name"
                                    type="text"
                                    placeholder="e.g., Tech Company Inc."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p v-if="form.errors.company_name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.company_name }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Job Title <span class="text-red-600">*</span>
                                </label>
                                <input
                                    v-model="form.job_title"
                                    type="text"
                                    placeholder="e.g., Senior Developer"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p v-if="form.errors.job_title" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.job_title }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Employment Type <span class="text-red-600">*</span>
                                </label>
                                <select
                                    v-model="form.employment_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Select Employment Type</option>
                                    <option value="full_time">Full Time</option>
                                    <option value="part_time">Part Time</option>
                                    <option value="contract">Contract</option>
                                    <option value="internship">Internship</option>
                                    <option value="freelance">Freelance</option>
                                </select>
                                <p v-if="form.errors.employment_type" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.employment_type }}
                                </p>
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input
                                        v-model="form.is_current"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">
                                        I currently work here
                                    </span>
                                </label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Start Date <span class="text-red-600">*</span>
                                </label>
                                <input
                                    v-model="form.start_date"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.start_date }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    End Date
                                    <span v-if="!form.is_current" class="text-red-600">*</span>
                                </label>
                                <input
                                    v-model="form.end_date"
                                    type="date"
                                    :disabled="form.is_current"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                />
                                <p v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.end_date }}
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Describe your responsibilities and achievements"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6">
                            <Link
                                :href="route('candidate.work-experiences.index')"
                                class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                            >
                                {{ form.processing ? 'Updating...' : 'Update Experience' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Props {
    workExperience: any;
}

const props = defineProps<Props>();

const form = useForm({
    company_name: props.workExperience.company_name || '',
    job_title: props.workExperience.job_title || '',
    employment_type: props.workExperience.employment_type || '',
    start_date: props.workExperience.start_date || '',
    end_date: props.workExperience.end_date || '',
    is_current: props.workExperience.is_current || false,
    description: props.workExperience.description || '',
});

const submit = () => {
    form.put(route('candidate.work-experiences.update', props.workExperience.id), {
        preserveScroll: true,
    });
};
</script>
