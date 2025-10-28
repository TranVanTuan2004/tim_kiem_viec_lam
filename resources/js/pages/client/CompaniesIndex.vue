<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useLanguage } from '@/composables/useLanguage';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { router } from '@inertiajs/vue3';
import {
    ArrowRight,
    Briefcase,
    Building2,
    CheckCircle2,
    ChevronLeft,
    ChevronRight,
    Filter,
    MapPin,
    Search,
    Sparkles,
    Star,
    TrendingUp,
    Users,
} from 'lucide-vue-next';
import { computed, defineProps, ref } from 'vue';

const props = defineProps({
    companies: {
        type: Object as () => any,
        required: true,
    },
    hasJobsFilter: {
        type: Boolean,
        default: false,
    },
});

const companiesData = computed(() => props.companies?.data || []);
const pagination = computed(() => ({
    current_page: props.companies?.current_page || 1,
    last_page: props.companies?.last_page || 1,
    per_page: props.companies?.per_page || 12,
    total: props.companies?.total || 0,
}));

const formatDate = (dateString: string) => {
    if (!dateString) return 'N/A';
    try {
        return new Date(dateString).toLocaleDateString('vi-VN');
    } catch (e) {
        return 'N/A';
    }
};

const getCompanySizeText = (size: string) => {
    if (!size) return 'Không xác định';

    const sizeMap: Record<string, string> = {
        '1-10': '1-10 nhân viên',
        '11-50': '11-50 nhân viên',
        '51-200': '51-200 nhân viên',
        '200+': '200+ nhân viên',
    };

    return sizeMap[size] || size;
};

const getLocationText = (company: any) => {
    const city = company.city;
    const province = company.province;

    if (city && province) {
        return `${city}, ${province}`;
    }
    return city || province || 'Không xác định';
};

const searchQuery = ref('');
const showFilters = ref(false);

const { t, currentLanguage } = useLanguage();

// Dynamic translations for company size based on language
const getCompanySizeTextTranslated = (size: string) => {
    if (!size)
        return currentLanguage.value === 'vi'
            ? 'Không xác định'
            : 'Not specified';

    const sizeMapVi: Record<string, string> = {
        '1-10': '1-10 nhân viên',
        '11-50': '11-50 nhân viên',
        '51-200': '51-200 nhân viên',
        '200+': '200+ nhân viên',
    };

    const sizeMapEn: Record<string, string> = {
        '1-10': '1-10 employees',
        '11-50': '11-50 employees',
        '51-200': '51-200 employees',
        '200+': '200+ employees',
    };

    const sizeMap = currentLanguage.value === 'vi' ? sizeMapVi : sizeMapEn;
    return sizeMap[size] || size;
};
</script>

