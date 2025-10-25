<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import type { PageProps } from '@inertiajs/core';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';


interface FlashProps {
  success?: string;
  error?: string;
}

type ExtendedPageProps = PageProps & { flash?: FlashProps };

// Lấy dữ liệu từ Inertia
const page = usePage<ExtendedPageProps>();
// const flashMessage = page.props.flash;
const flashMessage = ref<FlashProps>(page.props.flash || {});

// Ẩn thông báo sau 5 giây
onMounted(() => {
  if (flashMessage.value?.success || flashMessage.value?.error) {
    setTimeout(() => {
      flashMessage.value = {};
    }, 5000);
  }
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Hiển thị thông báo flash -->
        <div v-if="flashMessage?.success"
            class="mb-4 text-center text-sm font-medium text-green-600 bg-green-50 p-3 rounded-md border border-green-200">
            {{ flashMessage.success }}
        </div>

        <div v-if="flashMessage?.error"
            class="mb-4 text-center text-sm font-medium text-red-600 bg-red-50 p-3 rounded-md border border-red-200">
            {{ flashMessage.error }}
        </div>

        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
