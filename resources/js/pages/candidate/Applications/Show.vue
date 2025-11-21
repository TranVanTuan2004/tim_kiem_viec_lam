<template>
    <ClientLayout>
        <Head :title="`Application - ${application.job_posting.title}`" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Back Button -->
                <div class="mb-6">
                    <Link
                        href="/candidate/applications"
                        class="inline-flex items-center text-blue-600 hover:text-blue-800"
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
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>
                        Back to Applications
                    </Link>
                </div>

                <!-- Application Status -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Application Details
                                </h1>
                                <p class="mt-1 text-gray-600">
                                    Applied on
                                    {{ formatDate(application.applied_at) }}
                                </p>
                            </div>
                            <span
                                :class="getStatusClass(application.status)"
                                class="rounded-full px-4 py-2 text-sm font-medium"
                            >
                                {{ application.status }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Main Content -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- Job Information -->
                        <div
                            class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                        >
                            <div class="border-b border-gray-200 p-6">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    Job Information
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="flex items-start">
                                    <img
                                        v-if="
                                            application.job_posting.company.logo
                                        "
                                        :src="
                                            application.job_posting.company.logo
                                        "
                                        :alt="
                                            application.job_posting.company.name
                                        "
                                        class="h-20 w-20 rounded object-cover"
                                    />
                                    <div class="ml-4 flex-1">
                                        <h3
                                            class="text-2xl font-bold text-gray-900"
                                        >
                                            {{ application.job_posting.title }}
                                        </h3>
                                        <Link
                                            :href="`/companies/${application.job_posting.company.id}`"
                                            class="text-lg text-blue-600 hover:text-blue-800"
                                        >
                                            {{
                                                application.job_posting.company
                                                    .name
                                            }}
                                        </Link>
                                        <div
                                            class="mt-3 grid grid-cols-2 gap-4 text-sm"
                                        >
                                            <div>
                                                <span
                                                    class="font-medium text-gray-700"
                                                    >Location:</span
                                                >
                                                <span
                                                    class="ml-2 text-gray-600"
                                                    >{{
                                                        application.job_posting
                                                            .location
                                                    }}</span
                                                >
                                            </div>
                                            <div>
                                                <span
                                                    class="font-medium text-gray-700"
                                                    >Job Type:</span
                                                >
                                                <span
                                                    class="ml-2 text-gray-600 capitalize"
                                                    >{{
                                                        application.job_posting
                                                            .job_type
                                                    }}</span
                                                >
                                            </div>
                                            <div>
                                                <span
                                                    class="font-medium text-gray-700"
                                                    >Experience Level:</span
                                                >
                                                <span
                                                    class="ml-2 text-gray-600 capitalize"
                                                    >{{
                                                        application.job_posting
                                                            .experience_level
                                                    }}</span
                                                >
                                            </div>
                                            <div>
                                                <span
                                                    class="font-medium text-gray-700"
                                                    >Salary:</span
                                                >
                                                <span
                                                    class="ml-2 text-gray-600"
                                                >
                                                    {{
                                                        formatSalary(
                                                            application
                                                                .job_posting
                                                                .salary_min,
                                                            application
                                                                .job_posting
                                                                .salary_max,
                                                        )
                                                    }}
                                                </span>
                                            </div>
                                            <div>
                                                <span
                                                    class="font-medium text-gray-700"
                                                    >Application Deadline:</span
                                                >
                                                <span
                                                    class="ml-2 text-gray-600"
                                                    >{{
                                                        formatDate(
                                                            application
                                                                .job_posting
                                                                .deadline,
                                                        )
                                                    }}</span
                                                >
                                            </div>
                                            <div
                                                v-if="
                                                    application.job_posting
                                                        .industry
                                                "
                                            >
                                                <span
                                                    class="font-medium text-gray-700"
                                                    >Industry:</span
                                                >
                                                <span
                                                    class="ml-2 text-gray-600"
                                                    >{{
                                                        application.job_posting
                                                            .industry.name
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <h4 class="mb-2 font-medium text-gray-900">
                                        Job Description
                                    </h4>
                                    <div
                                        class="prose prose-sm max-w-none text-gray-600"
                                        v-html="
                                            application.job_posting.description
                                        "
                                    ></div>
                                </div>

                                <div
                                    v-if="
                                        application.job_posting.skills &&
                                        application.job_posting.skills.length >
                                            0
                                    "
                                    class="mt-6"
                                >
                                    <h4 class="mb-2 font-medium text-gray-900">
                                        Required Skills
                                    </h4>
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="skill in application
                                                .job_posting.skills"
                                            :key="skill.id"
                                            class="rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-800"
                                        >
                                            {{ skill.name }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <Link
                                        :href="`/jobs/${application.job_posting.id}`"
                                        class="inline-flex items-center text-blue-600 hover:text-blue-800"
                                    >
                                        View Full Job Posting →
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Cover Letter -->
                        <div
                            class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                        >
                            <div class="border-b border-gray-200 p-6">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    Your Cover Letter
                                </h2>
                            </div>
                            <div class="p-6">
                                <p
                                    v-if="application.cover_letter"
                                    class="whitespace-pre-wrap text-gray-600"
                                >
                                    {{ application.cover_letter }}
                                </p>
                                <p v-else class="text-gray-500 italic">
                                    No cover letter provided
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6 lg:col-span-1">
                        <!-- Actions -->
                        <div
                            class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                        >
                            <div class="border-b border-gray-200 p-6">
                                <h2 class="text-lg font-semibold text-gray-900">
                                    Actions
                                </h2>
                            </div>
                            <div class="space-y-3 p-6">
                                <Link
                                    :href="`/jobs/${application.job_posting.id}`"
                                    class="block w-full rounded-md bg-blue-600 px-4 py-2 text-center text-white hover:bg-blue-700"
                                >
                                    View Job Posting
                                </Link>
                                <Link
                                    :href="`/companies/${application.job_posting.company.id}`"
                                    class="block w-full rounded-md border border-gray-300 px-4 py-2 text-center text-gray-700 hover:bg-gray-50"
                                >
                                    View Company
                                </Link>
                                <button
                                    v-if="
                                        ['pending', 'reviewed'].includes(
                                            application.status,
                                        )
                                    "
                                    @click="withdrawApplication"
                                    class="block w-full rounded-md border border-red-600 px-4 py-2 text-center text-red-600 hover:bg-red-50"
                                >
                                    Withdraw Application
                                </button>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div
                            class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                        >
                            <div class="border-b border-gray-200 p-6">
                                <h2 class="text-lg font-semibold text-gray-900">
                                    Application Timeline
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500"
                                            >
                                                <svg
                                                    class="h-5 w-5 text-white"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                                    />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                Application Submitted
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{
                                                    formatDate(
                                                        application.applied_at,
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        v-if="application.status !== 'pending'"
                                        class="flex items-start"
                                    >
                                        <div class="flex-shrink-0">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-green-500"
                                            >
                                                <svg
                                                    class="h-5 w-5 text-white"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M5 13l4 4L19 7"
                                                    />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p
                                                class="text-sm font-medium text-gray-900 capitalize"
                                            >
                                                {{ application.status }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{
                                                    formatDate(
                                                        application.updated_at,
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Company Contact -->
                        <div
                            class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                        >
                            <div class="border-b border-gray-200 p-6">
                                <h2 class="text-lg font-semibold text-gray-900">
                                    Company Contact
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3 text-sm">
                                    <div
                                        v-if="
                                            application.job_posting.company
                                                .website
                                        "
                                    >
                                        <span class="font-medium text-gray-700"
                                            >Website:</span
                                        >
                                        <a
                                            :href="
                                                application.job_posting.company
                                                    .website
                                            "
                                            target="_blank"
                                            class="ml-2 text-blue-600 hover:text-blue-800"
                                        >
                                            Visit Website
                                        </a>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700"
                                            >Location:</span
                                        >
                                        <span class="ml-2 text-gray-600">{{
                                            application.job_posting.company
                                                .location
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup lang="ts">
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface Props {
    application: any;
}

defineProps<Props>();

const formatDate = (date: string) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const getStatusClass = (status: string) => {
    const classes: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800',
        reviewed: 'bg-blue-100 text-blue-800',
        shortlisted: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        accepted: 'bg-purple-100 text-purple-800',
        withdrawn: 'bg-gray-100 text-gray-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatSalary = (min: number, max: number) => {
    if (!min && !max) return 'Negotiable';
    if (!max) return `$${min.toLocaleString()}+`;
    return `$${min.toLocaleString()} - $${max.toLocaleString()}`;
};

const withdrawApplication = () => {
    if (confirm('Are you sure you want to withdraw this application?')) {
        router.post(
            `/candidate/applications/${application.id}/withdraw`,
            {},
            {
                onSuccess: () => {
                    router.visit('/candidate/applications');
                },
            },
        );
    }
};
</script>
