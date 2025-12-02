<script setup lang="ts">
import axios from 'axios';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { useLanguage } from '@/composables/useLanguage';
import { Link } from '@inertiajs/vue3';
import { Building2, Clock, DollarSign, Heart, MapPin } from 'lucide-vue-next';

import { getCompanyLogoUrl } from '@/utils/storage';
import { useClientToast } from '@/composables/useClientToast';

const { showToast } = useClientToast();

const toggleFavorite = async (job: any) => {
//   const previousState = job.is_favorited;
//   job.is_favorited = !job.is_favorited;

//   try {
//     const response = await axios.post(`/candidate/favorites/toggle/${job.id}`);
//     job.is_favorited = response.data.is_favorited;
//     alert(response.data.message); // hiển thị message từ backend
//   } catch (error: unknown) {
//     job.is_favorited = previousState;

//     let msg = 'Thao tác thất bại, vui lòng thử lại.';

//     if (axios.isAxiosError(error) && error.response) {
//         msg = error.response.data?.message || msg;
//     }

//     alert(msg);
//     }
    job = job || { is_favorited: false };

    const previousState = job.is_favorited;

    job.is_favorited = !previousState;

    try {
        const response = await axios.post(`/candidate/favorites/toggle/${job.id}`);

        job.is_favorited = response.data.is_favorited;

        showToast(
            'success',
            response.data.is_favorited ? 'Đã lưu' : 'Đã xóa',
            response.data.message
        );
    } catch (error: unknown) {
        job.is_favorited = previousState;

        let msg = 'Thao tác thất bại, vui lòng thử lại.';
        if (axios.isAxiosError(error) && error.response) {
        msg = error.response.data?.message || msg;
        }
        showToast('error', 'Lỗi', msg);
    }
};

const props = defineProps({
    featuredJobs: {
        type: Array as () => any[],
        default: () => [],
    },
});

