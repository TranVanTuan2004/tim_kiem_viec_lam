<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useLanguage } from '@/composables/useLanguage';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Building2,
    Check,
    FileText,
    Heart,
    Languages,
    Search,
    User,
} from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAuthenticated = computed(() => !!user.value);

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
                    <Link href="/saved-jobs">
                        <Button variant="ghost" size="icon">
                            <Heart class="h-5 w-5" />
                        </Button>
                    </Link>
                    <Link href="/dashboard">
                        <Button variant="default">
                            <User class="mr-2 h-4 w-4" />
                            {{ t.dashboard }}
                        </Button>
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
