<script setup lang="ts">
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Link } from '@inertiajs/vue3';
import { Users, Heart, Shield, Lightbulb, Globe, Sparkles, Star, HandHeart, Rocket, Trophy, Building2, Briefcase } from 'lucide-vue-next';

const coreValues = [
  { icon: Users, title: 'Tôn trọng & Hợp tác', desc: 'Xây dựng môi trường tin tưởng, hỗ trợ và làm việc nhóm hiệu quả.' },
  { icon: Lightbulb, title: 'Sáng tạo & Đổi mới', desc: 'Khuyến khích thử nghiệm, học hỏi và cải tiến liên tục.' },
  { icon: Shield, title: 'Chính trực', desc: 'Minh bạch, trách nhiệm và giữ lời hứa.' },
  { icon: Heart, title: 'Lấy con người làm trung tâm', desc: 'Nhân viên hạnh phúc, khách hàng hài lòng, sản phẩm tuyệt vời.' },
  { icon: Globe, title: 'Tác động tích cực', desc: 'Tạo giá trị bền vững cho cộng đồng công nghệ Việt Nam.' },
];

const initiatives = [
  { icon: Sparkles, title: 'Tech Sharing', desc: 'Brown bag, Lightning talk, Guild meetup định kỳ.' },
  { icon: Star, title: 'Recognition', desc: 'Kudos, Rising Star, Impact Maker theo quý.' },
  { icon: HandHeart, title: 'Well-being', desc: 'Sức khỏe, hỗ trợ tâm lý, day-off linh hoạt.' },
  { icon: Rocket, title: 'Growth Path', desc: 'Lộ trình phát triển cá nhân rõ ràng, mentor theo quý.' },
  { icon: Trophy, title: 'Hack Days', desc: 'Hackathon nội bộ, cuộc thi ý tưởng cải tiến.' },
];

const highlights = [
  { icon: Building2, title: 'Không gian hiện đại', desc: 'Khu vực mở, focus room và collaboration hub.' },
  { icon: Briefcase, title: 'Hybrid linh hoạt', desc: 'Tối ưu hiệu quả với mô hình làm việc kết hợp.' },
];
</script>

<template>
  <ClientLayout title="Văn Hóa Doanh Nghiệp">
    <section class="relative overflow-hidden bg-gradient-to-b from-red-50 via-white to-orange-50 dark:from-red-900/20 dark:via-zinc-900 dark:to-orange-900/10">
      <div class="max-w-6xl mx-auto px-6 pt-16 pb-12">
        <div class="grid md:grid-cols-2 gap-10 items-center">
          <div>
            <Badge class="mb-4 bg-red-600 text-white">Company Culture</Badge>
            <h1 class="text-3xl md:text-4xl font-bold tracking-tight">Xây dựng môi trường hạnh phúc để tạo nên sản phẩm xuất sắc</h1>
            <p class="mt-4 text-gray-600 dark:text-gray-300">Chúng tôi tin rằng văn hoá mạnh là nền tảng cho sự đổi mới và tăng trưởng bền vững. Ở đây, mỗi cá nhân đều được tôn trọng, khuyến khích và trao quyền để tỏa sáng.</p>
            <div class="mt-8 flex flex-wrap gap-3">
              <Button as-child class="bg-gradient-to-r from-red-600 to-orange-500">
                <Link href="/companies?has_jobs=1">Khám phá công ty</Link>
              </Button>
              <Button variant="outline" as-child>
                <Link href="/jobs">Xem việc làm</Link>
              </Button>
            </div>
          </div>
          <div class="relative">
            <div class="aspect-[4/3] rounded-2xl bg-gradient-to-br from-red-100 to-orange-100 dark:from-red-800 dark:to-orange-700 shadow-xl animate-gradient"></div>
          </div>
        </div>
      </div>
      <div class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full bg-red-200 opacity-30 blur-3xl dark:bg-red-900"></div>
      <div class="pointer-events-none absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-orange-200 opacity-30 blur-3xl dark:bg-orange-900"></div>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-16">
      <h2 class="text-2xl font-bold mb-6">Giá trị cốt lõi</h2>
      <div class="grid md:grid-cols-3 gap-6">
        <Card v-for="(v, i) in coreValues" :key="i" class="hover:shadow-lg transition-shadow">
          <CardHeader class="flex flex-row items-center gap-3">
            <component :is="v.icon" class="h-6 w-6 text-red-600" />
            <CardTitle class="text-lg">{{ v.title }}</CardTitle>
          </CardHeader>
          <CardContent class="text-gray-600 dark:text-gray-300">{{ v.desc }}</CardContent>
        </Card>
      </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 pb-16">
      <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
          <h2 class="text-2xl font-bold mb-6">Sáng kiến văn hoá</h2>
          <div class="grid md:grid-cols-2 gap-6">
            <Card v-for="(i, idx) in initiatives" :key="idx" class="hover:shadow-lg transition-shadow">
              <CardHeader class="flex flex-row items-center gap-3">
                <component :is="i.icon" class="h-6 w-6 text-orange-500" />
                <CardTitle class="text-lg">{{ i.title }}</CardTitle>
              </CardHeader>
              <CardContent class="text-gray-600 dark:text-gray-300">{{ i.desc }}</CardContent>
            </Card>
          </div>
        </div>
        <div>
          <h2 class="text-2xl font-bold mb-6">Điểm nhấn</h2>
          <div class="space-y-4">
            <Card v-for="(h, idh) in highlights" :key="idh" class="hover:shadow-md transition-shadow">
              <CardHeader class="flex flex-row items-center gap-3">
                <component :is="h.icon" class="h-6 w-6 text-red-600" />
                <CardTitle class="text-lg">{{ h.title }}</CardTitle>
              </CardHeader>
              <CardContent class="text-gray-600 dark:text-gray-300">{{ h.desc }}</CardContent>
            </Card>
          </div>
        </div>
      </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 pb-16">
      <div class="rounded-2xl p-8 bg-gradient-to-r from-red-600 to-orange-500 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
          <div>
            <h3 class="text-xl font-bold">Cùng kiến tạo văn hoá tuyệt vời</h3>
            <p class="mt-2 text-white/90">Tham gia vào cộng đồng nơi mọi người được lắng nghe, ghi nhận và phát triển.</p>
          </div>
          <div class="flex gap-3">
            <Button as-child variant="secondary">
              <Link href="/companies?has_jobs=1">Tìm công ty phù hợp</Link>
            </Button>
            <Button as-child variant="outline" class="text-white border-white/70 hover:bg-white hover:text-red-600">
              <Link href="/jobs">Bắt đầu ứng tuyển</Link>
            </Button>
          </div>
        </div>
      </div>
    </section>
  </ClientLayout>
</template>

<style scoped>
@keyframes gradient {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}
.animate-gradient { background-size: 200% 200%; animation: gradient 6s ease infinite; }
</style>
