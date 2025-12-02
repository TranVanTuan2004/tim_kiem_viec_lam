<script setup lang="ts">
import ChatWidget from '@/components/ChatWidget.vue';
import ClientFooter from '@/components/client/ClientFooter.vue';
import ClientHeader from '@/components/client/ClientHeader.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import { Head } from '@inertiajs/vue3';

interface Props {
    title?: string;
    fullscreen?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'ITviec - Tìm việc IT',
    fullscreen: false,
});
</script>

<template>
    <Head :title="title" />

    <div class="flex min-h-screen flex-col bg-background">
        <ClientHeader v-if="!props.fullscreen" />

        <main class="flex-1">
            <slot />
        </main>

        <ClientFooter v-if="!props.fullscreen" />

        <!-- Floating Support Chat Widget -->
        <ChatWidget v-if="!props.fullscreen" />

        <!-- Flash Messages -->
        <FlashMessage />
    </div>
</template>
