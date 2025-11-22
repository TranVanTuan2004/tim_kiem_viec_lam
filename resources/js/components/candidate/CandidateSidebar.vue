<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { 
    LayoutDashboard, 
    User, 
    Briefcase, 
    Bell,
    Heart, 
    Folder, 
    GraduationCap,
    ChevronRight
} from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const currentPath = computed(() => page.url);

const menuItems = [
    {
        label: 'Tổng quan',
        href: '/candidate/dashboard',
        icon: LayoutDashboard,
    },
    {
        label: 'Hồ sơ cá nhân',
        href: '/candidate/profile',
        icon: User,
    },
    {
        label: 'Đơn ứng tuyển',
        href: '/candidate/applications',
        icon: Briefcase,
    },
    {
        label: 'Thông báo',
        href: '/candidate/notifications',
        icon: Bell,
    },
    {
        label: 'Việc làm đã lưu',
        href: '/candidate/saved-jobs',
        icon: Heart,
    },
    {
        label: 'Portfolio',
        href: '/candidate/portfolios',
        icon: Folder,
    },
    {
        label: 'Kinh nghiệm làm việc',
        href: '/candidate/work-experiences',
        icon: Briefcase,
    },
    {
        label: 'Học vấn',
        href: '/candidate/educations',
        icon: GraduationCap,
    },
];

const isActive = (href: string) => {
    return currentPath.value.startsWith(href);
};
</script>

<template>
    <aside class="w-64 bg-white border-r border-gray-200 fixed left-0 top-16 bottom-0 hidden lg:block overflow-y-auto">
        <div class="p-6">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
                Menu Ứng viên
            </h2>
            <nav class="space-y-1">
                <Link
                    v-for="item in menuItems"
                    :key="item.href"
                    :href="item.href"
                    :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
                        isActive(item.href)
                            ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md'
                            : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600'
                    ]"
                >
                    <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                    <span class="flex-1">{{ item.label }}</span>
                    <ChevronRight 
                        v-if="isActive(item.href)" 
                        class="w-4 h-4 opacity-70" 
                    />
                </Link>
            </nav>
        </div>
    </aside>
</template>
