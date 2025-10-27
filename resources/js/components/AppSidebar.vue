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
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    Bookmark,
    Briefcase,
    CreditCard,
    FileText,
    Folder,
    Home,
    LayoutGrid,
    MessageSquare,
    User,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const { can, currentUser, hasRole } = usePermissions();
console.log(can('view users'));

// Determine dashboard link based on role
const getDashboardLink = () => {
    if (hasRole('Candidate')) {
        return '/candidate/dashboard';
    } else if (hasRole('Employer')) {
        return '/dashboard'; // Update when employer dashboard is ready
    }
    return dashboard(); // Admin or default
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
        title: 'My Profile',
        href: '/candidate/profile',
        icon: User,
    },
    {
        title: 'My Applications',
        href: '/candidate/applications',
        icon: FileText,
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
        title: 'Home',
        href: '/',
        icon: Home,
    },
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Users',
        href: '/admin/users',
        icon: Users,
        permission: 'view users',
    },
    {
        title: 'Chat',
        href: '/admin/chat',
        icon: MessageSquare,
        permission: 'view messages',
    },
    {
        title: 'Gói Dịch Vụ',
        href: '/admin/subscriptions',
        icon: CreditCard,
        permission: 'view subscriptions',
    },
    {
        title: 'ZaloPay Demo',
        href: '/admin/subscriptions/zalopay-demo',
        icon: CreditCard,
        permission: 'view subscriptions',
    },
];

console.log(currentUser.value);

// Get navigation items based on user role
const mainNavItems = computed(() => {
    if (hasRole('Candidate')) {
        return candidateNavItems;
    } else if (hasRole('Employer')) {
        return adminNavItems; // Update when employer nav is ready
    }
    return adminNavItems; // Default to admin nav
});

// Filter items theo permission (giống @can trong Blade)
const filteredMainNavItems = computed(() => {
    return mainNavItems.value.filter((item) => can(item.permission));
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