const { t } = useLanguage();
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
            <!-- Enhanced Section Header -->
            <div class="mb-16 text-center animate-fade-in-up">
                <div
                    class="mb-6 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950 dark:to-orange-950 px-5 py-2.5 text-sm font-semibold text-red-600 shadow-lg border border-red-100 dark:border-red-900"
                >
                    <span class="relative flex h-2 w-2">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex h-2 w-2 rounded-full bg-red-500"
                        ></span>
                    </span>
                    {{ t.hotJobs }}
                </div>
                <h2 class="mb-4 text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight bg-gradient-to-r from-gray-900 via-red-600 to-orange-600 dark:from-white dark:via-red-400 dark:to-orange-400 bg-clip-text text-transparent">
                    {{ t.featuredJobs }}
                </h2>
                <p class="mx-auto max-w-2xl text-lg md:text-xl text-muted-foreground leading-relaxed">
                    {{ t.featuredJobsDescription }}
                </p>
            </div>

            <!-- Enhanced Jobs Grid -->
            <div class="mb-12 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div
                    v-for="(job, index) in props.featuredJobs"
                    :key="job.id"
                    class="job-card animate-fade-in-up"
                    :style="{ animationDelay: `${index * 0.1}s` }"
                >
                    <Card
                        class="group relative h-full cursor-pointer overflow-hidden border-2 bg-card transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl dark:border-gray-800"
                    >
                        <!-- Clickable Overlay for Navigation -->
                        <Link 
                            :href="`/jobs/${job.slug}`"
                            class="absolute inset-0 z-10"
                            aria-label="View job details"
                        ></Link>

                        <!-- Animated Gradient Border -->
                        <div class="pointer-events-none absolute inset-0 rounded-xl border-2 border-transparent bg-gradient-to-r from-red-500 via-orange-500 to-red-500 opacity-0 transition-opacity duration-500 group-hover:opacity-100 [mask:linear-gradient(#fff_0_0)_padding-box,linear-gradient(#fff_0_0)] [mask-composite:exclude]"></div>
                        
                        <!-- Shine Effect -->
                        <div class="pointer-events-none absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/30 to-transparent group-hover:translate-x-full transition-transform duration-1000"></div>
                        
                        <!-- Enhanced Featured Badge -->
                        <div class="absolute top-0 right-0 z-20">
                            <div
                                class="flex items-center gap-1.5 rounded-bl-2xl bg-gradient-to-r from-red-600 via-orange-500 to-red-600 px-4 py-2 text-xs font-bold text-white shadow-2xl border border-white/20"
                            >
                                <span class="animate-pulse text-base">⭐</span>
                                <span>HOT</span>
                                <div class="absolute inset-0 bg-white/20 blur-xl"></div>
                            </div>
                        </div>

                        <CardHeader class="relative z-20">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex flex-1 items-start gap-4">
                                    <!-- Enhanced Company Logo -->
                                    <div
                                        class="relative flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-red-50 via-orange-50 to-yellow-50 text-3xl ring-2 ring-red-100 transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 group-hover:ring-red-300 dark:from-red-950 dark:via-orange-950 dark:to-yellow-950 dark:ring-red-900 shadow-lg"
                                    >
                                        <img
                                            :src="getCompanyLogoUrl(job.company_logo, job.company)"
                                            :alt="job.company"
                                            class="h-full w-full rounded-2xl object-cover relative z-10"
                                        />
                                        <!-- Glow Effect -->
                                        <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-red-400/30 to-orange-400/30 blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
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
                                <!-- <Button
                                    variant="ghost"
                                    size="icon"
                                    class="flex-shrink-0 text-muted-foreground transition-all hover:scale-110 hover:text-red-600"
                                    @click.prevent
                                >
                                    <Heart class="h-5 w-5" />
                                </Button> -->
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="relative z-30 flex-shrink-0 mt-8"
                                    :class="job?.is_favorited == 1 ? 'text-red-600' : 'text-gray-400 hover:text-red-600'"
                                    @click.prevent.stop="toggleFavorite(job)"
                                >
                                    <Heart
                                            :class="job.is_favorited ? 'fill-red-600 text-red-600' : 'text-gray-400'"
                                            class="h-5 w-5"
                                        />
                                </Button>

                            </div>
                        </CardHeader>

                        <CardContent class="relative z-20">
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

                                <!-- Enhanced Apply Button -->
                                <Button
                                    class="group/btn relative z-30 w-full bg-gradient-to-r from-red-600 via-orange-600 to-red-600 font-bold text-white shadow-lg transition-all duration-500 hover:from-red-700 hover:via-orange-700 hover:to-red-700 hover:shadow-xl hover:scale-105 overflow-hidden"
                                    size="lg"
                                    @click.prevent.stop="$inertia.visit(`/jobs/${job.slug}/apply`)"
                                >
                                    <span class="relative z-10 flex items-center justify-center">
                                        Ứng tuyển ngay
                                        <span class="ml-2 transition-transform duration-300 group-hover/btn:translate-x-1">→</span>
                                    </span>
                                    <!-- Shine Effect -->
                                    <span class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/30 to-transparent group-hover/btn:translate-x-full transition-transform duration-1000"></span>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Enhanced View All Button -->
            <div class="text-center animate-fade-in-up" style="animation-delay: 0.6s">
                <Link href="/jobs?featured=1">
                    <Button
                        size="lg"
                        variant="outline"
                        class="group gap-2 border-2 border-red-600 px-8 py-6 text-red-600 hover:bg-gradient-to-r hover:from-red-600 hover:to-orange-600 hover:text-white hover:border-transparent font-semibold transition-all duration-500 hover:scale-105 hover:shadow-xl"
                    >
                        Xem tất cả việc làm nổi bật
                        <span
                            class="transition-transform duration-300 group-hover:translate-x-1"
                            >→</span
                        >
                    </Button>
                </Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
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

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
    opacity: 0;
}

.job-card {
    opacity: 0;
}
</style>