<template>
    <ClientLayout :title="t.companiesPageTitle">
        <!-- Hero Section -->
        <section
            class="relative overflow-hidden bg-gradient-to-br from-red-50 via-orange-50 to-background py-20 dark:from-red-950/20 dark:via-orange-950/20"
        >
            <!-- Background Decorations -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div
                    class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-gradient-to-br from-red-200 to-orange-200 opacity-30 blur-3xl dark:from-red-800 dark:to-orange-800"
                ></div>
                <div
                    class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-gradient-to-tr from-orange-200 to-red-200 opacity-30 blur-3xl dark:from-orange-800 dark:to-red-800"
                ></div>
                <!-- Grid Pattern -->
                <div
                    class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:14px_24px]"
                ></div>
            </div>

            <div class="relative z-10 container mx-auto px-4">
                <!-- Badge -->
                <div class="mb-6 flex justify-center">
                    <div
                        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-red-50 to-orange-50 px-5 py-2.5 text-sm font-semibold text-red-600 shadow-lg ring-1 ring-red-100 dark:from-red-950 dark:to-orange-950 dark:ring-red-900"
                    >
                        <Building2 class="h-4 w-4" />
                        <span>{{ pagination.total }} {{ t.companies }}</span>
                        <div class="relative flex h-2 w-2">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"
                            ></span>
                            <span
                                class="relative inline-flex h-2 w-2 rounded-full bg-red-500"
                            ></span>
                        </div>
                    </div>
                </div>

                <!-- Title -->
                <div class="mb-8 text-center">
                    <h1
                        class="mb-4 text-5xl font-bold tracking-tight md:text-6xl"
                    >
                        <span
                            v-if="hasJobsFilter"
                            class="bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent"
                        >
                            {{ t.companiesHiring }}
                        </span>
                        <span v-else>
                            {{ t.companiesPageTitle }}
                        </span>
                    </h1>
                    <p class="mx-auto max-w-3xl text-lg text-muted-foreground">
                        {{
                            hasJobsFilter
                                ? t.discoverCompanies
                                : t.discoverAllCompanies
                        }}
                    </p>
                </div>

                <!-- Search and Filters -->
                <div class="mx-auto max-w-4xl">
                    <Card class="border-2 shadow-xl">
                        <CardContent class="p-6">
                            <div class="flex flex-col gap-4 lg:flex-row">
                                <!-- Search Box -->
                                <div class="flex-1">
                                    <div class="relative">
                                        <Search
                                            class="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-muted-foreground"
                                        />
                                        <Input
                                            v-model="searchQuery"
                                            :placeholder="t.searchByCompanyName"
                                            class="h-12 border-2 pl-12 text-base focus-visible:ring-red-500"
                                        />
                                    </div>
                                </div>

                                <!-- Filter Buttons -->
                                <div class="flex gap-2">
                                    <Button
                                        variant="outline"
                                        class="h-12 border-2 px-6 font-semibold hover:border-red-300 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950"
                                        @click="showFilters = !showFilters"
                                    >
                                        <Filter class="mr-2 h-4 w-4" />
                                        {{ t.filter }}
                                    </Button>
                                    <Button
                                        class="h-12 bg-gradient-to-r from-red-600 to-orange-500 px-8 font-semibold shadow-lg hover:shadow-xl"
                                    >
                                        <Search class="mr-2 h-4 w-4" />
                                        {{ t.search }}
                                    </Button>
                                </div>
                            </div>

                            <!-- Advanced Filters (Optional) -->
                            <div
                                v-if="showFilters"
                                class="mt-6 space-y-4 border-t pt-6"
                            >
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium"
                                        >
                                            {{ t.industry }}
                                        </label>
                                        <Button
                                            variant="outline"
                                            class="w-full justify-start border-2"
                                        >
                                            <TrendingUp class="mr-2 h-4 w-4" />
                                            {{ t.selectIndustry }}
                                        </Button>
                                    </div>
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium"
                                        >
                                            {{ t.location }}
                                        </label>
                                        <Button
                                            variant="outline"
                                            class="w-full justify-start border-2"
                                        >
                                            <MapPin class="mr-2 h-4 w-4" />
                                            {{ t.selectLocation }}
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Companies Section -->
        <div class="bg-gradient-to-b from-background to-muted/20">
            <div class="container mx-auto px-4 py-16">
                <!-- Stats Bar -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold">
                            {{ t.found }} {{ pagination.total }}
                            {{ t.companies }}
                        </h2>
                        <p class="text-muted-foreground">
                            {{ t.page }} {{ pagination.current_page }} /
                            {{ pagination.last_page }}
                        </p>
                    </div>
                    <div
                        class="flex items-center gap-4 text-sm text-muted-foreground"
                    >
                        <div class="flex items-center gap-2">
                            <Sparkles class="h-4 w-4 text-yellow-500" />
                            <span
                                >{{
                                    companiesData.filter(
                                        (c: any) => c.is_verified,
                                    ).length
                                }}
                                {{ t.verifiedCompanies }}</span
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <Briefcase class="h-4 w-4 text-red-500" />
                            <span
                                >{{
                                    companiesData.filter(
                                        (c: any) => c.job_postings_count > 0,
                                    ).length
                                }}
                                {{ t.hiring }}</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Companies Grid -->
                <div
                    class="mb-12 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
                >
                    <Card
                        v-for="company in companiesData"
                        :key="company.id"
                        class="group relative cursor-pointer overflow-hidden border-2 transition-all duration-300 hover:-translate-y-2 hover:border-red-200 hover:shadow-2xl dark:hover:border-red-800"
                        @click="
                            router.visit(`/companies/${company.company_slug}`)
                        "
                    >
                        <!-- Top Gradient Banner -->
                        <div
                            class="relative h-32 overflow-hidden bg-gradient-to-br from-red-500 via-orange-500 to-red-600"
                        >
                            <!-- Pattern Overlay -->
                            <div
                                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMSIgb3BhY2l0eT0iMC4xIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+')] opacity-30"
                            ></div>

                            <!-- Verified Badge -->
                            <div
                                v-if="
                                    company.is_verified ||
                                    (company.rating && company.rating >= 4.5)
                                "
                                class="absolute top-3 right-3 flex items-center gap-1 rounded-full bg-white/95 px-2.5 py-1 text-xs font-bold text-green-600 shadow-lg backdrop-blur-sm dark:bg-gray-900/95"
                            >
                                <CheckCircle2 class="h-3 w-3" />
                                <span>Verified</span>
                            </div>

                            <!-- Hot Jobs Badge -->
                            <div
                                v-if="company.job_postings_count > 5"
                                class="absolute top-3 left-3 flex items-center gap-1 rounded-full bg-gradient-to-r from-yellow-400 to-orange-400 px-2.5 py-1 text-xs font-bold text-white shadow-lg"
                            >
                                <Sparkles class="h-3 w-3" />
                                <span>Hot</span>
                            </div>

                            <!-- Floating Elements -->
                            <div class="absolute inset-0">
                                <div
                                    class="absolute top-4 left-4 h-16 w-16 rounded-full bg-white/10 blur-xl"
                                ></div>
                                <div
                                    class="absolute right-4 bottom-4 h-20 w-20 rounded-full bg-white/10 blur-xl"
                                ></div>
                            </div>
                        </div>

                        <CardContent class="relative px-5 pt-0 pb-6">
                            <!-- Company Logo -->
                            <div
                                class="absolute -top-14 left-1/2 -translate-x-1/2"
                            >
                                <div
                                    class="relative flex h-28 w-28 items-center justify-center overflow-hidden rounded-2xl border-4 border-background bg-white shadow-2xl ring-4 ring-red-50 transition-all duration-300 group-hover:scale-110 group-hover:ring-red-100 dark:bg-card dark:ring-red-950"
                                >
                                    <img
                                        v-if="company.logo"
                                        :src="company.logo"
                                        :alt="company.company_name"
                                        class="h-full w-full object-contain p-3"
                                    />
                                    <Building2
                                        v-else
                                        class="h-12 w-12 text-muted-foreground"
                                    />

                                    <!-- Shine Effect on Hover -->
                                    <div
                                        class="absolute inset-0 translate-x-[-100%] bg-gradient-to-tr from-transparent via-white/20 to-transparent transition-transform duration-1000 group-hover:translate-x-[100%]"
                                    ></div>
                                </div>
                            </div>

                            <!-- Company Info -->
                            <div class="space-y-4 pt-16 text-center">
                                <!-- Company Name -->
                                <h3
                                    class="line-clamp-2 text-lg leading-tight font-bold transition-colors group-hover:text-red-600"
                                >
                                    {{ company.company_name }}
                                </h3>

                                <!-- Rating -->
                                <div
                                    v-if="company.rating && company.rating > 0"
                                    class="flex items-center justify-center gap-2"
                                >
                                    <div class="flex items-center gap-1">
                                        <Star
                                            v-for="i in 5"
                                            :key="i"
                                            :class="[
                                                'h-4 w-4 transition-all',
                                                i <= Math.floor(company.rating)
                                                    ? 'fill-yellow-400 text-yellow-400'
                                                    : 'fill-gray-200 text-gray-200 dark:fill-gray-700 dark:text-gray-700',
                                            ]"
                                        />
                                    </div>
                                    <span class="text-sm font-semibold">
                                        {{ Number(company.rating).toFixed(1) }}
                                    </span>
                                    <span class="text-xs text-muted-foreground">
                                        ({{ company.total_reviews || 0 }})
                                    </span>
                                </div>

                                <!-- Industry Badge -->
                                <div v-if="company.industry">
                                    <Badge
                                        variant="secondary"
                                        class="font-medium"
                                    >
                                        {{ company.industry }}
                                    </Badge>
                                </div>

                                <!-- Description -->
                                <p
                                    class="line-clamp-2 min-h-[2.5rem] text-sm text-muted-foreground"
                                >
                                    {{ company.description || t.noDescription }}
                                </p>

                                <!-- Divider -->
                                <div
                                    class="mx-auto h-px w-16 bg-gradient-to-r from-transparent via-red-300 to-transparent"
                                ></div>

                                <!-- Stats -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div
                                        class="flex flex-col items-center gap-1 rounded-lg bg-muted/50 p-3 transition-colors hover:bg-muted"
                                    >
                                        <MapPin class="h-4 w-4 text-red-500" />
                                        <span
                                            class="line-clamp-1 text-xs font-medium"
                                        >
                                            {{ getLocationText(company) }}
                                        </span>
                                    </div>
                                    <div
                                        class="flex flex-col items-center gap-1 rounded-lg bg-muted/50 p-3 transition-colors hover:bg-muted"
                                    >
                                        <Users
                                            class="h-4 w-4 text-orange-500"
                                        />
                                        <span class="text-xs font-medium">
                                            {{
                                                getCompanySizeTextTranslated(
                                                    company.size,
                                                )
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Jobs Badge -->
                                <div
                                    v-if="company.job_postings_count > 0"
                                    class="flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-red-50 to-orange-50 p-3 dark:from-red-950/50 dark:to-orange-950/50"
                                >
                                    <Briefcase class="h-4 w-4 text-red-600" />
                                    <span
                                        class="text-sm font-bold text-red-600"
                                    >
                                        {{ company.job_postings_count }}
                                        {{ t.jobs }}
                                    </span>
                                </div>
                                <div v-else class="rounded-lg bg-muted/50 p-3">
                                    <span class="text-xs text-muted-foreground">
                                        {{ t.noJobs }}
                                    </span>
                                </div>

                                <!-- CTA Button -->
                                <Button
                                    class="group/btn w-full bg-gradient-to-r from-red-600 to-orange-500 font-semibold shadow-lg transition-all hover:scale-105 hover:shadow-xl"
                                    @click.stop="
                                        router.visit(
                                            `/companies/${company.company_slug}`,
                                        )
                                    "
                                >
                                    <span>{{ t.viewDetails }}</span>
                                    <ArrowRight
                                        class="ml-2 h-4 w-4 transition-transform group-hover/btn:translate-x-1"
                                    />
                                </Button>
                            </div>
                        </CardContent>

                        <!-- Hover Glow Effect -->
                        <div
                            class="absolute inset-0 -z-10 rounded-lg bg-gradient-to-r from-red-600/0 via-orange-600/0 to-red-600/0 opacity-0 blur-xl transition-opacity group-hover:opacity-30"
                        ></div>
                    </Card>
                </div>

                <!-- Pagination -->
                <div
                    v-if="pagination.last_page > 1"
                    class="flex justify-center"
                >
                    <Card class="border-2 shadow-lg">
                        <CardContent class="p-4">
                            <div class="flex items-center gap-2">
                                <Button
                                    variant="outline"
                                    size="icon"
                                    class="h-10 w-10"
                                    :disabled="pagination.current_page === 1"
                                    @click="
                                        router.visit(
                                            `/companies?page=${pagination.current_page - 1}`,
                                        )
                                    "
                                >
                                    <ChevronLeft class="h-4 w-4" />
                                </Button>

                                <div class="flex items-center gap-1">
                                    <Button
                                        v-for="page in Math.min(
                                            5,
                                            pagination.last_page,
                                        )"
                                        :key="page"
                                        :variant="
                                            page === pagination.current_page
                                                ? 'default'
                                                : 'outline'
                                        "
                                        :class="[
                                            'h-10 w-10',
                                            page === pagination.current_page
                                                ? 'bg-gradient-to-r from-red-600 to-orange-500 font-bold shadow-lg'
                                                : 'hover:border-red-300 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950',
                                        ]"
                                        size="sm"
                                        @click="
                                            router.visit(
                                                `/companies?page=${page}`,
                                            )
                                        "
                                    >
                                        {{ page }}
                                    </Button>
                                    <span
                                        v-if="pagination.last_page > 5"
                                        class="px-2 text-muted-foreground"
                                    >
                                        ...
                                    </span>
                                    <Button
                                        v-if="pagination.last_page > 5"
                                        variant="outline"
                                        class="h-10 w-10 hover:border-red-300 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950"
                                        size="sm"
                                        @click="
                                            router.visit(
                                                `/companies?page=${pagination.last_page}`,
                                            )
                                        "
                                    >
                                        {{ pagination.last_page }}
                                    </Button>
                                </div>

                                <Button
                                    variant="outline"
                                    size="icon"
                                    class="h-10 w-10"
                                    :disabled="
                                        pagination.current_page ===
                                        pagination.last_page
                                    "
                                    @click="
                                        router.visit(
                                            `/companies?page=${pagination.current_page + 1}`,
                                        )
                                    "
                                >
                                    <ChevronRight class="h-4 w-4" />
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Empty State -->
                <div v-if="companiesData.length === 0" class="py-20">
                    <Card class="border-2 border-dashed">
                        <CardContent class="py-16 text-center">
                            <div
                                class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-red-100 to-orange-100 dark:from-red-950 dark:to-orange-950"
                            >
                                <Building2 class="h-12 w-12 text-red-500" />
                            </div>
                            <h3 class="mb-3 text-2xl font-bold">
                                {{
                                    hasJobsFilter
                                        ? 'Không có công ty nào đang tuyển dụng'
                                        : 'Không tìm thấy công ty nào'
                                }}
                            </h3>
                            <p class="mb-6 text-lg text-muted-foreground">
                                {{
                                    hasJobsFilter
                                        ? 'Hiện tại chưa có công ty nào đăng việc làm. Hãy quay lại sau!'
                                        : 'Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm của bạn'
                                }}
                            </p>
                            <Button
                                size="lg"
                                class="bg-gradient-to-r from-red-600 to-orange-500 font-semibold shadow-lg"
                                @click="router.visit('/jobs')"
                            >
                                <Briefcase class="mr-2 h-5 w-5" />
                                Xem việc làm
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
