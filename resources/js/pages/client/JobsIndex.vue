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
import { Input } from '@/components/ui/input';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import {
    Briefcase,
    Building2,
    Clock,
    DollarSign,
    Filter,
    Heart,
    MapPin,
    Search,
    TrendingUp,
    X,
} from 'lucide-vue-next';
import { computed, defineProps, ref } from 'vue';

const props = defineProps({
    jobs: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({
            featured: false,
            q: '',
            location: '',
        }),
    },
});
const toggleFavorite = async (job: any) => {
    const previousState = job?.favorited_by[0]?.pivot.is_favorited;

    job.favorited_by[0].pivot.is_favorited = !previousState;

    try {
        const response = await axios.post(
            `/candidate/favorites/toggle/${job.id}`,
        );
        job.favorited_by[0].pivot.is_favorited = response.data.is_favorited;
        alert(response.data.message);
    } catch (error: unknown) {
        job.favorited_by[0].pivot.is_favorited = previousState;

        let msg = 'Thao tác thất bại, vui lòng thử lại.';
        if (axios.isAxiosError(error) && error.response) {
            msg = error.response.data?.message || msg;
        }
        alert(msg);
    }
};
const formatDate = (dateStr: string) => {
    return new Intl.DateTimeFormat('vi-VN').format(new Date(dateStr));
};
const showFilters = ref(false);
const jobs = ref<any[]>(props?.jobs?.data?.data ?? []);
const pageTitle = computed(() => {
    return props.filters.featured ? 'Việc làm nổi bật' : 'Tất cả việc làm IT';
});

const pageDescription = computed(() => {
    const total = props.jobs.total || 0;
    return props.filters.featured
        ? `Khám phá ${total} việc làm nổi bật`
        : `Khám phá ${total} cơ hội việc làm tuyệt vời`;
});

const hasActiveFilters = computed(
    () => props.filters.featured || props.filters.q || props.filters.location,
);

const searchQuery = ref(props.filters.q || '');

// Live search computed
const filteredJobs = computed(() => {
    if (!searchQuery.value) return jobs.value;

    const keyword = searchQuery.value.toLowerCase();

    return jobs.value.filter((job) => {
        const titleMatch = job.title.toLowerCase().includes(keyword);
        const companyMatch = job.company.company_name
            .toLowerCase()
            .includes(keyword);

        const skillsMatch = Array.isArray(job.skills)
            ? job.skills.some((skill: any) =>
                  (skill.name || skill).toLowerCase().includes(keyword),
              )
            : job.skills.toLowerCase().includes(keyword);

        return titleMatch || companyMatch || skillsMatch;
    });
});

</script>

