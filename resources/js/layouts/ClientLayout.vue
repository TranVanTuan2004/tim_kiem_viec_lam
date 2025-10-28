<script setup lang="ts">
import ChatWidget from '@/components/ChatWidget.vue';
import ClientFooter from '@/components/client/ClientFooter.vue';
import ClientHeader from '@/components/client/ClientHeader.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import { Head } from '@inertiajs/vue3';

interface Props {
    title?: string;
}

withDefaults(defineProps<Props>(), {
    title: 'ITviec - Tìm việc IT',
});
</script>

<template>
    <Head :title="title" />

    <div class="flex min-h-screen flex-col bg-background">
        <ClientHeader />

        <main class="flex-1">
            <slot />
        </main>

        <ClientFooter />

        <!-- Floating Support Chat Widget -->
        <ChatWidget />

        <!-- Flash Messages -->
        <FlashMessage />
    </div>
</template>
