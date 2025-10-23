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
import { Link, router } from '@inertiajs/vue3';
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
    <section class="bg-background py-16">
        <div class="container mx-auto px-4">
            <div class="mb-12 flex items-center justify-between">
                <div>
                    <h2 class="mb-2 text-3xl font-bold">Việc làm nổi bật</h2>
                    <p class="text-muted-foreground">
                        Cơ hội việc làm IT mới nhất
                    </p>
                </div>
                <Button
                    variant="outline"
                    @click="router.visit('/jobs?featured=1')"
                >
                    Xem tất cả
                </Button>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <Link
                    v-for="job in props.featuredJobs"
                    :key="job.id"
                    :href="`/jobs/${job.slug}`"
                >
                    <Card
                        class="group h-full cursor-pointer transition-all hover:shadow-lg"
                    >
                        <CardHeader>
                            <div class="flex items-start justify-between">
                                <div class="flex flex-1 items-start space-x-4">
                                    <div
                                        class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-lg bg-muted"
                                    >
                                        <img
                                            v-if="job.company_logo"
                                            :src="job.company_logo"
                                            :alt="job.company"
                                            class="h-full w-full rounded-lg object-contain p-2"
                                        />
                                        <div v-else class="text-3xl">
                                            {{ job.logo }}
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <CardTitle
                                            class="mb-1 text-xl transition-colors group-hover:text-red-600"
                                        >
                                            {{ job.title }}
                                        </CardTitle>
                                        <CardDescription
                                            class="flex items-center space-x-2"
                                        >
                                            <Building2 class="h-4 w-4" />
                                            <span>{{ job.company }}</span>
                                        </CardDescription>
                                    </div>
                                </div>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="text-muted-foreground hover:text-red-600"
                                >
                                    <Heart class="h-5 w-5" />
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div
                                class="flex h-full flex-col justify-between space-y-3"
                            >
                                <div>
                                    <!-- Job Info -->
                                    <div
                                        class="flex flex-wrap gap-4 text-sm text-muted-foreground"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <MapPin class="h-4 w-4" />
                                            <span>{{ job.location }}</span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <DollarSign class="h-4 w-4" />
                                            <span>{{ job.salary }}</span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <Clock class="h-4 w-4" />
                                            <span>{{ job.posted }}</span>
                                        </div>
                                    </div>

                                    <!-- Skills -->
                                    <div class="flex flex-wrap gap-2 pt-3">
                                        <Badge
                                            v-for="skill in job.skills"
                                            :key="skill"
                                            variant="secondary"
                                        >
                                            {{ skill }}
                                        </Badge>
                                        <Badge variant="outline">{{
                                            job.type
                                        }}</Badge>
                                    </div>
                                </div>

                                <!-- Apply Button -->
                                <div class="pt-4">
                                    <Button class="w-full" variant="default">
                                        Ứng tuyển ngay
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </section>
</template>
