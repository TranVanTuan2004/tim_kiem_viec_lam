<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import {
    Briefcase,
    Building2,
    Calendar,
    CheckCircle,
    Clock,
    DollarSign,
    ExternalLink,
    Globe,
    MapPin,
    Star,
    Users,
} from 'lucide-vue-next';
import { computed, defineProps } from 'vue';

const props = defineProps({
    company: {
        type: Object as () => any,
        required: true,
    },
    recentJobs: {
        type: Array as () => any[],
        default: () => [],
    },
    reviews: {
        type: Object as () => any,
        default: () => ({ data: [] }),
    },
    ratingStats: {
        type: Object as () => any,
        default: () => ({}),
    },
});

// Computed properties
const companyData = computed(() => props.company || {});
const recentJobsData = computed(() => props.recentJobs || []);
const reviewsData = computed(() => props.reviews?.data || []);
const ratingStatsData = computed(() => props.ratingStats || {});

const averageRating = computed(() => ratingStatsData.value.average_rating || 0);
const totalReviews = computed(() => ratingStatsData.value.total_reviews || 0);

const companySizeText = computed(() => {
    const size = companyData.value.size;
    if (!size) return 'Không xác định';

    const sizeMap: Record<string, string> = {
        '1-10': '1-10 nhân viên',
        '11-50': '11-50 nhân viên',
        '51-200': '51-200 nhân viên',
        '200+': '200+ nhân viên',
    };

    return sizeMap[size] || size;
});

const locationText = computed(() => {
    const city = companyData.value.city;
    const province = companyData.value.province;

    if (city && province) {
        return `${city}, ${province}`;
    }
    return city || province || 'Không xác định';
});

const formatDate = (dateString: string) => {
    try {
        return new Date(dateString).toLocaleDateString('vi-VN');
    } catch (e) {
        return 'N/A';
    }
};

const getSalaryText = (job: any) => {
    const min = job.min_salary;
    const max = job.max_salary;
    const type = job.salary_type || '';

    if (min && max) return `${min} - ${max} ${type}`;
    if (min) return `Từ ${min} ${type}`;
    if (max) return `Đến ${max} ${type}`;
    return 'Thỏa thuận';
};

const getEmploymentTypeText = (type: string) => {
    if (!type) return 'N/A';
    return type.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
};

const getExperienceLevelText = (level: string) => {
    if (!level) return 'N/A';
    return level.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
};

const renderStars = (rating: number) => {
    const stars = [];
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;

    for (let i = 0; i < fullStars; i++) {
        stars.push('★');
    }
    if (hasHalfStar) {
        stars.push('☆');
    }
    const emptyStars = 5 - Math.ceil(rating);
    for (let i = 0; i < emptyStars; i++) {
        stars.push('☆');
    }

    return stars.join('');
};

const getRatingPercentage = (starCount: number) => {
    if (totalReviews.value === 0) return 0;
    return Math.round((starCount / totalReviews.value) * 100);
};
</script>

