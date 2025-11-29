<script setup lang="ts">
import ClientHeader from '@/components/client/ClientHeader.vue';
import ClientFooter from '@/components/client/ClientFooter.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    title?: string;
}>();
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <Head :title="title || 'Authentication'" />
        
        <!-- Client Header -->
        <ClientHeader />
        
        <!-- Main Content -->
        <main class="flex-1 bg-gray-50">
            <slot />
        </main>
        
        <!-- Client Footer -->
        <ClientFooter />
    </div>
</template>
