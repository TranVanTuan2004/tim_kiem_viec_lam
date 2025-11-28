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
        class="relative min-h-[600px] md:min-h-[700px] overflow-hidden bg-gradient-to-br from-red-600 via-red-500 to-orange-500 rounded-3xl shadow-2xl"
    >
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Floating Orbs -->
            <div
                class="absolute top-20 left-10 h-64 w-64 rounded-full bg-yellow-400/20 blur-3xl animate-float-orb"
                style="animation-delay: 0s;"
            ></div>
            <div
                class="absolute top-40 right-20 h-80 w-80 rounded-full bg-orange-400/20 blur-3xl animate-float-orb"
                style="animation-delay: 2s;"
            ></div>
            <div
                class="absolute bottom-20 left-1/3 h-72 w-72 rounded-full bg-pink-400/20 blur-3xl animate-float-orb"
                style="animation-delay: 4s;"
            ></div>
            
            <!-- Animated Particles -->
            <div
                v-for="i in 30"
                :key="i"
                class="absolute rounded-full bg-white/10 animate-particle-float"
                :style="{
                    left: `${Math.random() * 100}%`,
                    top: `${Math.random() * 100}%`,
                    width: `${Math.random() * 6 + 2}px`,
                    height: `${Math.random() * 6 + 2}px`,
                    animationDelay: `${Math.random() * 5}s`,
                    animationDuration: `${Math.random() * 4 + 3}s`,
                }"
            ></div>
        </div>

        <!-- Grid Pattern Overlay -->
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-20"
        ></div>

        <div class="relative z-10 container mx-auto px-4 py-16 md:py-24">
            <div class="mx-auto max-w-5xl text-center">
                <!-- Heading with Typing Effect -->
                <div class="mb-6 flex flex-col items-center gap-4 animate-fade-in-down">
                    <div
                        class="inline-flex items-center gap-2 rounded-full bg-white/20 backdrop-blur-md px-5 py-2.5 text-sm font-semibold text-white border border-white/30 shadow-lg hover:bg-white/30 transition-all duration-300 hover:scale-105"
                    >
                        <TrendingUp class="h-4 w-4 animate-pulse" />
                        <span>{{ t.newJobsPerWeek }}</span>
                    </div>
                </div>

                <h1
                    class="mb-6 text-4xl font-extrabold tracking-tight text-white md:text-6xl lg:text-7xl animate-fade-in-up"
                    style="animation-delay: 0.2s"
                >
                    <span class="block mb-2 bg-gradient-to-r from-white via-yellow-100 to-white bg-clip-text text-transparent drop-shadow-2xl">
                        {{ t.findJobs }}
                    </span>
                    <div class="mt-2 inline-block">
                        <span class="text-yellow-300 font-bold drop-shadow-lg">{{ currentText }}</span>
                        <span class="animate-pulse text-yellow-300 ml-1">|</span>
                    </div>
                </h1>

                <p class="mb-12 text-xl text-white/95 md:text-2xl leading-relaxed drop-shadow-lg animate-fade-in-up" style="animation-delay: 0.4s">
                    {{ t.connectWithOver1000ITJobOpportunitiesFromTopCompanies }}
                </p>

                <!-- Enhanced Search Box -->
                <div
                    class="mb-8 rounded-3xl bg-white/95 backdrop-blur-xl p-6 md:p-8 shadow-2xl border border-white/20 dark:bg-card animate-fade-in-up hover:shadow-3xl transition-all duration-500"
                    style="animation-delay: 0.6s"
                >
                    <div class="flex flex-col gap-4 md:flex-row">
                        <div class="relative flex-1 group">
                            <Search
                                class="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-red-600"
                            />
                            <Input
                                v-model="searchQuery"
                                placeholder="Tìm kiếm: Java, ReactJS, NodeJS..."
                                class="h-14 pl-12 text-base border-2 focus:border-red-500 transition-all duration-300"
                                @keyup.enter="handleSearch"
                            />
                        </div>
                        <div class="relative flex-1 group">
                            <MapPin
                                class="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-red-600"
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
                            class="h-14 bg-gradient-to-r from-red-600 to-orange-600 px-8 hover:from-red-700 hover:to-orange-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 font-bold"
                        >
                            <Search class="mr-2 h-5 w-5" />
                            Tìm kiếm
                        </Button>
                    </div>
                </div>

                <!-- Enhanced Popular Keywords -->
                <div class="flex flex-wrap items-center justify-center gap-3 animate-fade-in-up" style="animation-delay: 0.8s">
                    <span class="text-sm font-semibold text-white/90 drop-shadow-md"
                        >Từ khóa phổ biến:</span
                    >
                    <Button
                        v-for="(keyword, index) in [
                            'Java',
                            'ReactJS',
                            'NodeJS',
                            'Python',
                            '.NET',
                        ]"
                        :key="keyword"
                        variant="secondary"
                        size="sm"
                        class="bg-white/20 text-white backdrop-blur-md hover:bg-white/40 border border-white/30 hover:scale-110 hover:shadow-lg transition-all duration-300 font-medium"
                        :style="{ animationDelay: `${0.9 + index * 0.1}s` }"
                        @click="quickSearch(keyword)"
                    >
                        {{ keyword }}
                    </Button>
                </div>

                <!-- Enhanced Quick Stats -->
                <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3 animate-fade-in-up" style="animation-delay: 1s">
                    <div
                        class="group relative rounded-2xl bg-white/10 backdrop-blur-md p-6 border border-white/20 transition-all duration-500 hover:bg-white/20 hover:scale-105 hover:shadow-2xl overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-yellow-400/0 to-yellow-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative z-10">
                            <div class="mb-3 flex items-center justify-center">
                                <div class="rounded-full bg-yellow-400/20 p-3 group-hover:bg-yellow-400/30 transition-colors">
                                    <Briefcase class="h-8 w-8 text-yellow-300 group-hover:scale-110 transition-transform" />
                                </div>
                            </div>
                            <div class="text-4xl font-extrabold text-white mb-1 drop-shadow-lg">1,000+</div>
                            <div class="text-sm font-medium text-white/90">Việc làm mới</div>
                        </div>
                    </div>
                    <div
                        class="group relative rounded-2xl bg-white/10 backdrop-blur-md p-6 border border-white/20 transition-all duration-500 hover:bg-white/20 hover:scale-105 hover:shadow-2xl overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-400/0 to-orange-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative z-10">
                            <div class="mb-3 flex items-center justify-center">
                                <div class="rounded-full bg-orange-400/20 p-3 group-hover:bg-orange-400/30 transition-colors">
                                    <Users class="h-8 w-8 text-yellow-300 group-hover:scale-110 transition-transform" />
                                </div>
                            </div>
                            <div class="text-4xl font-extrabold text-white mb-1 drop-shadow-lg">500+</div>
                            <div class="text-sm font-medium text-white/90">Công ty</div>
                        </div>
                    </div>
                    <div
                        class="group relative rounded-2xl bg-white/10 backdrop-blur-md p-6 border border-white/20 transition-all duration-500 hover:bg-white/20 hover:scale-105 hover:shadow-2xl overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-pink-400/0 to-pink-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative z-10">
                            <div class="mb-3 flex items-center justify-center">
                                <div class="rounded-full bg-pink-400/20 p-3 group-hover:bg-pink-400/30 transition-colors">
                                    <TrendingUp class="h-8 w-8 text-yellow-300 group-hover:scale-110 transition-transform" />
                                </div>
                            </div>
                            <div class="text-4xl font-extrabold text-white mb-1 drop-shadow-lg">10,000+</div>
                            <div class="text-sm font-medium text-white/90">Ứng viên</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</template>

<style scoped>
/* Fade In Animations */
@keyframes fade-in-down {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

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

.animate-fade-in-down {
    animation: fade-in-down 0.8s ease-out forwards;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
    opacity: 0;
}

/* Float Orb Animation */
@keyframes float-orb {
    0%, 100% {
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

/* Particle Float Animation */
@keyframes particle-float {
    0%, 100% {
        transform: translateY(0) translateX(0);
        opacity: 0.2;
    }
    50% {
        transform: translateY(-30px) translateX(15px);
        opacity: 0.6;
    }
}

.animate-particle-float {
    animation: particle-float 4s ease-in-out infinite;
}
</style>
