<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { useLanguage } from '@/composables/useLanguage';
import ClientLayout from '@/layouts/ClientLayout.vue';
import {
    Clock,
    Facebook,
    Linkedin,
    Mail,
    MapPin,
    MessageSquare,
    Phone,
    Send,
    Twitter,
} from 'lucide-vue-next';
import { ref } from 'vue';

const { t } = useLanguage();

const form = ref({
    name: '',
    email: '',
    subject: '',
    message: '',
});

const isSubmitting = ref(false);

const handleSubmit = async () => {
    isSubmitting.value = true;
    // Handle form submission
    setTimeout(() => {
        isSubmitting.value = false;
        alert('Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.');
        form.value = { name: '', email: '', subject: '', message: '' };
    }, 1000);
};
</script>

<template>
    <ClientLayout :title="t.contactUs">
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
                        <MessageSquare class="h-4 w-4" />
                        <span>Liên hệ</span>
                    </div>
                </div>

                <div class="mb-8 text-center">
                    <h1
                        class="mb-4 text-5xl font-bold tracking-tight md:text-6xl"
                    >
                        Liên hệ với
                        <span
                            class="bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent"
                        >
                            chúng tôi
                        </span>
                    </h1>
                    <p class="mx-auto max-w-3xl text-lg text-muted-foreground">
                        Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn. Hãy để
                        lại thông tin và chúng tôi sẽ phản hồi sớm nhất!
                    </p>
                </div>
            </div>
        </section>

        <!-- Contact Content -->
        <section class="bg-background py-20">
            <div class="container mx-auto px-4">
                <div class="grid gap-8 lg:grid-cols-3">
                    <!-- Contact Info -->
                    <div class="space-y-6">
                        <Card class="border-2 transition-all hover:shadow-xl">
                            <CardContent class="p-6">
                                <div
                                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-orange-500"
                                >
                                    <Mail class="h-6 w-6 text-white" />
                                </div>
                                <h3 class="mb-2 text-lg font-bold">Email</h3>
                                <p class="text-sm text-muted-foreground">
                                    Gửi email cho chúng tôi bất cứ lúc nào
                                </p>
                                <a
                                    href="mailto:contact@jobportal.com"
                                    class="mt-2 block font-semibold text-red-600 hover:text-red-700"
                                >
                                    contact@jobportal.com
                                </a>
                            </CardContent>
                        </Card>

                        <Card class="border-2 transition-all hover:shadow-xl">
                            <CardContent class="p-6">
                                <div
                                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-orange-500"
                                >
                                    <Phone class="h-6 w-6 text-white" />
                                </div>
                                <h3 class="mb-2 text-lg font-bold">
                                    Điện thoại
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    Liên hệ trực tiếp qua hotline
                                </p>
                                <a
                                    href="tel:+84123456789"
                                    class="mt-2 block font-semibold text-red-600 hover:text-red-700"
                                >
                                    (+84) 123 456 789
                                </a>
                            </CardContent>
                        </Card>

                        <Card class="border-2 transition-all hover:shadow-xl">
                            <CardContent class="p-6">
                                <div
                                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-orange-500"
                                >
                                    <MapPin class="h-6 w-6 text-white" />
                                </div>
                                <h3 class="mb-2 text-lg font-bold">Địa chỉ</h3>
                                <p class="text-sm text-muted-foreground">
                                    Ghé thăm văn phòng của chúng tôi
                                </p>
                                <p class="mt-2 font-medium">
                                    123 Đường ABC, Quận 1<br />
                                    TP. Hồ Chí Minh, Việt Nam
                                </p>
                            </CardContent>
                        </Card>

                        <Card class="border-2 transition-all hover:shadow-xl">
                            <CardContent class="p-6">
                                <div
                                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-orange-500"
                                >
                                    <Clock class="h-6 w-6 text-white" />
                                </div>
                                <h3 class="mb-2 text-lg font-bold">
                                    Giờ làm việc
                                </h3>
                                <div class="space-y-1 text-sm">
                                    <p class="flex justify-between">
                                        <span class="text-muted-foreground"
                                            >Thứ 2 - Thứ 6:</span
                                        >
                                        <span class="font-medium"
                                            >8:00 - 18:00</span
                                        >
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="text-muted-foreground"
                                            >Thứ 7:</span
                                        >
                                        <span class="font-medium"
                                            >8:00 - 12:00</span
                                        >
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="text-muted-foreground"
                                            >Chủ nhật:</span
                                        >
                                        <span class="font-medium">Nghỉ</span>
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Social Media -->
                        <Card
                            class="border-2 bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20"
                        >
                            <CardContent class="p-6">
                                <h3 class="mb-4 text-lg font-bold">
                                    Theo dõi chúng tôi
                                </h3>
                                <div class="flex gap-3">
                                    <a
                                        href="#"
                                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-white transition-all hover:scale-110 hover:shadow-lg dark:bg-card"
                                    >
                                        <Facebook
                                            class="h-5 w-5 text-blue-600"
                                        />
                                    </a>
                                    <a
                                        href="#"
                                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-white transition-all hover:scale-110 hover:shadow-lg dark:bg-card"
                                    >
                                        <Linkedin
                                            class="h-5 w-5 text-blue-700"
                                        />
                                    </a>
                                    <a
                                        href="#"
                                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-white transition-all hover:scale-110 hover:shadow-lg dark:bg-card"
                                    >
                                        <Twitter class="h-5 w-5 text-sky-500" />
                                    </a>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Contact Form -->
                    <div class="lg:col-span-2">
                        <Card class="border-2 shadow-xl">
                            <CardContent class="p-8">
                                <h2 class="mb-6 text-3xl font-bold">
                                    Gửi tin nhắn
                                </h2>
                                <form
                                    @submit.prevent="handleSubmit"
                                    class="space-y-6"
                                >
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div>
                                            <label
                                                class="mb-2 block text-sm font-medium"
                                                for="name"
                                            >
                                                Họ và tên
                                                <span class="text-red-600"
                                                    >*</span
                                                >
                                            </label>
                                            <Input
                                                id="name"
                                                v-model="form.name"
                                                placeholder="Nguyễn Văn A"
                                                required
                                                class="h-12 border-2"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="mb-2 block text-sm font-medium"
                                                for="email"
                                            >
                                                Email
                                                <span class="text-red-600"
                                                    >*</span
                                                >
                                            </label>
                                            <Input
                                                id="email"
                                                v-model="form.email"
                                                type="email"
                                                placeholder="email@example.com"
                                                required
                                                class="h-12 border-2"
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium"
                                            for="subject"
                                        >
                                            Tiêu đề
                                            <span class="text-red-600">*</span>
                                        </label>
                                        <Input
                                            id="subject"
                                            v-model="form.subject"
                                            placeholder="Chủ đề bạn muốn trao đổi"
                                            required
                                            class="h-12 border-2"
                                        />
                                    </div>

                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium"
                                            for="message"
                                        >
                                            Nội dung
                                            <span class="text-red-600">*</span>
                                        </label>
                                        <Textarea
                                            id="message"
                                            v-model="form.message"
                                            placeholder="Nội dung chi tiết bạn muốn chia sẻ..."
                                            required
                                            :rows="8"
                                            class="resize-none border-2"
                                        />
                                    </div>

                                    <Button
                                        type="submit"
                                        size="lg"
                                        :disabled="isSubmitting"
                                        class="w-full bg-gradient-to-r from-red-600 to-orange-500 font-semibold shadow-lg transition-all hover:scale-105 hover:shadow-xl md:w-auto"
                                    >
                                        <Send class="mr-2 h-5 w-5" />
                                        {{
                                            isSubmitting
                                                ? 'Đang gửi...'
                                                : 'Gửi tin nhắn'
                                        }}
                                    </Button>
                                </form>
                            </CardContent>
                        </Card>

                        <!-- Map Card -->
                        <Card class="mt-8 border-2">
                            <CardContent class="p-0">
                                <div
                                    class="h-96 w-full overflow-hidden rounded-lg bg-muted"
                                >
                                    <iframe
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4326571115455!2d106.69530631533431!3d10.77572399230157!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f4b3330bcc9%3A0x5a981a5efee9945!2zMTIzIMSQLiBOZ3V54buFbiBUaMOsIE1pbmggS2hhaSwgUGjGsOG7nW5nIDE!5e0!3m2!1svi!2s!4v1234567890"
                                        width="100%"
                                        height="100%"
                                        style="border: 0"
                                        allowfullscreen
                                        loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"
                                    ></iframe>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </section>
    </ClientLayout>
</template>
