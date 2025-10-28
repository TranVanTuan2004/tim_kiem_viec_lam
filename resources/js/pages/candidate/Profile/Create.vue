<template>
    <AuthenticatedLayout>
        <Head title="Create Profile" />

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 p-6">
                        <h1 class="text-2xl font-bold text-gray-900">
                            Create Your Profile
                        </h1>
                        <p class="mt-2 text-gray-600">
                            Complete your profile to access all features
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <!-- Personal Information -->
                        <div>
                            <h2
                                class="mb-4 text-lg font-semibold text-gray-900"
                            >
                                Personal Information
                            </h2>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Birth Date</label
                                    >
                                    <input
                                        v-model="form.birth_date"
                                        type="date"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <p
                                        v-if="form.errors.birth_date"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.birth_date }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Gender</label
                                    >
                                    <select
                                        v-model="form.gender"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    >
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >City</label
                                    >
                                    <input
                                        v-model="form.city"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Province</label
                                    >
                                    <input
                                        v-model="form.province"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Address</label
                                    >
                                    <input
                                        v-model="form.address"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div>
                            <h2
                                class="mb-4 text-lg font-semibold text-gray-900"
                            >
                                Professional Information
                            </h2>
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Professional Summary</label
                                    >
                                    <textarea
                                        v-model="form.summary"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Brief description of your professional background and career goals"
                                    ></textarea>
                                </div>

                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700"
                                            >Current Position</label
                                        >
                                        <input
                                            v-model="form.current_position"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700"
                                            >Current Company</label
                                        >
                                        <input
                                            v-model="form.current_company"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700"
                                            >Experience Level</label
                                        >
                                        <select
                                            v-model="form.experience_level"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        >
                                            <option value="">
                                                Select Experience Level
                                            </option>
                                            <option value="fresher">
                                                Fresher
                                            </option>
                                            <option value="junior">
                                                Junior
                                            </option>
                                            <option value="middle">
                                                Middle Level
                                            </option>
                                            <option value="senior">
                                                Senior
                                            </option>
                                            <option value="lead">Lead</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700"
                                            >Expected Salary (USD)</label
                                        >
                                        <input
                                            v-model.number="
                                                form.expected_salary
                                            "
                                            type="number"
                                            min="0"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >CV/Resume (PDF, DOC, DOCX - Max
                                        5MB)</label
                                    >
                                    <input
                                        @change="handleFileUpload"
                                        type="file"
                                        accept=".pdf,.doc,.docx"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100"
                                    />
                                    <p
                                        v-if="form.errors.cv_file"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.cv_file }}
                                    </p>
                                </div>

                                <div class="flex items-center">
                                    <input
                                        v-model="form.is_available"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <label
                                        class="ml-2 block text-sm text-gray-700"
                                    >
                                        I am available for work opportunities
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div
                            class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6"
                        >
                            <Link
                                :href="home.url()"
                                class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                            >
                                {{
                                    form.processing
                                        ? 'Creating...'
                                        : 'Create Profile'
                                }}
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
import { home } from '@/routes';
import { store } from '@/routes/candidate/profile';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Props {
    allSkills?: Array<any>;
    user: any;
}

defineProps<Props>();

const form = useForm({
    birth_date: '',
    gender: '',
    address: '',
    city: '',
    province: '',
    cv_file: null as File | null,
    summary: '',
    current_position: '',
    current_company: '',
    expected_salary: null as number | null,
    experience_level: '',
    is_available: true,
    skills: [] as number[],
});

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.cv_file = target.files[0];
    }
};

const submit = () => {
    form.post(store.url(), {
        preserveScroll: true,
    });
};
</script>