<template>
    <ClientLayout :title="pageTitle + ' - Job Portal'">
        <!-- Hero Section with Search -->
        <div
            class="relative overflow-hidden bg-gradient-to-br from-red-600 via-red-500 to-orange-500 py-16"
        >
            <div
                class="bg-grid-white/[0.05] absolute inset-0 bg-[size:20px_20px]"
            ></div>
            <div class="relative container mx-auto px-4">
                <div class="mx-auto max-w-3xl text-center text-white">
                    <div class="mb-4 flex items-center justify-center gap-3">
                        <Briefcase class="h-10 w-10" />
                        <h1 class="text-5xl font-bold tracking-tight">
                            {{ pageTitle }}
                        </h1>
                    </div>
                    <p class="mb-8 text-xl text-red-50">
                        {{ pageDescription }}
                    </p>

                    <!-- Active Filters -->
                    <div
                        v-if="hasActiveFilters"
                        class="mb-6 flex flex-wrap items-center justify-center gap-3"
                    >
                        <Badge
                            v-if="filters.featured"
                            class="gap-2 bg-white/20 px-4 py-2 text-sm text-white backdrop-blur-sm"
                        >
                            ⭐ Việc làm nổi bật
                            <Link href="/jobs" class="hover:opacity-75">
                                <X class="h-3 w-3" />
                            </Link>
                        </Badge>
                        <Badge
                            v-if="filters.q"
                            class="gap-2 bg-white/20 px-4 py-2 text-sm text-white backdrop-blur-sm"
                        >
                            Từ khóa: {{ filters.q }}
                            <Link
                                :href="`/jobs?${filters.location ? 'location=' + filters.location : ''}`"
                                class="hover:opacity-75"
                            >
                                <X class="h-3 w-3" />
                            </Link>
                        </Badge>
                        <Badge
                            v-if="filters.location"
                            class="gap-2 bg-white/20 px-4 py-2 text-sm text-white backdrop-blur-sm"
                        >
                            Địa điểm: {{ filters.location }}
                            <Link
                                :href="`/jobs?${filters.q ? 'q=' + filters.q : ''}`"
                                class="hover:opacity-75"
                            >
                                <X class="h-3 w-3" />
                            </Link>
                        </Badge>
                    </div>

                    <!-- Search Bar -->
                    <div class="mx-auto flex max-w-2xl gap-2">
                        <div class="relative flex-1">
                            <Search
                                class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                v-model="searchQuery"
                                placeholder="Tìm kiếm theo vị trí, công ty, kỹ năng..."
                                class="h-12 bg-white pl-10 text-base text-black shadow-lg"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <section class="bg-muted/30 py-12">
            <div class="container mx-auto px-4">
                <!-- Stats & Filter Bar -->
                <div
                    class="mb-6 flex flex-wrap items-center justify-between gap-4"
                >
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <TrendingUp class="h-5 w-5 text-red-600" />
                            <p class="text-sm font-medium">
                                <span class="text-lg font-bold text-red-600">
                                    1 - {{ filteredJobs.length }}
                                </span>
                                trong {{ filteredJobs.length }} việc làm
                            </p>
                        </div>
                        <Badge
                            v-if="props.filters.featured"
                            class="bg-gradient-to-r from-red-600 to-orange-500 text-white"
                        >
                            ⭐ Việc làm nổi bật
                        </Badge>
                    </div>

                    <div class="flex items-center gap-2">
                        <Link v-if="props.filters.featured" href="/jobs">
                            <Button variant="outline" size="sm" class="gap-2">
                                Xem tất cả việc làm
                            </Button>
                        </Link>
                        <Button
                            variant="outline"
                            size="sm"
                            @click="showFilters = !showFilters"
                            class="gap-2"
                        >
                            <Filter class="h-4 w-4" />
                            {{ showFilters ? 'Ẩn bộ lọc' : 'Hiện bộ lọc' }}
                        </Button>
                    </div>
                </div>

                <!-- Filters Panel (Collapsible) -->
                <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-2"
                >
                    <Card v-show="showFilters" class="mb-6 shadow-sm">
                        <CardContent class="p-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium"
                                        >Địa điểm</label
                                    >
                                    <div class="flex flex-wrap gap-2">
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >Tất cả</Badge
                                        >
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >Hà Nội</Badge
                                        >
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >TP.HCM</Badge
                                        >
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >Đà Nẵng</Badge
                                        >
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium"
                                        >Kinh nghiệm</label
                                    >
                                    <div class="flex flex-wrap gap-2">
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >Tất cả</Badge
                                        >
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >Junior</Badge
                                        >
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >Mid</Badge
                                        >
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >Senior</Badge
                                        >
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium"
                                        >Loại hình</label
                                    >
                                    <div class="flex flex-wrap gap-2">
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >Tất cả</Badge
                                        >
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >Full-time</Badge
                                        >
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >Part-time</Badge
                                        >
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium"
                                        >Mức lương</label
                                    >
                                    <div class="flex flex-wrap gap-2">
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >Tất cả</Badge
                                        >
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >0-1000$</Badge
                                        >
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >1000-2000$</Badge
                                        >
                                        <Badge
                                            variant="outline"
                                            class="cursor-pointer hover:bg-red-50"
                                            >2000+$</Badge
                                        >
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </Transition>

                <!-- Jobs Grid -->
                <div
                    v-if="filteredJobs.length > 0"
                    class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3"
                >
                    <Link
                        v-for="job in filteredJobs"
                        :key="job.id"
                        :href="`/jobs/${job.slug}`"
                        class="group"
                    >
                        <Card
                            class="h-full cursor-pointer border-2 border-gray-200 rounded-lg bg-white transition-all duration-300
         hover:-translate-y-1 hover:shadow-xl hover:border-red-500"
                        >
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl 
           bg-gray-100 border-2 border-gray-200 transition-all duration-300
           group-hover:border-red-500"
                                        >
                                            <img
                                                v-if="job.company.logo"
                                                :src="job.company.logo"
                                                class="h-full w-full object-contain"
                                            />
                                            <div v-else class="text-2xl">
                                                {{ job.logo }}
                                            </div>
                                        </div>
                                        <div>
                                            <CardTitle class="font-bold">{{
                                                job.title
                                            }}</CardTitle>
                                            <CardDescription
                                                class="flex items-center gap-1 text-xs"
                                            >
                                                <Building2
                                                    class="h-3.5 w-3.5"
                                                />
                                                {{ job.company.company_name }}
                                            </CardDescription>
                                            <Badge
                                                v-if="job.is_featured"
                                                class="mt-1 bg-red-500 text-[10px] text-white"
                                                >HOT</Badge
                                            >
                                        </div>
                                    </div>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        :class="
                                            job?.favorited_by?.[0]?.pivot
                                                ?.is_favorited
                                                ? 'text-red-600'
                                                : 'text-gray-400 hover:text-red-600'
                                        "
                                        @click.prevent="toggleFavorite(job)"
                                    >
                                        <Heart
                                            :class="
                                                job?.favorited_by?.[0]?.pivot
                                                    ?.is_favorited
                                                    ? 'fill-red-600 text-red-600'
                                                    : 'text-gray-400'
                                            "
                                            class="h-5 w-5"
                                        />
                                    </Button>
                                </div>
                            </CardHeader>

                            <CardContent class="space-y-2">
                                <div
                                    class="flex flex-wrap gap-2 text-xs text-gray-600"
                                >
                                    <div class="flex items-center gap-1">
                                        <MapPin class="h-3.5 w-3.5 text-orange-600" />
                                        <span>{{ job.location }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <DollarSign class="h-3.5 w-3.5 text-green-600" />
                                        <span class="text-green-600"
                                            >{{
                                                Number(job.min_salary).toFixed(
                                                    0,
                                                )
                                            }}$ -
                                            {{
                                                Number(job.max_salary).toFixed(
                                                    0,
                                                )
                                            }}$</span
                                        >
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <Clock class="h-3.5 w-3.5 text-orange-600" />
                                        <span>{{
                                            formatDate(job.created_at)
                                        }}</span>
                                    </div>
                                </div>

                                <!-- Skills -->
                                <div class="flex flex-wrap gap-1">
                                    <Badge
                                        v-for="skill in job.skills"
                                        :key="skill.id || skill"
                                        variant="secondary"
                                        class="text-[10px]"
                                    >
                                        {{ skill.name || skill }}
                                    </Badge>
                                </div>

                                <!-- Apply Button -->
                                <Button class="mt-2 w-full bg-black text-white transition-colors duration-300 hover:bg-orange-600"
                                    >Ứng tuyển ngay →</Button
                                >
                            </CardContent>
                        </Card>
                    </Link>
                </div>

                <!-- Pagination -->
                <div
                    v-if="props.jobs.links && props.jobs.last_page > 1"
                    class="mt-12 flex flex-wrap justify-center gap-2"
                >
                    <template
                        v-for="(page, index) in props.jobs.links"
                        :key="index"
                    >
                        <Link
                            v-if="page.url"
                            :href="page.url"
                            :class="[
                                'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition-all',
                                page.active
                                    ? 'pointer-events-none bg-gradient-to-r from-red-600 to-orange-500 text-white shadow-md'
                                    : 'border-2 bg-background hover:border-red-200 hover:bg-red-50 hover:text-red-600',
                            ]"
                            :preserve-scroll="true"
                            v-html="page.label"
                        >
                        </Link>
                        <span
                            v-else
                            :class="[
                                'inline-flex items-center justify-center rounded-lg border-2 bg-background px-4 py-2 text-sm font-medium',
                                'cursor-not-allowed opacity-40',
                            ]"
                            v-html="page.label"
                        >
                        </span>
                    </template>
                </div>

                <!-- Empty State -->
                <div
                    v-if="!props.jobs.data || props.jobs.data.length === 0"
                    class="py-16 text-center"
                >
                    <div
                        class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-muted"
                    >
                        <Briefcase class="h-12 w-12 text-muted-foreground" />
                    </div>
                    <h3 class="mb-2 text-xl font-semibold">
                        Không tìm thấy việc làm nào
                    </h3>
                    <p class="text-muted-foreground">
                        Thử điều chỉnh bộ lọc hoặc tìm kiếm với từ khóa khác
                    </p>
                </div>
            </div>
        </section>
    </ClientLayout>
</template>
