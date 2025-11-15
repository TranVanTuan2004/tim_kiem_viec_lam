<script setup lang="ts">
import { ref, reactive, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { throttle } from 'lodash';

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

// ----- COMPUTED -----
const filteredSkills = computed(() =>
  props.allSkills.filter(s =>
    s.name.toLowerCase().includes(skillQuery.value.toLowerCase()) &&
    !selectedSkills.value.find(sel => sel.id === s.id)
  )
);

const filteredCities = computed(() =>
  props.allCities.filter(c =>
    c.name.toLowerCase().includes(cityQuery.value.toLowerCase())
  )
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
      if (append) {
        candidates.value.push(...page.candidates.data);
      } else {
        candidates.value = page.candidates.data;
        totalPages.value = page.candidates.last_page;
        currentPage.value = page.candidates.current_page;
      }
    }
  });
}, 300);

// ----- WATCH FILTERS -----
watch([keyword, experienceLevel, selectedSkills, skillExperience, selectedCity], () => {
  currentPage.value = 1;
  fetchCandidates(false);
});

// ----- LOAD MORE -----
const loadMore = () => {
  if (currentPage.value < totalPages.value && !loading.value) {
    currentPage.value++;
    fetchCandidates(true);
  }
};
</script>

<template>
  <AppLayout>
    <div class="space-y-6 p-6">
      <h1 class="text-2xl font-semibold">Tìm kiếm ứng viên</h1>

      <!-- Keyword -->
      <input v-model="keyword" placeholder="Tên, vị trí" class="border rounded p-2 w-full"/>

      <!-- Experience Level -->
      <select v-model="experienceLevel" class="border rounded p-2 w-full">
        <option value="">Tất cả Level</option>
        <option value="Junior">Junior</option>
        <option value="Mid">Mid</option>
        <option value="Senior">Senior</option>
      </select>

      <!-- Skills Autocomplete -->
      <div class="flex flex-col">
        <label class="font-medium">Kỹ năng</label>
        <input v-model="skillQuery" placeholder="Nhập kỹ năng..." class="border rounded p-2"/>
        <div class="flex flex-wrap gap-2 mt-1">
          <span v-for="s in selectedSkills" :key="s.id" class="bg-blue-500 text-white px-3 py-1 rounded flex items-center gap-1">
            {{ s.name }}
            <button @click="selectedSkills.splice(selectedSkills.indexOf(s),1)">✕</button>
          </span>
        </div>
        <div v-if="filteredSkills.length" class="border rounded p-2 mt-1 max-h-40 overflow-y-auto">
          <div v-for="s in filteredSkills" :key="s.id" @click="{
            selectedSkills.push(s);
            skillQuery='';
            skillExperience[s.id] = 0;
          }" class="hover:bg-gray-100 cursor-pointer p-1 rounded">{{ s.name }}</div>
        </div>
      </div>

      <!-- Slider số năm kinh nghiệm -->
      <div v-for="s in selectedSkills" :key="s.id" class="flex items-center gap-2">
        <span class="w-32">{{ s.name }}:</span>
        <input type="range" min="0" max="20" v-model.number="skillExperience[s.id]" class="flex-1"/>
        <span>{{ skillExperience[s.id] || 0 }} năm</span>
      </div>

      <!-- City Autocomplete -->
      <div class="flex gap-2">
        <div class="flex-1 relative">
          <input v-model="cityQuery" placeholder="Thành phố" class="border rounded p-2 w-full"/>
          <div v-if="filteredCities.length && cityQuery" class="border rounded p-2 max-h-40 overflow-y-auto mt-1 absolute bg-white z-10 w-full">
            <div v-for="c in filteredCities" :key="c.id" @click="selectedCity=c; cityQuery='';" class="hover:bg-gray-100 cursor-pointer p-1 rounded">
              {{ c.name }} - {{ c.province }}
            </div>
          </div>
        </div>
        <input :value="selectedCity?.province" placeholder="Tỉnh/Quận" class="border rounded p-2 w-1/3" readonly/>
      </div>

      <!-- Candidate Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <Card v-for="c in candidates" :key="c.id" class="transition hover:shadow-md">
          <CardHeader>
            <CardTitle>{{ c.user.name }}</CardTitle>
          </CardHeader>
          <CardContent>
            <p>{{ c.summary }}</p>
            <p class="font-medium mt-2">Kỹ năng:</p>
            <ul class="list-disc ml-5">
              <li v-for="s in c.skills" :key="s.id">{{ s.name }} - {{ s.pivot.years_experience }} năm</li>
            </ul>
            <p class="mt-2">Level: {{ c.experience_level }}</p>
            <p>Mức lương mong muốn: {{ c.expected_salary }}</p>
            <p>Địa điểm: {{ c.city }} - {{ c.province }}</p>
          </CardContent>
        </Card>
      </div>

      <!-- Load More -->
      <div class="flex justify-center mt-4">
        <Button v-if="currentPage < totalPages" @click="loadMore" :disabled="loading">
          {{ loading ? 'Đang tải...' : 'Xem thêm' }}
        </Button>
      </div>

      <div v-if="!candidates.length && !loading" class="py-10 text-center text-gray-500">
        Không tìm thấy ứng viên phù hợp.
      </div>
    </div>
  </AppLayout>
</template>
