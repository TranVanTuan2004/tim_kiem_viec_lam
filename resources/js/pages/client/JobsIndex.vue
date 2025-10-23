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
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Link } from '@inertiajs/vue3';
import {
    Building2,
    Clock,
    DollarSign,
    Heart,
    MapPin,
    X,
} from 'lucide-vue-next';
import { computed, defineProps } from 'vue';

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

const pageTitle = computed(() =>
    props.filters.featured ? 'Việc làm nổi bật' : 'Tất cả việc làm IT',
);

const pageDescription = computed(() => {
    const total = props.jobs.total || 0;
    return props.filters.featured
        ? `Khám phá ${total} việc làm nổi bật`
        : `Khám phá ${total} cơ hội việc làm tuyệt vời`;
});

const hasActiveFilters = computed(
    () => props.filters.featured || props.filters.q || props.filters.location,
);
</script>

<template>
    <ClientLayout :title="pageTitle + ' - Job Portal'">
        <!-- Hero Header -->
        <section class="bg-gradient-to-r from-red-600 to-orange-500 py-16">
            <div class="container mx-auto px-4">
                <div class="text-center text-white">
                    <h1 class="mb-4 text-5xl font-bold tracking-tight">
                        {{ pageTitle }}
                    </h1>
                    <p class="mb-8 text-xl text-red-50">
                        {{ pageDescription }}
                    </p>

                    <!-- Active Filters -->
                    <div
                        v-if="hasActiveFilters"
                        class="flex flex-wrap items-center justify-center gap-3"
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
                </div>
            </div>
        </section>

        <!-- Jobs List -->
        <section class="bg-background py-12">
            <div class="container mx-auto px-4">
                <!-- Jobs Grid -->
                <div
                    v-if="jobs.data && jobs.data.length > 0"
                    class="mb-12 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <Link
                        v-for="job in jobs.data"
                        :key="job.id"
                        :href="`/jobs/${job.slug}`"
                    >
                        <Card
                            class="group h-full cursor-pointer transition-all hover:-translate-y-1 hover:shadow-lg"
                        >
                            <CardHeader>
                                <div class="flex items-start justify-between">
                                    <div
                                        class="flex flex-1 items-start space-x-4"
                                    >
                                        <div
                                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-red-50 to-orange-50 text-2xl dark:from-red-950 dark:to-orange-950"
                                        >
                                            {{ job.logo }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <CardTitle
                                                class="mb-1 line-clamp-2 text-lg transition-colors group-hover:text-red-600"
                                            >
                                                {{ job.title }}
                                            </CardTitle>
                                            <CardDescription
                                                class="flex items-center space-x-2 text-sm"
                                            >
                                                <Building2
                                                    class="h-3 w-3 flex-shrink-0"
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
                                        class="flex-shrink-0 text-muted-foreground hover:text-red-600"
                                        @click.prevent
                                    >
                                        <Heart class="h-4 w-4" />
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-3">
                                    <!-- Job Info -->
                                    <div
                                        class="flex flex-wrap gap-3 text-xs text-muted-foreground"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <MapPin class="h-3 w-3" />
                                            <span>{{ job.location }}</span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <DollarSign class="h-3 w-3" />
                                            <span>{{ job.salary }}</span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <Clock class="h-3 w-3" />
                                            <span>{{ job.posted }}</span>
                                        </div>
                                    </div>

                                    <!-- Skills -->
                                    <div class="flex flex-wrap gap-2">
                                        <Badge
                                            v-for="skill in job.skills"
                                            :key="skill"
                                            variant="secondary"
                                            class="text-xs"
                                        >
                                            {{ skill }}
                                        </Badge>
                                        <Badge
                                            v-if="job.is_featured"
                                            class="bg-gradient-to-r from-red-600 to-orange-500 text-xs text-white"
                                        >
                                            ⭐ Featured
                                        </Badge>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                </div>

                <!-- No Results -->
                <div v-else class="py-20 text-center">
                    <div class="mx-auto mb-4 text-6xl">😔</div>
                    <h3 class="mb-2 text-xl font-semibold">
                        Không tìm thấy việc làm
                    </h3>
                    <p class="mb-6 text-muted-foreground">
                        Thử điều chỉnh bộ lọc hoặc tìm kiếm của bạn
                    </p>
                    <Link href="/jobs">
                        <Button variant="outline">Xóa bộ lọc</Button>
                    </Link>
                </div>

                <!-- Pagination -->
                <div
                    v-if="jobs.data && jobs.data.length > 0"
                    class="flex items-center justify-center gap-2"
                >
                    <Link
                        v-for="link in jobs.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'rounded-lg px-4 py-2 text-sm font-medium transition-colors',
                            link.active
                                ? 'bg-red-600 text-white'
                                : link.url
                                  ? 'bg-white text-foreground hover:bg-red-50 dark:bg-card dark:hover:bg-red-950'
                                  : 'cursor-not-allowed bg-muted text-muted-foreground',
                        ]"
                        :disabled="!link.url"
                        v-html="link.label"
                    />
                </div>
            </div>
        </section>
    </ClientLayout>
</template>
