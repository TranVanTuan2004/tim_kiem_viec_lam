<script setup lang="ts">
import BannerCarousel from '@/components/BannerCarousel.vue';
import FeaturedJobs from '@/components/client/FeaturedJobs.vue';
import HeroSection from '@/components/client/HeroSection.vue';
import JobSearchSection from '@/components/client/JobSearchSection.vue';
import StatsSection from '@/components/client/StatsSection.vue';
import TopCompanies from '@/components/client/TopCompanies.vue';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { onMounted, nextTick } from 'vue';

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
    popularCategories: {
        type: Array as () => any[],
        default: () => [],
    },
    sections: {
        type: Array as () => any[],
        default: () => [],
    },
});

// Scroll animations with Intersection Observer
onMounted(() => {
    nextTick(() => {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px',
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in-view');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all sections
        const sections = document.querySelectorAll('.home-section');
        sections.forEach((section) => observer.observe(section));
    });
});
</script>

<template>
    <ClientLayout title="Tìm việc IT - Job Portal">
        <div class="home-page">
            <template v-for="section in props.sections" :key="section.section_key">
                <!-- Hero Section -->
                <div 
                    v-if="section.section_key === 'hero'" 
                    class="home-section hero-section mb-8 md:mb-12"
                >
                    <BannerCarousel v-if="props.banners.length > 0" :banners="props.banners" />
                    <HeroSection v-else />
                </div>

                <!-- Job Search / Categories -->
                <div 
                    v-else-if="section.section_key === 'categories'"
                    class="home-section"
                >
                    <JobSearchSection 
                        :popular-categories="props.popularCategories" 
                    />
                </div>

                <!-- Stats -->
                <div 
                    v-else-if="section.section_key === 'statistics'"
                    class="home-section"
                >
                    <StatsSection />
                </div>

                <!-- Featured Jobs -->
                <div 
                    v-else-if="section.section_key === 'featured_jobs'"
                    class="home-section"
                >
                    <FeaturedJobs 
                        :featured-jobs="props.featuredJobs" 
                    />
                </div>

                <!-- Top Companies -->
                <div 
                    v-else-if="section.section_key === 'top_companies'"
                    class="home-section"
                >
                    <TopCompanies 
                        :top-companies="props.topCompanies" 
                    />
                </div>
            </template>
        </div>
    </ClientLayout>
</template>

<style scoped>
.home-page {
    position: relative;
}

.home-section {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}

.home-section.animate-in-view {
    opacity: 1;
    transform: translateY(0);
}

/* Hero section should appear immediately */
.hero-section {
    opacity: 1;
    transform: translateY(0);
}

/* Smooth scroll behavior */
:deep(*) {
    scroll-behavior: smooth;
}

/* Add parallax effect to hero */
.hero-section {
    position: relative;
    z-index: 1;
}
</style>
