<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useInitials } from '@/composables/useInitials';
import { useLanguage } from '@/composables/useLanguage';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Bell,
    Briefcase,
    Building2,
    Check,
    FileText,
    Folder,
    GraduationCap,
    AlertTriangle,
    Heart,
    Heart as HeartIcon,
    Languages,
    LayoutDashboard,
    LogOut,
    MessageSquare,
    Search,
    User,
    User as UserIcon,
    Bot,
} from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAuthenticated = computed(() => !!user.value);
const isCandidate = computed(() => {
    if (!user.value || !user.value.roles) {
        return false;
    }

    // getRoleNames() returns a collection of strings
    const roles = Array.isArray(user.value.roles)
        ? user.value.roles
        : [...user.value.roles];

    // Check if 'Candidate' is in the roles array
    return roles.includes('Candidate');
});

const isEmployer = computed(() => {
    if (!user.value || !user.value.roles) {
        return false;
    }

    const roles = Array.isArray(user.value.roles)
        ? user.value.roles
        : [...user.value.roles];

    return roles.includes('Employer');
});

const isAdmin = computed(() => {
    if (!user.value || !user.value.roles) {
        return false;
    }

    const roles = Array.isArray(user.value.roles)
        ? user.value.roles
        : [...user.value.roles];

    return roles.includes('Admin');
});

const dashboardLink = computed(() => {
    if (isCandidate.value) {
        return '/candidate/dashboard';
    } else if (isEmployer.value) {
        return '/employer/dashboard';
    } else if (isAdmin.value) {
        return '/admin/dashboard';
    }
    return '/profile'; // Default to candidate
});

const profileLink = computed(() => {
    if (isCandidate.value) {
        return '/candidate/profile';
    } else if (isEmployer.value) {
        return '/employer/profile';
    } else if (isAdmin.value) {
        return '/admin/profile';
    }
    return '/profile'; // Default to candidate
});

// Get candidate profile avatar if available
const candidateProfile = computed(() => {
    return (page.props as any).candidateProfile || null;
});

const userAvatar = computed(() => {
    // Try to get avatar from candidate profile first
    if (candidateProfile.value?.avatar_url) {
        return candidateProfile.value.avatar_url;
    }
    // Fallback to user avatar if exists
    if (user.value?.avatar) {
        return user.value.avatar;
    }
    return null;
});

// Always show avatar for candidate (even without image, will show initials)
const showAvatar = computed(() => {
    return isCandidate.value && isAuthenticated.value;
});

const { getInitials } = useInitials();
const { currentLanguage, setLanguage, t } = useLanguage();
</script>

