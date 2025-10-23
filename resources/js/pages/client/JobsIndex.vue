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
} from 'lucide-vue-next';
import { defineProps, ref } from 'vue';

const props = defineProps({
    jobs: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({
            featured: false,
        }),
    },
});

const showFilters = ref(false);
</script>

<template>
    <ClientLayout title="Tất cả việc làm - Job Portal">
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
                            {{
                                props.filters.featured
                                    ? 'Việc làm nổi bật'
                                    : 'Tất cả việc làm IT'
                            }}
                        </h1>
                    </div>
                    <p class="mb-8 text-xl text-red-50">
                        Khám phá
                        <span class="font-bold text-white">{{
                            props.jobs.total || 0
                        }}</span>
                        {{
                            props.filters.featured
                                ? 'việc làm nổi bật'
                                : 'cơ hội việc làm tuyệt vời'
                        }}
                    </p>

                    <!-- Search Bar -->
                    <div class="mx-auto flex max-w-2xl gap-2">
                        <div class="relative flex-1">
                            <Search
                                class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                placeholder="Tìm kiếm theo vị trí, công ty, kỹ năng..."
                                class="h-12 bg-white pl-10 text-base shadow-lg"
                            />
                        </div>
                        <Button
                            size="lg"
                            class="h-12 bg-white px-8 text-red-600 hover:bg-red-50"
                        >
                            Tìm kiếm
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
                                <span class="text-lg font-bold text-red-600"
                                    >{{ props.jobs.from || 0 }} -
                                    {{ props.jobs.to || 0 }}</span
                                >
                                <span class="text-muted-foreground">
                                    trong {{ props.jobs.total || 0 }} việc làm
                                </span>
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
                    v-if="props.jobs.data && props.jobs.data.length > 0"
                    class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3"
                >
                    <Link
                        v-for="job in props.jobs.data"
                        :key="job.id"
                        :href="`/jobs/${job.slug}`"
                        class="group"
                    >
                        <Card
                            class="h-full cursor-pointer border-2 bg-card transition-all duration-300 hover:-translate-y-1 hover:border-red-200 hover:shadow-2xl"
                        >
                            <CardHeader class="pb-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex flex-1 items-start gap-3">
                                        <div
                                            class="flex h-14 w-14 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-red-50 to-orange-50 ring-2 ring-red-100 transition-transform duration-300 group-hover:scale-110 group-hover:ring-red-200"
                                        >
                                            <img
                                                v-if="job.company_logo"
                                                :src="job.company_logo"
                                                :alt="job.company"
                                                class="h-full w-full object-contain p-2"
                                            />
                                            <div v-else class="text-2xl">
                                                {{ job.logo }}
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="mb-2">
                                                <CardTitle
                                                    class="line-clamp-2 text-lg leading-tight font-bold transition-colors group-hover:text-red-600"
                                                >
                                                    {{ job.title }}
                                                </CardTitle>
                                                <Badge
                                                    v-if="job.is_featured"
                                                    class="mt-1.5 animate-pulse bg-gradient-to-r from-red-600 to-orange-500 text-[10px] font-semibold"
                                                >
                                                    ⭐ HOT
                                                </Badge>
                                            </div>
                                            <CardDescription
                                                class="flex items-center gap-1.5 text-xs"
                                            >
                                                <Building2
                                                    class="h-3.5 w-3.5 flex-shrink-0"
                                                />
                                                <span class="truncate">{{
                                                    job.company
                                                }}</span>
                                            </CardDescription>
                                        </div>
                                    </div>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8 flex-shrink-0 text-muted-foreground transition-colors hover:text-red-600"
                                        @click.prevent
                                    >
                                        <Heart class="h-4 w-4" />
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <!-- Job Info -->
                                <div
                                    class="flex flex-wrap gap-x-4 gap-y-2 text-xs text-muted-foreground"
                                >
                                    <div class="flex items-center gap-1.5">
                                        <MapPin class="h-3.5 w-3.5" />
                                        <span>{{ job.location }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <DollarSign class="h-3.5 w-3.5" />
                                        <span class="font-medium">{{
                                            job.salary
                                        }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <Clock class="h-3.5 w-3.5" />
                                        <span>{{ job.posted }}</span>
                                    </div>
                                </div>

                                <!-- Skills -->
                                <div class="flex flex-wrap gap-1.5">
                                    <Badge
                                        v-for="skill in job.skills"
                                        :key="skill"
                                        variant="secondary"
                                        class="text-[10px]"
                                    >
                                        {{ skill }}
                                    </Badge>
                                    <Badge
                                        variant="outline"
                                        class="text-[10px]"
                                    >
                                        {{ job.type }}
                                    </Badge>
                                </div>

                                <!-- Apply Button -->
                                <Button
                                    class="w-full transition-all group-hover:bg-red-600 group-hover:shadow-md"
                                    variant="default"
                                >
                                    Ứng tuyển ngay →
                                </Button>
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
