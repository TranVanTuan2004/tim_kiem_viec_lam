<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { useLanguage } from '@/composables/useLanguage';
import ClientLayout from '@/layouts/ClientLayout.vue';
import {
    Award,
    Building2,
    CheckCircle2,
    Heart,
    Sparkles,
    Target,
    TrendingUp,
    Users,
} from 'lucide-vue-next';

const { t } = useLanguage();
</script>

<template>
    <ClientLayout :title="t.aboutPageTitle">
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
                        <span>{{ t.aboutUs }}</span>
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
                        {{ t.aboutUs }}
                        <span
                            class="bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent"
                        >
                            {{ t.jobPortal }}
                        </span>
                    </h1>
                    <p class="mx-auto max-w-3xl text-lg text-muted-foreground">
                        {{ t.aboutDescription }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Mission & Vision -->
        <section class="bg-background py-20">
            <div class="container mx-auto px-4">
                <div class="grid gap-8 md:grid-cols-2">
                    <!-- Mission -->
                    <Card class="border-2 transition-all hover:shadow-xl">
                        <CardContent class="p-8">
                            <div
                                class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-red-500 to-orange-500 shadow-lg"
                            >
                                <Target class="h-8 w-8 text-white" />
                            </div>
                            <h2 class="mb-4 text-3xl font-bold">
                                {{ t.mission }}
                            </h2>
                            <p
                                class="text-lg leading-relaxed text-muted-foreground"
                            >
                                {{ t.missionText }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Vision -->
                    <Card class="border-2 transition-all hover:shadow-xl">
                        <CardContent class="p-8">
                            <div
                                class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-red-500 shadow-lg"
                            >
                                <TrendingUp class="h-8 w-8 text-white" />
                            </div>
                            <h2 class="mb-4 text-3xl font-bold">
                                {{ t.vision }}
                            </h2>
                            <p
                                class="text-lg leading-relaxed text-muted-foreground"
                            >
                                {{ t.visionText }}
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Core Values -->
        <section
            class="bg-gradient-to-b from-muted/30 via-background to-muted/30 py-20"
        >
            <div class="container mx-auto px-4">
                <div class="mb-12 text-center">
                    <div
                        class="mb-4 inline-flex items-center gap-2 rounded-full bg-red-50 px-4 py-2 text-sm font-medium text-red-600 dark:bg-red-950"
                    >
                        <Sparkles class="h-4 w-4" />
                        {{ t.coreValues }}
                    </div>
                    <h2 class="mb-4 text-4xl font-bold">
                        {{ t.coreValuesTitle }}
                    </h2>
                    <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                        {{ t.coreValuesDescription }}
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Value 1 -->
                    <Card
                        class="group border-2 transition-all hover:-translate-y-2 hover:border-red-200 hover:shadow-xl dark:hover:border-red-800"
                    >
                        <CardContent class="p-6 text-center">
                            <div
                                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-red-100 to-orange-100 transition-transform group-hover:scale-110 dark:from-red-950 dark:to-orange-950"
                            >
                                <CheckCircle2 class="h-8 w-8 text-red-600" />
                            </div>
                            <h3 class="mb-2 text-xl font-bold">
                                {{ t.quality }}
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                {{ t.qualityDesc }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Value 2 -->
                    <Card
                        class="group border-2 transition-all hover:-translate-y-2 hover:border-red-200 hover:shadow-xl dark:hover:border-red-800"
                    >
                        <CardContent class="p-6 text-center">
                            <div
                                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-red-100 to-orange-100 transition-transform group-hover:scale-110 dark:from-red-950 dark:to-orange-950"
                            >
                                <Heart class="h-8 w-8 text-red-600" />
                            </div>
                            <h3 class="mb-2 text-xl font-bold">
                                {{ t.dedication }}
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                {{ t.dedicationDesc }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Value 3 -->
                    <Card
                        class="group border-2 transition-all hover:-translate-y-2 hover:border-red-200 hover:shadow-xl dark:hover:border-red-800"
                    >
                        <CardContent class="p-6 text-center">
                            <div
                                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-red-100 to-orange-100 transition-transform group-hover:scale-110 dark:from-red-950 dark:to-orange-950"
                            >
                                <Award class="h-8 w-8 text-red-600" />
                            </div>
                            <h3 class="mb-2 text-xl font-bold">
                                {{ t.credibility }}
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                {{ t.credibilityDesc }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Value 4 -->
                    <Card
                        class="group border-2 transition-all hover:-translate-y-2 hover:border-red-200 hover:shadow-xl dark:hover:border-red-800"
                    >
                        <CardContent class="p-6 text-center">
                            <div
                                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-red-100 to-orange-100 transition-transform group-hover:scale-110 dark:from-red-950 dark:to-orange-950"
                            >
                                <Users class="h-8 w-8 text-red-600" />
                            </div>
                            <h3 class="mb-2 text-xl font-bold">
                                {{ t.community }}
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                {{ t.communityDesc }}
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="bg-background py-20">
            <div class="container mx-auto px-4">
                <Card class="border-2 shadow-xl">
                    <CardContent class="p-12">
                        <div class="grid gap-8 md:grid-cols-4">
                            <div class="text-center">
                                <div
                                    class="mb-2 bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-5xl font-bold text-transparent"
                                >
                                    1000+
                                </div>
                                <p class="text-muted-foreground">
                                    {{ t.totalCompanies }}
                                </p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="mb-2 bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-5xl font-bold text-transparent"
                                >
                                    5000+
                                </div>
                                <p class="text-muted-foreground">
                                    {{ t.totalJobs }}
                                </p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="mb-2 bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-5xl font-bold text-transparent"
                                >
                                    50K+
                                </div>
                                <p class="text-muted-foreground">
                                    {{ t.totalCandidates }}
                                </p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="mb-2 bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-5xl font-bold text-transparent"
                                >
                                    95%
                                </div>
                                <p class="text-muted-foreground">
                                    {{ t.satisfaction }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </section>

        <!-- Story Section -->
        <section class="bg-muted/30 py-20">
            <div class="container mx-auto px-4">
                <div class="mx-auto max-w-4xl">
                    <Card class="border-2">
                        <CardContent class="p-12">
                            <h2 class="mb-6 text-3xl font-bold">
                                {{ t.ourStory }}
                            </h2>
                            <div
                                class="space-y-4 text-lg leading-relaxed text-muted-foreground"
                            >
                                <p>
                                    {{ t.storyParagraph1 }}
                                </p>
                                <p>
                                    {{ t.storyParagraph2 }}
                                </p>
                                <p>
                                    {{ t.storyParagraph3 }}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>
    </ClientLayout>
</template>
