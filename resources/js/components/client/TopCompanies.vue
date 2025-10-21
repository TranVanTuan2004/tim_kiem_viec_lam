<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { router } from '@inertiajs/vue3';
import { MapPin, Star, Users } from 'lucide-vue-next';

// Mock data - In real app, this would come from props or API
const topCompanies = [
    {
        id: 1,
        name: 'TechCorp Vietnam',
        slug: 'techcorp-vietnam',
        logo: '🏢',
        rating: 4.5,
        reviews: 1200,
        jobs: 45,
        location: 'Hà Nội',
        employees: '10,000+',
        description: 'Công ty công nghệ giả lập cho mục đích test',
    },
    {
        id: 2,
        name: 'FPT Software',
        slug: 'fpt-software',
        logo: '💻',
        rating: 4.7,
        reviews: 890,
        jobs: 32,
        location: 'TP.HCM',
        employees: '5,000+',
        description: 'Công ty phần mềm hàng đầu Việt Nam',
    },
    {
        id: 3,
        name: 'VNG Corporation',
        slug: 'vng-corporation',
        logo: '🎮',
        rating: 4.3,
        reviews: 650,
        jobs: 28,
        location: 'TP.HCM',
        employees: '2,000+',
        description: 'Công ty công nghệ và giải trí',
    },
    {
        id: 4,
        name: 'Tiki',
        slug: 'tiki',
        logo: '🛒',
        rating: 4.6,
        reviews: 1500,
        jobs: 56,
        location: 'TP.HCM',
        employees: '3,000+',
        description: 'Sàn thương mại điện tử',
    },
];
</script>

<template>
    <section class="bg-muted/50 py-16">
        <div class="container mx-auto px-4">
            <div class="mb-12 flex items-center justify-between">
                <div>
                    <h2 class="mb-2 text-3xl font-bold">Top công ty nổi bật</h2>
                    <p class="text-muted-foreground">
                        Các công ty uy tín đang tuyển dụng
                    </p>
                </div>
                <Button variant="outline" @click="router.visit('/companies')"
                    >Xem tất cả</Button
                >
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <Card
                    v-for="company in topCompanies"
                    :key="company.id"
                    class="group cursor-pointer overflow-hidden transition-all hover:shadow-lg"
                    @click="router.visit(`/companies/${company.slug}`)"
                >
                    <div
                        class="h-24 bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-950 dark:to-orange-950"
                    ></div>
                    <CardContent class="relative pt-0">
                        <div class="absolute -top-12 left-1/2 -translate-x-1/2">
                            <div
                                class="flex h-24 w-24 items-center justify-center rounded-xl border-4 border-background bg-white text-5xl shadow-lg dark:bg-card"
                            >
                                {{ company.logo }}
                            </div>
                        </div>

                        <div class="space-y-3 pt-14 text-center">
                            <h3
                                class="text-lg font-semibold transition-colors group-hover:text-red-600"
                            >
                                {{ company.name }}
                            </h3>

                            <div
                                class="flex items-center justify-center space-x-1 text-sm"
                            >
                                <Star
                                    class="h-4 w-4 fill-yellow-400 text-yellow-400"
                                />
                                <span class="font-medium">{{
                                    company.rating
                                }}</span>
                                <span class="text-muted-foreground"
                                    >({{ company.reviews }} reviews)</span
                                >
                            </div>

                            <p
                                class="line-clamp-2 text-sm text-muted-foreground"
                            >
                                {{ company.description }}
                            </p>

                            <div
                                class="flex items-center justify-center gap-3 text-xs text-muted-foreground"
                            >
                                <div class="flex items-center space-x-1">
                                    <MapPin class="h-3 w-3" />
                                    <span>{{ company.location }}</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <Users class="h-3 w-3" />
                                    <span>{{ company.employees }}</span>
                                </div>
                            </div>

                            <Badge
                                variant="secondary"
                                class="w-full justify-center"
                            >
                                {{ company.jobs }} việc làm đang tuyển
                            </Badge>

                            <Button
                                variant="outline"
                                class="w-full"
                                @click.stop="
                                    router.visit(`/companies/${company.slug}`)
                                "
                            >
                                Xem công ty
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </section>
</template>
