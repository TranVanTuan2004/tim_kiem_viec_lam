<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { 
  Layout, 
  Edit, 
  Eye, 
  EyeOff,
  Save,
  Info
} from 'lucide-vue-next';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';

interface HomepageSection {
  id: number;
  section_key: string;
  title: string;
  subtitle: string | null;
  content: Record<string, any>;
  order: number;
  is_active: boolean;
}

interface Props {
  sections: HomepageSection[];
}

const props = defineProps<Props>();

const editingSection = ref<HomepageSection | null>(null);
const isDialogOpen = ref(false);
const isToggleDialogOpen = ref(false);
const sectionToToggle = ref<HomepageSection | null>(null);

const form = useForm({
  title: '',
  subtitle: '',
  content: '',
  is_active: true,
});

function openEditDialog(section: HomepageSection) {
  editingSection.value = section;
  form.title = section.title;
  form.subtitle = section.subtitle || '';
  form.content = JSON.stringify(section.content, null, 2);
  form.is_active = section.is_active;
  isDialogOpen.value = true;
}

function submitUpdate() {
  if (!editingSection.value) return;

  router.put(`/admin/homepage/${editingSection.value.id}`, {
    title: form.title,
    subtitle: form.subtitle,
    content: form.content,
    is_active: form.is_active,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      isDialogOpen.value = false;
      editingSection.value = null;
    },
  });
}

function openToggleDialog(section: HomepageSection) {
  sectionToToggle.value = section;
  isToggleDialogOpen.value = true;
}

function confirmToggle() {
  if (!sectionToToggle.value) return;
  
  router.post(`/admin/homepage/${sectionToToggle.value.id}/toggle-active`, {}, { 
    preserveScroll: true,
    onSuccess: () => {
      isToggleDialogOpen.value = false;
      sectionToToggle.value = null;
    }
  });
}

function getSectionTypeLabel(key: string) {
  const labels: Record<string, string> = {
    hero: 'Hero Section',
    featured_jobs: 'Việc làm nổi bật',
    top_companies: 'Công ty hàng đầu',
    statistics: 'Thống kê',
    categories: 'Danh mục',
    testimonials: 'Đánh giá',
  };
  return labels[key] || key;
}

function getSectionTypeBadgeClass(key: string) {
  const classes: Record<string, string> = {
    hero: 'bg-purple-100 text-purple-800',
    featured_jobs: 'bg-blue-100 text-blue-800',
    top_companies: 'bg-green-100 text-green-800',
    statistics: 'bg-orange-100 text-orange-800',
    categories: 'bg-pink-100 text-pink-800',
    testimonials: 'bg-yellow-100 text-yellow-800',
  };
  return classes[key] || 'bg-gray-100 text-gray-800';
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Nội dung Trang chủ', href: '/admin/homepage' }
];
</script>

