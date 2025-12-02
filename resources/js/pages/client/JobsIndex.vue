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
import { getCompanyLogoUrl } from '@/utils/storage';
import { Link, router } from '@inertiajs/vue3';
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
import { computed, defineProps, nextTick, onMounted, ref } from 'vue';

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
    // Kiểm tra và khởi tạo favorited_by nếu không tồn tại hoặc rỗng
    if (!job.favorited_by || job.favorited_by.length === 0) {
        job.favorited_by = [{
            pivot: { is_favorited: false }
        }];
    }

    const previousState = job.favorited_by[0].pivot.is_favorited;

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
const jobs = ref<any[]>(props?.jobs?.data ?? []);
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

// Search functionality
const searchQuery = ref(props.filters.q || '');
const locationQuery = ref(props.filters.location || '');

// Handle search
const handleSearch = () => {
    const params: any = {};

    if (searchQuery.value) {
        params.q = searchQuery.value;
    }

    if (locationQuery.value) {
        params.location = locationQuery.value;
    }

    if (props.filters.featured) {
        params.featured = '1';
    }

    router.get('/jobs', params, {
        preserveState: false,
        preserveScroll: false,
    });
};

// Scroll animations
onMounted(() => {
    nextTick(() => {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px',
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in-view');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        const elements = document.querySelectorAll('.animate-on-scroll');
        elements.forEach((el) => observer.observe(el));
    });
});
</script>

