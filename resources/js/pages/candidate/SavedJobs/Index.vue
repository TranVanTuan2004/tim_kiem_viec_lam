<template>
    <ClientLayout>
        <Head title="Việc làm đã lưu" />

        <div
            class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div
                    class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 via-purple-700 to-indigo-700 shadow-xl"
                >
                    <!-- <div class="inset-0 bg-black/10"></div> -->
                    <div class="relative px-8 py-10 sm:px-12">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1
                                    class="text-3xl font-bold text-white sm:text-4xl"
                                >
                                    Việc làm đã lưu
                                </h1>
                                <p class="mt-3 text-lg text-purple-100">
                                    Các việc làm bạn đã đánh dấu để xem lại sau
                                </p>
                            </div>
                            <div class="hidden sm:block">
                                <Bookmark class="h-16 w-16 text-white/30" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <Card class="mb-6">
                    <CardHeader>
                        <CardTitle class="text-lg">Bộ lọc</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Tìm kiếm
                                </label>
                                <div class="relative">
                                    <Search
                                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                                    />
                                    <input
                                        v-model="localFilters.search"
                                        @input="debounceSearch"
                                        type="text"
                                        placeholder="Tìm việc làm hoặc công ty..."
                                        class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Loại công việc
                                </label>
                                <select
                                    v-model="localFilters.job_type"
                                    @change="applyFilters"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="all">Tất cả loại</option>
                                    <option value="full-time">
                                        Toàn thời gian
                                    </option>
                                    <option value="part-time">
                                        Bán thời gian
                                    </option>
                                    <option value="contract">Hợp đồng</option>
                                    <option value="freelance">Freelance</option>
                                    <option value="internship">Thực tập</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Địa điểm
                                </label>
                                <div class="relative">
                                    <MapPin
                                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                                    />
                                    <input
                                        v-model="localFilters.location"
                                        @input="debounceSearch"
                                        type="text"
                                        placeholder="Thành phố hoặc khu vực"
                                        class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Saved Jobs Grid -->
                <div
                    v-if="savedJobs.data.length > 0"
                    class="grid grid-cols-1 gap-6 md:grid-cols-2"
                >
                    <Card
                        v-for="job in savedJobs.data"
                        :key="job.id"
                        class="group transition-all hover:border-blue-300 hover:shadow-lg"
                    >
                        <CardContent class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex flex-1 items-start space-x-4">
                                    <img
                                        v-if="job.company.logo"
                                        :src="job.company.logo"
                                        :alt="job.company.name"
                                        class="h-16 w-16 rounded-lg border border-gray-200 object-cover"
                                    />
                                    <div
                                        v-else
                                        class="flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100"
                                    >
                                        <Building2
                                            class="h-8 w-8 text-gray-400"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="flex items-start justify-between"
                                        >
                                            <div class="flex-1">
                                                <Link
                                                    :href="`/jobs/${job.id}`"
                                                    class="line-clamp-2 text-xl font-semibold text-gray-900 transition-colors hover:text-blue-600"
                                                >
                                                    {{ job.title }}
                                                </Link>
                                                <Link
                                                    :href="`/companies/${job.company.id}`"
                                                    class="mt-1 block text-gray-600 transition-colors hover:text-blue-600"
                                                >
                                                    {{ job.company.name }}
                                                </Link>
                                            </div>
                                            <button
                                                @click="unsaveJob(job.id)"
                                                class="ml-4 flex h-8 w-8 items-center justify-center rounded-full text-red-600 transition-colors hover:bg-red-50"
                                                title="Bỏ lưu"
                                            >
                                                <X class="h-5 w-5" />
                                            </button>
                                        </div>

                                        <div
                                            class="mt-4 flex flex-wrap items-center gap-2"
                                        >
                                            <Badge
                                                variant="outline"
                                                class="text-xs"
                                            >
                                                <MapPin class="mr-1 h-3 w-3" />
                                                {{ job.location }}
                                            </Badge>
                                            <Badge
                                                variant="outline"
                                                class="text-xs capitalize"
                                            >
                                                <Briefcase
                                                    class="mr-1 h-3 w-3"
                                                />
                                                {{
                                                    getJobTypeLabel(
                                                        job.job_type,
                                                    )
                                                }}
                                            </Badge>
                                            <Badge
                                                v-if="
                                                    job.salary_min ||
                                                    job.salary_max
                                                "
                                                variant="secondary"
                                                class="text-xs"
                                            >
                                                <DollarSign
                                                    class="mr-1 h-3 w-3"
                                                />
                                                {{
                                                    formatSalary(
                                                        job.salary_min,
                                                        job.salary_max,
                                                    )
                                                }}
                                            </Badge>
                                            <Badge
                                                v-if="job.industry"
                                                variant="outline"
                                                class="text-xs"
                                            >
                                                <Building2
                                                    class="mr-1 h-3 w-3"
                                                />
                                                {{ job.industry.name }}
                                            </Badge>
                                        </div>

                                        <p
                                            v-if="job.description"
                                            class="mt-4 line-clamp-2 text-sm text-gray-600"
                                        >
                                            {{ stripHtml(job.description) }}
                                        </p>

                                        <div
                                            class="mt-4 flex items-center justify-between"
                                        >
                                            <span class="text-xs text-gray-500">
                                                <Calendar
                                                    class="mr-1 inline h-3 w-3"
                                                />
                                                Đã lưu
                                                {{ formatDate(job.saved_at) }}
                                            </span>
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <Link
                                                    :href="`/jobs/${job.id}`"
                                                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:outline-none"
                                                >
                                                    Xem chi tiết
                                                </Link>
                                                <Link
                                                    v-if="!job.has_applied"
                                                    :href="`/jobs/${job.id}/apply`"
                                                    class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                                                >
                                                    Ứng tuyển ngay
                                                </Link>
                                                <Badge
                                                    v-else
                                                    variant="secondary"
                                                >
                                                    Đã ứng tuyển
                                                </Badge>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Empty State -->
                <Card v-else>
                    <CardContent class="py-16 text-center">
                        <div
                            class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-purple-100"
                        >
                            <Bookmark class="h-10 w-10 text-purple-600" />
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">
                            Chưa có việc làm đã lưu
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Lưu các việc làm bạn quan tâm để xem lại sau!
                        </p>
                        <div class="mt-6">
                            <Link
                                href="/jobs"
                                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                            >
                                <Search class="mr-2 h-4 w-4" />
                                Tìm việc làm
                            </Link>
                        </div>
                    </CardContent>
                </Card>

                <!-- Pagination -->
                <div v-if="savedJobs.data.length > 0" class="mt-6">
                    <Card>
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Hiển thị {{ savedJobs.from }} đến
                                    {{ savedJobs.to }} trong tổng số
                                    {{ savedJobs.total }} việc làm
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
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Bookmark,
    Briefcase,
    Building2,
    Calendar,
    DollarSign,
    MapPin,
    Search,
    X,
} from 'lucide-vue-next';
import { reactive } from 'vue';

