<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Search, MapPin } from 'lucide-vue-next';
import { ref } from 'vue';

const searchQuery = ref('');
const location = ref('');

const handleSearch = () => {
    // Handle search logic
    console.log('Searching:', searchQuery.value, location.value);
};
</script>

<template>
    <section class="relative bg-gradient-to-br from-red-50 via-white to-orange-50 dark:from-red-950 dark:via-background dark:to-orange-950">
        <div class="container mx-auto px-4 py-20 md:py-32">
            <div class="max-w-5xl mx-auto text-center">
                <!-- Heading -->
                <h1 class="text-4xl md:text-6xl font-bold tracking-tight mb-6">
                    Tìm việc làm <span class="text-red-600">IT</span> ưng ý
                </h1>
                <p class="text-xl text-muted-foreground mb-12">
                    Khám phá hơn 1,000+ cơ hội việc làm IT hấp dẫn từ các công ty hàng đầu
                </p>

                <!-- Search Box -->
                <div class="bg-white dark:bg-card rounded-2xl shadow-2xl p-6 mb-8">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1 relative">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                            <Input 
                                v-model="searchQuery"
                                placeholder="Tìm kiếm: Java, ReactJS, NodeJS..." 
                                class="pl-10 h-12 text-base"
                            />
                        </div>
                        <div class="flex-1 relative">
                            <MapPin class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                            <Input 
                                v-model="location"
                                placeholder="Địa điểm: Hà Nội, TP.HCM..." 
                                class="pl-10 h-12 text-base"
                            />
                        </div>
                        <Button 
                            @click="handleSearch"
                            size="lg" 
                            class="h-12 px-8 bg-red-600 hover:bg-red-700"
                        >
                            <Search class="h-5 w-5 mr-2" />
                            Tìm kiếm
                        </Button>
                    </div>
                </div>

                <!-- Popular Keywords -->
                <div class="flex flex-wrap items-center justify-center gap-2">
                    <span class="text-sm text-muted-foreground">Từ khóa phổ biến:</span>
                    <Button variant="outline" size="sm">Java</Button>
                    <Button variant="outline" size="sm">ReactJS</Button>
                    <Button variant="outline" size="sm">NodeJS</Button>
                    <Button variant="outline" size="sm">Python</Button>
                    <Button variant="outline" size="sm">.NET</Button>
                </div>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-red-200 dark:bg-red-900 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-40 right-10 w-72 h-72 bg-orange-200 dark:bg-orange-900 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-200 dark:bg-pink-900 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
        </div>
    </section>
</template>

<style scoped>
@keyframes blob {
    0%, 100% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>

