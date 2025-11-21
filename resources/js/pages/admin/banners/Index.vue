<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
  Search, 
  Image as ImageIcon, 
  Plus, 
  Edit, 
  Trash2, 
  Eye, 
  EyeOff,
  GripVertical,
  Calendar,
  Info
} from 'lucide-vue-next';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';

interface Banner {
  id: number;
  title: string;
  description: string | null;
  image_url: string;
  link_url: string | null;
  button_text: string | null;
  order: number;
  is_active: boolean;
  type: 'hero' | 'promotional' | 'announcement';
  start_date: string | null;
  end_date: string | null;
  created_at: string;
}

interface Props {
  banners: {
    data: Banner[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  filters: {
    search?: string;
    type?: string;
    status?: string;
  };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const type = ref(props.filters.type || '');
const status = ref(props.filters.status || '');
const isToggleDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const bannerToToggle = ref<Banner | null>(null);
const bannerToDelete = ref<Banner | null>(null);

function applyFilters() {
  router.get('/admin/banners', { 
    search: search.value || undefined,
    type: type.value || undefined,
    status: status.value || undefined,
  }, { preserveState: false });
}

function clearFilters() {
  search.value = '';
  type.value = '';
  status.value = '';
  applyFilters();
}

function openToggleDialog(banner: Banner) {
  bannerToToggle.value = banner;
  isToggleDialogOpen.value = true;
}

function confirmToggle() {
  if (!bannerToToggle.value) return;
  
  router.post(`/admin/banners/${bannerToToggle.value.id}/toggle-active`, {}, { 
    preserveScroll: true,
    onSuccess: () => {
      isToggleDialogOpen.value = false;
      bannerToToggle.value = null;
    }
  });
}

function openDeleteDialog(banner: Banner) {
  bannerToDelete.value = banner;
  isDeleteDialogOpen.value = true;
}

function confirmDelete() {
  if (!bannerToDelete.value) return;
  
  router.delete(`/admin/banners/${bannerToDelete.value.id}`, { 
    preserveScroll: true,
    onSuccess: () => {
      isDeleteDialogOpen.value = false;
      bannerToDelete.value = null;
    }
  });
}

function getBannerTypeLabel(type: string) {
  const labels: Record<string, string> = {
    hero: 'Hero Banner',
    promotional: 'Khuyến mãi',
    announcement: 'Thông báo',
  };
  return labels[type] || type;
}

function getBannerTypeBadgeClass(type: string) {
  const classes: Record<string, string> = {
    hero: 'bg-purple-100 text-purple-800',
    promotional: 'bg-blue-100 text-blue-800',
    announcement: 'bg-orange-100 text-orange-800',
  };
  return classes[type] || 'bg-gray-100 text-gray-800';
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý Banner', href: '/admin/banners' }
];
</script>

<template>
  <Head title="Quản lý Banner" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-[20px]">
      
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
        <div>
          <h1 class="text-3xl font-bold tracking-tight flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <ImageIcon class="h-6 w-6 text-primary" />
            </div>
            Quản lý Banner
          </h1>
          <p class="text-muted-foreground mt-2 ml-[52px]">
            Quản lý banner hiển thị trên trang chủ
          </p>
        </div>
        <Link href="/admin/banners/create">
          <Button class="flex items-center gap-2">
            <Plus class="h-4 w-4" />
            Tạo Banner Mới
          </Button>
        </Link>
      </div>

      <!-- Filters -->
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <Search class="h-5 w-5" />
            Bộ lọc
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid gap-4 md:grid-cols-4 mb-4">
            <Input
              v-model="search"
              placeholder="Tìm kiếm tiêu đề..."
              @keyup.enter="applyFilters"
            />
            
            <select v-model="type" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
              <option value="">Tất cả loại</option>
              <option value="hero">Hero Banner</option>
              <option value="promotional">Khuyến mãi</option>
              <option value="announcement">Thông báo</option>
            </select>

            <select v-model="status" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
              <option value="">Tất cả</option>
              <option value="1">Đang hoạt động</option>
              <option value="0">Đã tắt</option>
            </select>

            <div class="flex gap-2">
              <Button @click="applyFilters" class="flex-1">Tìm</Button>
              <Button variant="outline" @click="clearFilters">Xóa</Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Banners List -->
      <!-- Banners Table -->
      <div class="bg-white rounded-md shadow overflow-hidden">
        <div class="responsive-table-wrapper">
          <table class="w-full text-sm text-left mobile-card-view">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
              <tr>
                <th class="px-4 py-3">Hình ảnh</th>
                <th class="px-4 py-3">Tiêu đề / Mô tả</th>
                <th class="px-4 py-3">Loại</th>
                <th class="px-4 py-3">Thứ tự</th>
                <th class="px-4 py-3">Trạng thái</th>
                <th class="px-4 py-3 text-right">Hành động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="banner in banners.data" :key="banner.id" class="border-b hover:bg-gray-50">
                <td class="px-4 py-3" data-label="Hình ảnh">
                  <div class="h-12 w-20 rounded overflow-hidden bg-gray-100">
                    <img 
                      :src="banner.image_url" 
                      :alt="banner.title"
                      class="h-full w-full object-cover"
                    />
                  </div>
                </td>
                <td class="px-4 py-3 max-w-xs" data-label="Tiêu đề / Mô tả">
                  <div class="font-medium text-gray-900 truncate" :title="banner.title">{{ banner.title }}</div>
                  <div class="text-gray-500 truncate text-xs" :title="banner.description || ''">{{ banner.description }}</div>
                </td>
                <td class="px-4 py-3" data-label="Loại">
                  <Badge :class="getBannerTypeBadgeClass(banner.type)">
                    {{ getBannerTypeLabel(banner.type) }}
                  </Badge>
                </td>
                <td class="px-4 py-3" data-label="Thứ tự">
                  {{ banner.order }}
                </td>
                <td class="px-4 py-3" data-label="Trạng thái">
                  <Badge :class="banner.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    {{ banner.is_active ? 'Hiển thị' : 'Đã tắt' }}
                  </Badge>
                </td>
                <td class="px-4 py-3 text-right" data-label="Hành động">
                  <div class="flex items-center justify-end gap-2">
                    <Link :href="`/admin/banners/${banner.id}/edit`">
                      <Button size="sm" variant="outline" class="flex items-center gap-1">
                        <Edit class="h-4 w-4" />
                        Sửa
                      </Button>
                    </Link>
                    
                    <Button 
                      size="sm" 
                      variant="outline" 
                      @click="openToggleDialog(banner)"
                      class="flex items-center gap-1"
                    >
                      <component :is="banner.is_active ? EyeOff : Eye" class="h-4 w-4" />
                      {{ banner.is_active ? 'Tắt' : 'Bật' }}
                    </Button>

                    <Button 
                      size="sm" 
                      variant="destructive" 
                      @click="openDeleteDialog(banner)"
                      class="flex items-center gap-1"
                    >
                      <Trash2 class="h-4 w-4" />
                      Xóa
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="banners.last_page > 1" class="flex items-center justify-center gap-2 pt-6 mt-6 border-t">
        <Button
          :disabled="banners.current_page === 1"
          variant="outline"
          size="sm"
          @click="router.get(`/admin/banners?page=${banners.current_page - 1}`)"
        >
          Trước
        </Button>
        <span class="text-sm text-muted-foreground">
          Trang {{ banners.current_page }} / {{ banners.last_page }}
        </span>
        <Button
          :disabled="banners.current_page === banners.last_page"
          variant="outline"
          size="sm"
          @click="router.get(`/admin/banners?page=${banners.current_page + 1}`)"
        >
          Sau
        </Button>
      </div>

      <!-- Toggle Confirmation Modal -->
      <Dialog v-model:open="isToggleDialogOpen">
        <DialogContent class="sm:max-w-md">
          <DialogHeader>
            <div class="flex items-center gap-3 mb-2">
              <div class="h-12 w-12 rounded-full flex items-center justify-center"
                   :class="bannerToToggle?.is_active ? 'bg-orange-100' : 'bg-green-100'">
                <component 
                  :is="bannerToToggle?.is_active ? EyeOff : Eye" 
                  class="h-6 w-6"
                  :class="bannerToToggle?.is_active ? 'text-orange-600' : 'text-green-600'"
                />
              </div>
              <div>
                <DialogTitle class="text-xl">
                  {{ bannerToToggle?.is_active ? 'Tắt Banner' : 'Bật Banner' }}
                </DialogTitle>
                <DialogDescription>
                  Xác nhận thay đổi trạng thái
                </DialogDescription>
              </div>
            </div>
          </DialogHeader>

          <div class="space-y-4">
            <!-- Banner Info -->
            <div class="bg-muted/50 rounded-lg p-4 space-y-2">
              <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-muted-foreground">Banner:</span>
                <span class="text-sm font-semibold">{{ bannerToToggle?.title }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-muted-foreground">Loại:</span>
                <Badge :class="getBannerTypeBadgeClass(bannerToToggle?.type || '')">
                  {{ getBannerTypeLabel(bannerToToggle?.type || '') }}
                </Badge>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-muted-foreground">Trạng thái hiện tại:</span>
                <Badge :class="bannerToToggle?.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ bannerToToggle?.is_active ? 'Đang hoạt động' : 'Đã tắt' }}
                </Badge>
              </div>
            </div>

            <!-- Warning Message -->
            <div class="flex gap-3 p-3 rounded-lg"
                 :class="bannerToToggle?.is_active ? 'bg-orange-50 border border-orange-200' : 'bg-green-50 border border-green-200'">
              <Info class="h-5 w-5 flex-shrink-0 mt-0.5"
                    :class="bannerToToggle?.is_active ? 'text-orange-600' : 'text-green-600'" />
              <div class="text-sm"
                   :class="bannerToToggle?.is_active ? 'text-orange-900' : 'text-green-900'">
                <p class="font-medium mb-1">
                  {{ bannerToToggle?.is_active ? 'Tắt banner này?' : 'Bật banner này?' }}
                </p>
                <p>
                  {{ bannerToToggle?.is_active 
                    ? 'Banner sẽ không hiển thị trên trang chủ nữa.' 
                    : 'Banner sẽ được hiển thị trở lại trên trang chủ.' 
                  }}
                </p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-2 pt-2">
              <Button 
                type="button" 
                variant="outline" 
                @click="isToggleDialogOpen = false"
              >
                Hủy
              </Button>
              <Button 
                @click="confirmToggle"
                :class="bannerToToggle?.is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700'"
              >
                <component :is="bannerToToggle?.is_active ? EyeOff : Eye" class="h-4 w-4 mr-2" />
                {{ bannerToToggle?.is_active ? 'Xác nhận tắt' : 'Xác nhận bật' }}
              </Button>
            </div>
          </div>
        </DialogContent>
      </Dialog>

      <!-- Delete Confirmation Modal -->
      <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent class="sm:max-w-md">
          <DialogHeader>
            <div class="flex items-center gap-3 mb-2">
              <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                <Trash2 class="h-6 w-6 text-red-600" />
              </div>
              <div>
                <DialogTitle class="text-xl">
                  Xóa Banner
                </DialogTitle>
                <DialogDescription>
                  Hành động này không thể hoàn tác
                </DialogDescription>
              </div>
            </div>
          </DialogHeader>

          <div class="space-y-4">
            <!-- Banner Info -->
            <div class="bg-muted/50 rounded-lg p-4 space-y-2">
              <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-muted-foreground">Banner:</span>
                <span class="text-sm font-semibold">{{ bannerToDelete?.title }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-muted-foreground">Loại:</span>
                <Badge :class="getBannerTypeBadgeClass(bannerToDelete?.type || '')">
                  {{ getBannerTypeLabel(bannerToDelete?.type || '') }}
                </Badge>
              </div>
            </div>

            <!-- Warning Message -->
            <div class="flex gap-3 p-3 rounded-lg bg-red-50 border border-red-200">
              <Info class="h-5 w-5 flex-shrink-0 mt-0.5 text-red-600" />
              <div class="text-sm text-red-900">
                <p class="font-medium mb-1">
                  Bạn có chắc muốn xóa banner này?
                </p>
                <p>
                  Banner sẽ bị xóa vĩnh viễn khỏi hệ thống. Hành động này không thể hoàn tác!
                </p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-2 pt-2">
              <Button 
                type="button" 
                variant="outline" 
                @click="isDeleteDialogOpen = false"
              >
                Hủy
              </Button>
              <Button 
                variant="destructive"
                @click="confirmDelete"
              >
                <Trash2 class="h-4 w-4 mr-2" />
                Xác nhận xóa
              </Button>
            </div>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  </AppLayout>
</template>
