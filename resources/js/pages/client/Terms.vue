<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { useLanguage } from '@/composables/useLanguage';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { AlertCircle, CheckCircle2, FileText } from 'lucide-vue-next';

const { t } = useLanguage();
</script>

<template>
    <ClientLayout :title="t.termsOfService">
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
                        <FileText class="h-4 w-4" />
                        <span>Điều khoản</span>
                    </div>
                </div>

                <div class="mb-8 text-center">
                    <h1
                        class="mb-4 text-5xl font-bold tracking-tight md:text-6xl"
                    >
                        Điều khoản
                        <span
                            class="bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent"
                        >
                            sử dụng
                        </span>
                    </h1>
                    <p class="mx-auto max-w-3xl text-lg text-muted-foreground">
                        Vui lòng đọc kỹ các điều khoản và điều kiện trước khi sử
                        dụng dịch vụ của chúng tôi
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
                                    <AlertCircle class="h-6 w-6 text-white" />
                                </div>
                                <h2 class="text-2xl font-bold">Giới thiệu</h2>
                            </div>
                            <div class="space-y-4 text-muted-foreground">
                                <p>
                                    Chào mừng bạn đến với Job Portal. Bằng việc
                                    truy cập và sử dụng dịch vụ của chúng tôi,
                                    bạn đồng ý tuân thủ và chịu sự ràng buộc bởi
                                    các điều khoản và điều kiện sau đây.
                                </p>
                                <p>
                                    Nếu bạn không đồng ý với bất kỳ phần nào của
                                    các điều khoản này, vui lòng không sử dụng
                                    dịch vụ của chúng tôi.
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Section 1 -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-950"
                                >
                                    <span class="font-bold text-red-600"
                                        >1</span
                                    >
                                </div>
                                <h3 class="text-xl font-bold">
                                    Điều khoản chung
                                </h3>
                            </div>
                            <div class="space-y-3 text-muted-foreground">
                                <div class="flex gap-2">
                                    <CheckCircle2
                                        class="mt-1 h-5 w-5 flex-shrink-0 text-green-600"
                                    />
                                    <p>
                                        Bạn phải đủ 18 tuổi trở lên để sử dụng
                                        dịch vụ này.
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <CheckCircle2
                                        class="mt-1 h-5 w-5 flex-shrink-0 text-green-600"
                                    />
                                    <p>
                                        Bạn có trách nhiệm duy trì tính bảo mật
                                        của tài khoản và mật khẩu.
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <CheckCircle2
                                        class="mt-1 h-5 w-5 flex-shrink-0 text-green-600"
                                    />
                                    <p>
                                        Bạn đồng ý cung cấp thông tin chính xác,
                                        cập nhật và đầy đủ trong quá trình đăng
                                        ký.
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <CheckCircle2
                                        class="mt-1 h-5 w-5 flex-shrink-0 text-green-600"
                                    />
                                    <p>
                                        Bạn không được sử dụng dịch vụ cho các
                                        mục đích bất hợp pháp hoặc trái phép.
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Section 2 -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-950"
                                >
                                    <span class="font-bold text-red-600"
                                        >2</span
                                    >
                                </div>
                                <h3 class="text-xl font-bold">
                                    Quyền và trách nhiệm của người dùng
                                </h3>
                            </div>
                            <div class="space-y-4 text-muted-foreground">
                                <div>
                                    <h4
                                        class="mb-2 font-semibold text-foreground"
                                    >
                                        Đối với Ứng viên:
                                    </h4>
                                    <ul class="space-y-2">
                                        <li class="flex gap-2">
                                            <CheckCircle2
                                                class="mt-1 h-5 w-5 flex-shrink-0 text-green-600"
                                            />
                                            <span
                                                >Cung cấp thông tin CV chính xác
                                                và trung thực.</span
                                            >
                                        </li>
                                        <li class="flex gap-2">
                                            <CheckCircle2
                                                class="mt-1 h-5 w-5 flex-shrink-0 text-green-600"
                                            />
                                            <span
                                                >Không spam hoặc gửi đơn ứng
                                                tuyển không phù hợp.</span
                                            >
                                        </li>
                                        <li class="flex gap-2">
                                            <CheckCircle2
                                                class="mt-1 h-5 w-5 flex-shrink-0 text-green-600"
                                            />
                                            <span
                                                >Tôn trọng quyền riêng tư của
                                                nhà tuyển dụng.</span
                                            >
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    <h4
                                        class="mb-2 font-semibold text-foreground"
                                    >
                                        Đối với Nhà tuyển dụng:
                                    </h4>
                                    <ul class="space-y-2">
                                        <li class="flex gap-2">
                                            <CheckCircle2
                                                class="mt-1 h-5 w-5 flex-shrink-0 text-green-600"
                                            />
                                            <span
                                                >Đăng tin tuyển dụng chính xác
                                                và trung thực.</span
                                            >
                                        </li>
                                        <li class="flex gap-2">
                                            <CheckCircle2
                                                class="mt-1 h-5 w-5 flex-shrink-0 text-green-600"
                                            />
                                            <span
                                                >Không phân biệt đối xử trong
                                                quá trình tuyển dụng.</span
                                            >
                                        </li>
                                        <li class="flex gap-2">
                                            <CheckCircle2
                                                class="mt-1 h-5 w-5 flex-shrink-0 text-green-600"
                                            />
                                            <span
                                                >Bảo mật thông tin cá nhân của
                                                ứng viên.</span
                                            >
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Section 3 -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-950"
                                >
                                    <span class="font-bold text-red-600"
                                        >3</span
                                    >
                                </div>
                                <h3 class="text-xl font-bold">
                                    Sở hữu trí tuệ
                                </h3>
                            </div>
                            <div class="space-y-3 text-muted-foreground">
                                <p>
                                    Tất cả nội dung, tính năng và chức năng trên
                                    nền tảng Job Portal (bao gồm nhưng không
                                    giới hạn văn bản, đồ họa, logo, biểu tượng,
                                    hình ảnh) đều thuộc sở hữu của Job Portal và
                                    được bảo vệ bởi luật bản quyền.
                                </p>
                                <p>
                                    Bạn không được sao chép, sửa đổi, phân phối
                                    hoặc sử dụng bất kỳ nội dung nào mà không có
                                    sự cho phép bằng văn bản của chúng tôi.
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Section 4 -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-950"
                                >
                                    <span class="font-bold text-red-600"
                                        >4</span
                                    >
                                </div>
                                <h3 class="text-xl font-bold">
                                    Giới hạn trách nhiệm
                                </h3>
                            </div>
                            <div class="space-y-3 text-muted-foreground">
                                <p>
                                    Job Portal cung cấp nền tảng kết nối giữa
                                    nhà tuyển dụng và ứng viên. Chúng tôi không
                                    chịu trách nhiệm về:
                                </p>
                                <ul class="space-y-2">
                                    <li class="flex gap-2">
                                        <CheckCircle2
                                            class="mt-1 h-5 w-5 flex-shrink-0 text-orange-600"
                                        />
                                        <span
                                            >Tính chính xác của thông tin do
                                            người dùng cung cấp.</span
                                        >
                                    </li>
                                    <li class="flex gap-2">
                                        <CheckCircle2
                                            class="mt-1 h-5 w-5 flex-shrink-0 text-orange-600"
                                        />
                                        <span
                                            >Kết quả của quá trình tuyển dụng
                                            giữa các bên.</span
                                        >
                                    </li>
                                    <li class="flex gap-2">
                                        <CheckCircle2
                                            class="mt-1 h-5 w-5 flex-shrink-0 text-orange-600"
                                        />
                                        <span
                                            >Bất kỳ tranh chấp nào phát sinh
                                            giữa nhà tuyển dụng và ứng
                                            viên.</span
                                        >
                                    </li>
                                </ul>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Section 5 -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-950"
                                >
                                    <span class="font-bold text-red-600"
                                        >5</span
                                    >
                                </div>
                                <h3 class="text-xl font-bold">
                                    Chấm dứt dịch vụ
                                </h3>
                            </div>
                            <div class="space-y-3 text-muted-foreground">
                                <p>
                                    Chúng tôi có quyền tạm ngưng hoặc chấm dứt
                                    quyền truy cập của bạn vào dịch vụ nếu bạn
                                    vi phạm các điều khoản này hoặc có hành vi
                                    không phù hợp.
                                </p>
                                <p>
                                    Bạn có thể chấm dứt tài khoản của mình bất
                                    cứ lúc nào bằng cách liên hệ với chúng tôi.
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Section 6 -->
                    <Card class="border-2">
                        <CardContent class="p-8">
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-950"
                                >
                                    <span class="font-bold text-red-600"
                                        >6</span
                                    >
                                </div>
                                <h3 class="text-xl font-bold">
                                    Thay đổi điều khoản
                                </h3>
                            </div>
                            <div class="space-y-3 text-muted-foreground">
                                <p>
                                    Chúng tôi có quyền sửa đổi hoặc thay thế các
                                    điều khoản này bất cứ lúc nào. Chúng tôi sẽ
                                    thông báo cho bạn về những thay đổi quan
                                    trọng.
                                </p>
                                <p>
                                    Việc bạn tiếp tục sử dụng dịch vụ sau khi có
                                    thay đổi đồng nghĩa với việc bạn chấp nhận
                                    các điều khoản mới.
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Contact -->
                    <Card
                        class="border-2 bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20"
                    >
                        <CardContent class="p-8">
                            <h3 class="mb-4 text-xl font-bold">Liên hệ</h3>
                            <p class="mb-4 text-muted-foreground">
                                Nếu bạn có bất kỳ câu hỏi nào về Điều khoản sử
                                dụng này, vui lòng liên hệ với chúng tôi:
                            </p>
                            <div class="space-y-2 text-sm">
                                <p>
                                    <span class="font-semibold">Email:</span>
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
