<script setup lang="ts">
import ClientLayout from '@/layouts/ClientLayout.vue';
import HeroSection from '@/components/client/HeroSection.vue';
import JobSearchSection from '@/components/client/JobSearchSection.vue';
import FeaturedJobs from '@/components/client/FeaturedJobs.vue';
import TopCompanies from '@/components/client/TopCompanies.vue';
import StatsSection from '@/components/client/StatsSection.vue';
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
        <FeaturedJobs />
        
        <!-- Top Companies -->
        <TopCompanies />
    </ClientLayout>
</template>

