<script setup lang="ts">
import { ref, reactive, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardHeader, CardContent, CardTitle, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { throttle } from 'lodash';
import { 
  Search, 
  MapPin, 
  Briefcase, 
  DollarSign, 
  User, 
  Filter, 
  X, 
  Loader2,
  GraduationCap,
  Star
} from 'lucide-vue-next';

// ----- PROPS -----
const props = defineProps<{
  candidates: any, // pagination object
  filters: any,
  allSkills: { id: number; name: string }[],
  allCities: { id: number; name: string; province: string }[],
}>();

// ----- STATE -----
const keyword = ref(props.filters.keyword || '');
const experienceLevel = ref(props.filters.experience_level || '');
const selectedSkills = ref<{id:number,name:string}[]>([]);
const skillExperience = reactive<Record<number, number>>({}); // skillId => years
const skillQuery = ref('');

const cityQuery = ref('');
const selectedCity = ref<{id:number,name:string,province:string} | null>(null);

const currentPage = ref(props.candidates.current_page || 1);
const totalPages = ref(props.candidates.last_page || 1);
const candidates = ref(props.candidates.data || []);
const loading = ref(false);

// Initialize selected skills from props if needed (not implemented in controller yet but good practice)
// For now, we just start empty or rely on user input. 
// If the controller passed back selected skills objects, we would populate them here.

// ----- COMPUTED -----
const filteredSkills = computed(() =>
  props.allSkills.filter(s =>
    s.name.toLowerCase().includes(skillQuery.value.toLowerCase()) &&
    !selectedSkills.value.find(sel => sel.id === s.id)
  ).slice(0, 10) // Limit to 10 suggestions
);

const filteredCities = computed(() =>
  props.allCities.filter(c =>
    c.name.toLowerCase().includes(cityQuery.value.toLowerCase())
  ).slice(0, 10) // Limit to 10 suggestions
);

// ----- FETCH CANDIDATES -----
const fetchCandidates = throttle((append = false) => {
  loading.value = true;
  router.get('/employer/candidates/search', {
    keyword: keyword.value,
    experience_level: experienceLevel.value,
    skills: selectedSkills.value.map(s => s.id).join(','),
    skill_experience: Object.entries(skillExperience).map(([id, yrs]) => `${id}:${yrs}`).join(','),
    city: selectedCity.value?.name || '',
    province: selectedCity.value?.province || '',
    page: currentPage.value,
  }, {
    preserveState: true,
    replace: true,
    onSuccess: (page) => {
      loading.value = false;
      const props = page.props as any;
      if (append) {
        candidates.value.push(...props.candidates.data);
      } else {
        candidates.value = props.candidates.data;
        totalPages.value = props.candidates.last_page;
        currentPage.value = props.candidates.current_page;
      }
    },
    onError: () => {
        loading.value = false;
    }
  });
}, 500);

// ----- WATCH FILTERS -----
watch([keyword, experienceLevel, selectedSkills, skillExperience, selectedCity], () => {
  currentPage.value = 1;
  fetchCandidates(false);
});

// ----- ACTIONS -----
const loadMore = () => {
  if (currentPage.value < totalPages.value && !loading.value) {
    currentPage.value++;
    fetchCandidates(true);
  }
};

const resetFilters = () => {
    keyword.value = '';
    experienceLevel.value = '';
    selectedSkills.value = [];
    for (const key in skillExperience) {
        delete skillExperience[key];
    }
    selectedCity.value = null;
    cityQuery.value = '';
};

const removeSkill = (skill: {id:number,name:string}) => {
    const index = selectedSkills.value.indexOf(skill);
    if (index > -1) {
        selectedSkills.value.splice(index, 1);
        delete skillExperience[skill.id];
    }
};

const addSkill = (skill: {id:number,name:string}) => {
    selectedSkills.value.push(skill);
    skillQuery.value = '';
    skillExperience[skill.id] = 0;
};

const selectCity = (city: {id:number,name:string,province:string}) => {
    selectedCity.value = city;
    cityQuery.value = '';
};

const clearCity = () => {
    selectedCity.value = null;
    cityQuery.value = '';
};
</script>

<template>
  <AppLayout>
    <div class="container mx-auto p-4 md:p-6 max-w-7xl">
      <div class="flex flex-col md:flex-row gap-6">
        
        <!-- LEFT SIDEBAR: FILTERS -->
        <div class="w-full md:w-1/4 space-y-6">
            <Card class="sticky top-4">
                <CardHeader class="pb-3">
                    <div class="flex items-center justify-between">
                        <CardTitle class="text-lg font-semibold flex items-center gap-2">
                            <Filter class="w-5 h-5" />
                            Bộ lọc
                        </CardTitle>
                        <Button variant="ghost" size="sm" class="h-8 text-xs text-muted-foreground hover:text-primary" @click="resetFilters">
                            Xóa lọc
                        </Button>
                    </div>
                </CardHeader>
                <Separator />
                <CardContent class="space-y-6 pt-6">
                    
                    <!-- Keyword -->
                    <div class="space-y-2">
                        <Label>Từ khóa</Label>
                        <div class="relative">
                            <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input v-model="keyword" placeholder="Tên, vị trí, công ty..." class="pl-9" />
                        </div>
                    </div>

                    <!-- City -->
                    <div class="space-y-2">
                        <Label>Địa điểm</Label>
                        <div v-if="selectedCity" class="flex items-center justify-between p-2 border rounded-md bg-muted/50">
                            <span class="text-sm font-medium truncate">{{ selectedCity.name }}</span>
                            <Button variant="ghost" size="icon" class="h-6 w-6" @click="clearCity">
                                <X class="h-4 w-4" />
                            </Button>
                        </div>
                        <div v-else class="relative">
                            <MapPin class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input v-model="cityQuery" placeholder="Nhập thành phố..." class="pl-9" />
                            
                            <!-- City Suggestions -->
                            <div v-if="filteredCities.length && cityQuery" class="absolute z-10 w-full mt-1 bg-white border rounded-md shadow-lg max-h-60 overflow-y-auto">
                                <div 
                                    v-for="c in filteredCities" 
                                    :key="c.id" 
                                    @click="selectCity(c)" 
                                    class="px-3 py-2 text-sm hover:bg-accent cursor-pointer transition-colors"
                                >
                                    {{ c.name }} <span class="text-xs text-muted-foreground">- {{ c.province }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Experience Level -->
                    <div class="space-y-2">
                        <Label>Cấp bậc</Label>
                        <select 
                            v-model="experienceLevel" 
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="">Tất cả cấp bậc</option>
                            <option value="Intern">Intern</option>
                            <option value="Fresher">Fresher</option>
                            <option value="Junior">Junior</option>
                            <option value="Mid">Mid</option>
                            <option value="Senior">Senior</option>
                            <option value="Lead">Lead</option>
                            <option value="Manager">Manager</option>
                        </select>
                    </div>

                    <!-- Skills -->
                    <div class="space-y-2">
                        <Label>Kỹ năng</Label>
                        <div class="relative">
                            <Input v-model="skillQuery" placeholder="Thêm kỹ năng..." />
                            <!-- Skill Suggestions -->
                            <div v-if="filteredSkills.length && skillQuery" class="absolute z-10 w-full mt-1 bg-white border rounded-md shadow-lg max-h-60 overflow-y-auto">
                                <div 
                                    v-for="s in filteredSkills" 
                                    :key="s.id" 
                                    @click="addSkill(s)" 
                                    class="px-3 py-2 text-sm hover:bg-accent cursor-pointer transition-colors"
                                >
                                    {{ s.name }}
                                </div>
                            </div>
                        </div>

                        <!-- Selected Skills List -->
                        <div class="space-y-3 mt-3">
                            <div v-for="s in selectedSkills" :key="s.id" class="bg-secondary/30 p-3 rounded-md space-y-2 border">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-sm">{{ s.name }}</span>
                                    <Button variant="ghost" size="icon" class="h-5 w-5 text-muted-foreground hover:text-destructive" @click="removeSkill(s)">
                                        <X class="h-3 w-3" />
                                    </Button>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input 
                                        type="range" 
                                        min="0" 
                                        max="10" 
                                        v-model.number="skillExperience[s.id]" 
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary"
                                    />
                                    <span class="text-xs w-12 text-right font-medium">{{ skillExperience[s.id] || 0 }} năm</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </CardContent>
            </Card>
        </div>

        <!-- RIGHT CONTENT: RESULTS -->
        <div class="flex-1 space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight">Kết quả tìm kiếm</h1>
                <span class="text-muted-foreground text-sm">{{ candidates.length }} ứng viên hiển thị</span>
            </div>

            <!-- Loading State -->
            <div v-if="loading && !candidates.length" class="flex justify-center py-12">
                <Loader2 class="h-8 w-8 animate-spin text-primary" />
            </div>

            <!-- Results Grid -->
            <div v-else-if="candidates.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-4">
                <Card v-for="c in candidates" :key="c.id" class="group hover:shadow-md transition-all duration-300 border-l-4 border-l-transparent hover:border-l-primary">
                    <CardHeader class="pb-2">
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3">
                                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-lg">
                                    {{ c.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <CardTitle class="text-lg group-hover:text-primary transition-colors">{{ c.user.name }}</CardTitle>
                                    <div class="text-sm text-muted-foreground flex items-center gap-1 mt-1">
                                        <MapPin class="w-3 h-3" />
                                        {{ c.city }}
                                    </div>
                                </div>
                            </div>
                            <Badge variant="outline" class="bg-background">{{ c.experience_level }}</Badge>
                        </div>
                    </CardHeader>
                    <CardContent class="pb-2 space-y-3">
                        <div v-if="c.summary" class="text-sm text-muted-foreground line-clamp-2">
                            {{ c.summary }}
                        </div>
                        
                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-1 text-gray-600">
                                <DollarSign class="w-4 h-4" />
                                <span>{{ c.expected_salary || 'Thỏa thuận' }}</span>
                            </div>
                            <div class="flex items-center gap-1 text-gray-600">
                                <Briefcase class="w-4 h-4" />
                                <span>{{ c.years_of_experience || 0 }} năm KN</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <Badge v-for="s in c.skills.slice(0, 3)" :key="s.id" variant="secondary" class="text-xs font-normal">
                                {{ s.name }}
                                <span v-if="s.pivot.years_experience" class="ml-1 text-muted-foreground">({{ s.pivot.years_experience }}y)</span>
                            </Badge>
                            <Badge v-if="c.skills.length > 3" variant="outline" class="text-xs">
                                +{{ c.skills.length - 3 }}
                            </Badge>
                        </div>
                    </CardContent>
                    <CardFooter class="pt-2">
                        <Button class="w-full bg-primary/90 hover:bg-primary" size="sm">
                            Xem hồ sơ
                        </Button>
                    </CardFooter>
                </Card>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16 bg-white rounded-lg border border-dashed">
                <div class="bg-gray-50 h-20 w-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <User class="h-10 w-10 text-gray-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-900">Không tìm thấy ứng viên</h3>
                <p class="text-muted-foreground mt-1 max-w-sm mx-auto">
                    Thử điều chỉnh bộ lọc hoặc từ khóa tìm kiếm để thấy nhiều kết quả hơn.
                </p>
                <Button variant="outline" class="mt-4" @click="resetFilters">
                    Xóa bộ lọc
                </Button>
            </div>

            <!-- Load More -->
            <div v-if="currentPage < totalPages" class="flex justify-center pt-4">
                <Button variant="outline" @click="loadMore" :disabled="loading" class="min-w-[150px]">
                    <Loader2 v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
                    {{ loading ? 'Đang tải...' : 'Xem thêm ứng viên' }}
                </Button>
            </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Custom scrollbar for suggestions */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}
.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.1);
    border-radius: 3px;
}
</style>
