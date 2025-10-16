<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Search, User, Building2, FileText, Heart } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAuthenticated = computed(() => !!user.value);
</script>

<template>
    <header class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div class="container mx-auto px-4 flex h-16 items-center justify-between">
            <!-- Logo -->
            <Link href="/" class="flex items-center space-x-2">
                <div class="flex items-center space-x-2">
                    <div class="h-8 w-8 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">IT</span>
                    </div>
                    <span class="text-xl font-bold">Job Portal</span>
                </div>
            </Link>

            <!-- Navigation -->
            <nav class="hidden md:flex items-center space-x-6">
                <Link href="/jobs" class="text-sm font-medium transition-colors hover:text-primary">
                    <div class="flex items-center space-x-2">
                        <Search class="h-4 w-4" />
                        <span>Tìm việc</span>
                    </div>
                </Link>
                <Link href="/companies" class="text-sm font-medium transition-colors hover:text-primary">
                    <div class="flex items-center space-x-2">
                        <Building2 class="h-4 w-4" />
                        <span>Công ty</span>
                    </div>
                </Link>
                <Link href="/blog" class="text-sm font-medium transition-colors hover:text-primary">
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
                            <User class="h-4 w-4 mr-2" />
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

