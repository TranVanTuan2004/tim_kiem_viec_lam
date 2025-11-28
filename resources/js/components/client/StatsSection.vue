<script setup lang="ts">
import { Users, Building2, Briefcase, TrendingUp } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

const stats = [
    { label: 'Ứng viên', value: '50K+', icon: Users, color: 'from-blue-500 to-cyan-500' },
    { label: 'Công ty', value: '500+', icon: Building2, color: 'from-red-500 to-pink-500' },
    { label: 'Việc làm', value: '1000+', icon: Briefcase, color: 'from-orange-500 to-yellow-500' },
    { label: 'Tuyển dụng/tháng', value: '200+', icon: TrendingUp, color: 'from-green-500 to-emerald-500' },
];

const animatedStats = ref(stats.map(() => ({ animated: false })));

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting && !animatedStats.value[index].animated) {
                    animatedStats.value[index].animated = true;
                }
            });
        },
        { threshold: 0.5 }
    );

    const statElements = document.querySelectorAll('.stat-item');
    statElements.forEach((el) => observer.observe(el));
});
</script>

<template>
    <section class="py-20 bg-gradient-to-br from-muted/50 via-background to-muted/30 relative overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-0 left-1/4 h-64 w-64 rounded-full bg-red-200/20 blur-3xl dark:bg-red-900/10 animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 h-64 w-64 rounded-full bg-blue-200/20 blur-3xl dark:bg-blue-900/10 animate-pulse" style="animation-delay: 1s"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                <div 
                    v-for="(stat, index) in stats" 
                    :key="stat.label"
                    class="stat-item text-center group"
                >
                    <div class="relative inline-flex items-center justify-center mb-6">
                        <!-- Icon Container with Gradient -->
                        <div :class="[
                            'relative w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-gradient-to-br',
                            stat.color,
                            'shadow-lg group-hover:shadow-2xl transition-all duration-500 group-hover:scale-110 group-hover:rotate-3'
                        ]">
                            <component :is="stat.icon" class="h-8 w-8 md:h-10 md:w-10 text-white absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" />
                            <!-- Glow Effect -->
                            <div :class="[
                                'absolute inset-0 rounded-2xl bg-gradient-to-br opacity-0 group-hover:opacity-50 blur-xl transition-opacity duration-500',
                                stat.color
                            ]"></div>
                        </div>
                        <!-- Floating Particles -->
                        <div class="absolute -top-2 -right-2 w-3 h-3 rounded-full bg-yellow-400 opacity-0 group-hover:opacity-100 animate-bounce" style="animation-delay: 0.2s"></div>
                        <div class="absolute -bottom-2 -left-2 w-2 h-2 rounded-full bg-pink-400 opacity-0 group-hover:opacity-100 animate-bounce" style="animation-delay: 0.4s"></div>
                    </div>
                    
                    <div 
                        :class="[
                            'text-4xl md:text-5xl font-extrabold mb-3 bg-gradient-to-r bg-clip-text text-transparent transition-all duration-500',
                            stat.color,
                            animatedStats[index].animated ? 'animate-count-up' : 'opacity-0'
                        ]"
                    >
                        {{ stat.value }}
                    </div>
                    <div class="text-sm md:text-base font-semibold text-muted-foreground group-hover:text-foreground transition-colors">
                        {{ stat.label }}
                    </div>
                    
                    <!-- Decorative Line -->
                    <div :class="[
                        'mx-auto mt-4 h-1 w-16 rounded-full bg-gradient-to-r opacity-0 group-hover:opacity-100 transition-opacity duration-500',
                        stat.color
                    ]"></div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
@keyframes count-up {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.8);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.animate-count-up {
    animation: count-up 0.8s ease-out forwards;
}
</style>