<template>
  <Head title="Quản lý Nội dung Trang chủ" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-[20px]">
      
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
        <div>
          <h1 class="text-3xl font-bold tracking-tight flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <Layout class="h-6 w-6 text-primary" />
            </div>
            Quản lý Nội dung Trang chủ
          </h1>
          <p class="text-muted-foreground mt-2 ml-[52px]">
            Cấu hình các section hiển thị trên trang chủ
          </p>
        </div>
      </div>

      <!-- Info Card -->
      <Card class="bg-blue-50 border-blue-200">
        <CardContent class="pt-6">
          <div class="flex gap-3">
            <Info class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
            <div class="text-sm text-blue-900">
              <p class="font-medium mb-1">Hướng dẫn sử dụng</p>
              <p>Các section này điều khiển nội dung hiển thị trên trang chủ. Click "Chỉnh sửa" để cập nhật tiêu đề, mô tả và cấu hình JSON cho mỗi section.</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Sections List -->
      <div class="space-y-4">
        <Card
          v-for="section in sections"
          :key="section.id"
          class="hover:shadow-lg transition-shadow"
        >
          <CardHeader>
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <CardTitle>{{ section.title }}</CardTitle>
                  <Badge :class="getSectionTypeBadgeClass(section.section_key)">
                    {{ getSectionTypeLabel(section.section_key) }}
                  </Badge>
                  <Badge :class="section.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    {{ section.is_active ? 'Hoạt động' : 'Đã tắt' }}
                  </Badge>
                </div>
                <CardDescription v-if="section.subtitle">
                  {{ section.subtitle }}
                </CardDescription>
              </div>
              <div class="flex items-center gap-2">
                <Dialog v-model:open="isDialogOpen">
                  <DialogTrigger as-child>
                    <Button 
                      size="sm" 
                      variant="outline" 
                      @click="openEditDialog(section)"
                      class="flex items-center gap-1"
                    >
                      <Edit class="h-4 w-4" />
                      Chỉnh sửa
                    </Button>
                  </DialogTrigger>
                  <DialogContent class="max-w-2xl max-h-[80vh] overflow-y-auto">
                    <DialogHeader>
                      <DialogTitle>Chỉnh sửa Section</DialogTitle>
                      <DialogDescription>
                        Cập nhật thông tin cho {{ editingSection?.title }}
                      </DialogDescription>
                    </DialogHeader>
                    
                    <form @submit.prevent="submitUpdate" class="space-y-4">
                      <div class="space-y-2">
                        <Label for="title">Tiêu đề</Label>
                        <Input
                          id="title"
                          v-model="form.title"
                          placeholder="Nhập tiêu đề"
                        />
                      </div>

                      <div class="space-y-2">
                        <Label for="subtitle">Mô tả</Label>
                        <Input
                          id="subtitle"
                          v-model="form.subtitle"
                          placeholder="Nhập mô tả"
                        />
                      </div>

                      <div class="space-y-2">
                        <Label for="content">Cấu hình JSON</Label>
                        <Textarea
                          id="content"
                          v-model="form.content"
                          :rows="10"
                          placeholder='{"key": "value"}'
                          class="font-mono text-sm"
                        />
                        <p class="text-xs text-muted-foreground">
                          Nhập JSON hợp lệ. Ví dụ: {"limit": 6, "show_featured": true}
                        </p>
                      </div>

                      <div class="flex items-center gap-2">
                        <input
                          id="is_active"
                          type="checkbox"
                          v-model="form.is_active"
                          class="h-4 w-4 rounded border-gray-300"
                        />
                        <Label for="is_active">Trạng thái hoạt động</Label>
                      </div>

                      <div class="flex justify-end gap-2 pt-4">
                        <Button 
                          type="button" 
                          variant="outline" 
                          @click="isDialogOpen = false"
                        >
                          Hủy
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                          <Save class="h-4 w-4 mr-2" />
                          {{ form.processing ? 'Đang lưu...' : 'Lưu thay đổi' }}
                        </Button>
                      </div>
                    </form>
                  </DialogContent>
                </Dialog>

                <Button 
                  size="sm" 
                  variant="outline" 
                  @click="openToggleDialog(section)"
                  class="flex items-center gap-1"
                >
                  <component :is="section.is_active ? EyeOff : Eye" class="h-4 w-4" />
                  {{ section.is_active ? 'Tắt' : 'Bật' }}
                </Button>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div class="bg-muted/50 rounded-lg p-4">
              <p class="text-sm font-medium mb-2">Cấu hình hiện tại:</p>
              <pre class="text-xs overflow-x-auto">{{ JSON.stringify(section.content, null, 2) }}</pre>
            </div>
          </CardContent>
        </Card>

        <!-- Empty State -->
        <div v-if="sections.length === 0" class="text-center py-12">
          <Layout class="h-16 w-16 mx-auto mb-4 text-muted-foreground/30" />
          <p class="text-muted-foreground">Chưa có section nào</p>
        </div>
      </div>

      <!-- Toggle Confirmation Modal -->
      <Dialog v-model:open="isToggleDialogOpen">
        <DialogContent class="sm:max-w-md">
          <DialogHeader>
            <div class="flex items-center gap-3 mb-2">
              <div class="h-12 w-12 rounded-full flex items-center justify-center"
                   :class="sectionToToggle?.is_active ? 'bg-orange-100' : 'bg-green-100'">
                <component 
                  :is="sectionToToggle?.is_active ? EyeOff : Eye" 
                  class="h-6 w-6"
                  :class="sectionToToggle?.is_active ? 'text-orange-600' : 'text-green-600'"
                />
              </div>
              <div>
                <DialogTitle class="text-xl">
                  {{ sectionToToggle?.is_active ? 'Tắt Section' : 'Bật Section' }}
                </DialogTitle>
                <DialogDescription>
                  Xác nhận thay đổi trạng thái
                </DialogDescription>
              </div>
            </div>
          </DialogHeader>

          <div class="space-y-4">
            <!-- Section Info -->
            <div class="bg-muted/50 rounded-lg p-4 space-y-2">
              <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-muted-foreground">Section:</span>
                <span class="text-sm font-semibold">{{ sectionToToggle?.title }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-muted-foreground">Loại:</span>
                <Badge :class="getSectionTypeBadgeClass(sectionToToggle?.section_key || '')">
                  {{ getSectionTypeLabel(sectionToToggle?.section_key || '') }}
                </Badge>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-muted-foreground">Trạng thái hiện tại:</span>
                <Badge :class="sectionToToggle?.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ sectionToToggle?.is_active ? 'Đang hoạt động' : 'Đã tắt' }}
                </Badge>
              </div>
            </div>

            <!-- Warning Message -->
            <div class="flex gap-3 p-3 rounded-lg"
                 :class="sectionToToggle?.is_active ? 'bg-orange-50 border border-orange-200' : 'bg-green-50 border border-green-200'">
              <Info class="h-5 w-5 flex-shrink-0 mt-0.5"
                    :class="sectionToToggle?.is_active ? 'text-orange-600' : 'text-green-600'" />
              <div class="text-sm"
                   :class="sectionToToggle?.is_active ? 'text-orange-900' : 'text-green-900'">
                <p class="font-medium mb-1">
                  {{ sectionToToggle?.is_active ? 'Tắt section này?' : 'Bật section này?' }}
                </p>
                <p>
                  {{ sectionToToggle?.is_active 
                    ? 'Section sẽ không hiển thị trên trang chủ nữa.' 
                    : 'Section sẽ được hiển thị trở lại trên trang chủ.' 
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
                :class="sectionToToggle?.is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700'"
              >
                <component :is="sectionToToggle?.is_active ? EyeOff : Eye" class="h-4 w-4 mr-2" />
                {{ sectionToToggle?.is_active ? 'Xác nhận tắt' : 'Xác nhận bật' }}
              </Button>
            </div>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  </AppLayout>
</template>
