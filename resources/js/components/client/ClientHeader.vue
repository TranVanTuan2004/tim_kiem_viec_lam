<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Link, usePage } from '@inertiajs/vue3';
import { Building2, FileText, Heart, Search, User } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAuthenticated = computed(() => !!user.value);
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
                        <span>Tìm việc</span>
                    </div>
                </Link>
                <Link
                    href="/companies?has_jobs=1"
                    class="text-sm font-medium transition-colors hover:text-primary"
                >
                    <div class="flex items-center space-x-2">
                        <Building2 class="h-4 w-4" />
                        <span>Công ty</span>
                    </div>
                </Link>
                <Link
                    href="/blog"
                    class="text-sm font-medium transition-colors hover:text-primary"
                >
                    <div class="flex items-center space-x-2">
                        <FileText class="h-4 w-4" />
                        <span>Blog IT</span>
                    </div>
                </Link>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center space-x-4">
                <template v-if="isAuthenticated">
                    <Link href="/saved-jobs">
                        <Button variant="ghost" size="icon">
                            <Heart class="h-5 w-5" />
                        </Button>
                    </Link>
                    <Link href="/dashboard">
                        <Button variant="default">
                            <User class="mr-2 h-4 w-4" />
                            Dashboard
                        </Button>
                    </Link>
                </template>
                <template v-else>
                    <Link href="/login">
                        <Button variant="ghost">Đăng nhập</Button>
                    </Link>
                    <Link href="/register">
                        <Button variant="default">Đăng ký</Button>
                    </Link>
                </template>
            </div>
        </div>
    </header>
</template>
