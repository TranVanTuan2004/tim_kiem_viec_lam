<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { ChevronLeft, ChevronRight, Sparkles } from 'lucide-vue-next';

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
const isTransitioning = ref(false);
const progressKey = ref(0);
let autoplayTimer: ReturnType<typeof setInterval> | null = null;

function nextSlide() {
  if (isTransitioning.value) return;
  isTransitioning.value = true;
  progressKey.value++; // Reset progress bar
  currentIndex.value = (currentIndex.value + 1) % props.banners.length;
  setTimeout(() => {
    isTransitioning.value = false;
  }, 800);
}

function prevSlide() {
  if (isTransitioning.value) return;
  isTransitioning.value = true;
  currentIndex.value = (currentIndex.value - 1 + props.banners.length) % props.banners.length;
  setTimeout(() => {
    isTransitioning.value = false;
  }, 800);
}

function goToSlide(index: number) {
  if (isTransitioning.value || index === currentIndex.value) return;
  isTransitioning.value = true;
  progressKey.value++; // Reset progress bar
  currentIndex.value = index;
  setTimeout(() => {
    isTransitioning.value = false;
  }, 800);
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
    hero: 'from-blue-600/80 via-purple-600/80 to-pink-600/80',
    promotional: 'from-orange-500/80 via-red-500/80 to-pink-500/80',
    announcement: 'from-blue-500/80 via-cyan-500/80 to-teal-500/80',
  };
  return gradients[type] || 'from-gray-600/80 to-gray-800/80';
}

const currentBanner = computed(() => props.banners[currentIndex.value]);
</script>

