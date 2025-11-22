<template>
    <CandidateLayout>
        <Head title="Bảng điều khiển" />

        <div
            class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <div
                    class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 shadow-xl"
                >
                    <!-- <div class="inset-0 bg-black/10"></div> -->
                    <div class="relative px-8 py-10 sm:px-12">
                        <h1 class="text-3xl font-bold text-white sm:text-4xl">
                            Chào mừng trở lại, {{ $page.props.auth.user.name }}!
                        </h1>
                        <p class="mt-3 text-lg text-blue-100">
                            Tổng quan về hoạt động tìm việc của bạn
                        </p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div
                    class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <Card class="group transition-shadow hover:shadow-lg">
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p
                                        class="text-sm font-medium text-gray-600"
                                    >
                                        Tổng đơn ứng tuyển
                                    </p>
                                    <p
                                        class="mt-2 text-3xl font-bold text-gray-900"
                                    >
                                        {{ stats.total_applications }}
                                    </p>
                                </div>
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-xl bg-blue-100 transition-colors group-hover:bg-blue-200"
                                >
                                    <FileText class="h-8 w-8 text-blue-600" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="group transition-shadow hover:shadow-lg">
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p
                                        class="text-sm font-medium text-gray-600"
                                    >
                                        Đang chờ xử lý
                                    </p>
                                    <p
                                        class="mt-2 text-3xl font-bold text-yellow-600"
                                    >
                                        {{ stats.pending_applications }}
                                    </p>
                                </div>
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-xl bg-yellow-100 transition-colors group-hover:bg-yellow-200"
                                >
                                    <Clock class="h-8 w-8 text-yellow-600" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="group transition-shadow hover:shadow-lg">
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p
                                        class="text-sm font-medium text-gray-600"
                                    >
                                        Đã được chọn
                                    </p>
                                    <p
                                        class="mt-2 text-3xl font-bold text-green-600"
                                    >
                                        {{ stats.shortlisted_applications }}
                                    </p>
                                </div>
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-xl bg-green-100 transition-colors group-hover:bg-green-200"
                                >
                                    <CheckCircle
                                        class="h-8 w-8 text-green-600"
                                    />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="group transition-shadow hover:shadow-lg">
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p
                                        class="text-sm font-medium text-gray-600"
                                    >
                                        Việc làm đã lưu
                                    </p>
                                    <p
                                        class="mt-2 text-3xl font-bold text-purple-600"
                                    >
                                        {{ stats.saved_jobs }}
                                    </p>
                                </div>
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-xl bg-purple-100 transition-colors group-hover:bg-purple-200"
                                >
                                    <Bookmark class="h-8 w-8 text-purple-600" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="group transition-shadow hover:shadow-lg">
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p
                                        class="text-sm font-medium text-gray-600"
                                    >
                                        Portfolio
                                    </p>
                                    <p
                                        class="mt-2 text-3xl font-bold text-indigo-600"
                                    >
                                        {{ stats.portfolios }}
                                    </p>
                                </div>
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-xl bg-indigo-100 transition-colors group-hover:bg-indigo-200"
                                >
                                    <Briefcase
                                        class="h-8 w-8 text-indigo-600"
                                    />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="group transition-shadow hover:shadow-lg">
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p
                                        class="text-sm font-medium text-gray-600"
                                    >
                                        Lượt xem hồ sơ
                                    </p>
                                    <p
                                        class="mt-2 text-3xl font-bold text-pink-600"
                                    >
                                        {{ stats.profile_views }}
                                    </p>
                                </div>
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-xl bg-pink-100 transition-colors group-hover:bg-pink-200"
                                >
                                    <Eye class="h-8 w-8 text-pink-600" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Recent Applications -->
                    <div class="lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <CardTitle class="flex items-center gap-2">
                                        <FileText
                                            class="h-5 w-5 text-blue-600"
                                        />
                                        Đơn ứng tuyển gần đây
                                    </CardTitle>
                                    <Button as-child variant="ghost" size="sm">
                                        <Link href="/candidate/applications">
                                            Xem tất cả
                                            <ArrowRight class="ml-1 h-4 w-4" />
                                        </Link>
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div
                                    v-if="recentApplications.length > 0"
                                    class="space-y-4"
                                >
                                    <div
                                        v-for="application in recentApplications"
                                        :key="application.id"
                                        class="group rounded-lg border border-gray-200 bg-white p-4 transition-all hover:border-blue-300 hover:shadow-md"
                                    >
                                        <div class="flex items-start space-x-4">
                                            <img
                                                v-if="
                                                    application.job_posting
                                                        .company.logo
                                                "
                                                :src="
                                                    application.job_posting
                                                        .company.logo
                                                "
                                                :alt="
                                                    application.job_posting
                                                        .company.name
                                                "
                                                class="h-14 w-14 rounded-lg border border-gray-200 object-cover"
                                            />
                                            <div
                                                v-else
                                                class="flex h-14 w-14 items-center justify-center rounded-lg bg-gray-100"
                                            >
                                                <Building2
                                                    class="h-7 w-7 text-gray-400"
                                                />
                                            </div>
                                            <div class="flex-1">
                                                <Link
                                                    :href="`/jobs/${application.job_posting.id}`"
                                                    class="text-lg font-semibold text-gray-900 transition-colors hover:text-blue-600"
                                                >
                                                    {{
                                                        application.job_posting
                                                            .title
                                                    }}
                                                </Link>
                                                <p
                                                    class="mt-1 text-sm text-gray-600"
                                                >
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
                                                    class="mt-3 flex items-center gap-3"
                                                >
                                                    <Badge
                                                        :variant="
                                                            getStatusVariant(
                                                                application.status,
                                                            )
                                                        "
                                                        class="text-xs"
                                                    >
                                                        {{
                                                            getStatusLabel(
                                                                application.status,
                                                            )
                                                        }}
                                                    </Badge>
                                                    <span
                                                        class="text-xs text-gray-500"
                                                    >
                                                        <Calendar
                                                            class="mr-1 inline h-3 w-3"
                                                        />
                                                        {{
                                                            formatDate(
                                                                application.applied_at,
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                            <Button
                                                as-child
                                                variant="ghost"
                                                size="sm"
                                            >
                                                <Link
                                                    :href="`/candidate/applications/${application.id}`"
                                                >
                                                    Xem chi tiết
                                                </Link>
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="py-12 text-center">
                                    <FileText
                                        class="mx-auto h-12 w-12 text-gray-400"
                                    />
                                    <h3
                                        class="mt-4 text-sm font-medium text-gray-900"
                                    >
                                        Chưa có đơn ứng tuyển
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Bắt đầu ứng tuyển để xem chúng ở đây!
                                    </p>
                                    <div class="mt-6">
                                        <Button as-child>
                                            <Link href="/jobs">
                                                Tìm việc làm
                                            </Link>
                                        </Button>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Quick Actions Sidebar -->
                    <div class="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle class="text-lg">
                                    Thao tác nhanh
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-3">
                                <Button
                                    as-child
                                    variant="outline"
                                    class="w-full justify-start"
                                >
                                    <Link href="/jobs">
                                        <Search class="mr-2 h-4 w-4" />
                                        Tìm việc làm
                                    </Link>
                                </Button>
                                <Button
                                    as-child
                                    variant="outline"
                                    class="w-full justify-start"
                                >
                                    <Link href="/profile">
                                        <User class="mr-2 h-4 w-4" />
                                        Xem hồ sơ
                                    </Link>
                                </Button>
                                <Button
                                    as-child
                                    variant="outline"
                                    class="w-full justify-start"
                                >
                                    <Link href="/candidate/portfolios">
                                        <Briefcase class="mr-2 h-4 w-4" />
                                        Quản lý Portfolio
                                    </Link>
                                </Button>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle class="text-lg">
                                    Thống kê nhanh
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div
                                    class="flex items-center justify-between rounded-lg bg-blue-50 p-3"
                                >
                                    <span
                                        class="text-sm font-medium text-gray-700"
                                    >
                                        Tỷ lệ được chọn
                                    </span>
                                    <span
                                        v-if="stats.total_applications > 0"
                                        class="text-sm font-bold text-blue-600"
                                    >
                                        {{
                                            Math.round(
                                                (stats.shortlisted_applications /
                                                    stats.total_applications) *
                                                    100,
                                            )
                                        }}%
                                    </span>
                                    <span v-else class="text-sm text-gray-500">
                                        0%
                                    </span>
                                </div>
                                <div
                                    class="flex items-center justify-between rounded-lg bg-green-50 p-3"
                                >
                                    <span
                                        class="text-sm font-medium text-gray-700"
                                    >
                                        Hồ sơ hoàn chỉnh
                                    </span>
                                    <Badge
                                        v-if="candidateProfile"
                                        variant="default"
                                    >
                                        Đã hoàn thành
                                    </Badge>
                                    <Badge v-else variant="secondary">
                                        Chưa hoàn thành
                                    </Badge>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- Recommended Jobs -->
                <Card class="mt-6">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Sparkles class="h-5 w-5 text-blue-600" />
                            Việc làm đề xuất
                        </CardTitle>
                        <CardDescription>
                            Dựa trên hồ sơ và kỹ năng của bạn
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="recommendedJobs.length > 0"
                            class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
                        >
                            <div
                                v-for="job in recommendedJobs"
                                :key="job.id"
                                class="group overflow-hidden rounded-lg border border-gray-200 bg-white transition-all hover:border-blue-300 hover:shadow-lg"
                            >
                                <div class="p-5">
                                    <div class="flex items-start space-x-4">
                                        <img
                                            v-if="job.company.logo"
                                            :src="job.company.logo"
                                            :alt="job.company.name"
                                            class="h-12 w-12 rounded-lg border border-gray-200 object-cover"
                                        />
                                        <div
                                            v-else
                                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100"
                                        >
                                            <Building2
                                                class="h-6 w-6 text-gray-400"
                                            />
                                        </div>
                                        <div class="flex-1">
                                            <Link
                                                :href="`/jobs/${job.id}`"
                                                class="text-lg font-semibold text-gray-900 transition-colors hover:text-blue-600"
                                            >
                                                {{ job.title }}
                                            </Link>
                                            <p
                                                class="mt-1 text-sm text-gray-600"
                                            >
                                                {{ job.company.name }}
                                            </p>
                                            <div
                                                class="mt-3 flex flex-wrap items-center gap-2"
                                            >
                                                <Badge
                                                    variant="outline"
                                                    class="text-xs"
                                                >
                                                    <MapPin
                                                        class="mr-1 h-3 w-3"
                                                    />
                                                    {{ job.location }}
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
                                            </div>
                                            <Button
                                                as-child
                                                class="mt-4 w-full"
                                                size="sm"
                                            >
                                                <Link :href="`/jobs/${job.id}`">
                                                    Xem chi tiết
                                                </Link>
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-12 text-center">
                            <Sparkles class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-4 text-sm font-medium text-gray-900">
                                Chưa có việc làm đề xuất
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Hoàn thiện hồ sơ của bạn để nhận được đề xuất
                                tốt hơn!
                            </p>
                            <div class="mt-6">
                                <Button as-child variant="outline">
                                    <Link href="/profile">
                                        Hoàn thiện hồ sơ
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </CandidateLayout>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import CandidateLayout from '@/layouts/CandidateLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Bookmark,
    Briefcase,
    Building2,
    Calendar,
    CheckCircle,
    Clock,
    DollarSign,
    Eye,
    FileText,
    MapPin,
    Search,
    Sparkles,
    User,
} from 'lucide-vue-next';

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
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        pending: 'Đang chờ',
        reviewed: 'Đã xem',
        shortlisted: 'Đã chọn',
        rejected: 'Từ chối',
        accepted: 'Chấp nhận',
        withdrawn: 'Đã rút',
    };
    return labels[status] || status;
};

const getStatusVariant = (
    status: string,
): 'default' | 'secondary' | 'destructive' | 'outline' => {
    const variants: Record<
        string,
        'default' | 'secondary' | 'destructive' | 'outline'
    > = {
        pending: 'secondary',
        reviewed: 'outline',
        shortlisted: 'default',
        rejected: 'destructive',
        accepted: 'default',
        withdrawn: 'secondary',
    };
    return variants[status] || 'secondary';
};

const formatSalary = (min: number, max: number) => {
    if (!min && !max) return 'Thỏa thuận';
    if (!max) return `$${min.toLocaleString()}+`;
    return `$${min.toLocaleString()} - $${max.toLocaleString()}`;
};
</script>

