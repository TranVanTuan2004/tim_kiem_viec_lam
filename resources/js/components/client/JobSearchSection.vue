<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Briefcase, Code, Database, Smartphone, Cloud, Shield } from 'lucide-vue-next';

const categories = [
    { name: 'Backend Developer', icon: Code, count: 234 },
    { name: 'Frontend Developer', icon: Smartphone, count: 189 },
    { name: 'Full Stack Developer', icon: Briefcase, count: 156 },
    { name: 'DevOps Engineer', icon: Cloud, count: 92 },
    { name: 'Data Engineer', icon: Database, count: 78 },
    { name: 'Security Engineer', icon: Shield, count: 45 },
];
</script>

<template>
    <section class="py-16 bg-background">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Danh mục công việc phổ biến</h2>
                <p class="text-muted-foreground">Tìm kiếm theo chuyên môn</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="category in categories" 
                    :key="category.name"
                    class="group relative overflow-hidden rounded-xl border bg-card p-6 hover:shadow-lg transition-all hover:border-red-200 dark:hover:border-red-800 cursor-pointer"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="p-2 bg-red-50 dark:bg-red-950 rounded-lg group-hover:bg-red-100 dark:group-hover:bg-red-900 transition-colors">
                                    <component :is="category.icon" class="h-6 w-6 text-red-600" />
                                </div>
                            </div>
                            <h3 class="font-semibold text-lg mb-1">{{ category.name }}</h3>
                            <p class="text-sm text-muted-foreground">{{ category.count }} vị trí</p>
                        </div>
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <Button variant="ghost" size="sm">
                                Xem →
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-8">
                <Button variant="outline" size="lg">
                    Xem tất cả danh mục
                </Button>
            </div>
        </div>
    </section>
</template>

