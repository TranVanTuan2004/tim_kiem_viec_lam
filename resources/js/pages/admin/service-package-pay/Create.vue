<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Save } from 'lucide-vue-next';

const props = defineProps({
  package: Object,
  paymentHistory: Array,
  packages: Array,
});

// Form nâng cấp/gia hạn
const upgradeForm = useForm({
  package_id: props.package?.id ?? '',
  payment_method: 'bank_transfer',
});

const showUpgradeDialog = ref(false);

const upgradeSubscription = () => {
  upgradeForm.post(route('admin.upgrade'), {
    onSuccess: () => (showUpgradeDialog.value = false),
  });
};

const renewSubscription = () => {
  router.post(route('admin.renew'), { payment_method: 'bank_transfer' });
};
</script>

<template>
  <Head title="Chi tiết gói dịch vụ" />

  <AppLayout>
    <div class="space-y-6">

      <!-- Thông tin gói hiện tại -->
      <Card>
        <CardHeader>
          <CardTitle>Gói hiện tại</CardTitle>
        </CardHeader>
        <CardContent>
          <p><strong>Gói:</strong> {{ package.name }}</p>
          <p><strong>Giá:</strong> {{ package.price }} VNĐ</p>
          <p><strong>Hạn:</strong> {{ new Date(package.expires_at).toLocaleDateString() }}</p>

          <p class="mt-2">
            <strong>Trạng thái:</strong>
            <Badge :variant="package.is_active ? 'default' : 'destructive'">
              {{ package.is_active ? 'Đang hoạt động' : 'Đã hết hạn' }}
            </Badge>
          </p>

          <div class="mt-4 flex gap-2">
            <Button @click="showUpgradeDialog = true">Nâng cấp</Button>
            <Button variant="outline" @click="renewSubscription">Gia hạn</Button>
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
                  <th class="border-b p-2">Trạng thái</th>
                  <th class="border-b p-2">Ngày</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="payment in paymentHistory" :key="payment.id">
                  <td class="p-2">{{ payment.package?.name ?? 'Đã xóa' }}</td>
                  <td class="p-2">{{ payment.amount }} VNĐ</td>
                  <td class="p-2">
                    <Badge :variant="payment.status === 'completed' ? 'default' : 'destructive'">
                      {{ payment.status }}
                    </Badge>
                  </td>
                  <td class="p-2">{{ new Date(payment.created_at).toLocaleDateString() }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else>
            Chưa có thanh toán nào.
          </div>
        </CardContent>
      </Card>

      <!-- Dialog nâng cấp -->
      <Dialog v-model:open="showUpgradeDialog">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Nâng cấp gói dịch vụ</DialogTitle>
            <DialogDescription>Chọn gói và phương thức thanh toán.</DialogDescription>
          </DialogHeader>

          <div class="space-y-4 mt-4">

            <label>Gói dịch vụ</label>
            <select v-model="upgradeForm.package_id" class="w-full border rounded p-2">
              <option disabled value="">-- Chọn gói --</option>
              <option v-for="pkg in packages" :key="pkg.id" :value="pkg.id">
                {{ pkg.name }} - {{ pkg.price }} VNĐ
              </option>
            </select>

            <label>Phương thức thanh toán</label>
            <select v-model="upgradeForm.payment_method" class="w-full border rounded p-2">
              <option value="bank_transfer">Chuyển khoản</option>
              <option value="credit_card">Thẻ tín dụng</option>
              <option value="zalopay">ZaloPay</option>
              <option value="vnpay">VNPay</option>
            </select>

            <Button class="w-full" @click="upgradeSubscription" :disabled="upgradeForm.processing">
              <Save class="inline mr-2" /> Nâng cấp
            </Button>
          </div>
        </DialogContent>
      </Dialog>

    </div>
  </AppLayout>
</template>
