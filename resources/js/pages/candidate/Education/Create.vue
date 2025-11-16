<template>
    <AuthenticatedLayout>
        <Head title="Add Education" />

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 p-6">
                        <h1 class="text-2xl font-bold text-gray-900">
                            Add Education
                        </h1>
                        <p class="mt-2 text-gray-600">
                            Add your educational background
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Institution <span class="text-red-600">*</span>
                                </label>
                                <input
                                    v-model="form.institution"
                                    type="text"
                                    placeholder="e.g., University of Technology"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p v-if="form.errors.institution" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.institution }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Degree <span class="text-red-600">*</span>
                                </label>
                                <input
                                    v-model="form.degree"
                                    type="text"
                                    placeholder="e.g., Bachelor of Science"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p v-if="form.errors.degree" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.degree }}
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Field of Study <span class="text-red-600">*</span>
                                </label>
                                <input
                                    v-model="form.field_of_study"
                                    type="text"
                                    placeholder="e.g., Computer Science"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p v-if="form.errors.field_of_study" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.field_of_study }}
                                </p>
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
                                </label>
                                <input
                                    v-model="form.end_date"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.end_date }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    GPA (0-4.0)
                                </label>
                                <input
                                    v-model.number="form.gpa"
                                    type="number"
                                    min="0"
                                    max="4"
                                    step="0.01"
                                    placeholder="e.g., 3.8"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p v-if="form.errors.gpa" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.gpa }}
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Add any additional information about your education"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6">
                            <Link
                                :href="route('candidate.educations.index')"
                                class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                            >
                                {{ form.processing ? 'Adding...' : 'Add Education' }}
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

const route = (name: string, params?: any) => {
    return window.route(name, params);
};

const form = useForm({
    institution: '',
    degree: '',
    field_of_study: '',
    start_date: '',
    end_date: '',
    gpa: null as number | null,
    description: '',
});

const submit = () => {
    form.post(route('candidate.educations.store'), {
        preserveScroll: true,
    });
};
</script>