<template>
    <ClientLayout :title="pageTitle + ' - Job Portal'">
        <!-- Enhanced Hero Section with Search -->
        <div
            class="relative overflow-hidden bg-gradient-to-br from-red-600 via-red-500 to-orange-500 py-20 md:py-24"
        >
            <!-- Animated Background -->
            <div class="absolute inset-0 overflow-hidden">
                <div
                    class="bg-grid-white/[0.05] absolute inset-0 bg-[size:20px_20px]"
                ></div>
                <!-- Floating Orbs -->
                <div
                    class="animate-float-orb absolute top-20 left-10 h-64 w-64 rounded-full bg-yellow-400/20 blur-3xl"
                    style="animation-delay: 0s"
                ></div>
                <div
                    class="animate-float-orb absolute top-40 right-20 h-80 w-80 rounded-full bg-orange-400/20 blur-3xl"
                    style="animation-delay: 2s"
                ></div>
                <div
                    class="animate-float-orb absolute bottom-20 left-1/3 h-72 w-72 rounded-full bg-pink-400/20 blur-3xl"
                    style="animation-delay: 4s"
                ></div>
            </div>

            <div class="relative z-10 container mx-auto px-4">
                <div class="mx-auto max-w-3xl text-center text-white">
                    <div
                        class="animate-on-scroll mb-6 flex items-center justify-center gap-3"
                    >
                        <div
                            class="rounded-full bg-white/20 p-3 backdrop-blur-sm"
                        >
                            <Briefcase class="h-10 w-10 animate-pulse" />
                        </div>
                        <h1
                            class="bg-gradient-to-r from-white via-yellow-100 to-white bg-clip-text text-5xl font-extrabold tracking-tight text-transparent drop-shadow-2xl md:text-6xl lg:text-7xl"
                        >
                            {{ pageTitle }}
                        </h1>
                    </div>
                    <p
                        class="animate-on-scroll mb-8 text-xl text-red-50/95 drop-shadow-lg md:text-2xl"
                        style="animation-delay: 0.1s"
                    >
                        {{ pageDescription }}
                    </p>

                    <!-- Enhanced Active Filters -->
                    <div
                        v-if="hasActiveFilters"
                        class="animate-on-scroll mb-6 flex flex-wrap items-center justify-center gap-3"
                        style="animation-delay: 0.2s"
                    >
                        <Badge
                            v-if="filters.featured"
                            class="gap-2 border border-white/30 bg-white/20 px-4 py-2 text-sm font-semibold text-white backdrop-blur-md transition-all duration-300 hover:scale-105 hover:bg-white/30"
                        >
                            ⭐ Việc làm nổi bật
                            <Link
                                href="/jobs"
                                class="transition-opacity hover:opacity-75"
                            >
                                <X class="h-3 w-3" />
                            </Link>
                        </Badge>
                        <Badge
                            v-if="filters.q"
                            class="gap-2 border border-white/30 bg-white/20 px-4 py-2 text-sm font-semibold text-white backdrop-blur-md transition-all duration-300 hover:scale-105 hover:bg-white/30"
                        >
                            Từ khóa: {{ filters.q }}
                            <Link
                                :href="`/jobs?${filters.location ? 'location=' + filters.location : ''}`"
                                class="transition-opacity hover:opacity-75"
                            >
                                <X class="h-3 w-3" />
                            </Link>
                        </Badge>
                        <Badge
                            v-if="filters.location"
                            class="gap-2 border border-white/30 bg-white/20 px-4 py-2 text-sm font-semibold text-white backdrop-blur-md transition-all duration-300 hover:scale-105 hover:bg-white/30"
                        >
                            Địa điểm: {{ filters.location }}
                            <Link
                                :href="`/jobs?${filters.q ? 'q=' + filters.q : ''}`"
                                class="transition-opacity hover:opacity-75"
                            >
                                <X class="h-3 w-3" />
                            </Link>
                        </Badge>
                    </div>

                    <!-- Enhanced Search Bar -->
                    <div
                        class="animate-on-scroll mx-auto flex max-w-2xl gap-2"
                        style="animation-delay: 0.3s"
                    >
                        <div class="group relative flex-1">
                            <Search
                                class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-red-600"
                            />
                            <Input
                                v-model="searchQuery"
                                placeholder="Tìm kiếm theo vị trí, công ty, kỹ năng..."
                                class="h-12 border-2 bg-white/95 pl-10 text-base shadow-xl backdrop-blur-sm transition-all duration-300 focus-visible:border-red-500 focus-visible:ring-red-500"
                                @keyup.enter="handleSearch"
                            />
                        </div>
                        <Button
                            size="lg"
                            class="group/btn relative h-12 overflow-hidden bg-white px-8 font-semibold text-red-600 shadow-xl transition-all duration-300 hover:scale-105 hover:bg-red-50 hover:shadow-2xl"
                            @click="handleSearch"
                        >
                            <span class="relative z-10">Tìm kiếm</span>
                            <span
                                class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-red-50/50 to-transparent transition-transform duration-1000 group-hover/btn:translate-x-full"
                            ></span>
                        </Button>
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
                                    1 - {{ jobs.length }}
                                </span>
                                trong {{ jobs.length }} việc làm
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

                <!-- Enhanced Jobs Grid -->
                <div
                    v-if="jobs.length > 0"
                    class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        v-for="(job, index) in jobs"
                        :key="job.id"
                        class="group job-card animate-on-scroll"
                        :style="{ animationDelay: `${index * 0.1}s` }"
                    >
                        <Card
                            class="relative h-full cursor-pointer overflow-hidden border-2 bg-card transition-all duration-500 hover:-translate-y-2 hover:border-red-300 hover:shadow-2xl"
                        >
                            <!-- Clickable Overlay for Navigation -->
                            <Link 
                                :href="`/jobs/${job.slug}`"
                                class="absolute inset-0 z-10"
                                aria-label="View job details"
                            ></Link>

                            <!-- Shine Effect -->
                            <div
                                class="pointer-events-none absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-1000 group-hover:translate-x-full"
                            ></div>

                            <!-- Gradient Border on Hover -->
                            <div
                                class="pointer-events-none absolute inset-0 rounded-xl border-2 border-transparent bg-gradient-to-r from-red-500 via-orange-500 to-red-500 [mask-composite:exclude] opacity-0 transition-opacity duration-500 [mask:linear-gradient(#fff_0_0)_padding-box,linear-gradient(#fff_0_0)] group-hover:opacity-100"
                            ></div>
                            <CardHeader class="pb-3 relative z-20">
                                <div class="flex items-start justify-between">
                                    <div class="flex flex-1 items-start gap-3">
                                        <div
                                            class="relative flex h-14 w-14 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-red-50 via-orange-50 to-yellow-50 shadow-lg ring-2 ring-red-100 transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 group-hover:ring-red-300"
                                        >
                                            <img
                                                :src="
                                                    getCompanyLogoUrl(
                                                        job.company.logo,
                                                        job.company
                                                            .company_name,
                                                    )
                                                "
                                                :alt="job.company.company_name"
                                                class="relative z-10 h-full w-full object-contain p-2"
                                            />
                                            <!-- Glow Effect -->
                                            <div
                                                class="absolute inset-0 rounded-xl bg-gradient-to-br from-red-400/30 to-orange-400/30 opacity-0 blur-xl transition-opacity duration-500 group-hover:opacity-100"
                                            ></div>
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
                                        class="relative z-30 flex-shrink-0 mt-8"
                                        :class="
                                            job?.favorited_by?.[0]?.pivot
                                                ?.is_favorited
                                                ? 'text-red-600'
                                                : 'text-gray-400 hover:text-red-600'
                                        "
                                        @click.prevent.stop="toggleFavorite(job)"
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

                            <CardContent class="space-y-2 relative z-20">
                                <div
                                    class="flex flex-wrap gap-2 text-xs text-gray-600"
                                >
                                    <div class="flex items-center gap-1">
                                        <MapPin
                                            class="h-3.5 w-3.5 text-orange-600"
                                        />
                                        <span>{{ job.location }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <DollarSign
                                            class="h-3.5 w-3.5 text-green-600"
                                        />
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
                                        <Clock
                                            class="h-3.5 w-3.5 text-orange-600"
                                        />
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

                                <!-- Enhanced Apply Button -->
                                <Button
                                    class="group/btn relative w-full overflow-hidden bg-gradient-to-r from-red-600 via-orange-600 to-red-600 font-semibold shadow-lg transition-all duration-500 hover:scale-105 hover:from-red-700 hover:via-orange-700 hover:to-red-700 hover:shadow-xl"
                                    variant="default"
                                >
                                    <span
                                        class="relative z-10 flex items-center justify-center"
                                    >
                                        Ứng tuyển ngay
                                        <span
                                            class="ml-2 transition-transform duration-300 group-hover/btn:translate-x-1"
                                            >→</span
                                        >
                                    </span>
                                    <!-- Shine Effect -->
                                    <span
                                        class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/30 to-transparent transition-transform duration-1000 group-hover/btn:translate-x-full"
                                    ></span>
                                </Button>
                            </CardContent>
                        </Card>
                    </div>
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

                <!-- Enhanced Empty State -->
                <div
                    v-if="!props.jobs.data || props.jobs.data.length === 0"
                    class="animate-on-scroll py-20 text-center"
                >
                    <div
                        class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-red-100 to-orange-100 shadow-lg dark:from-red-950 dark:to-orange-950"
                    >
                        <Briefcase
                            class="h-12 w-12 animate-pulse text-red-500"
                        />
                    </div>
                    <h3 class="mb-3 text-2xl font-bold">
                        Không tìm thấy việc làm nào
                    </h3>
                    <p class="mb-6 text-lg text-muted-foreground">
                        Thử điều chỉnh bộ lọc hoặc tìm kiếm với từ khóa khác
                    </p>
                    <Button
                        size="lg"
                        class="bg-gradient-to-r from-red-600 to-orange-500 font-semibold shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl"
                        @click="showFilters = true"
                    >
                        <Filter class="mr-2 h-5 w-5" />
                        Mở bộ lọc
                    </Button>
                </div>
            </div>
        </section>
    </ClientLayout>
</template>

<style scoped>
/* Scroll Animations */
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition:
        opacity 0.8s ease-out,
        transform 0.8s ease-out;
}

.animate-on-scroll.animate-in-view {
    opacity: 1;
    transform: translateY(0);
}

.job-card {
    opacity: 0;
}

/* Float Orb Animation */
@keyframes float-orb {
    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(50px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-30px, 30px) scale(0.9);
    }
}

.animate-float-orb {
    animation: float-orb 8s ease-in-out infinite;
}
</style>
