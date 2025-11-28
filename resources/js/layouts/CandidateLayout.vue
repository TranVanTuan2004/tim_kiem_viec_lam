<script setup lang="ts">
import ChatWidget from '@/components/ChatWidget.vue';
import ClientFooter from '@/components/client/ClientFooter.vue';
import ClientHeader from '@/components/client/ClientHeader.vue';
import CandidateSidebar from '@/components/candidate/CandidateSidebar.vue';
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

        <div class="flex flex-1">
            <!-- Sidebar -->
            <CandidateSidebar />

            <!-- Main Content with margin to account for fixed sidebar -->
            <main class="flex-1 bg-gray-50 lg:ml-64">
                <slot />
            </main>
        </div>

        <!-- Footer with margin to account for fixed sidebar -->
        <div class="lg:ml-64">
            <ClientFooter />
        </div>

        <!-- Floating Support Chat Widget -->
        <ChatWidget />

        <!-- Flash Messages -->
        <FlashMessage />
    </div>
</template>