<template>
    <ClientLayout :title="companyData.name || 'Chi tiết công ty'">
        <div class="bg-muted/40">
            <div class="container mx-auto px-4 py-12">
                <!-- Breadcrumb -->
                <div class="mb-6">
                    <div class="flex items-center gap-4 text-sm">
                        <Link
                            href="/"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            Trang chủ
                        </Link>
                        <span class="text-muted-foreground">/</span>
                        <Link
                            href="/companies"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            Danh sách công ty
                        </Link>
                        <span class="text-muted-foreground">/</span>
                        <span class="font-medium text-foreground">
                            {{ companyData.name }}
                        </span>
                    </div>
                </div>

                <!-- Company Header -->
                <div class="mb-8">
                    <div class="flex items-start gap-6">
                        <!-- Company Logo -->
                        <div class="flex-shrink-0">
                            <div
                                class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-lg border bg-white shadow-sm"
                            >
                                <img
                                    v-if="companyData.logo"
                                    :src="companyData.logo"
                                    :alt="companyData.name"
                                    class="h-full w-full object-cover"
                                />
                                <Building2
                                    v-else
                                    class="h-12 w-12 text-muted-foreground"
                                />
                            </div>
                        </div>

                        <!-- Company Info -->
                        <div class="flex-1">
                            <div class="mb-2 flex items-center gap-2">
                                <h1 class="text-3xl font-bold">
                                    {{ companyData.name }}
                                </h1>
                                <CheckCircle
                                    v-if="companyData.is_verified"
                                    class="h-6 w-6 text-green-600"
                                />
                            </div>

                            <div
                                class="mb-4 flex items-center gap-4 text-muted-foreground"
                            >
                                <span class="flex items-center gap-1">
                                    <MapPin class="h-4 w-4" />
                                    {{ locationText }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <Users class="h-4 w-4" />
                                    {{ companySizeText }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <Building2 class="h-4 w-4" />
                                    {{ companyData.industry || 'N/A' }}
                                </span>
                            </div>

                            <!-- Rating -->
                            <div
                                v-if="totalReviews > 0"
                                class="flex items-center gap-2"
                            >
                                <div class="flex items-center gap-1">
                                    <Star
                                        class="h-5 w-5 fill-current text-yellow-500"
                                    />
                                    <span class="font-semibold">{{
                                        averageRating
                                    }}</span>
                                </div>
                                <span class="text-muted-foreground"
                                    >({{ totalReviews }} đánh giá)</span
                                >
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <Button
                                v-if="companyData.website"
                                variant="outline"
                                @click="
                                    () =>
                                        (globalThis as any).window.open(
                                            companyData.website,
                                            '_blank',
                                        )
                                "
                            >
                                <Globe class="mr-2 h-4 w-4" />
                                Website
                            </Button>
                            <Button variant="default">
                                <Briefcase class="mr-2 h-4 w-4" />
                                Xem việc làm
                            </Button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Main Content -->
                    <div class="space-y-8 lg:col-span-2">
                        <!-- Company Description -->
                        <Card>
                            <CardHeader>
                                <CardTitle>Giới thiệu công ty</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div
                                    class="prose dark:prose-invert max-w-none"
                                    v-html="
                                        companyData.description ||
                                        'Chưa có thông tin mô tả.'
                                    "
                                ></div>
                            </CardContent>
                        </Card>

                        <!-- Recent Jobs -->
                        <Card v-if="recentJobsData.length > 0">
                            <CardHeader>
                                <CardTitle>Việc làm đang tuyển</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-4">
                                    <div
                                        v-for="job in recentJobsData"
                                        :key="job.id"
                                        class="cursor-pointer rounded-lg border p-4 transition-colors hover:bg-muted/50"
                                        @click="
                                            router.visit(`/jobs/${job.slug}`)
                                        "
                                    >
                                        <div
                                            class="flex items-start justify-between"
                                        >
                                            <div class="flex-1">
                                                <h3
                                                    class="mb-2 text-lg font-semibold"
                                                >
                                                    {{ job.title }}
                                                </h3>
                                                <div
                                                    class="mb-2 flex items-center gap-4 text-sm text-muted-foreground"
                                                >
                                                    <span
                                                        class="flex items-center gap-1"
                                                    >
                                                        <MapPin
                                                            class="h-4 w-4"
                                                        />
                                                        {{ job.location }}
                                                    </span>
                                                    <span
                                                        class="flex items-center gap-1"
                                                    >
                                                        <Briefcase
                                                            class="h-4 w-4"
                                                        />
                                                        {{
                                                            getEmploymentTypeText(
                                                                job.employment_type,
                                                            )
                                                        }}
                                                    </span>
                                                    <span
                                                        class="flex items-center gap-1"
                                                    >
                                                        <DollarSign
                                                            class="h-4 w-4"
                                                        />
                                                        {{ getSalaryText(job) }}
                                                    </span>
                                                </div>
                                                <div
                                                    class="flex items-center gap-4 text-sm text-muted-foreground"
                                                >
                                                    <span>{{
                                                        getExperienceLevelText(
                                                            job.experience_level,
                                                        )
                                                    }}</span>
                                                    <span
                                                        class="flex items-center gap-1"
                                                    >
                                                        <Clock
                                                            class="h-4 w-4"
                                                        />
                                                        {{
                                                            formatDate(
                                                                job.published_at,
                                                            )
                                                        }}
                                                    </span>
                                                </div>

                                                <!-- Skills -->
                                                <div
                                                    v-if="
                                                        job.skills &&
                                                        job.skills.length > 0
                                                    "
                                                    class="mt-3 flex flex-wrap gap-2"
                                                >
                                                    <Badge
                                                        v-for="skill in job.skills"
                                                        :key="skill.id"
                                                        variant="secondary"
                                                        class="text-xs"
                                                    >
                                                        {{ skill.name }}
                                                    </Badge>
                                                </div>
                                            </div>
                                            <ExternalLink
                                                class="h-4 w-4 text-muted-foreground"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 text-center">
                                    <Button
                                        variant="outline"
                                        @click="
                                            router.visit(
                                                `/companies/${companyData.slug}/jobs`,
                                            )
                                        "
                                    >
                                        Xem tất cả việc làm
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Reviews Section -->
                        <Card v-if="reviewsData.length > 0">
                            <CardHeader>
                                <CardTitle>Đánh giá từ nhân viên</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-6">
                                    <div
                                        v-for="review in reviewsData"
                                        :key="review.id"
                                        class="border-b pb-4 last:border-b-0"
                                    >
                                        <div class="flex items-start gap-4">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-muted"
                                                >
                                                    <Users
                                                        class="h-5 w-5 text-muted-foreground"
                                                    />
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div
                                                    class="mb-2 flex items-center gap-2"
                                                >
                                                    <div
                                                        class="flex items-center gap-1"
                                                    >
                                                        <Star
                                                            v-for="i in 5"
                                                            :key="i"
                                                            class="h-4 w-4"
                                                            :class="
                                                                i <=
                                                                review.rating
                                                                    ? 'fill-current text-yellow-500'
                                                                    : 'text-gray-300'
                                                            "
                                                        />
                                                    </div>
                                                    <span
                                                        class="text-sm text-muted-foreground"
                                                    >
                                                        {{
                                                            formatDate(
                                                                review.created_at,
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                                <h4 class="mb-1 font-semibold">
                                                    {{ review.title }}
                                                </h4>
                                                <p
                                                    class="text-muted-foreground"
                                                >
                                                    {{ review.review }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Company Info Card -->
                        <Card>
                            <CardHeader>
                                <CardTitle>Thông tin công ty</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div
                                    v-if="companyData.website"
                                    class="flex items-center gap-2"
                                >
                                    <Globe
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <a
                                        :href="companyData.website"
                                        target="_blank"
                                        class="text-blue-600 hover:underline"
                                    >
                                        {{ companyData.website }}
                                    </a>
                                </div>

                                <div class="flex items-center gap-2">
                                    <MapPin
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <span>{{
                                        companyData.address || locationText
                                    }}</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <Users
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <span>{{ companySizeText }}</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <Building2
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <span>{{
                                        companyData.industry || 'N/A'
                                    }}</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <Calendar
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <span
                                        >Thành lập:
                                        {{
                                            formatDate(companyData.created_at)
                                        }}</span
                                    >
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Rating Breakdown -->
                        <Card v-if="totalReviews > 0">
                            <CardHeader>
                                <CardTitle>Đánh giá chi tiết</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-3">
                                    <div
                                        v-for="rating in [5, 4, 3, 2, 1]"
                                        :key="rating"
                                        class="flex items-center gap-2"
                                    >
                                        <span
                                            class="inline-flex w-8 items-center gap-1 text-sm"
                                        >
                                            {{ rating }}
                                            <Star
                                                class="h-3.5 w-3.5 fill-current text-yellow-500"
                                            />
                                        </span>
                                        <div
                                            class="h-2 flex-1 rounded-full bg-muted"
                                        >
                                            <div
                                                class="h-2 rounded-full bg-yellow-500"
                                                :style="{
                                                    width: `${getRatingPercentage(ratingStatsData[`${rating}_star`] || 0)}%`,
                                                }"
                                            ></div>
                                        </div>
                                        <span
                                            class="w-8 text-sm text-muted-foreground"
                                        >
                                            {{
                                                ratingStatsData[
                                                    `${rating}_star`
                                                ] || 0
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <Separator class="my-4" />

                                <div class="text-center">
                                    <div class="text-3xl font-bold">
                                        {{ averageRating }}
                                    </div>
                                    <div
                                        class="mb-2 flex items-center justify-center gap-1"
                                    >
                                        <Star
                                            v-for="i in 5"
                                            :key="i"
                                            class="h-5 w-5"
                                            :class="
                                                i <= Math.floor(averageRating)
                                                    ? 'fill-current text-yellow-500'
                                                    : 'text-gray-300'
                                            "
                                        />
                                    </div>
                                    <div class="text-sm text-muted-foreground">
                                        Dựa trên {{ totalReviews }} đánh giá
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            <Button
                                v-if="companyData.website"
                                variant="outline"
                                class="w-full"
                                @click="
                                    () =>
                                        (globalThis as any).window.open(
                                            companyData.website,
                                            '_blank',
                                        )
                                "
                            >
                                <Globe class="mr-2 h-4 w-4" />
                                Truy cập website
                            </Button>

                            <Button
                                class="w-full"
                                @click="
                                    router.visit(
                                        `/companies/${companyData.slug}/jobs`,
                                    )
                                "
                            >
                                <Briefcase class="mr-2 h-4 w-4" />
                                Xem tất cả việc làm
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<style scoped>
.prose h2 {
    margin-bottom: 1rem;
}
.prose p {
    margin-bottom: 1.5rem;
}
</style>
