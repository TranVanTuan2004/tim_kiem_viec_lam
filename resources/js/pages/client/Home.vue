<script setup lang="ts">
import BannerCarousel from '@/components/BannerCarousel.vue';
import FeaturedJobs from '@/components/client/FeaturedJobs.vue';
import HeroSection from '@/components/client/HeroSection.vue';
import JobSearchSection from '@/components/client/JobSearchSection.vue';
import StatsSection from '@/components/client/StatsSection.vue';
import TopCompanies from '@/components/client/TopCompanies.vue';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { defineProps } from 'vue';

const props = defineProps({
    banners: {
        type: Array as () => any[],
        default: () => [],
    },
    featuredJobs: {
        type: Array as () => any[],
        default: () => [],
    },
    topCompanies: {
        type: Array as () => any[],
        default: () => [],
    },
});
</script>

<template>
    <ClientLayout title="Tìm việc IT - Job Portal">
        <!-- Banner Carousel -->
        <div v-if="props.banners.length > 0" class="mb-8">
            <BannerCarousel :banners="props.banners" />
        </div>

        <!-- Hero Section (if no banners) -->
        <HeroSection v-else />

        <!-- Job Search -->
        <JobSearchSection />

        <!-- Stats -->
        <StatsSection />

        <!-- Featured Jobs -->
        <FeaturedJobs :featured-jobs="props.featuredJobs" />

        <!-- Top Companies -->
        <TopCompanies :top-companies="props.topCompanies" />
    </ClientLayout>
</template>
