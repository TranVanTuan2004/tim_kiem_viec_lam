<template>
    <AuthenticatedLayout>
        <Head title="Candidate Dashboard" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h1 class="text-3xl font-bold text-gray-900">
                            Welcome back, {{ $page.props.auth.user.name }}!
                        </h1>
                        <p class="mt-2 text-gray-600">
                            Here's an overview of your job search activity
                        </p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div
                    class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 rounded-md bg-blue-500 p-3"
                                >
                                    <svg
                                        class="h-6 w-6 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="truncate text-sm font-medium text-gray-500"
                                        >
                                            Total Applications
                                        </dt>
                                        <dd
                                            class="text-2xl font-semibold text-gray-900"
                                        >
                                            {{ stats.total_applications }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 rounded-md bg-yellow-500 p-3"
                                >
                                    <svg
                                        class="h-6 w-6 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="truncate text-sm font-medium text-gray-500"
                                        >
                                            Pending
                                        </dt>
                                        <dd
                                            class="text-2xl font-semibold text-gray-900"
                                        >
                                            {{ stats.pending_applications }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 rounded-md bg-green-500 p-3"
                                >
                                    <svg
                                        class="h-6 w-6 text-white"
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
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="truncate text-sm font-medium text-gray-500"
                                        >
                                            Shortlisted
                                        </dt>
                                        <dd
                                            class="text-2xl font-semibold text-gray-900"
                                        >
                                            {{ stats.shortlisted_applications }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 rounded-md bg-purple-500 p-3"
                                >
                                    <svg
                                        class="h-6 w-6 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="truncate text-sm font-medium text-gray-500"
                                        >
                                            Saved Jobs
                                        </dt>
                                        <dd
                                            class="text-2xl font-semibold text-gray-900"
                                        >
                                            {{ stats.saved_jobs }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 rounded-md bg-indigo-500 p-3"
                                >
                                    <svg
                                        class="h-6 w-6 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="truncate text-sm font-medium text-gray-500"
                                        >
                                            Portfolios
                                        </dt>
                                        <dd
                                            class="text-2xl font-semibold text-gray-900"
                                        >
                                            {{ stats.portfolios }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 rounded-md bg-pink-500 p-3"
                                >
                                    <svg
                                        class="h-6 w-6 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="truncate text-sm font-medium text-gray-500"
                                        >
                                            Profile Views
                                        </dt>
                                        <dd
                                            class="text-2xl font-semibold text-gray-900"
                                        >
                                            {{ stats.profile_views }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Applications -->
                <div
                    class="mb-8 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="border-b border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-900">
                                Recent Applications
                            </h2>
                            <Link
                                :href="route('candidate.applications.index')"
                                class="text-blue-600 hover:text-blue-800"
                            >
                                View All
                            </Link>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <div
                            v-for="application in recentApplications"
                            :key="application.id"
                            class="p-6 transition hover:bg-gray-50"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <img
                                            v-if="
                                                application.job_posting.company
                                                    .logo
                                            "
                                            :src="
                                                application.job_posting.company
                                                    .logo
                                            "
                                            :alt="
                                                application.job_posting.company
                                                    .name
                                            "
                                            class="mr-4 h-12 w-12 rounded object-cover"
                                        />
                                        <div>
                                            <h3
                                                class="text-lg font-semibold text-gray-900"
                                            >
                                                {{
                                                    application.job_posting
                                                        .title
                                                }}
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                {{
                                                    application.job_posting
                                                        .company.name
                                                }}
                                                •
                                                {{
                                                    application.job_posting
                                                        .location
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="mt-2 flex items-center text-sm text-gray-500"
                                    >
                                        <span
                                            >Applied
                                            {{
                                                formatDate(
                                                    application.applied_at,
                                                )
                                            }}</span
                                        >
                                        <span class="mx-2">•</span>
                                        <span
                                            :class="
                                                getStatusClass(
                                                    application.status,
                                                )
                                            "
                                            class="rounded-full px-2 py-1 text-xs font-medium"
                                        >
                                            {{ application.status }}
                                        </span>
                                    </div>
                                </div>
                                <Link
                                    :href="
                                        route(
                                            'candidate.applications.show',
                                            application.id,
                                        )
                                    "
                                    class="ml-4 text-blue-600 hover:text-blue-800"
                                >
                                    View Details
                                </Link>
                            </div>
                        </div>
                        <div
                            v-if="recentApplications.length === 0"
                            class="p-6 text-center text-gray-500"
                        >
                            No applications yet. Start applying to jobs!
                        </div>
                    </div>
                </div>

                <!-- Recommended Jobs -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Recommended Jobs
                        </h2>
                    </div>
                    <div
                        class="grid grid-cols-1 gap-4 p-6 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="job in recommendedJobs"
                            :key="job.id"
                            class="rounded-lg border border-gray-200 p-4 transition hover:shadow-md"
                        >
                            <div class="flex items-start">
                                <img
                                    v-if="job.company.logo"
                                    :src="job.company.logo"
                                    :alt="job.company.name"
                                    class="h-12 w-12 rounded object-cover"
                                />
                                <div class="ml-3 flex-1">
                                    <h3
                                        class="text-base font-semibold text-gray-900"
                                    >
                                        {{ job.title }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ job.company.name }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ job.location }}
                                    </p>
                                    <div
                                        class="mt-2 flex items-center justify-between"
                                    >
                                        <span
                                            class="text-sm font-medium text-green-600"
                                        >
                                            {{
                                                formatSalary(
                                                    job.salary_min,
                                                    job.salary_max,
                                                )
                                            }}
                                        </span>
                                    </div>
                                    <Link
                                        :href="route('jobs.show', job.id)"
                                        class="mt-3 block rounded-md bg-blue-600 px-3 py-2 text-center text-sm text-white hover:bg-blue-700"
                                    >
                                        View Job
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="recommendedJobs.length === 0"
                            class="col-span-full py-8 text-center text-gray-500"
                        >
                            No recommended jobs at the moment. Complete your
                            profile to get better recommendations!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    stats: {
        total_applications: number;
        pending_applications: number;
        shortlisted_applications: number;
        saved_jobs: number;
        portfolios: number;
        profile_views: number;
    };
    recentApplications: Array<any>;
    recommendedJobs: Array<any>;
    candidateProfile: any;
}

defineProps<Props>();

const formatDate = (date: string) => {
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
</script>
