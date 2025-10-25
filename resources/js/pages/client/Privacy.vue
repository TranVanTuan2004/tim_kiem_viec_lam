<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { useLanguage } from '@/composables/useLanguage';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Bell, Database, Eye, Lock, Shield, UserCheck } from 'lucide-vue-next';

const { t } = useLanguage();
</script>

<template>
    <ClientLayout :title="t.privacyPolicy">
        <!-- Hero Section -->
        <section
            class="relative overflow-hidden bg-gradient-to-br from-red-50 via-orange-50 to-background py-20 dark:from-red-950/20 dark:via-orange-950/20"
        >
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div
                    class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-gradient-to-br from-red-200 to-orange-200 opacity-30 blur-3xl dark:from-red-800 dark:to-orange-800"
                ></div>
                <div
                    class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-gradient-to-tr from-orange-200 to-red-200 opacity-30 blur-3xl dark:from-orange-800 dark:to-red-800"
                ></div>
            </div>

            <div class="relative z-10 container mx-auto px-4">
                <div class="mb-6 flex justify-center">
                    <div
                        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-red-50 to-orange-50 px-5 py-2.5 text-sm font-semibold text-red-600 shadow-lg ring-1 ring-red-100 dark:from-red-950 dark:to-orange-950 dark:ring-red-900"
                    >
                        <Shield class="h-4 w-4" />
                        <span>Bảo mật</span>
                    </div>
                </div>

                <div class="mb-8 text-center">
                    <h1
                        class="mb-4 text-5xl font-bold tracking-tight md:text-6xl"
                    >
                        Chính sách
                        <span
                            class="bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent"
                        >
                            bảo mật
                        </span>
                    </h1>
                    <p class="mx-auto max-w-3xl text-lg text-muted-foreground">
                        Chúng tôi cam kết bảo vệ quyền riêng tư và thông tin cá
                        nhân của bạn
                    </p>
                    <p class="mt-4 text-sm text-muted-foreground">
                        Cập nhật lần cuối:
                        {{ new Date().toLocaleDateString('vi-VN') }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Content -->
        <section class="bg-background py-20">
            <div class="container mx-auto px-4">
                <div class="mx-auto max-w-4xl space-y-8">
                    <!-- Introduction -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-orange-500"
                                >
                                    <Shield class="h-6 w-6 text-white" />
                                </div>
                                <h2 class="text-2xl font-bold">
                                    Cam kết bảo mật
                                </h2>
                            </div>
                            <div class="space-y-4 text-muted-foreground">
                                <p>
                                    Tại Job Portal, chúng tôi hiểu rằng quyền
                                    riêng tư của bạn là vô cùng quan trọng.
                                    Chính sách bảo mật này giải thích cách chúng
                                    tôi thu thập, sử dụng, và bảo vệ thông tin
                                    cá nhân của bạn.
                                </p>
                                <p>
                                    Bằng việc sử dụng dịch vụ của chúng tôi, bạn
                                    đồng ý với các điều khoản trong chính sách
                                    bảo mật này.
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Section 1: Data Collection -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-950"
                                >
                                    <Database class="h-6 w-6 text-blue-600" />
                                </div>
                                <h3 class="text-xl font-bold">
                                    Thông tin chúng tôi thu thập
                                </h3>
                            </div>
                            <div class="space-y-4 text-muted-foreground">
                                <div>
                                    <h4
                                        class="mb-2 font-semibold text-foreground"
                                    >
                                        Thông tin cá nhân:
                                    </h4>
                                    <ul class="ml-6 list-disc space-y-2">
                                        <li>Họ tên, email, số điện thoại</li>
                                        <li>
                                            Thông tin CV (kinh nghiệm, học vấn,
                                            kỹ năng)
                                        </li>
                                        <li>
                                            Thông tin công ty (đối với nhà tuyển
                                            dụng)
                                        </li>
                                        <li>Hình ảnh hồ sơ và logo công ty</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4
                                        class="mb-2 font-semibold text-foreground"
                                    >
                                        Thông tin tự động:
                                    </h4>
                                    <ul class="ml-6 list-disc space-y-2">
                                        <li>
                                            Địa chỉ IP và thông tin thiết bị
                                        </li>
                                        <li>
                                            Dữ liệu về cách bạn sử dụng dịch vụ
                                        </li>
                                        <li>
                                            Cookies và công nghệ theo dõi tương
                                            tự
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Section 2: How We Use Data -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-950"
                                >
                                    <Eye class="h-6 w-6 text-green-600" />
                                </div>
                                <h3 class="text-xl font-bold">
                                    Cách chúng tôi sử dụng thông tin
                                </h3>
                            </div>
                            <div class="space-y-3 text-muted-foreground">
                                <p>
                                    Chúng tôi sử dụng thông tin của bạn cho các
                                    mục đích sau:
                                </p>
                                <ul class="ml-6 list-disc space-y-2">
                                    <li>
                                        Cung cấp và cải thiện dịch vụ tuyển dụng
                                    </li>
                                    <li>
                                        Kết nối ứng viên với nhà tuyển dụng phù
                                        hợp
                                    </li>
                                    <li>
                                        Gửi thông báo về việc làm và cập nhật
                                        dịch vụ
                                    </li>
                                    <li>
                                        Phân tích và cải thiện trải nghiệm người
                                        dùng
                                    </li>
                                    <li>
                                        Bảo vệ an ninh và ngăn chặn gian lận
                                    </li>
                                    <li>Tuân thủ các yêu cầu pháp lý</li>
                                </ul>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Section 3: Data Sharing -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-950"
                                >
                                    <UserCheck
                                        class="h-6 w-6 text-purple-600"
                                    />
                                </div>
                                <h3 class="text-xl font-bold">
                                    Chia sẻ thông tin
                                </h3>
                            </div>
                            <div class="space-y-4 text-muted-foreground">
                                <p>
                                    Chúng tôi có thể chia sẻ thông tin của bạn
                                    với:
                                </p>
                                <div class="space-y-3">
                                    <div class="rounded-lg bg-muted/50 p-4">
                                        <h4
                                            class="mb-1 font-semibold text-foreground"
                                        >
                                            Nhà tuyển dụng
                                        </h4>
                                        <p class="text-sm">
                                            Khi bạn ứng tuyển vào một vị trí,
                                            thông tin CV của bạn sẽ được chia sẻ
                                            với nhà tuyển dụng đó.
                                        </p>
                                    </div>
                                    <div class="rounded-lg bg-muted/50 p-4">
                                        <h4
                                            class="mb-1 font-semibold text-foreground"
                                        >
                                            Đối tác dịch vụ
                                        </h4>
                                        <p class="text-sm">
                                            Các bên thứ ba cung cấp dịch vụ hỗ
                                            trợ như lưu trữ dữ liệu, phân tích,
                                            và thanh toán.
                                        </p>
                                    </div>
                                    <div class="rounded-lg bg-muted/50 p-4">
                                        <h4
                                            class="mb-1 font-semibold text-foreground"
                                        >
                                            Yêu cầu pháp lý
                                        </h4>
                                        <p class="text-sm">
                                            Khi được yêu cầu bởi pháp luật hoặc
                                            để bảo vệ quyền lợi của chúng tôi và
                                            người dùng khác.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Section 4: Data Security -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-950"
                                >
                                    <Lock class="h-6 w-6 text-red-600" />
                                </div>
                                <h3 class="text-xl font-bold">
                                    Bảo mật dữ liệu
                                </h3>
                            </div>
                            <div class="space-y-3 text-muted-foreground">
                                <p>
                                    Chúng tôi áp dụng các biện pháp bảo mật kỹ
                                    thuật và tổ chức để bảo vệ thông tin của
                                    bạn:
                                </p>
                                <ul class="ml-6 list-disc space-y-2">
                                    <li>
                                        Mã hóa dữ liệu khi truyền tải (SSL/TLS)
                                    </li>
                                    <li>
                                        Hệ thống tường lửa và phát hiện xâm nhập
                                    </li>
                                    <li>Kiểm soát truy cập nghiêm ngặt</li>
                                    <li>Sao lưu dữ liệu định kỳ</li>
                                    <li>
                                        Đào tạo nhân viên về bảo mật thông tin
                                    </li>
                                </ul>
                                <p class="text-sm italic">
                                    Lưu ý: Mặc dù chúng tôi nỗ lực hết sức để
                                    bảo vệ dữ liệu, không có phương thức truyền
                                    tải qua Internet nào là an toàn 100%.
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Section 5: Your Rights -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-950"
                                >
                                    <Bell class="h-6 w-6 text-orange-600" />
                                </div>
                                <h3 class="text-xl font-bold">Quyền của bạn</h3>
                            </div>
                            <div class="space-y-3 text-muted-foreground">
                                <p>
                                    Bạn có các quyền sau đối với dữ liệu cá nhân
                                    của mình:
                                </p>
                                <div class="grid gap-3 md:grid-cols-2">
                                    <div
                                        class="rounded-lg bg-gradient-to-br from-red-50 to-orange-50 p-4 dark:from-red-950/20 dark:to-orange-950/20"
                                    >
                                        <h4
                                            class="mb-1 font-semibold text-foreground"
                                        >
                                            Quyền truy cập
                                        </h4>
                                        <p class="text-sm">
                                            Yêu cầu xem thông tin chúng tôi có
                                            về bạn
                                        </p>
                                    </div>
                                    <div
                                        class="rounded-lg bg-gradient-to-br from-red-50 to-orange-50 p-4 dark:from-red-950/20 dark:to-orange-950/20"
                                    >
                                        <h4
                                            class="mb-1 font-semibold text-foreground"
                                        >
                                            Quyền chỉnh sửa
                                        </h4>
                                        <p class="text-sm">
                                            Cập nhật hoặc sửa đổi thông tin
                                            không chính xác
                                        </p>
                                    </div>
                                    <div
                                        class="rounded-lg bg-gradient-to-br from-red-50 to-orange-50 p-4 dark:from-red-950/20 dark:to-orange-950/20"
                                    >
                                        <h4
                                            class="mb-1 font-semibold text-foreground"
                                        >
                                            Quyền xóa
                                        </h4>
                                        <p class="text-sm">
                                            Yêu cầu xóa dữ liệu cá nhân của bạn
                                        </p>
                                    </div>
                                    <div
                                        class="rounded-lg bg-gradient-to-br from-red-50 to-orange-50 p-4 dark:from-red-950/20 dark:to-orange-950/20"
                                    >
                                        <h4
                                            class="mb-1 font-semibold text-foreground"
                                        >
                                            Quyền từ chối
                                        </h4>
                                        <p class="text-sm">
                                            Từ chối nhận email marketing hoặc
                                            quảng cáo
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Section 6: Cookies -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-950"
                                >
                                    <span class="text-2xl">🍪</span>
                                </div>
                                <h3 class="text-xl font-bold">Cookies</h3>
                            </div>
                            <div class="space-y-3 text-muted-foreground">
                                <p>
                                    Chúng tôi sử dụng cookies và công nghệ tương
                                    tự để cải thiện trải nghiệm của bạn. Bạn có
                                    thể quản lý cookies thông qua cài đặt trình
                                    duyệt của mình.
                                </p>
                                <div class="rounded-lg bg-muted/50 p-4">
                                    <h4
                                        class="mb-2 font-semibold text-foreground"
                                    >
                                        Các loại cookies chúng tôi sử dụng:
                                    </h4>
                                    <ul
                                        class="ml-6 list-disc space-y-1 text-sm"
                                    >
                                        <li>
                                            Cookies cần thiết: Đảm bảo chức năng
                                            cơ bản
                                        </li>
                                        <li>
                                            Cookies phân tích: Hiểu cách bạn sử
                                            dụng trang web
                                        </li>
                                        <li>
                                            Cookies chức năng: Ghi nhớ tùy chọn
                                            của bạn
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Section 7: Updates -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <h3 class="mb-4 text-xl font-bold">
                                Thay đổi chính sách
                            </h3>
                            <div class="space-y-3 text-muted-foreground">
                                <p>
                                    Chúng tôi có thể cập nhật Chính sách bảo mật
                                    này theo thời gian. Chúng tôi sẽ thông báo
                                    cho bạn về những thay đổi quan trọng bằng
                                    cách đăng chính sách mới trên trang này và
                                    cập nhật ngày "Cập nhật lần cuối".
                                </p>
                                <p>
                                    Chúng tôi khuyến khích bạn xem lại chính
                                    sách này định kỳ để cập nhật thông tin về
                                    cách chúng tôi bảo vệ dữ liệu của bạn.
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Contact -->
                    <Card
                        class="border-2 bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20"
                    >
                        <CardContent class="p-8">
                            <h3 class="mb-4 text-xl font-bold">
                                Liên hệ về bảo mật
                            </h3>
                            <p class="mb-4 text-muted-foreground">
                                Nếu bạn có bất kỳ câu hỏi hoặc thắc mắc nào về
                                Chính sách bảo mật này hoặc cách chúng tôi xử lý
                                dữ liệu của bạn, vui lòng liên hệ:
                            </p>
                            <div class="space-y-2 text-sm">
                                <p>
                                    <span class="font-semibold"
                                        >Email bảo mật:</span
                                    >
                                    <a
                                        href="mailto:privacy@jobportal.com"
                                        class="ml-2 text-red-600 hover:underline"
                                    >
                                        privacy@jobportal.com
                                    </a>
                                </p>
                                <p>
                                    <span class="font-semibold"
                                        >Email chung:</span
                                    >
                                    <a
                                        href="mailto:contact@jobportal.com"
                                        class="ml-2 text-red-600 hover:underline"
                                    >
                                        contact@jobportal.com
                                    </a>
                                </p>
                                <p>
                                    <span class="font-semibold"
                                        >Điện thoại:</span
                                    >
                                    <a
                                        href="tel:+84123456789"
                                        class="ml-2 text-red-600 hover:underline"
                                    >
                                        (+84) 123 456 789
                                    </a>
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>
    </ClientLayout>
</template>
