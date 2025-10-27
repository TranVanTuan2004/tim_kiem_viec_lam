<template>
    <AuthenticatedLayout>
        <Head title="Saved Jobs" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h1 class="text-3xl font-bold text-gray-900">
                            Saved Jobs
                        </h1>
                        <p class="mt-2 text-gray-600">
                            Jobs you've bookmarked for later review
                        </p>
                    </div>
                </div>

                <!-- Filters -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                    >Search</label
                                >
                                <input
                                    v-model="localFilters.search"
                                    @input="debounceSearch"
                                    type="text"
                                    placeholder="Search jobs or companies..."
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                    >Job Type</label
                                >
                                <select
                                    v-model="localFilters.job_type"
                                    @change="applyFilters"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="all">All Types</option>
                                    <option value="full-time">Full-time</option>
                                    <option value="part-time">Part-time</option>
                                    <option value="contract">Contract</option>
                                    <option value="freelance">Freelance</option>
                                    <option value="internship">
                                        Internship
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                    >Location</label
                                >
                                <input
                                    v-model="localFilters.location"
                                    @input="debounceSearch"
                                    type="text"
                                    placeholder="City or region"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saved Jobs Grid -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="grid grid-cols-1 divide-y divide-gray-200">
                        <div
                            v-for="job in savedJobs.data"
                            :key="job.id"
                            class="p-6 transition hover:bg-gray-50"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-start">
                                        <img
                                            v-if="job.company.logo"
                                            :src="job.company.logo"
                                            :alt="job.company.name"
                                            class="h-16 w-16 rounded object-cover"
                                        />
                                        <div class="ml-4 flex-1">
                                            <div
                                                class="flex items-start justify-between"
                                            >
                                                <div class="flex-1">
                                                    <Link
                                                        :href="
                                                            route(
                                                                'jobs.show',
                                                                job.id,
                                                            )
                                                        "
                                                        class="text-xl font-semibold text-gray-900 hover:text-blue-600"
                                                    >
                                                        {{ job.title }}
                                                    </Link>
                                                    <Link
                                                        :href="
                                                            route(
                                                                'companies.show',
                                                                job.company.id,
                                                            )
                                                        "
                                                        class="mt-1 block text-gray-600 hover:text-blue-600"
                                                    >
                                                        {{ job.company.name }}
                                                    </Link>
                                                </div>
                                                <button
                                                    @click="unsaveJob(job.id)"
                                                    class="ml-4 text-red-600 hover:text-red-800"
                                                    title="Remove from saved"
                                                >
                                                    <svg
                                                        class="h-6 w-6"
                                                        fill="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"
                                                        />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div
                                                class="mt-3 flex flex-wrap items-center gap-4 text-sm text-gray-500"
                                            >
                                                <div class="flex items-center">
                                                    <svg
                                                        class="mr-1 h-4 w-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                                        />
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                                        />
                                                    </svg>
                                                    {{ job.location }}
                                                </div>
                                                <div class="flex items-center">
                                                    <svg
                                                        class="mr-1 h-4 w-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                                        />
                                                    </svg>
                                                    <span class="capitalize">{{
                                                        job.job_type
                                                    }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg
                                                        class="mr-1 h-4 w-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                        />
                                                    </svg>
                                                    {{
                                                        formatSalary(
                                                            job.salary_min,
                                                            job.salary_max,
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    v-if="job.industry"
                                                    class="flex items-center"
                                                >
                                                    <svg
                                                        class="mr-1 h-4 w-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                                        />
                                                    </svg>
                                                    {{ job.industry.name }}
                                                </div>
                                            </div>

                                            <p
                                                v-if="job.description"
                                                class="mt-3 line-clamp-2 text-gray-600"
                                            >
                                                {{ stripHtml(job.description) }}
                                            </p>

                                            <div
                                                class="mt-4 flex items-center space-x-3"
                                            >
                                                <Link
                                                    :href="
                                                        route(
                                                            'jobs.show',
                                                            job.id,
                                                        )
                                                    "
                                                    class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
                                                >
                                                    View Details
                                                </Link>
                                                <Link
                                                    v-if="!job.has_applied"
                                                    :href="
                                                        route(
                                                            'jobs.apply',
                                                            job.id,
                                                        )
                                                    "
                                                    class="rounded-md border border-blue-600 px-4 py-2 text-sm text-blue-600 hover:bg-blue-50"
                                                >
                                                    Apply Now
                                                </Link>
                                                <span
                                                    v-else
                                                    class="rounded-md bg-gray-100 px-4 py-2 text-sm text-gray-600"
                                                >
                                                    Already Applied
                                                </span>
                                                <span
                                                    class="text-xs text-gray-500"
                                                >
                                                    Saved
                                                    {{
                                                        formatDate(job.saved_at)
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="savedJobs.data.length === 0"
                            class="p-12 text-center"
                        >
                            <svg
                                class="mx-auto h-12 w-12 text-gray-400"
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
                            <h3 class="mt-2 text-sm font-medium text-gray-900">
                                No saved jobs
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Save jobs you're interested in to review them
                                later!
                            </p>
                            <div class="mt-6">
                                <Link
                                    :href="route('jobs.index')"
                                    class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                                >
                                    Browse Jobs
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="savedJobs.data.length > 0"
                        class="border-t border-gray-200 p-6"
                    >
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ savedJobs.from }} to
                                {{ savedJobs.to }} of {{ savedJobs.total }} jobs
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in savedJobs.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    :class="[
                                        link.active
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-white text-gray-700 hover:bg-gray-50',
                                        !link.url
                                            ? 'cursor-not-allowed opacity-50'
                                            : '',
                                        'rounded-md border border-gray-300 px-3 py-2 text-sm',
                                    ]"
                                    v-html="link.label"
                                >
                                </Link>
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
import { reactive } from 'vue';

interface Props {
    savedJobs: any;
    filters: any;
}

const props = defineProps<Props>();

const localFilters = reactive({ ...props.filters });
let searchTimeout: number | null = null;

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const formatSalary = (min: number, max: number) => {
    if (!min && !max) return 'Negotiable';
    if (!max) return `$${min.toLocaleString()}+`;
    return `$${min.toLocaleString()} - $${max.toLocaleString()}`;
};

const stripHtml = (html: string) => {
    const tmp = document.createElement('DIV');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
};

const applyFilters = () => {
    router.get(route('candidate.saved-jobs.index'), localFilters, {
        preserveState: true,
        preserveScroll: true,
    });
};

const debounceSearch = () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
};

const unsaveJob = (jobId: number) => {
    router.delete(route('candidate.saved-jobs.destroy', jobId), {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        },
    });
};
</script>
