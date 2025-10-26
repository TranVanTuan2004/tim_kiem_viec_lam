<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { router } from '@inertiajs/vue3';
import { Briefcase, MapPin, Search, TrendingUp, Users } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { useLanguage } from '@/composables/useLanguage';
import LocationSelector from './LocationSelector.vue';
const { t } = useLanguage();

const searchQuery = ref('');
const location = ref('');

// Typing effect
const typingTexts = [
    'Developer',
    'Designer',
    'Data Analyst',
    'Tester',
    'DevOps',
];
const currentText = ref('');
const currentIndex = ref(0);
const charIndex = ref(0);
const isDeleting = ref(false);

onMounted(() => {
    typeEffect();
});

const typeEffect = () => {
    const current = typingTexts[currentIndex.value];

    if (!isDeleting.value) {
        currentText.value = current.substring(0, charIndex.value + 1);
        charIndex.value++;

        if (charIndex.value === current.length) {
            setTimeout(() => {
                isDeleting.value = true;
                typeEffect();
            }, 2000);
            return;
        }
    } else {
        currentText.value = current.substring(0, charIndex.value - 1);
        charIndex.value--;

        if (charIndex.value === 0) {
            isDeleting.value = false;
            currentIndex.value = (currentIndex.value + 1) % typingTexts.length;
        }
    }

    setTimeout(typeEffect, isDeleting.value ? 50 : 150);
};

const handleSearch = () => {
    router.visit('/jobs', {
        data: {
            q: searchQuery.value,
            location: location.value,
        },
    });
};

const quickSearch = (keyword: string) => {
    searchQuery.value = keyword;
    handleSearch();
};
</script>

<template>
    <section
        class="relative min-h-[600px] overflow-hidden bg-gradient-to-br from-red-600 via-red-500 to-orange-500"
    >
        <div class="relative z-10 container mx-auto px-4 py-16 md:py-24">
            <div class="mx-auto max-w-5xl text-center">
                <!-- Heading with Typing Effect -->
                <div class="mb-6 flex flex-col items-center gap-4">
                    <div
                        class="inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm"
                    >
                        <TrendingUp class="h-4 w-4" />
                        <span>{{ t.newJobsPerWeek }}</span>
                    </div>
                </div>

                <h1
                    class="mb-4 text-4xl font-bold tracking-tight text-white md:text-6xl lg:text-7xl"
                >
                    {{ t.findJobs }}
                    <div class="mt-2 inline-block">
                        <span class="text-yellow-300">{{ currentText }}</span>
                        <span class="animate-pulse text-yellow-300">|</span>
                    </div>
                </h1>

                <p class="mb-12 text-xl text-white/90 md:text-2xl">
                    {{ t.connectWithOver1000ITJobOpportunitiesFromTopCompanies }}
                </p>

                <!-- Search Box -->
                <div
                    class="mb-8 rounded-2xl bg-white p-6 shadow-2xl dark:bg-card"
                >
                    <div class="flex flex-col gap-4 md:flex-row">
                        <div class="relative flex-1">
                            <Search
                                class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                v-model="searchQuery"
                                placeholder="Tìm kiếm: Java, ReactJS, NodeJS..."
                                class="h-14 pl-10 text-base"
                                @keyup.enter="handleSearch"
                            />
                        </div>
                        <div class="relative flex-1">
                            <MapPin
                                class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-muted-foreground"
                            />
                            <LocationSelector
                            v-model="location"
                            placeholder="Chọn địa điểm..."
                            @search="handleSearch"
                            />
                        </div>
                        <Button
                            @click="handleSearch"
                            size="lg"
                            class="h-14 bg-red-600 px-8 hover:bg-red-700"
                        >
                            <Search class="mr-2 h-5 w-5" />
                            Tìm kiếm
                        </Button>
                    </div>
                </div>

                <!-- Popular Keywords -->
                <div class="flex flex-wrap items-center justify-center gap-2">
                    <span class="text-sm font-medium text-white/80"
                        >Từ khóa phổ biến:</span
                    >
                    <Button
                        v-for="keyword in [
                            'Java',
                            'ReactJS',
                            'NodeJS',
                            'Python',
                            '.NET',
                        ]"
                        :key="keyword"
                        variant="secondary"
                        size="sm"
                        class="bg-white/20 text-white backdrop-blur-sm hover:bg-white/30"
                        @click="quickSearch(keyword)"
                    >
                        {{ keyword }}
                    </Button>
                </div>

                <!-- Quick Stats -->
                <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div
                        class="group rounded-xl bg-white/10 p-6 backdrop-blur-sm transition-all hover:bg-white/20"
                    >
                        <div class="mb-2 flex items-center justify-center">
                            <Briefcase class="h-8 w-8 text-yellow-300" />
                        </div>
                        <div class="text-3xl font-bold text-white">1,000+</div>
                        <div class="text-sm text-white/80">Việc làm mới</div>
                    </div>
                    <div
                        class="group rounded-xl bg-white/10 p-6 backdrop-blur-sm transition-all hover:bg-white/20"
                    >
                        <div class="mb-2 flex items-center justify-center">
                            <Users class="h-8 w-8 text-yellow-300" />
                        </div>
                        <div class="text-3xl font-bold text-white">500+</div>
                        <div class="text-sm text-white/80">Công ty</div>
                    </div>
                    <div
                        class="group rounded-xl bg-white/10 p-6 backdrop-blur-sm transition-all hover:bg-white/20"
                    >
                        <div class="mb-2 flex items-center justify-center">
                            <TrendingUp class="h-8 w-8 text-yellow-300" />
                        </div>
                        <div class="text-3xl font-bold text-white">10,000+</div>
                        <div class="text-sm text-white/80">Ứng viên</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Animated Background -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="animate-blob absolute top-0 -left-4 h-72 w-72 rounded-full bg-yellow-400/30 opacity-70 blur-3xl filter"
            ></div>
            <div
                class="animation-delay-2000 animate-blob absolute top-0 right-4 h-72 w-72 rounded-full bg-orange-400/30 opacity-70 blur-3xl filter"
            ></div>
            <div
                class="animation-delay-4000 animate-blob absolute -bottom-8 left-20 h-72 w-72 rounded-full bg-pink-400/30 opacity-70 blur-3xl filter"
            ></div>
        </div>

        <!-- Grid Pattern Overlay -->
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"
        ></div>
    </section>
</template>

<style scoped>
@keyframes blob {
    0%,
    100% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
