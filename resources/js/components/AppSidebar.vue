<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { usePermissions } from '@/composables/usePermissions';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Activity,
    AlertTriangle,
    Bell,
    BookOpen,
    Bookmark,
    Briefcase,
    Calendar,
    CreditCard,
    FileText,
    Folder,
    Home,
    Image,
    Layout,
    LayoutGrid,
    MessageSquare,
    User,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const { can, currentUser, hasRole } = usePermissions();
const page = usePage();

// Fallback: coi như candidate context nếu URL đang ở /candidate
const isCandidateContext = computed(() => {
    return hasRole('Candidate') || page.url.startsWith('/candidate');
});

// Determine dashboard link based on role
const getDashboardLink = () => {
    if (isCandidateContext.value) {
        return '/candidate/dashboard';
    } else if (hasRole('Employer')) {
        return '/employer/dashboard';
    } else if (hasRole('Admin')) {
        return '/admin/dashboard';
    }
    return '/candidate/dashboard'; // Default to candidate
};

// Define different navigation items for different roles
const candidateNavItems: NavItem[] = [
    {
        title: 'Home',
        href: '/',
        icon: Home,
    },
    {
        title: 'Dashboard',
        href: '/candidate/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Hồ sơ cá nhân',
        href: '/profile',
        icon: User,
    },
    {
        title: 'My Applications',
        href: '/candidate/applications',
        icon: FileText,
    },
    {
        title: 'Lịch Phỏng Vấn',
        href: '/candidate/interviews',
        icon: MessageSquare,
    },
    {
        title: 'Saved Jobs',
        href: '/candidate/saved-jobs',
        icon: Bookmark,
    },
    {
        title: 'Portfolio',
        href: '/candidate/portfolios',
        icon: Briefcase,
    },
];

const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Quản lý người dùng',
        href: '/admin/users',
        icon: Users,
        permission: 'view users',
    },
    {
        title: 'Quản lý báo cáo',
        href: '/admin/reports',
        icon: FileText,
        permission: 'view reports',
    },
    {
        title: 'Quản lý ứng tuyển',
        href: '/admin/applications',
        icon: FileText,
        permission: 'view applications',
    },
    {
        title: 'Quản lý tin tuyển dụng',
        href: '/admin/job-postings',
        icon: Briefcase,
        permission: 'view users',
    },
    {
        title: 'Quản lý lịch phỏng vấn',
        href: '/admin/interviews',
        icon: Calendar,
        permission: 'view users',
    },
    {
        title: 'Quản lý Banner',
        href: '/admin/banners',
        icon: Image,
        permission: 'view users',
    },
    {
        title: 'Nội dung Trang chủ',
        href: '/admin/homepage',
        icon: Layout,
        permission: 'view users',
    },
    {
        title: 'Chat',
        href: '/admin/chat',
        icon: MessageSquare,
        permission: 'view messages',
    },
    {
        title: 'Thông báo',
        href: '/admin/notifications',
        icon: Bell,
        permission: 'view users',
    },
    {
        title: 'Activity Logs',
        href: '/admin/activity-logs',
        icon: Activity,
        permission: 'view users',
    },
    {
        title: 'Quản lý gói dịch vụ',
        href: '/admin/service-packages',
        icon: CreditCard,
        permission: 'view users',
    },
    {
        title: 'Gói Dịch Vụ',
        href: '/admin/subscriptions',
        icon: CreditCard,
        permission: 'view subscriptions',
    },
];

const employerNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/employer/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Tin tuyển dụng',
        href: '/employer/posting',
        icon: Briefcase,
    },
    {
        title: 'Lịch sử ứng tuyển',
        href: '/employer/applications',
        icon: FileText,
    },
    {
        title: 'Theo dõi báo cáo',
        href: '/employer/reports',
        icon: AlertTriangle,
    },
    {
        title: 'Lịch phỏng vấn',
        href: '/employer/interviews',
        icon: MessageSquare,
    },
    {
        title: 'Tìm ứng viên',
        href: '/employer/candidates/search',
        icon: Users,
    },
    {
        title: 'Cài đặt công ty',
        href: '/employer/settings/company',
        icon: User,
    },
];

// Get navigation items based on user role
const mainNavItems = computed(() => {
    if (isCandidateContext.value) {
        return candidateNavItems;
    } else if (hasRole('Employer')) {
        return employerNavItems;
    }
    return adminNavItems; // Default to admin nav
});

// Filter items theo permission (giống @can trong Blade)
// Items không có permission sẽ luôn được hiển thị (dành cho candidate items)
const filteredMainNavItems = computed(() => {
    return mainNavItems.value.filter((item) => {
        // Nếu item không có permission, luôn hiển thị (ví dụ: candidate menu items)
        if (!item.permission) {
            return true;
        }
        // Nếu có permission, kiểm tra quyền (ví dụ: admin menu items)
        return can(item.permission);
    });
});

// Footer navigation items - hide for candidates
const footerNavItems = computed(() => {
    // Don't show footer links for candidates
    if (hasRole('Candidate')) {
        return [];
    }

    return [
        {
            title: 'Github Repo',
            href: 'https://github.com/laravel/vue-starter-kit',
            icon: Folder,
        },
        {
            title: 'Documentation',
            href: 'https://laravel.com/docs/starter-kits#vue',
            icon: BookOpen,
        },
    ];
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="getDashboardLink()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="filteredMainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter
                v-if="footerNavItems.length > 0"
                :items="footerNavItems"
            />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
