<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Facebook, Linkedin, Twitter, Youtube } from 'lucide-vue-next';
</script>

<template>
    <footer class="border-t bg-muted/50">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Về Job Portal</h3>
                    <ul class="space-y-2">
                        <li><Link href="/about" class="text-sm text-muted-foreground hover:text-primary">Giới thiệu</Link></li>
                        <li><Link href="/contact" class="text-sm text-muted-foreground hover:text-primary">Liên hệ</Link></li>
                        <li><Link href="/terms" class="text-sm text-muted-foreground hover:text-primary">Điều khoản</Link></li>
                        <li><Link href="/privacy" class="text-sm text-muted-foreground hover:text-primary">Chính sách</Link></li>
                    </ul>
                </div>

                <!-- For Candidates -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Ứng viên</h3>
                    <ul class="space-y-2">
                        <li><Link href="/jobs" class="text-sm text-muted-foreground hover:text-primary">Tìm việc làm</Link></li>
                        <li><Link href="/companies" class="text-sm text-muted-foreground hover:text-primary">Danh sách công ty</Link></li>
                        <li><Link href="/cv-templates" class="text-sm text-muted-foreground hover:text-primary">Mẫu CV</Link></li>
                        <li><Link href="/career-guide" class="text-sm text-muted-foreground hover:text-primary">Hướng dẫn</Link></li>
                    </ul>
                </div>

                <!-- For Employers -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Nhà tuyển dụng</h3>
                    <ul class="space-y-2">
                        <li><Link href="/employer/register" class="text-sm text-muted-foreground hover:text-primary">Đăng tuyển dụng</Link></li>
                        <li><Link href="/employer/pricing" class="text-sm text-muted-foreground hover:text-primary">Bảng giá</Link></li>
                        <li><Link href="/employer/support" class="text-sm text-muted-foreground hover:text-primary">Hỗ trợ</Link></li>
                    </ul>
                </div>

                <!-- Social -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Theo dõi</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-muted-foreground hover:text-primary">
                            <Facebook class="h-5 w-5" />
                        </a>
                        <a href="#" class="text-muted-foreground hover:text-primary">
                            <Linkedin class="h-5 w-5" />
                        </a>
                        <a href="#" class="text-muted-foreground hover:text-primary">
                            <Twitter class="h-5 w-5" />
                        </a>
                        <a href="#" class="text-muted-foreground hover:text-primary">
                            <Youtube class="h-5 w-5" />
                        </a>
                    </div>
                    <p class="text-sm text-muted-foreground mt-4">
                        © 2025 Job Portal. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>

