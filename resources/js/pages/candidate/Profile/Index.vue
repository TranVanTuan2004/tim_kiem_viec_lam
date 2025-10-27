<template>
    <AuthenticatedLayout>
        <Head title="My Profile" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="flex items-center justify-between p-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">
                                My Profile
                            </h1>
                            <p class="mt-2 text-gray-600">
                                Manage your professional information
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <button
                                @click="toggleAvailability"
                                :class="
                                    profile.is_available
                                        ? 'bg-green-600 hover:bg-green-700'
                                        : 'bg-gray-600 hover:bg-gray-700'
                                "
                                class="rounded-md px-4 py-2 text-white transition"
                            >
                                {{
                                    profile.is_available
                                        ? 'Available for Work'
                                        : 'Not Available'
                                }}
                            </button>
                            <Link
                                :href="edit.url()"
                                class="rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
                            >
                                Edit Profile
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="border-b border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Personal Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Full Name</label
                                >
                                <p class="mt-1 text-gray-900">
                                    {{ user.name }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Email</label
                                >
                                <p class="mt-1 text-gray-900">
                                    {{ user.email }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Phone</label
                                >
                                <p class="mt-1 text-gray-900">
                                    {{ user.phone || 'Not provided' }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Gender</label
                                >
                                <p class="mt-1 text-gray-900 capitalize">
                                    {{ profile.gender || 'Not specified' }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Date of Birth</label
                                >
                                <p class="mt-1 text-gray-900">
                                    {{
                                        profile.birth_date
                                            ? formatDate(profile.birth_date)
                                            : 'Not provided'
                                    }}
                                    <span
                                        v-if="profile.age"
                                        class="text-gray-600"
                                        >({{ profile.age }} years old)</span
                                    >
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Location</label
                                >
                                <p class="mt-1 text-gray-900">
                                    {{
                                        profile.city && profile.province
                                            ? `${profile.city}, ${profile.province}`
                                            : 'Not provided'
                                    }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-6" v-if="profile.address">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Address</label
                            >
                            <p class="mt-1 text-gray-900">
                                {{ profile.address }}
                            </p>
                        </div>
                        <div class="mt-6" v-if="profile.summary">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Professional Summary</label
                            >
                            <p class="mt-1 text-gray-900">
                                {{ profile.summary }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Professional Details -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="border-b border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Professional Details
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Current Position</label
                                >
                                <p class="mt-1 text-gray-900">
                                    {{
                                        profile.current_position ||
                                        'Not provided'
                                    }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Current Company</label
                                >
                                <p class="mt-1 text-gray-900">
                                    {{
                                        profile.current_company ||
                                        'Not provided'
                                    }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Experience Level</label
                                >
                                <p class="mt-1 text-gray-900 capitalize">
                                    {{
                                        profile.experience_level ||
                                        'Not provided'
                                    }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Expected Salary</label
                                >
                                <p class="mt-1 text-gray-900">
                                    {{
                                        profile.expected_salary
                                            ? `$${profile.expected_salary.toLocaleString()}`
                                            : 'Negotiable'
                                    }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-6" v-if="profile.cv_url">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >CV/Resume</label
                            >
                            <a
                                :href="profile.cv_url"
                                target="_blank"
                                class="mt-1 inline-flex items-center text-blue-600 hover:text-blue-800"
                            >
                                <svg
                                    class="mr-2 h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                                Download CV
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Skills -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="border-b border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Skills
                        </h2>
                    </div>
                    <div class="p-6">
                        <div
                            v-if="profile.skills && profile.skills.length > 0"
                            class="space-y-4"
                        >
                            <div
                                v-for="skill in profile.skills"
                                :key="skill.id"
                                class="flex items-center justify-between rounded-lg border border-gray-200 p-3"
                            >
                                <div>
                                    <h3 class="font-medium text-gray-900">
                                        {{ skill.name }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ skill.level }} •
                                        {{ skill.years_experience }} year(s)
                                        experience
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-gray-500">No skills added yet</p>
                    </div>
                </div>

                <!-- Work Experience -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="border-b border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Work Experience
                        </h2>
                    </div>
                    <div class="p-6">
                        <div
                            v-if="
                                profile.work_experiences &&
                                profile.work_experiences.length > 0
                            "
                            class="space-y-6"
                        >
                            <div
                                v-for="exp in profile.work_experiences"
                                :key="exp.id"
                                class="border-l-4 border-blue-500 pl-4"
                            >
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ exp.position }}
                                </h3>
                                <p class="text-gray-700">
                                    {{ exp.company_name }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ formatDate(exp.start_date) }} -
                                    {{
                                        exp.is_current
                                            ? 'Present'
                                            : formatDate(exp.end_date)
                                    }}
                                </p>
                                <p
                                    v-if="exp.description"
                                    class="mt-2 text-gray-600"
                                >
                                    {{ exp.description }}
                                </p>
                            </div>
                        </div>
                        <p v-else class="text-gray-500">
                            No work experience added yet
                        </p>
                    </div>
                </div>

                <!-- Education -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="border-b border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Education
                        </h2>
                    </div>
                    <div class="p-6">
                        <div
                            v-if="
                                profile.educations &&
                                profile.educations.length > 0
                            "
                            class="space-y-6"
                        >
                            <div
                                v-for="edu in profile.educations"
                                :key="edu.id"
                                class="border-l-4 border-green-500 pl-4"
                            >
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ edu.degree }}
                                </h3>
                                <p class="text-gray-700">
                                    {{ edu.institution }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ edu.field_of_study }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ formatDate(edu.start_date) }} -
                                    {{
                                        edu.is_current
                                            ? 'Present'
                                            : formatDate(edu.end_date)
                                    }}
                                </p>
                                <p
                                    v-if="edu.description"
                                    class="mt-2 text-gray-600"
                                >
                                    {{ edu.description }}
                                </p>
                            </div>
                        </div>
                        <p v-else class="text-gray-500">
                            No education added yet
                        </p>
                    </div>
                </div>

                <!-- Portfolios -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div
                        class="flex items-center justify-between border-b border-gray-200 p-6"
                    >
                        <h2 class="text-xl font-semibold text-gray-900">
                            Portfolio Projects
                        </h2>
                        <Link
                            :href="portfoliosIndex.url()"
                            class="text-blue-600 hover:text-blue-800"
                        >
                            Manage Portfolios
                        </Link>
                    </div>
                    <div class="p-6">
                        <div
                            v-if="
                                profile.portfolios &&
                                profile.portfolios.length > 0
                            "
                            class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
                        >
                            <div
                                v-for="portfolio in profile.portfolios"
                                :key="portfolio.id"
                                class="overflow-hidden rounded-lg border border-gray-200 transition hover:shadow-md"
                            >
                                <img
                                    v-if="portfolio.thumbnail"
                                    :src="portfolio.thumbnail"
                                    :alt="portfolio.title"
                                    class="h-40 w-full object-cover"
                                />
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-900">
                                        {{ portfolio.title }}
                                    </h3>
                                    <p
                                        class="mt-1 line-clamp-2 text-sm text-gray-600"
                                    >
                                        {{ portfolio.description }}
                                    </p>
                                    <a
                                        v-if="portfolio.project_url"
                                        :href="portfolio.project_url"
                                        target="_blank"
                                        class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-800"
                                    >
                                        View Project →
                                    </a>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-gray-500">
                            No portfolio projects yet
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { index as portfoliosIndex } from '@/routes/candidate/portfolios';
import {
    edit,
    toggleAvailability as toggleAvailabilityRoute,
} from '@/routes/candidate/profile';
import { Head, Link, router } from '@inertiajs/vue3';

interface Props {
    profile: any;
    user: any;
}

defineProps<Props>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const toggleAvailability = () => {
    router.post(
        toggleAvailabilityRoute.url(),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                // Handle success if needed
            },
        },
    );
};
</script>
