<template>
    <AuthenticatedLayout>
        <Head title="My Applications" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h1 class="text-3xl font-bold text-gray-900">
                            My Applications
                        </h1>
                        <p class="mt-2 text-gray-600">
                            Track your job application status
                        </p>
                    </div>
                </div>

                <!-- Statistics -->
                <div
                    class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6"
                >
                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <div class="text-2xl font-bold text-gray-900">
                            {{ stats.total }}
                        </div>
                        <div class="text-sm text-gray-600">Total</div>
                    </div>
                    <div class="rounded-lg bg-yellow-50 p-4 shadow-sm">
                        <div class="text-2xl font-bold text-yellow-900">
                            {{ stats.pending }}
                        </div>
                        <div class="text-sm text-yellow-700">Pending</div>
                    </div>
                    <div class="rounded-lg bg-blue-50 p-4 shadow-sm">
                        <div class="text-2xl font-bold text-blue-900">
                            {{ stats.reviewed }}
                        </div>
                        <div class="text-sm text-blue-700">Reviewed</div>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4 shadow-sm">
                        <div class="text-2xl font-bold text-green-900">
                            {{ stats.shortlisted }}
                        </div>
                        <div class="text-sm text-green-700">Shortlisted</div>
                    </div>
                    <div class="rounded-lg bg-purple-50 p-4 shadow-sm">
                        <div class="text-2xl font-bold text-purple-900">
                            {{ stats.accepted }}
                        </div>
                        <div class="text-sm text-purple-700">Accepted</div>
                    </div>
                    <div class="rounded-lg bg-red-50 p-4 shadow-sm">
                        <div class="text-2xl font-bold text-red-900">
                            {{ stats.rejected }}
                        </div>
                        <div class="text-sm text-red-700">Rejected</div>
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
                                    >Status</label
                                >
                                <select
                                    v-model="localFilters.status"
                                    @change="applyFilters"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="all">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="reviewed">Reviewed</option>
                                    <option value="shortlisted">
                                        Shortlisted
                                    </option>
                                    <option value="rejected">Rejected</option>
                                    <option value="accepted">Accepted</option>
                                    <option value="withdrawn">Withdrawn</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                    >Sort By</label
                                >
                                <select
                                    v-model="localFilters.sort_by"
                                    @change="applyFilters"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="created_at">
                                        Application Date
                                    </option>
                                    <option value="updated_at">
                                        Last Updated
                                    </option>
                                </select>
                            </div>
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
                        </div>
                    </div>
                </div>

                <!-- Applications List -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="divide-y divide-gray-200">
                        <div
                            v-for="application in applications.data"
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
                                            class="mr-4 h-16 w-16 rounded object-cover"
                                        />
                                        <div>
                                            <Link
                                                :href="`/jobs/${application.job_posting.id}`"
                                                class="text-xl font-semibold text-gray-900 hover:text-blue-600"
                                            >
                                                {{
                                                    application.job_posting
                                                        .title
                                                }}
                                            </Link>
                                            <p class="mt-1 text-gray-600">
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
                                            <div
                                                class="mt-2 flex items-center space-x-4 text-sm text-gray-500"
                                            >
                                                <span
                                                    >Applied
                                                    {{
                                                        formatDate(
                                                            application.applied_at,
                                                        )
                                                    }}</span
                                                >
                                                <span>•</span>
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
                                    </div>
                                </div>
                                <div class="ml-4 flex items-center space-x-2">
                                    <Link
                                        :href="`/candidate/applications/${application.id}`"
                                        class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                                    >
                                        View Details
                                    </Link>
                                    <button
                                        v-if="
                                            ['pending', 'reviewed'].includes(
                                                application.status,
                                            )
                                        "
                                        @click="
                                            withdrawApplication(application.id)
                                        "
                                        class="rounded-md border border-red-600 px-4 py-2 text-red-600 hover:bg-red-50"
                                    >
                                        Withdraw
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="applications.data.length === 0"
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
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">
                                No applications found
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Start applying to jobs to see them here!
                            </p>
                            <div class="mt-6">
                                <Link
                                    href="/jobs"
                                    class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                                >
                                    Browse Jobs
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="applications.data.length > 0"
                        class="border-t border-gray-200 p-6"
                    >
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ applications.from }} to
                                {{ applications.to }} of
                                {{ applications.total }} applications
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in applications.links"
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
    applications: any;
    stats: any;
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

const applyFilters = () => {
    router.get('/candidate/applications', localFilters, {
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

const withdrawApplication = (id: number) => {
    if (confirm('Are you sure you want to withdraw this application?')) {
        router.post(
            `/candidate/applications/${id}/withdraw`,
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Handle success
                },
            },
        );
    }
};
</script>
