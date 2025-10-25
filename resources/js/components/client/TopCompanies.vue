<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { useLanguage } from '@/composables/useLanguage';
import { router } from '@inertiajs/vue3';
import {
    ArrowRight,
    Briefcase,
    CheckCircle2,
    MapPin,
    Star,
    TrendingUp,
    Users,
} from 'lucide-vue-next';
import { defineProps } from 'vue';

const props = defineProps({
    topCompanies: {
        type: Array as () => any[],
        default: () => [],
    },
});

const { t } = useLanguage();
</script>

<template>
    <section
        class="relative overflow-hidden bg-gradient-to-b from-muted/30 via-background to-muted/20 py-20"
    >
        <!-- Background Decorations -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-gradient-to-br from-red-100 to-orange-100 opacity-20 blur-3xl dark:from-red-900 dark:to-orange-900"
            ></div>
            <div
                class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-gradient-to-tr from-orange-100 to-red-100 opacity-20 blur-3xl dark:from-orange-900 dark:to-red-900"
            ></div>
            <!-- Grid Pattern -->
            <div
                class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:14px_24px]"
            ></div>
        </div>

        <div class="relative z-10 container mx-auto px-4">
            <!-- Section Header -->
            <div class="mb-16 text-center">
                <div
                    class="mb-4 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-red-50 to-orange-50 px-5 py-2.5 text-sm font-semibold text-red-600 shadow-lg ring-1 ring-red-100 dark:from-red-950 dark:to-orange-950 dark:ring-red-900"
                >
                    <TrendingUp class="h-4 w-4" />
                    <span>{{ t.topCompanies }}</span>
                    <div class="relative flex h-2 w-2">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex h-2 w-2 rounded-full bg-red-500"
                        ></span>
                    </div>
                </div>

                <h2 class="mb-4 text-4xl font-bold tracking-tight md:text-5xl">
                    {{ t.topCompanies }}
                </h2>

                <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                    {{ t.topCompaniesDescription }}
                </p>
            </div>

            <!-- Companies Grid -->
            <div
                class="mb-12 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4"
            >
                <Card
                    v-for="company in props.topCompanies"
                    :key="company.id"
                    class="group relative cursor-pointer overflow-hidden border-2 transition-all duration-300 hover:-translate-y-2 hover:border-red-200 hover:shadow-2xl dark:hover:border-red-800"
                    @click="router.visit(`/companies/${company.slug}`)"
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
                            v-if="company.rating >= 4.5"
                            class="absolute top-3 right-3 flex items-center gap-1 rounded-full bg-white/95 px-2.5 py-1 text-xs font-bold text-green-600 shadow-lg backdrop-blur-sm dark:bg-gray-900/95"
                        >
                            <CheckCircle2 class="h-3 w-3" />
                            <span>Verified</span>
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
                        <div class="absolute -top-14 left-1/2 -translate-x-1/2">
                            <div
                                class="relative flex h-28 w-28 items-center justify-center overflow-hidden rounded-2xl border-4 border-background bg-white shadow-2xl ring-4 ring-red-50 transition-all duration-300 group-hover:scale-110 group-hover:ring-red-100 dark:bg-card dark:ring-red-950"
                            >
                                <img
                                    v-if="company.logo"
                                    :src="company.logo"
                                    :alt="company.name"
                                    class="h-full w-full object-contain p-3"
                                />
                                <div v-else class="text-5xl">🏢</div>

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
                                {{ company.name }}
                            </h3>

                            <!-- Rating -->
                            <div class="flex items-center justify-center gap-2">
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
                                    {{ company.rating }}
                                </span>
                                <span class="text-xs text-muted-foreground">
                                    ({{ company.reviews }})
                                </span>
                            </div>

                            <!-- Description -->
                            <p
                                class="line-clamp-2 min-h-[2.5rem] text-sm text-muted-foreground"
                            >
                                {{ company.description }}
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
                                        {{ company.location }}
                                    </span>
                                </div>
                                <div
                                    class="flex flex-col items-center gap-1 rounded-lg bg-muted/50 p-3 transition-colors hover:bg-muted"
                                >
                                    <Users class="h-4 w-4 text-orange-500" />
                                    <span class="text-xs font-medium">
                                        {{ company.employees }}
                                    </span>
                                </div>
                            </div>

                            <!-- Jobs Badge -->
                            <div
                                class="flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-red-50 to-orange-50 p-3 dark:from-red-950/50 dark:to-orange-950/50"
                            >
                                <Briefcase class="h-4 w-4 text-red-600" />
                                <span class="text-sm font-bold text-red-600">
                                    {{ company.jobs }} {{ t.jobs }}
                                </span>
                            </div>

                            <!-- CTA Button -->
                            <Button
                                class="group/btn w-full bg-gradient-to-r from-red-600 to-orange-500 font-semibold shadow-lg transition-all hover:scale-105 hover:shadow-xl"
                                @click.stop="
                                    router.visit(`/companies/${company.slug}`)
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

            <!-- View All Button -->
            <div class="text-center">
                <Button
                    size="lg"
                    variant="outline"
                    class="group border-2 px-8 font-semibold shadow-lg transition-all hover:border-red-300 hover:bg-red-50 hover:text-red-600 hover:shadow-xl dark:hover:bg-red-950"
                    @click="router.visit('/companies')"
                >
                    <TrendingUp class="mr-2 h-5 w-5" />
                    {{ t.exploreAllCompanies }}
                    <ArrowRight
                        class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1"
                    />
                </Button>
            </div>
        </div>
    </section>
</template>