<template>
    <header
        class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60"
    >
        <div
            class="container mx-auto flex h-16 items-center justify-between px-4"
        >
            <!-- Logo -->
            <Link href="/" class="flex items-center space-x-2">
                <div class="flex items-center space-x-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-red-500 to-red-600"
                    >
                        <span class="text-lg font-bold text-white">IT</span>
                    </div>
                    <span class="text-xl font-bold">Job Portal</span>
                </div>
            </Link>

            <!-- Navigation -->
            <nav class="hidden items-center space-x-6 md:flex">
                <Link
                    href="/jobs"
                    class="text-sm font-medium transition-colors hover:text-primary"
                >
                    <div class="flex items-center space-x-2">
                        <Search class="h-4 w-4" />
                        <span>{{ t.findJobs }}</span>
                    </div>
                </Link>
                <Link
                    href="/companies?has_jobs=1"
                    class="text-sm font-medium transition-colors hover:text-primary"
                >
                    <div class="flex items-center space-x-2">
                        <Building2 class="h-4 w-4" />
                        <span>{{ t.companies }}</span>
                    </div>
                </Link>
                <Link
                    href="/blog"
                    class="text-sm font-medium transition-colors hover:text-primary"
                >
                    <div class="flex items-center space-x-2">
                        <FileText class="h-4 w-4" />
                        <span>{{ t.blog }}</span>
                    </div>
                </Link>
                <Link
                    href="/ai"
                    class="text-sm font-medium transition-colors hover:text-primary"
                >
                    <div class="flex items-center space-x-2">
                        <Bot class="h-4 w-4" />
                        <span>Chat AI</span>
                    </div>
                </Link>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center space-x-3">
                <!-- Language Switcher -->
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="group h-9 gap-2 px-3"
                        >
                            <Languages
                                class="h-4 w-4 transition-transform group-hover:scale-110"
                            />
                            <span
                                class="hidden text-xs font-medium uppercase sm:inline"
                            >
                                {{ currentLanguage }}
                            </span>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-48">
                        <DropdownMenuItem
                            @click="setLanguage('vi')"
                            class="cursor-pointer gap-2"
                        >
                            <div
                                class="flex w-full items-center justify-between"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">🇻🇳</span>
                                    <span>Tiếng Việt</span>
                                </div>
                                <Check
                                    v-if="currentLanguage === 'vi'"
                                    class="h-4 w-4 text-red-600"
                                />
                            </div>
                        </DropdownMenuItem>
                        <DropdownMenuItem
                            @click="setLanguage('en')"
                            class="cursor-pointer gap-2"
                        >
                            <div
                                class="flex w-full items-center justify-between"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">🇬🇧</span>
                                    <span>English</span>
                                </div>
                                <Check
                                    v-if="currentLanguage === 'en'"
                                    class="h-4 w-4 text-red-600"
                                />
                            </div>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>

                <template v-if="isAuthenticated">
                    <Link href="/candidate/favorites">
                        <Button variant="ghost" size="icon">
                            <Heart class="h-5 w-5" />
                        </Button>
                    </Link>

                    <!-- Avatar Dropdown Menu for Candidate -->
                    <DropdownMenu v-if="isCandidate">
                        <DropdownMenuTrigger asChild>
                            <button
                                class="relative flex h-9 w-9 cursor-pointer items-center justify-center rounded-full transition-all hover:ring-2 hover:ring-primary focus:ring-2 focus:ring-primary focus:outline-none"
                                title="Menu tài khoản"
                            >
                                <Avatar
                                    class="h-9 w-9 border-2 border-gray-200 transition-colors hover:border-primary"
                                >
                                    <AvatarImage
                                        v-if="userAvatar"
                                        :src="userAvatar"
                                        :alt="user.value?.name || 'User'"
                                    />
                                    <AvatarFallback
                                        class="bg-gradient-to-br from-blue-500 to-purple-600 text-sm font-semibold text-white"
                                    >
                                        {{
                                            getInitials(user.value?.name || '')
                                        }}
                                    </AvatarFallback>
                                </Avatar>
                            </button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-64">
                            <!-- User Info Section -->
                            <div
                                class="flex items-center gap-3 border-b px-3 py-3"
                            >
                                <Avatar class="h-12 w-12">
                                    <AvatarImage
                                        v-if="userAvatar"
                                        :src="userAvatar"
                                        :alt="user.value?.name || 'User'"
                                    />
                                    <AvatarFallback
                                        class="bg-gradient-to-br from-blue-500 to-purple-600 text-sm font-semibold text-white"
                                    >
                                        {{
                                            getInitials(user.value?.name || '')
                                        }}
                                    </AvatarFallback>
                                </Avatar>
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="truncate text-sm font-semibold text-gray-900"
                                    >
                                        {{ user.value?.name }}
                                    </p>
                                    <p class="truncate text-xs text-gray-500">
                                        {{ user.value?.email }}
                                    </p>
                                </div>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-1">
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/candidate/dashboard"
                                        class="flex items-center gap-3 px-3 py-2"
                                    >
                                        <LayoutDashboard class="h-4 w-4" />
                                        <span>Tổng quan</span>
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/candidate/profile"
                                        class="flex items-center gap-3 px-3 py-2"
                                    >
                                        <UserIcon class="h-4 w-4" />
                                        <span>Hồ sơ cá nhân</span>
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/candidate/applications"
                                        class="flex items-center gap-3 px-3 py-2"
                                    >
                                        <Briefcase class="h-4 w-4" />
                                        <span>Đơn ứng tuyển</span>
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/candidate/notifications"
                                        class="flex items-center gap-3 px-3 py-2"
                                    >
                                        <Bell class="h-4 w-4" />
                                        <span>Thông báo</span>
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/candidate/interviews"
                                        class="flex items-center gap-3 px-3 py-2"
                                    >
                                        <MessageSquare class="h-4 w-4" />
                                        <span>Lịch phỏng vấn</span>
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/candidate/cvs"
                                        class="flex items-center gap-3 px-3 py-2"
                                    >
                                        <FileText class="h-4 w-4" />
                                        <span>Quản lý CV</span>
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/candidate/saved-jobs"
                                        class="flex items-center gap-3 px-3 py-2"
                                    >
                                        <HeartIcon class="h-4 w-4" />
                                        <span>Việc làm đã lưu</span>
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/candidate/reports"
                                        class="flex items-center gap-3 px-3 py-2"
                                    >
                                        <AlertTriangle class="h-4 w-4" />
                                        <span>Theo dõi báo cáo</span>
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/candidate/portfolios"
                                        class="flex items-center gap-3 px-3 py-2"
                                    >
                                        <Folder class="h-4 w-4" />
                                        <span>Portfolio</span>
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/candidate/work-experiences"
                                        class="flex items-center gap-3 px-3 py-2"
                                    >
                                        <Briefcase class="h-4 w-4" />
                                        <span>Kinh nghiệm làm việc</span>
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/candidate/educations"
                                        class="flex items-center gap-3 px-3 py-2"
                                    >
                                        <GraduationCap class="h-4 w-4" />
                                        <span>Học vấn</span>
                                    </Link>
                                </DropdownMenuItem>
                                <div class="my-1 border-t"></div>
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/logout"
                                        method="post"
                                        class="flex items-center gap-3 px-3 py-2 text-red-600 hover:text-red-700"
                                    >
                                        <LogOut class="h-4 w-4" />
                                        <span>Đăng xuất</span>
                                    </Link>
                                </DropdownMenuItem>
                            </div>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <!-- Beautiful Avatar for Employer/Admin (no dropdown) -->
                    <Link v-else :href="dashboardLink">
                        <button
                            class="relative flex h-10 w-10 cursor-pointer items-center justify-center rounded-full transition-all hover:ring-2 hover:ring-red-500 hover:scale-105 focus:ring-2 focus:ring-red-500 focus:outline-none"
                            :title="`Go to ${isEmployer ? 'Employer' : 'Admin'} Dashboard`"
                        >
                            <Avatar
                                class="h-10 w-10 border-2 border-gray-200 transition-all hover:border-red-500 hover:shadow-lg"
                            >
                                <AvatarImage
                                    v-if="userAvatar"
                                    :src="userAvatar"
                                    :alt="user.value?.name || 'User'"
                                />
                                <AvatarFallback
                                    :class="[
                                        'text-sm font-bold text-white',
                                        isEmployer 
                                            ? 'bg-gradient-to-br from-indigo-500 to-purple-600' 
                                            : 'bg-gradient-to-br from-red-500 to-orange-600'
                                    ]"
                                >
                                    {{
                                        getInitials(user.value?.name || '')
                                    }}
                                </AvatarFallback>
                            </Avatar>
                            <!-- Online indicator dot -->
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-green-500"></span>
                        </button>
                    </Link>
                </template>
                <template v-else>
                    <Link href="/login">
                        <Button variant="ghost">{{ t.login }}</Button>
                    </Link>
                    <Link href="/register">
                        <Button variant="default">{{ t.register }}</Button>
                    </Link>
                </template>
            </div>
        </div>
    </header>
</template>
