<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

interface Banner {
  id: number;
  title: string;
  description: string | null;
  image_url: string;
  link_url: string | null;
  button_text: string | null;
  type: 'hero' | 'promotional' | 'announcement';
}

interface Props {
  banners: Banner[];
  autoplay?: boolean;
  interval?: number;
}

const props = withDefaults(defineProps<Props>(), {
  autoplay: true,
  interval: 5000,
});

const currentIndex = ref(0);
let autoplayTimer: ReturnType<typeof setInterval> | null = null;

function nextSlide() {
  currentIndex.value = (currentIndex.value + 1) % props.banners.length;
}

function prevSlide() {
  currentIndex.value = (currentIndex.value - 1 + props.banners.length) % props.banners.length;
}

function goToSlide(index: number) {
  currentIndex.value = index;
}

function startAutoplay() {
  if (props.autoplay && props.banners.length > 1) {
    autoplayTimer = setInterval(nextSlide, props.interval);
  }
}

function stopAutoplay() {
  if (autoplayTimer) {
    clearInterval(autoplayTimer);
    autoplayTimer = null;
  }
}

onMounted(() => {
  startAutoplay();
});

onUnmounted(() => {
  stopAutoplay();
});

function getBannerGradient(type: string) {
  const gradients: Record<string, string> = {
    hero: 'from-blue-600 to-purple-600',
    promotional: 'from-orange-500 to-red-500',
    announcement: 'from-blue-500 to-cyan-500',
  };
  return gradients[type] || 'from-gray-600 to-gray-800';
}
</script>

<template>
  <div v-if="banners.length > 0" class="relative w-full overflow-hidden rounded-2xl shadow-2xl">
    <!-- Banner Slides -->
    <div class="relative h-[400px] md:h-[500px]">
      <TransitionGroup name="slide">
        <div
          v-for="(banner, index) in banners"
          v-show="index === currentIndex"
          :key="banner.id"
          class="absolute inset-0"
        >
          <!-- Background Image with Overlay -->
          <div class="absolute inset-0">
            <img
              :src="banner.image_url"
              :alt="banner.title"
              class="w-full h-full object-cover"
            />
            <div :class="['absolute inset-0 bg-gradient-to-r opacity-10', getBannerGradient(banner.type)]"></div>
          </div>

          <!-- Content -->
          <div class="relative h-full flex items-center">
            <div class="container mx-auto px-6 md:px-12">
              <div class="max-w-2xl text-white">
                <h1 class="text-4xl md:text-6xl font-bold mb-4 drop-shadow-lg">
                  {{ banner.title }}
                </h1>
                <p v-if="banner.description" class="text-lg md:text-xl mb-8 drop-shadow-md opacity-95">
                  {{ banner.description }}
                </p>
                <a
                  v-if="banner.link_url && banner.button_text"
                  :href="banner.link_url"
                  class="inline-flex items-center px-8 py-4 bg-white text-gray-900 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105"
                >
                  {{ banner.button_text }}
                  <ChevronRight class="ml-2 h-5 w-5" />
                </a>
              </div>
            </div>
          </div>
        </div>
      </TransitionGroup>
    </div>

    <!-- Navigation Arrows -->
    <button
      v-if="banners.length > 1"
      @click="prevSlide"
      @mouseenter="stopAutoplay"
      @mouseleave="startAutoplay"
      class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white p-3 rounded-full transition-all duration-300 hover:scale-110"
      aria-label="Previous slide"
    >
      <ChevronLeft class="h-6 w-6" />
    </button>

    <button
      v-if="banners.length > 1"
      @click="nextSlide"
      @mouseenter="stopAutoplay"
      @mouseleave="startAutoplay"
      class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white p-3 rounded-full transition-all duration-300 hover:scale-110"
      aria-label="Next slide"
    >
      <ChevronRight class="h-6 w-6" />
    </button>

    <!-- Dots Indicator -->
    <div
      v-if="banners.length > 1"
      class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2"
    >
      <button
        v-for="(banner, index) in banners"
        :key="`dot-${banner.id}`"
        @click="goToSlide(index)"
        @mouseenter="stopAutoplay"
        @mouseleave="startAutoplay"
        :class="[
          'h-2 rounded-full transition-all duration-300',
          index === currentIndex
            ? 'w-8 bg-white'
            : 'w-2 bg-white/50 hover:bg-white/75'
        ]"
        :aria-label="`Go to slide ${index + 1}`"
      ></button>
    </div>
  </div>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: all 0.8s ease-in-out;
}

.slide-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.slide-leave-to {
  opacity: 0;
  transform: translateX(-100%);
}
</style>
