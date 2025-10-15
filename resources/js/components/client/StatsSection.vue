    <script setup lang="ts">
import { Users, Building2, Briefcase, TrendingUp } from 'lucide-vue-next';

const stats = [
    { label: 'Ứng viên', value: '50K+', icon: Users },
    { label: 'Công ty', value: '500+', icon: Building2 },
    { label: 'Việc làm', value: '1000+', icon: Briefcase },
    { label: 'Tuyển dụng/tháng', value: '200+', icon: TrendingUp },
];
</script>

<template>
    <section class="py-16 bg-muted/50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div 
                    v-for="stat in stats" 
                    :key="stat.label"
                    class="text-center"
                >
                    <div class="inline-flex items-center justify-center w-12 h-12 mb-4 bg-red-100 dark:bg-red-950 rounded-full">
                        <component :is="stat.icon" class="h-6 w-6 text-red-600" />
                    </div>
                    <div class="text-3xl font-bold mb-2">{{ stat.value }}</div>
                    <div class="text-sm text-muted-foreground">{{ stat.label }}</div>
                </div>
            </div>
        </div>
    </section>
</template>

