<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Info } from 'lucide-vue-next';

const props = defineProps({
  packages: Array,
  currentSubscription: Object,
  paymentHistory: Array
});

const flash = usePage().props.flash ?? {};


// Dialog gói
const showPackageDialog = ref(false);
const selectedPackage = ref(null);
const openPackageDialog = (pkg) => {
  selectedPackage.value = pkg;
  showPackageDialog.value = true;
};

// Dialog thanh toán
const showPaymentDialog = ref(false);
const selectedPayment = ref(null);
const openPaymentDialog = (payment) => {
  selectedPayment.value = payment;
  showPaymentDialog.value = true;
};

// CRUD
const deletePackage = (id) => {
  if (confirm('Bạn có chắc muốn xóa gói này không?')) {
    router.delete(route('admin.service-packages.destroy', id));
  }
};

const toggleActive = (id) => {
  router.post(route('admin.service-packages.toggle', id));
};

// Search / sort
const search = ref('');
const sort = ref('');

const applyFilter = () => {
  router.get(route('admin.service-packages.index'), {
    search: search.value,
    sort: sort.value
  }, { preserveState: true, replace: true });
};
</script>

<template>
  <Head title="Quản lý gói dịch vụ" />

  <AppLayout>
    <div class="space-y-6">

      <!-- Flash -->
      <div v-if="flash.success" class="bg-green-100 text-green-800 p-2 rounded">{{ flash.success }}</div>
      <div v-if="flash.error" class="bg-red-100 text-red-800 p-2 rounded">{{ flash.error }}</div>


      <!-- Search -->
      <div class="flex space-x-2 items-center">
        <input v-model="search" placeholder="Tìm gói..." class="border p-2 rounded" />

        <select v-model="sort" class="border p-2 rounded">
          <option value="">-- Sắp xếp --</option>
          <option value="price_asc">Giá tăng dần</option>
          <option value="price_desc">Giá giảm dần</option>
          <option value="duration_asc">Thời gian tăng</option>
          <option value="duration_desc">Thời gian giảm</option>
        </select>

        <Button @click="applyFilter">Lọc</Button>
        <Button @click="router.get(route('admin.service-packages.create'))">Thêm gói mới</Button>
      </div>

      <!-- Gói hiện tại -->
      <Card>
        <CardHeader>
          <CardTitle>Gói dịch vụ hiện tại</CardTitle>
        </CardHeader>

        <CardContent>
          <div v-if="currentSubscription">
            <p><strong>Gói:</strong> {{ currentSubscription.name }}</p>
            <p><strong>Giá:</strong> {{ currentSubscription.price }} VNĐ</p>
            <p><strong>Thời gian:</strong> {{ currentSubscription.duration_days }} ngày</p>
          </div>
          <div v-else>Chưa có gói dịch vụ nào.</div>
        </CardContent>
      </Card>

      <!-- Danh sách gói -->
      <Card>
        <CardHeader>
          <CardTitle>Danh sách gói dịch vụ</CardTitle>
        </CardHeader>

        <CardContent>
          <div class="grid md:grid-cols-2 gap-4">
            <div v-for="pkg in packages" :key="pkg.id" class="border rounded p-4 hover:shadow">
              <div class="flex justify-between">
                <h3 class="font-semibold text-lg">{{ pkg.name }}</h3>
                <Badge v-if="pkg.is_active" variant="default">Active</Badge>
              </div>

              <p class="mt-2 text-sm">{{ pkg.description }}</p>
              <p class="mt-2"><strong>Giá:</strong> {{ pkg.price }} VNĐ</p>
              <p><strong>Thời gian:</strong> {{ pkg.duration_days }} ngày</p>

              <div class="flex space-x-2 mt-3">
                <Button size="sm" variant="outline" @click="router.get(route('admin.service-packages.edit', pkg.id))">Sửa</Button>
                <Button size="sm" variant="destructive" @click="deletePackage(pkg.id)">Xóa</Button>
                <Button size="sm" variant="secondary" @click="toggleActive(pkg.id)">
                  {{ pkg.is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}
                </Button>
                <Button size="sm" variant="outline" @click="openPackageDialog(pkg)">
                  <Info class="inline mr-1" /> Xem
                </Button>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Lịch sử thanh toán -->
      <Card>
        <CardHeader>
          <CardTitle>Lịch sử thanh toán</CardTitle>
        </CardHeader>

        <CardContent>
          <div v-if="paymentHistory.length">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr>
                  <th class="border-b p-2">Gói</th>
                  <th class="border-b p-2">Số tiền</th>
                  <th class="border-b p-2">Phương thức</th>
                  <th class="border-b p-2">Trạng thái</th>
                  <th class="border-b p-2">Ngày</th>
                  <th class="border-b p-2"></th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="payment in paymentHistory" :key="payment.id">
                  <td class="p-2">{{ payment.package?.name ?? 'Đã xóa' }}</td>
                  <td class="p-2">{{ payment.amount }} VNĐ</td>
                  <td class="p-2">{{ payment.payment_method }}</td>

                  <td class="p-2">
                    <Badge :variant="payment.status === 'completed' ? 'default' : 'destructive'">
                      {{ payment.status }}
                    </Badge>
                  </td>

                  <td class="p-2">{{ new Date(payment.created_at).toLocaleDateString() }}</td>

                  <td class="p-2">
                    <Button size="sm" variant="outline" @click="openPaymentDialog(payment)">
                      <Info class="inline mr-1" /> Xem
                    </Button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else>Chưa có giao dịch nào.</div>
        </CardContent>
      </Card>

    </div>

    <!-- Dialog gói -->
    <Dialog v-model:open="showPackageDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Chi tiết gói dịch vụ</DialogTitle>
        </DialogHeader>

        <div v-if="selectedPackage" class="space-y-2 mt-2">
          <p><strong>Tên:</strong> {{ selectedPackage.name }}</p>
          <p><strong>Mô tả:</strong> {{ selectedPackage.description }}</p>
          <p><strong>Giá:</strong> {{ selectedPackage.price }} VNĐ</p>
          <p><strong>Thời gian:</strong> {{ selectedPackage.duration_days }} ngày</p>
          <p><strong>Giới hạn bài đăng:</strong> {{ selectedPackage.max_jobs }}</p>

          <p v-if="selectedPackage.features">
            <strong>Tính năng:</strong>
            {{ Array.isArray(selectedPackage.features) ? selectedPackage.features.join(', ') : selectedPackage.features }}
          </p>

          <div class="text-right mt-4">
            <Button @click="showPackageDialog = false">Đóng</Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>

    <!-- Dialog thanh toán -->
    <Dialog v-model:open="showPaymentDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Chi tiết thanh toán</DialogTitle>
        </DialogHeader>

        <div v-if="selectedPayment" class="space-y-2 mt-2">
          <p><strong>Gói:</strong> {{ selectedPayment.package?.name ?? 'Đã xóa' }}</p>
          <p><strong>Số tiền:</strong> {{ selectedPayment.amount }} VNĐ</p>
          <p><strong>Phương thức:</strong> {{ selectedPayment.payment_method }}</p>

          <p>
            <strong>Trạng thái:</strong>
            <Badge :variant="selectedPayment.status === 'completed' ? 'default' : 'destructive'">
              {{ selectedPayment.status }}
            </Badge>
          </p>

          <p><strong>Ngày tạo:</strong> {{ new Date(selectedPayment.created_at).toLocaleDateString() }}</p>

          <div v-if="selectedPayment.details">
            <strong>Chi tiết giao dịch:</strong>
            <pre>{{ selectedPayment.details }}</pre>
          </div>

          <div class="text-right mt-4">
            <Button @click="showPaymentDialog = false">Đóng</Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>

  </AppLayout>
</template>
