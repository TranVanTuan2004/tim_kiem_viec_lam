<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Briefcase, Code, Database, Smartphone, Cloud, Shield, Monitor, Globe, Server, Terminal, Cpu, Layers } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    popularCategories: {
        type: Array as () => any[],
        default: () => [],
    },
});

const iconMap: Record<string, any> = {
    'Code': Code,
    'Smartphone': Smartphone,
    'Briefcase': Briefcase,
    'Cloud': Cloud,
    'Database': Database,
    'Shield': Shield,
    'Monitor': Monitor,
    'Globe': Globe,
    'Server': Server,
    'Terminal': Terminal,
    'Cpu': Cpu,
    'Layers': Layers,
};

const getIcon = (iconName: string) => {
    return iconMap[iconName] || Briefcase; // Default to Briefcase if icon not found
};
</script>

<template>
    <section class="py-20 bg-gradient-to-b from-background via-muted/30 to-background relative overflow-hidden">
        <!-- Background Decorations -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-0 right-0 h-96 w-96 rounded-full bg-red-100/30 blur-3xl dark:bg-red-900/20"></div>
            <div class="absolute bottom-0 left-0 h-96 w-96 rounded-full bg-orange-100/30 blur-3xl dark:bg-orange-900/20"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 animate-fade-in-up">
                <div class="inline-flex items-center gap-2 rounded-full bg-red-50 dark:bg-red-950 px-4 py-2 mb-4 text-sm font-semibold text-red-600">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-red-500"></span>
                    </span>
                    Danh mục phổ biến
                </div>
                <h2 class="text-4xl md:text-5xl font-extrabold mb-4 bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                    Danh mục công việc phổ biến
                </h2>
                <p class="text-lg text-muted-foreground max-w-2xl mx-auto">Tìm kiếm theo chuyên môn và khám phá cơ hội nghề nghiệp phù hợp với bạn</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <Link 
                    v-for="(category, index) in props.popularCategories" 
                    :key="category.id"
                    :href="route('jobs.index', { industry: category.slug })"
                    class="group relative overflow-hidden rounded-2xl border-2 bg-card p-6 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:border-red-300 dark:hover:border-red-700 cursor-pointer block animate-fade-in-up"
                    :style="{ animationDelay: `${index * 0.1}s` }"
                >
                    <!-- Animated Gradient Background -->
                    <div class="absolute inset-0 bg-gradient-to-br from-red-50/0 via-red-50/50 to-orange-50/0 opacity-0 transition-opacity duration-500 group-hover:opacity-100 dark:from-red-950/0 dark:via-red-950/50 dark:to-orange-950/0"></div>
                    
                    <!-- Shine Effect -->
                    <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent group-hover:translate-x-full transition-transform duration-1000"></div>

                    <!-- Content -->
                    <div class="relative flex items-start justify-between z-10">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="relative p-4 bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-950/50 dark:to-orange-950/50 rounded-2xl group-hover:from-red-100 group-hover:to-orange-100 dark:group-hover:from-red-900 dark:group-hover:to-orange-900 transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-lg">
                                    <component :is="getIcon(category.icon)" class="h-7 w-7 text-red-600 dark:text-red-400 transition-transform group-hover:scale-110" />
                                    <!-- Glow Effect -->
                                    <div class="absolute inset-0 rounded-2xl bg-red-400/20 blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                </div>
                            </div>
                            <h3 class="font-bold text-xl mb-2 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors duration-300">
                                {{ category.name }}
                            </h3>
                            <p class="text-sm text-muted-foreground font-semibold flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 text-xs font-bold">
                                    {{ category.count }}
                                </span>
                                vị trí đang tuyển
                            </p>
                        </div>
                        <div class="translate-x-4 opacity-0 transition-all duration-500 group-hover:translate-x-0 group-hover:opacity-100">
                            <div class="rounded-full bg-gradient-to-r from-red-500 to-orange-500 p-3 text-white shadow-lg group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right">
                                    <path d="M5 12h14"/>
                                    <path d="m12 5 7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Hover Border Glow -->
                    <div class="absolute inset-0 rounded-2xl border-2 border-transparent bg-gradient-to-r from-red-500 via-orange-500 to-red-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10 blur-sm"></div>
                </Link>
            </div>

            <div class="text-center mt-12 animate-fade-in-up" style="animation-delay: 0.8s">
                <Link :href="route('industries.index')">
                    <Button variant="outline" size="lg" class="group border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white px-8 font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg">
                        Xem tất cả danh mục
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 transition-transform group-hover:translate-x-1">
                            <path d="M5 12h14"/>
                            <path d="m12 5 7 7-7 7"/>
                        </svg>
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
</style>

