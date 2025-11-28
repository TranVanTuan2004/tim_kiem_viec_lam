<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Briefcase, Code, Database, Smartphone, Cloud, Shield, Monitor, Globe, Server, Terminal, Cpu, Layers } from 'lucide-vue-next';

defineOptions({ layout: ClientLayout });

const props = defineProps({
    industries: {
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
    return iconMap[iconName] || Briefcase;
};
</script>

<template>
    <Head title="Danh mục nghề nghiệp"/>

    <div class="bg-background min-h-screen py-12">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold mb-4">Tất cả danh mục nghề nghiệp</h1>
                <p class="text-muted-foreground text-lg">Khám phá cơ hội việc làm theo lĩnh vực chuyên môn của bạn</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <Link 
                    v-for="category in props.industries" 
                    :key="category.id"
                    :href="route('jobs.index', { industry: category.slug })"
                    class="group relative overflow-hidden rounded-xl border bg-card p-6 hover:shadow-lg transition-all hover:border-red-200 dark:hover:border-red-800 cursor-pointer block"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="p-3 bg-red-50 dark:bg-red-950 rounded-lg group-hover:bg-red-100 dark:group-hover:bg-red-900 transition-colors">
                                    <component :is="getIcon(category.icon)" class="h-8 w-8 text-red-600" />
                                </div>
                            </div>
                            <h3 class="font-semibold text-xl mb-2">{{ category.name }}</h3>
                            <p class="text-sm text-muted-foreground mb-2 line-clamp-2">{{ category.description }}</p>
                            <div class="flex items-center text-sm font-medium text-red-600">
                                {{ category.count }} việc làm đang tuyển
                            </div>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>
