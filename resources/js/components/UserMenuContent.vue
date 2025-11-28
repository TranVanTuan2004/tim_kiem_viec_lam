<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { edit } from '@/routes/profile';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { Briefcase, LogOut, Settings, User as UserIcon } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    user: User;
}

const props = defineProps<Props>();

const handleLogout = () => {
    router.post(
        '/logout',
        {},
        {
            onFinish: () => router.flushAll(),
        },
    );
};

// Determine user role and profile link
const isCandidate = computed(() =>
    props.user.roles?.some((role) => role.name === 'Candidate'),
);

const isEmployer = computed(() =>
    props.user.roles?.some((role) => role.name === 'Employer'),
);

const isAdmin = computed(() =>
    props.user.roles?.some((role) => role.name === 'Admin'),
);

const profileLink = computed(() => {
    if (isCandidate.value) {
        return '/profile';
    } else if (isEmployer.value) {
        return '/employer/profile'; // Update this when employer profile is ready
    } else if (isAdmin.value) {
        return edit(); // Admin uses settings page
    }
    return edit(); // Fallback to settings
});

const dashboardLink = computed(() => {
    if (isCandidate.value) {
        return '/profile';
    } else if (isEmployer.value) {
        return '/admin/dashboard';
    } else if (isAdmin.value) {
        return '/admin/dashboard';
    }
    return '/profile'; // Default to candidate
});
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <!-- Dashboard Link for Candidates -->
        <DropdownMenuItem v-if="isCandidate" :as-child="true">
            <Link
                class="block w-full"
                :href="dashboardLink"
                prefetch
                as="button"
            >
                <Briefcase class="mr-2 h-4 w-4" />
                Dashboard
            </Link>
        </DropdownMenuItem>

        <!-- Profile Link -->
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="profileLink" prefetch as="button">
                <UserIcon class="mr-2 h-4 w-4" />
                {{ isCandidate ? 'My Profile' : 'Profile' }}
            </Link>
        </DropdownMenuItem>

        <!-- Settings Link (for Admin) -->
        <DropdownMenuItem v-if="isAdmin" :as-child="true">
            <Link class="block w-full" :href="edit()" prefetch as="button">
                <Settings class="mr-2 h-4 w-4" />
                Settings
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem @click="handleLogout">
        <LogOut class="mr-2 h-4 w-4" />
        Log out
    </DropdownMenuItem>
</template>
