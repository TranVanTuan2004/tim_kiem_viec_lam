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
import { Link } from '@inertiajs/vue3';
import { Building2, Clock, DollarSign, Heart, MapPin } from 'lucide-vue-next';
import { defineProps } from 'vue';

const props = defineProps({
    featuredJobs: {
        type: Array as () => any[],
        default: () => [],
    },
});
</script>

<template>
    <section
        class="relative overflow-hidden bg-gradient-to-b from-background to-muted/20 py-20"
    >
        <!-- Background Decoration -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute top-10 -right-20 h-64 w-64 rounded-full bg-red-100/50 opacity-20 blur-3xl dark:bg-red-900/20"
            ></div>
            <div
                class="absolute bottom-10 -left-20 h-64 w-64 rounded-full bg-orange-100/50 opacity-20 blur-3xl dark:bg-orange-900/20"
            ></div>
        </div>

        <div class="relative z-10 container mx-auto px-4">
            <!-- Section Header -->
            <div class="mb-12 text-center">
                <div
                    class="mb-4 inline-flex items-center gap-2 rounded-full bg-red-50 px-4 py-2 text-sm font-medium text-red-600 dark:bg-red-950"
                >
                    <span class="relative flex h-2 w-2">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex h-2 w-2 rounded-full bg-red-500"
                        ></span>
                    </span>
                    Hot Jobs
                </div>
                <h2 class="mb-3 text-4xl font-bold tracking-tight md:text-5xl">
                    Việc làm <span class="text-red-600">nổi bật</span>
                </h2>
                <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                    Cơ hội việc làm IT hấp dẫn nhất từ các công ty hàng đầu
                </p>
            </div>

            <!-- Jobs Grid -->
            <div class="mb-12 grid grid-cols-1 gap-6 md:grid-cols-2">
                <Link
                    v-for="job in props.featuredJobs"
                    :key="job.id"
                    :href="`/jobs/${job.slug}`"
                >
                    <Card
                        class="group relative h-full cursor-pointer overflow-hidden border-2 transition-all duration-300 hover:-translate-y-2 hover:border-red-200 hover:shadow-2xl dark:hover:border-red-900"
                    >
                        <!-- Featured Badge -->
                        <div class="absolute top-0 right-0 z-10">
                            <div
                                class="flex items-center gap-1 rounded-bl-lg bg-gradient-to-r from-red-600 to-orange-500 px-3 py-1.5 text-xs font-bold text-white shadow-lg"
                            >
                                <span class="animate-pulse">⭐</span>
                                HOT
                            </div>
                        </div>

                        <CardHeader>
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex flex-1 items-start gap-4">
                                    <!-- Company Logo -->
                                    <div
                                        class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-red-50 to-orange-50 text-3xl ring-2 ring-red-100 transition-transform duration-300 group-hover:scale-110 group-hover:ring-red-200 dark:from-red-950 dark:to-orange-950 dark:ring-red-900"
                                    >
                                        <img
                                            v-if="job.company_logo"
                                            :src="job.company_logo"
                                            :alt="job.company"
                                            class="h-full w-full rounded-xl object-contain p-2"
                                        />
                                        <div v-else class="text-3xl">
                                            {{ job.logo }}
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <CardTitle
                                            class="mb-2 line-clamp-2 text-xl leading-tight font-bold transition-colors group-hover:text-red-600"
                                        >
                                            {{ job.title }}
                                        </CardTitle>
                                        <CardDescription
                                            class="flex items-center gap-2 text-sm"
                                        >
                                            <Building2
                                                class="h-4 w-4 flex-shrink-0"
                                            />
                                            <span
                                                class="truncate font-medium"
                                                >{{ job.company }}</span
                                            >
                                        </CardDescription>
                                    </div>
                                </div>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="flex-shrink-0 text-muted-foreground transition-all hover:scale-110 hover:text-red-600"
                                    @click.prevent
                                >
                                    <Heart class="h-5 w-5" />
                                </Button>
                            </div>
                        </CardHeader>

                        <CardContent>
                            <div class="space-y-4">
                                <!-- Job Info -->
                                <div
                                    class="flex flex-wrap gap-4 text-sm text-muted-foreground"
                                >
                                    <div class="flex items-center gap-1.5">
                                        <MapPin class="h-4 w-4 text-red-500" />
                                        <span>{{ job.location }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <DollarSign
                                            class="h-4 w-4 text-green-500"
                                        />
                                        <span
                                            class="font-medium text-green-600 dark:text-green-400"
                                            >{{ job.salary }}</span
                                        >
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <Clock
                                            class="h-4 w-4 text-orange-500"
                                        />
                                        <span>{{ job.posted }}</span>
                                    </div>
                                </div>

                                <!-- Skills -->
                                <div class="flex flex-wrap gap-2">
                                    <Badge
                                        v-for="skill in job.skills"
                                        :key="skill"
                                        variant="secondary"
                                        class="bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300"
                                    >
                                        {{ skill }}
                                    </Badge>
                                    <Badge
                                        variant="outline"
                                        class="border-red-200 dark:border-red-800"
                                    >
                                        {{ job.type }}
                                    </Badge>
                                </div>

                                <!-- Apply Button -->
                                <Button
                                    class="w-full bg-red-600 font-semibold transition-all group-hover:bg-red-700 hover:bg-red-700 hover:shadow-lg"
                                    size="lg"
                                >
                                    Ứng tuyển ngay →
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>

            <!-- View All Button -->
            <div class="text-center">
                <Link href="/jobs?featured=1">
                    <Button
                        size="lg"
                        variant="outline"
                        class="group gap-2 border-2 border-red-600 px-8 text-red-600 hover:bg-red-600 hover:text-white"
                    >
                        Xem tất cả việc làm nổi bật
                        <span
                            class="transition-transform group-hover:translate-x-1"
                            >→</span
                        >
                    </Button>
                </Link>
            </div>
        </div>
    </section>
</template>
