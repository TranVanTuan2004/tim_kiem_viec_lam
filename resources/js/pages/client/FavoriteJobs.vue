<script setup lang="ts">
import axios from 'axios';
import { computed, ref } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import type { PageProps } from '@inertiajs/core';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Heart, Building2, MapPin, DollarSign, Clock } from 'lucide-vue-next';
import { useClientToast } from '@/composables/useClientToast';

interface Job {
  id: number;
  title: string;
  company: string;
  location: string;
  salary: string;
  posted: string;
  skills: string[];
  type: string;
  is_favorited: boolean;
  company_logo?: string;
  slug?: string;
  is_featured?: boolean;
}

interface FavoritePageProps extends PageProps {
  favorites: Job[];
  auth: any;
}

const page = usePage<FavoritePageProps>();
const favorites = ref(page.props.favorites || []);
const auth = computed(() => page.props.auth);

const { showToast } = useClientToast();

const toggleFavorite = async (job: Job) => {
  const prev = job.is_favorited;
  job.is_favorited = !job.is_favorited;

  try {
    const res = await axios.post(`/candidate/favorites/toggle/${job.id}`);
    job.is_favorited = res.data.is_favorited;

    // Nếu bỏ yêu thích, xóa ngay khỏi danh sách
    if (!job.is_favorited) {
      favorites.value = favorites.value.filter(f => f.id !== job.id);
    }
    
    showToast(
      'success',
      res.data.is_favorited ? 'Đã lưu' : 'Đã xóa',
      res.data.message
    );
  } catch (err) {
    job.is_favorited = prev;
    showToast('error', 'Lỗi', 'Thao tác thất bại, vui lòng thử lại.');
  }
};
</script>

<template>
  <ClientLayout title="Danh sách tin tuyển dụng yêu thích">
    <div class="container mx-auto p-4">
      <div v-if="favorites.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <Link
          v-for="job in favorites"
          :key="job.id"
          :href="`/jobs/${job.slug || job.id}`"
          class="group"
        >
          <Card class="h-full cursor-pointer border-2 bg-card transition-all duration-300 hover:-translate-y-1 hover:border-red-200 hover:shadow-2xl">
            <CardHeader class="pb-3">
              <div class="flex items-start justify-between">
                <div class="flex flex-1 items-start gap-3">
                  <!-- Logo công ty -->
                  <div
                    class="flex h-14 w-14 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-red-50 to-orange-50 ring-2 ring-red-100 transition-transform duration-300 group-hover:scale-110 group-hover:ring-red-200"
                  >
                    <img
                      v-if="job.company_logo"
                      :src="job.company_logo"
                      :alt="job.company"
                      class="h-full w-full object-contain p-2"
                    />
                    <div v-else class="text-2xl">{{ job.company ? job.company[0] : 'C' }}</div>

                  </div>

                  <div class="min-w-0 flex-1">
                    <div class="mb-2">
                      <CardTitle
                        class="line-clamp-2 text-lg leading-tight font-bold transition-colors group-hover:text-red-600"
                      >
                        {{ job.title }}
                      </CardTitle>
                      <Badge
                        v-if="job.is_featured"
                        class="mt-1.5 animate-pulse bg-gradient-to-r from-red-600 to-orange-500 text-[10px] font-semibold"
                      >
                        ⭐ HOT
                      </Badge>
                    </div>
                    <CardDescription class="flex items-center gap-1.5 text-xs">
                      <Building2 class="h-3.5 w-3.5 flex-shrink-0" />
                      <span class="truncate">{{ job.company }}</span>
                    </CardDescription>
                  </div>
                </div>

                <Button
                  variant="ghost"
                  size="icon"
                  :class="job.is_favorited ? 'text-red-600' : 'text-gray-400 hover:text-red-600'"
                  @click.prevent="toggleFavorite(job)"
                >
                  <Heart
                    :class="job.is_favorited ? 'fill-red-600 text-red-600' : ''"
                    class="h-5 w-5"
                  />
                </Button>
              </div>
            </CardHeader>

            <CardContent class="space-y-4">
              <div class="flex flex-wrap gap-x-4 gap-y-2 text-xs text-muted-foreground">
                <div class="flex items-center gap-1.5">
                  <MapPin class="h-3.5 w-3.5" />
                  <span>{{ job.location }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                  <DollarSign class="h-3.5 w-3.5" />
                  <span class="font-medium">
                    {{ job.salary }}
                  </span>

                </div>
                <div class="flex items-center gap-1.5">
                  <Clock class="h-3.5 w-3.5" />
                  <span>{{ job.posted }}</span>
                </div>
              </div>

              <div class="flex flex-wrap gap-1.5">
                <Badge
                  v-for="skill in job.skills || []"
                  :key="skill"
                  variant="secondary"
                  class="text-[10px]"
                >
                  {{ skill }}
                </Badge>
                <Badge variant="outline" class="text-[10px]">{{ job.type || 'Full time' }}</Badge>
              </div>

              <Button
                class="w-full transition-all group-hover:bg-red-600 group-hover:shadow-md"
                variant="default"
              >
                Ứng tuyển ngay →
              </Button>
            </CardContent>
          </Card>
        </Link>
      </div>

      <div v-else class="py-16 text-center">
        <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-muted">
          <Heart class="h-12 w-12 text-muted-foreground" />
        </div>
        <h3 class="mb-2 text-xl font-semibold">
          Chưa có công việc nào trong danh sách yêu thích
        </h3>
      </div>
    </div>
  </ClientLayout>
</template>