<template>
  <div v-if="banners.length > 0" class="relative w-full overflow-hidden rounded-3xl shadow-2xl group">
    <!-- Animated Background Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-10">
      <div
        v-for="i in 20"
        :key="i"
        class="absolute rounded-full bg-white/10 animate-float"
        :style="{
          left: `${Math.random() * 100}%`,
          top: `${Math.random() * 100}%`,
          width: `${Math.random() * 4 + 2}px`,
          height: `${Math.random() * 4 + 2}px`,
          animationDelay: `${Math.random() * 5}s`,
          animationDuration: `${Math.random() * 3 + 2}s`,
        }"
      ></div>
    </div>

    <!-- Banner Slides -->
    <div class="relative h-[450px] md:h-[600px] lg:h-[650px] overflow-hidden">
      <TransitionGroup name="slide-fade" tag="div">
        <div
          v-for="(banner, index) in banners"
          v-show="index === currentIndex"
          :key="`banner-${banner.id}-${currentIndex}`"
          class="absolute inset-0"
        >
          <!-- Background Image with Parallax Effect -->
          <div class="absolute inset-0 transform scale-110 transition-transform duration-[10000ms] group-hover:scale-100">
            <img
              :src="banner.image_url"
              :alt="banner.title"
              class="w-full h-full object-cover"
              loading="eager"
            />
            <!-- Multi-layer Gradient Overlay -->
            <div :class="['absolute inset-0 bg-gradient-to-br', getBannerGradient(banner.type)]"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/40 to-transparent"></div>
          </div>

          <!-- Animated Content -->
          <div class="relative h-full flex items-center z-20">
            <div class="container mx-auto px-6 md:px-12 lg:px-16">
              <div class="max-w-3xl">
                <!-- Sparkle Icon -->
                <div class="mb-6 animate-fade-in-up" style="animation-delay: 0.2s">
                  <div class="inline-flex items-center gap-2 rounded-full bg-white/20 backdrop-blur-md px-4 py-2 border border-white/30">
                    <Sparkles class="h-4 w-4 text-yellow-300 animate-pulse" />
                    <span class="text-sm font-semibold text-white">Khám phá ngay</span>
                  </div>
                </div>

                <!-- Title with Animation -->
                <h1 
                  class="text-4xl md:text-6xl lg:text-7xl font-extrabold mb-6 text-white leading-tight drop-shadow-2xl animate-fade-in-up"
                  style="animation-delay: 0.4s"
                >
                  <span class="block mb-2 bg-gradient-to-r from-white via-yellow-100 to-white bg-clip-text text-transparent">
                    {{ banner.title }}
                  </span>
                </h1>

                <!-- Description with Animation -->
                <p 
                  v-if="banner.description" 
                  class="text-lg md:text-xl lg:text-2xl mb-10 text-white/95 drop-shadow-lg leading-relaxed animate-fade-in-up"
                  style="animation-delay: 0.6s"
                >
                  {{ banner.description }}
                </p>

                <!-- CTA Button with Animation -->
                <div class="animate-fade-in-up" style="animation-delay: 0.8s">
                  <a
                    v-if="banner.link_url && banner.button_text"
                    :href="banner.link_url"
                    class="group/btn inline-flex items-center gap-3 px-8 py-5 bg-gradient-to-r from-white to-yellow-50 text-gray-900 font-bold rounded-2xl hover:from-yellow-50 hover:to-white transition-all duration-500 shadow-2xl hover:shadow-yellow-500/50 transform hover:scale-105 hover:-translate-y-1 relative overflow-hidden"
                  >
                    <!-- Shine Effect -->
                    <span class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/50 to-transparent group-hover/btn:translate-x-full transition-transform duration-1000"></span>
                    <span class="relative z-10">{{ banner.button_text }}</span>
                    <ChevronRight class="relative z-10 h-5 w-5 transition-transform duration-300 group-hover/btn:translate-x-1" />
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Decorative Elements -->
          <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
        </div>
      </TransitionGroup>
    </div>

    <!-- Enhanced Navigation Arrows -->
    <button
      v-if="banners.length > 1"
      @click="prevSlide"
      @mouseenter="stopAutoplay"
      @mouseleave="startAutoplay"
      class="absolute left-4 md:left-6 top-1/2 -translate-y-1/2 z-30 bg-white/10 hover:bg-white/30 backdrop-blur-md text-white p-4 rounded-full transition-all duration-300 hover:scale-110 hover:shadow-2xl border border-white/20 group/arrow"
      aria-label="Previous slide"
    >
      <ChevronLeft class="h-6 w-6 md:h-7 md:w-7 transition-transform group-hover/arrow:-translate-x-1" />
    </button>

    <button
      v-if="banners.length > 1"
      @click="nextSlide"
      @mouseenter="stopAutoplay"
      @mouseleave="startAutoplay"
      class="absolute right-4 md:right-6 top-1/2 -translate-y-1/2 z-30 bg-white/10 hover:bg-white/30 backdrop-blur-md text-white p-4 rounded-full transition-all duration-300 hover:scale-110 hover:shadow-2xl border border-white/20 group/arrow"
      aria-label="Next slide"
    >
      <ChevronRight class="h-6 w-6 md:h-7 md:w-7 transition-transform group-hover/arrow:translate-x-1" />
    </button>

    <!-- Enhanced Dots Indicator -->
    <div
      v-if="banners.length > 1"
      class="absolute bottom-6 md:bottom-8 left-1/2 -translate-x-1/2 z-30 flex gap-3 bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/20"
    >
      <button
        v-for="(banner, index) in banners"
        :key="`dot-${banner.id}`"
        @click="goToSlide(index)"
        @mouseenter="stopAutoplay"
        @mouseleave="startAutoplay"
        :class="[
          'h-2.5 rounded-full transition-all duration-500 relative overflow-hidden',
          index === currentIndex
            ? 'w-10 bg-white shadow-lg shadow-white/50'
            : 'w-2.5 bg-white/50 hover:bg-white/75 hover:w-8'
        ]"
        :aria-label="`Go to slide ${index + 1}`"
      >
        <span
          v-if="index === currentIndex"
          class="absolute inset-0 bg-gradient-to-r from-yellow-300 to-white animate-pulse"
        ></span>
      </button>
    </div>

    <!-- Progress Bar -->
    <div
      v-if="banners.length > 1 && autoplay"
      class="absolute bottom-0 left-0 right-0 h-1 bg-white/10 z-30 overflow-hidden"
    >
      <div
        class="h-full bg-gradient-to-r from-yellow-400 via-orange-400 to-white animate-progress"
        :key="progressKey"
      ></div>
    </div>
  </div>
</template>

<style scoped>
/* Slide Fade Transition */
.slide-fade-enter-active {
  transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-fade-leave-active {
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-fade-enter-from {
  opacity: 0;
  transform: translateX(30px) scale(0.95);
  filter: blur(10px);
}

.slide-fade-leave-to {
  opacity: 0;
  transform: translateX(-30px) scale(0.95);
  filter: blur(10px);
}

.slide-fade-enter-to,
.slide-fade-leave-from {
  opacity: 1;
  transform: translateX(0) scale(1);
  filter: blur(0);
}

/* Fade In Up Animation */
@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
  opacity: 0;
}

/* Float Animation for Particles */
@keyframes float {
  0%, 100% {
    transform: translateY(0) translateX(0);
    opacity: 0.3;
  }
  50% {
    transform: translateY(-20px) translateX(10px);
    opacity: 0.6;
  }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

/* Progress Bar Animation */
@keyframes progress {
  from {
    width: 0%;
  }
  to {
    width: 100%;
  }
}

.animate-progress {
  animation: progress 5s linear forwards;
}
</style>
