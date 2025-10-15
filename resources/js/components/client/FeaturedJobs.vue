<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { MapPin, DollarSign, Clock, Heart, Building2 } from 'lucide-vue-next';

// Mock data - sẽ được thay thế bằng real data sau
const featuredJobs = [
    {
        id: 1,
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
    <section class="py-16 bg-background">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-bold mb-2">Việc làm nổi bật</h2>
                    <p class="text-muted-foreground">Cơ hội việc làm IT mới nhất</p>
                </div>
                <Button variant="outline">Xem tất cả</Button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <Card 
                    v-for="job in featuredJobs" 
                    :key="job.id"
                    class="group hover:shadow-lg transition-all cursor-pointer"
                >
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                <div class="text-4xl">{{ job.logo }}</div>
                                <div class="flex-1">
                                    <CardTitle class="text-xl mb-1 group-hover:text-red-600 transition-colors">
                                        {{ job.title }}
                                    </CardTitle>
                                    <CardDescription class="flex items-center space-x-2">
                                        <Building2 class="h-4 w-4" />
                                        <span>{{ job.company }}</span>
                                    </CardDescription>
                                </div>
                            </div>
                            <Button variant="ghost" size="icon" class="text-muted-foreground hover:text-red-600">
                                <Heart class="h-5 w-5" />
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <!-- Job Info -->
                            <div class="flex flex-wrap gap-4 text-sm text-muted-foreground">
                                <div class="flex items-center space-x-1">
                                    <MapPin class="h-4 w-4" />
                                    <span>{{ job.location }}</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <DollarSign class="h-4 w-4" />
                                    <span>{{ job.salary }}</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <Clock class="h-4 w-4" />
                                    <span>{{ job.posted }}</span>
                                </div>
                            </div>

                            <!-- Skills -->
                            <div class="flex flex-wrap gap-2">
                                <Badge 
                                    v-for="skill in job.skills" 
                                    :key="skill"
                                    variant="secondary"
                                >
                                    {{ skill }}
                                </Badge>
                                <Badge variant="outline">{{ job.type }}</Badge>
                            </div>

                            <!-- Apply Button -->
                            <div class="pt-2">
                                <Button class="w-full" variant="default">
                                    Ứng tuyển ngay
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </section>
</template>

