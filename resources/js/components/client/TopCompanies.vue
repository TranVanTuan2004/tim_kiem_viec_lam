<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Star, MapPin, Users } from 'lucide-vue-next';

// Mock data
const topCompanies = [
    {
        id: 1,
        name: 'FPT Software',
        logo: '🏢',
        rating: 4.5,
        reviews: 1200,
        jobs: 45,
        location: 'Hà Nội',
        employees: '10,000+',
        description: 'Công ty phần mềm hàng đầu Việt Nam',
    },
    {
        id: 2,
        name: 'VNG Corporation',
        logo: '🎮',
        rating: 4.7,
        reviews: 890,
        jobs: 32,
        location: 'TP.HCM',
        employees: '5,000+',
        description: 'Công ty công nghệ và giải trí',
    },
    {
        id: 3,
        name: 'Tiki',
        logo: '🛒',
        rating: 4.3,
        reviews: 650,
        jobs: 28,
        location: 'TP.HCM',
        employees: '2,000+',
        description: 'Sàn thương mại điện tử',
    },
    {
        id: 4,
        name: 'Shopee',
        logo: '🛍️',
        rating: 4.6,
        reviews: 1500,
        jobs: 56,
        location: 'TP.HCM',
        employees: '3,000+',
        description: 'E-commerce platform',
    },
];
</script>

<template>
    <section class="py-16 bg-muted/50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-bold mb-2">Top công ty nổi bật</h2>
                    <p class="text-muted-foreground">Các công ty uy tín đang tuyển dụng</p>
                </div>
                <Button variant="outline">Xem tất cả</Button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <Card 
                    v-for="company in topCompanies" 
                    :key="company.id"
                    class="group hover:shadow-lg transition-all cursor-pointer overflow-hidden"
                >
                    <div class="h-24 bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-950 dark:to-orange-950"></div>
                    <CardContent class="relative pt-0">
                        <div class="absolute -top-12 left-1/2 -translate-x-1/2">
                            <div class="w-24 h-24 bg-white dark:bg-card rounded-xl shadow-lg flex items-center justify-center text-5xl border-4 border-background">
                                {{ company.logo }}
                            </div>
                        </div>
                        
                        <div class="pt-14 text-center space-y-3">
                            <h3 class="font-semibold text-lg group-hover:text-red-600 transition-colors">
                                {{ company.name }}
                            </h3>
                            
                            <div class="flex items-center justify-center space-x-1 text-sm">
                                <Star class="h-4 w-4 fill-yellow-400 text-yellow-400" />
                                <span class="font-medium">{{ company.rating }}</span>
                                <span class="text-muted-foreground">({{ company.reviews }} reviews)</span>
                            </div>

                            <p class="text-sm text-muted-foreground line-clamp-2">
                                {{ company.description }}
                            </p>

                            <div class="flex items-center justify-center gap-3 text-xs text-muted-foreground">
                                <div class="flex items-center space-x-1">
                                    <MapPin class="h-3 w-3" />
                                    <span>{{ company.location }}</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <Users class="h-3 w-3" />
                                    <span>{{ company.employees }}</span>
                                </div>
                            </div>

                            <Badge variant="secondary" class="w-full justify-center">
                                {{ company.jobs }} việc làm đang tuyển
                            </Badge>

                            <Button variant="outline" class="w-full">
                                Xem công ty
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </section>
</template>

