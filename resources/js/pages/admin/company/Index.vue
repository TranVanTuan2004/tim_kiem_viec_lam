<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import type { CompanyType, UserType } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Eye, Edit, Trash2, Plus, User } from 'lucide-vue-next';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const page = usePage<any>();
const search = ref(page.props.filters.search || '');
const hasFilter = computed(() => !!search.value);

const isViewDialogOpen = ref(false);
const companyToView = ref<CompanyType & { user?: UserType } | null>(null);
const isDeleteDialogOpen = ref(false);
const companyToDelete = ref<CompanyType | null>(null);

function applyFilters() {
  router.get(
    '/admin/companies',
    { search: search.value || undefined },
    { preserveState: true, replace: true } // cập nhật dữ liệu mới
  );
}

function clearFilters() {
  search.value = '';
  applyFilters();
}

function openViewDialog(company: CompanyType & { user?: UserType }) {
  companyToView.value = company;
  isViewDialogOpen.value = true;
}

function openDeleteDialog(company: CompanyType) {
  companyToDelete.value = company;
  isDeleteDialogOpen.value = true;
}

function confirmDelete() {
  if (!companyToDelete.value) return;
  router.delete(`/admin/companies/${companyToDelete.value.company_slug}`, {
    preserveScroll: true,
    onSuccess: (page) => {
      toast.success(page.props.flash?.success || 'Xóa công ty thành công');
      isDeleteDialogOpen.value = false;
    },
    onError: () => toast.error('Xóa công ty thất bại!'),
  });
}

if (page.props.flash?.success) toast.success(page.props.flash.success);

const breadcrumbs = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Quản lý công ty', href: '/admin/companies' },
];
</script>

<template>
  <Head title="Quản lý công ty" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 m-6">

      <!-- Header -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-3">
          <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center">
            <User class="h-6 w-6 text-primary" />
          </div>
          <div class="flex flex-col">
            <h1 class="text-2xl font-bold">Quản lý công ty</h1>
            <p class="text-muted-foreground text-sm">Danh sách công ty và thông tin quản lý</p>
          </div>
        </div>
        <Link href="/admin/companies/create">
          <Button class="flex items-center gap-2 bg-primary text-white hover:bg-primary/90">
            <Plus class="h-4 w-4" /> Tạo công ty mới
          </Button>
        </Link>
      </div>

      <!-- Filter Card -->
      <div class="flex flex-wrap gap-3 items-center mb-4">
        <Input v-model="search" placeholder="Tìm kiếm công ty..." @keyup.enter="applyFilters" class="flex-1 min-w-[200px]" />
        <Button @click="applyFilters">Tìm</Button>
        <Button v-if="hasFilter" variant="outline" @click="clearFilters">Xóa tìm kiếm</Button>
      </div>

      <!-- Companies Table -->
      <div class="bg-white shadow rounded overflow-hidden">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-700">
            <tr>
              <th class="px-4 py-2">Tên công ty</th>
              <th class="px-4 py-2">User quản lý</th>
              <th class="px-4 py-2">Thành phố / Tỉnh</th>
              <th class="px-4 py-2 text-right">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in page.props.companies.data" :key="c.id" class="border-t hover:bg-gray-50">
              <td class="px-4 py-2">{{ c.company_name }}</td>
              <td class="px-4 py-2">{{ c.user?.name ?? '—' }}</td>
              <td class="px-4 py-2">{{ c.city }}, {{ c.province }}</td>
              <td class="px-4 py-2 text-right flex justify-end gap-2">
                <Button size="sm" class="flex items-center gap-1" @click="openViewDialog(c)">
                  <Eye class="h-3 w-3" /> Xem
                </Button>
                <Link :href="`/admin/companies/${c.company_slug}/edit`">
                  <Button size="sm" variant="outline" class="flex items-center gap-1">
                    <Edit class="h-3 w-3" /> Sửa
                  </Button>
                </Link>
                <Button size="sm" variant="destructive" class="flex items-center gap-1" @click="openDeleteDialog(c)">
                  <Trash2 class="h-3 w-3" /> Xóa
                </Button>
              </td>
            </tr>
            <tr v-if="page.props.companies.data.length === 0">
              <td colspan="4" class="p-3 text-center text-gray-500">Không có dữ liệu</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="page.props.companies.last_page > 1" class="flex justify-center gap-2 mt-4">
        <Button :disabled="page.props.companies.current_page === 1" variant="outline" size="sm" @click="router.get(`/admin/companies?page=${page.props.companies.current_page - 1}`)">Trước</Button>
        <span class="text-sm text-muted-foreground">Trang {{ page.props.companies.current_page }} / {{ page.props.companies.last_page }}</span>
        <Button :disabled="page.props.companies.current_page === page.props.companies.last_page" variant="outline" size="sm" @click="router.get(`/admin/companies?page=${page.props.companies.current_page + 1}`)">Sau</Button>
      </div>

      <!-- View Dialog -->
      <Dialog v-model:open="isViewDialogOpen">
        <DialogContent class="sm:max-w-lg max-h-[80vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>Chi tiết công ty: {{ companyToView?.company_name }}</DialogTitle>
            <DialogDescription>Xem thông tin đầy đủ của công ty</DialogDescription>
          </DialogHeader>
          <div class="bg-white p-4 rounded shadow space-y-4 mt-2">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
              <div><strong>ID:</strong> {{ companyToView?.id }}</div>
              <div><strong>User quản lý:</strong> {{ companyToView?.user?.name ?? '—' }}</div>
              <div><strong>Tỉnh:</strong> {{ companyToView?.province }}</div>
              <div><strong>Thành phố:</strong> {{ companyToView?.city }}</div>
              <div><strong>Ngày tạo:</strong> {{ companyToView?.created_at }}</div>
              <div><strong>Cập nhật:</strong> {{ companyToView?.updated_at }}</div>
            </div>
            <div class="flex flex-col space-y-2">
              <div>
                <strong>Mô tả:</strong>
                <p class="mt-1 whitespace-pre-wrap text-sm">{{ companyToView?.description || '—' }}</p>
              </div>
              <div>
                <strong>Ngành nghề:</strong>
                <p class="mt-1 whitespace-pre-wrap text-sm">{{ companyToView?.industry || '—' }}</p>
              </div>
            </div>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <Button variant="outline" @click="isViewDialogOpen = false">Đóng</Button>
          </div>
        </DialogContent>
      </Dialog>

      <!-- Delete Dialog -->
      <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Xác nhận xóa</DialogTitle>
          </DialogHeader>
          <div>Bạn có chắc chắn muốn xóa công ty "{{ companyToDelete?.company_name }}" không?</div>
          <div class="mt-4 flex justify-end gap-2">
            <Button variant="outline" @click="isDeleteDialogOpen = false">Hủy</Button>
            <Button variant="destructive" @click="confirmDelete">Xóa</Button>
          </div>
        </DialogContent>
      </Dialog>

    </div>
  </AppLayout>
</template>
