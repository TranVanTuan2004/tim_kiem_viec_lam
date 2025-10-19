<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ClientHeader from '@/components/client/ClientHeader.vue';
import ClientFooter from '@/components/client/ClientFooter.vue';
import ChatWidget from '@/components/ChatWidget.vue';

interface Props {
    title?: string;
}

withDefaults(defineProps<Props>(), {
    title: 'ITviec - Tìm việc IT',
});
</script>

<template>
    <Head :title="title" />
    
    <div class="min-h-screen flex flex-col bg-background">
        <ClientHeader />
        
        <main class="flex-1">
            <slot />
        </main>
        
        <ClientFooter />
        
        <!-- Floating Support Chat Widget -->
        <ChatWidget />
    </div>
</template>

