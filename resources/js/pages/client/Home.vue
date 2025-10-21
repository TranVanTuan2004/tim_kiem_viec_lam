<script setup lang="ts">
import FeaturedJobs from '@/components/client/FeaturedJobs.vue';
import HeroSection from '@/components/client/HeroSection.vue';
import JobSearchSection from '@/components/client/JobSearchSection.vue';
import StatsSection from '@/components/client/StatsSection.vue';
import TopCompanies from '@/components/client/TopCompanies.vue';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { defineProps } from 'vue';

const props = defineProps({
    featuredJobs: {
        type: Array as () => any[],
        default: () => [],
    },
});
</script>

<template>
    <ClientLayout title="Tìm việc IT - Job Portal">
        <!-- Hero Section -->
        <HeroSection />

        <!-- Job Search -->
        <JobSearchSection />

        <!-- Stats -->
        <StatsSection />

        <!-- Featured Jobs -->
        <FeaturedJobs :featured-jobs="props.featuredJobs" />

        <!-- Top Companies -->
        <TopCompanies />
    </ClientLayout>
</template>
