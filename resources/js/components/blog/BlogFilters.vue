<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import Icon from '@/components/Icon.vue';
import LanguageSelector from '@/components/LanguageSelector.vue';
import { useLanguage } from '@/composables/useLanguage';

interface FilterOptions {
  sort?: string;
  direction?: string;
  search?: string;
  view?: string;
}

interface Props {
  filters?: FilterOptions;
  routeName: string;
  routeParams?: Record<string, any>;
  translations: any;
}

const props = defineProps<Props>();

// Use props.filters as defaults or set defaults if not provided
const sort = ref(props.filters?.sort || 'updated');
const direction = ref(props.filters?.direction || 'desc');
const search = ref(props.filters?.search || '');
const viewMode = ref(props.filters?.view || 'gallery');

// Use the language composable
const { currentLang } = useLanguage();

// Check if code is running in browser environment
const isBrowser = typeof window !== 'undefined';

// Update viewMode in localStorage when changed
const updateViewMode = (mode: string) => {
  viewMode.value = mode;
  if (isBrowser) {
    localStorage.setItem('blogViewMode', mode);
  }
  applyFilters();
};

// Load viewMode from localStorage on mount
onMounted(() => {
  if (isBrowser) {
    const savedViewMode = localStorage.getItem('blogViewMode');
    if (savedViewMode) {
      viewMode.value = savedViewMode;
    }
  }
});

// Computed properties
const isList = computed(() => viewMode.value === 'list');
const isGallery = computed(() => viewMode.value === 'gallery');

// Function to apply filters
const applyFilters = () => {
  router.get(route(props.routeName, props.routeParams || {}), {
    sort: sort.value,
    direction: direction.value,
    search: search.value,
    view: viewMode.value,
    locale: currentLang.value
  }, {
    preserveState: true,
    replace: true
  });
};

// Handle search form submission
const handleSearchSubmit = (e: Event) => {
  e.preventDefault();
  applyFilters();
};

// Change sort option
const handleSortChange = (e: Event) => {
  const target = e.target as HTMLSelectElement;
  sort.value = target.value;
  applyFilters();
};

// Toggle sort direction
const toggleSortDirection = () => {
  direction.value = direction.value === 'asc' ? 'desc' : 'asc';
  applyFilters();
};

// Expose values for parent component
defineExpose({
  sort,
  direction,
  search,
  viewMode,
  isList,
  isGallery
});
</script>

<template>
  <div class="mb-8 flex flex-col gap-4">
    <!-- Search and language selector -->
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
      <div class="w-full md:w-1/3">
        <form @submit="handleSearchSubmit" class="flex gap-2">
          <Input 
            type="search" 
            v-model="search"
            :placeholder="translations.blog.search_placeholder"
            class="w-full"
          />
          <Button type="submit" variant="default" size="default">
            <Icon name="search" class="h-4 w-4" />
          </Button>
        </form>
      </div>
      
      <!-- Language selector -->
      <div class="flex justify-end">
        <LanguageSelector />
      </div>
    </div>
    
    <!-- Sort controls and view toggles -->
    <div class="flex items-center gap-4 w-full justify-between">
      <!-- Sort options with direction toggle -->
      <div class="flex items-center gap-2">
        <select 
          :value="sort" 
          @change="handleSortChange"
          class="px-4 py-2 rounded-md border border-input bg-background text-sm"
        >
          <option value="updated">{{ translations.blog.sort_updated }}</option>
          <option value="views">{{ translations.blog.sort_views }}</option>
          <option value="created">{{ translations.blog.sort_created }}</option>
        </select>
        
        <Button 
          variant="outline" 
          size="sm" 
          class="aspect-square" 
          @click="toggleSortDirection"
          :aria-label="direction === 'asc' ? 'Sort ascending' : 'Sort descending'"
        >
          <Icon :name="direction === 'asc' ? 'arrow-up' : 'arrow-down'" class="h-4 w-4" />
        </Button>
      </div>
      
      <!-- View mode toggle -->
      <div class="flex-shrink-0 flex items-center gap-2 bg-muted rounded-md p-1">
        <TooltipProvider>
          <Tooltip>
            <TooltipTrigger asChild>
              <Button 
                size="sm" 
                :variant="isGallery ? 'default' : 'ghost'"
                class="rounded-md aspect-square" 
                @click="updateViewMode('gallery')"
              >
                <Icon name="grid" class="h-4 w-4" />
              </Button>
            </TooltipTrigger>
            <TooltipContent>{{ translations.blog.gallery_view }}</TooltipContent>
          </Tooltip>
        </TooltipProvider>
        
        <TooltipProvider>
          <Tooltip>
            <TooltipTrigger asChild>
              <Button 
                size="sm"
                :variant="isList ? 'default' : 'ghost'"
                class="rounded-md aspect-square"
                @click="updateViewMode('list')"
              >
                <Icon name="list" class="h-4 w-4" />
              </Button>
            </TooltipTrigger>
            <TooltipContent>{{ translations.blog.list_view }}</TooltipContent>
          </Tooltip>
        </TooltipProvider>
      </div>
    </div>
  </div>
</template>