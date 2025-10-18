<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Link } from '@inertiajs/vue3';
import { Building2, Clock, DollarSign, Heart, MapPin } from 'lucide-vue-next';

// Mock data - sẽ được thay thế bằng real data sau
const featuredJobs = [
    {
        id: 1,
        slug: 'senior-backend-developer',
        title: 'Senior Backend Developer',
        company: 'TechCorp Vietnam',
        logo: '🏢',
        location: 'Hà Nội',
        salary: '1000-2000 USD',
        type: 'Full-time',
        skills: ['Java', 'Spring Boot', 'MySQL'],
        posted: '2 ngày trước',
    },
    {
        id: 2,
        slug: 'frontend-developer-reactjs',
        title: 'Frontend Developer (ReactJS)',
        company: 'Digital Solutions',
        logo: '💼',
        location: 'TP.HCM',
        salary: '800-1500 USD',
        type: 'Full-time',
        skills: ['ReactJS', 'TypeScript', 'Tailwind'],
        posted: '1 ngày trước',
    },
    {
        id: 3,
        slug: 'full-stack-developer',
        title: 'Full Stack Developer',
        company: 'StartUp Innovation',
        logo: '🚀',
        location: 'Remote',
        salary: '1200-2500 USD',
        type: 'Remote',
        skills: ['NodeJS', 'Vue.js', 'MongoDB'],
        posted: '3 giờ trước',
    },
    {
        id: 4,
        slug: 'devops-engineer',
        title: 'DevOps Engineer',
        company: 'Cloud Services Ltd',
        logo: '☁️',
        location: 'Đà Nẵng',
        salary: 'Thỏa thuận',
        type: 'Full-time',
        skills: ['AWS', 'Docker', 'Kubernetes'],
        posted: '5 ngày trước',
    },
];
</script>

<template>
    <section class="bg-background py-16">
        <div class="container mx-auto px-4">
            <div class="mb-12 flex items-center justify-between">
                <div>
                    <h2 class="mb-2 text-3xl font-bold">Việc làm nổi bật</h2>
                    <p class="text-muted-foreground">
                        Cơ hội việc làm IT mới nhất
                    </p>
                </div>
                <Button variant="outline">Xem tất cả</Button>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <Link
                    v-for="job in featuredJobs"
                    :key="job.id"
                    :href="`/jobs/${job.slug}`"
                >
                    <Card
                        class="group h-full cursor-pointer transition-all hover:shadow-lg"
                    >
                        <CardHeader>
                            <div class="flex items-start justify-between">
                                <div class="flex flex-1 items-start space-x-4">
                                    <div class="text-4xl">{{ job.logo }}</div>
                                    <div class="flex-1">
                                        <CardTitle
                                            class="mb-1 text-xl transition-colors group-hover:text-red-600"
                                        >
                                            {{ job.title }}
                                        </CardTitle>
                                        <CardDescription
                                            class="flex items-center space-x-2"
                                        >
                                            <Building2 class="h-4 w-4" />
                                            <span>{{ job.company }}</span>
                                        </CardDescription>
                                    </div>
                                </div>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="text-muted-foreground hover:text-red-600"
                                >
                                    <Heart class="h-5 w-5" />
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div
                                class="flex h-full flex-col justify-between space-y-3"
                            >
                                <div>
                                    <!-- Job Info -->
                                    <div
                                        class="flex flex-wrap gap-4 text-sm text-muted-foreground"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <MapPin class="h-4 w-4" />
                                            <span>{{ job.location }}</span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <DollarSign class="h-4 w-4" />
                                            <span>{{ job.salary }}</span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <Clock class="h-4 w-4" />
                                            <span>{{ job.posted }}</span>
                                        </div>
                                    </div>

                                    <!-- Skills -->
                                    <div class="flex flex-wrap gap-2 pt-3">
                                        <Badge
                                            v-for="skill in job.skills"
                                            :key="skill"
                                            variant="secondary"
                                        >
                                            {{ skill }}
                                        </Badge>
                                        <Badge variant="outline">{{
                                            job.type
                                        }}</Badge>
                                    </div>
                                </div>

                                <!-- Apply Button -->
                                <div class="pt-4">
                                    <Button class="w-full" variant="default">
                                        Ứng tuyển ngay
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </section>
</template>