interface Props {
    savedJobs: any;
    filters: any;
}

const props = defineProps<Props>();

const localFilters = reactive({ ...props.filters });
let searchTimeout: number | null = null;

const formatDate = (date: string) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatSalary = (min: number, max: number) => {
    if (!min && !max) return 'Thỏa thuận';
    if (!max) return `$${min.toLocaleString()}+`;
    return `$${min.toLocaleString()} - $${max.toLocaleString()}`;
};

const getJobTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
        'full-time': 'Toàn thời gian',
        'part-time': 'Bán thời gian',
        contract: 'Hợp đồng',
        freelance: 'Freelance',
        internship: 'Thực tập',
    };
    return labels[type] || type;
};

const stripHtml = (html: string) => {
    if (typeof document === 'undefined') {
        // SSR fallback
        return html.replace(/<[^>]*>/g, '').substring(0, 150);
    }
    const tmp = document.createElement('DIV');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
};

const applyFilters = () => {
    router.get('/candidate/saved-jobs', localFilters, {
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
    if (confirm('Bạn có chắc muốn bỏ lưu việc làm này?')) {
        router.delete(`/candidate/saved-jobs/${jobId}`, {
            preserveScroll: true,
            onSuccess: () => {
                // Handle success
            },
        });
    }
};
</script>
