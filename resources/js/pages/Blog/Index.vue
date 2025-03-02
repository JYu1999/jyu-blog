<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';

// Get translations reactively using a computed property
import { computed } from 'vue';
const page = usePage();
const translations = computed(() => page.props.translations);

interface Post {
  id: number;
  title: string;
  slug: string;
  summary: string;
  featured_image: string | null;
  status: string;
  views: number;
  created_at: string;
  updated_at: string;
  relative_updated_at: string;
  category: {
    id: number;
    name: string;
    slug: string;
  } | null;
}

interface Props {
  posts: {
    data: Post[];
    links: {
      first: string;
      last: string;
      prev: string | null;
      next: string | null;
    };
    meta: {
      current_page: number;
      from: number;
      last_page: number;
      path: string;
      per_page: number;
      to: number;
      total: number;
    };
  };
  filters?: {
    sort: string;
    search: string;
    view: string;
  };
}

const props = defineProps<Props>();

import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { 
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import Icon from '@/components/Icon.vue';

// Use props.filters as defaults or set defaults if not provided
const sort = ref(props.filters?.sort || 'updated');
const direction = ref(props.filters?.direction || 'desc');
const search = ref(props.filters?.search || '');
const viewMode = ref(props.filters?.view || 'gallery');
const currentLang = ref(props.filters?.locale || 'en'); // Default language

// Update viewMode in localStorage when changed
const updateViewMode = (mode: string) => {
  viewMode.value = mode;
  if (typeof localStorage !== 'undefined') {
    localStorage.setItem('blogViewMode', mode);
  }
  applyFilters();
};

// Check if code is running in browser environment
const isBrowser = typeof window !== 'undefined';

// Load viewMode from localStorage on mount
onMounted(() => {
  if (isBrowser) {
    const savedViewMode = localStorage.getItem('blogViewMode');
    if (savedViewMode) {
      viewMode.value = savedViewMode;
    }
    
    // Load locale from localStorage if exists
    const savedLocale = localStorage.getItem('locale');
    if (savedLocale) {
      currentLang.value = savedLocale;
      document.documentElement.lang = savedLocale;
    } else {
      // Use browser language as fallback or the HTML lang attribute
      currentLang.value = document.documentElement.lang || 'en';
    }
  }
});

// Computed properties
const isList = computed(() => viewMode.value === 'list');
const isGallery = computed(() => viewMode.value === 'gallery');

// Function to apply filters
const applyFilters = () => {
  router.get(route('blog.index'), {
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

// Update locale and apply filters
const updateLocale = (locale: string) => {
  currentLang.value = locale;
  if (isBrowser) {
    localStorage.setItem('locale', locale);
    document.documentElement.lang = locale;
  }
  applyFilters();
};

const navigateToPost = (slug: string) => {
  window.location.href = route('blog.show', slug);
};
</script>

<template>
  <Head>
    <title>{{ translations.blog.blog_title }}</title>
    <meta name="description" :content="translations.blog.blog_description" />
  </Head>

  <DefaultLayout>
    <div class="container mx-auto py-16 px-4 sm:px-6 lg:px-8">
      <!-- Hero section -->
      <div class="text-center mb-10 max-w-3xl mx-auto">
        <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-6 leading-tight">{{ translations.blog.blog_title }}</h1>
        <p class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed">
          {{ translations.blog.blog_description }}
        </p>
      </div>
      
      <!-- Filter controls -->
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
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="outline" size="sm" class="gap-2">
                  <span v-if="currentLang === 'en'">🇺🇸 English</span>
                  <span v-else-if="currentLang === 'zh'">🇹🇼 繁體中文</span>
                  <span v-else-if="currentLang === 'zh-CN'">🇨🇳 简体中文</span>
                  <span v-else-if="currentLang === 'ja'">🇯🇵 日本語</span>
                  <span v-else-if="currentLang === 'vi'">🇻🇳 Tiếng Việt</span>
                  <span v-else>🌐 Language</span>
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                <DropdownMenuItem @click="() => updateLocale('en')">
                  🇺🇸 English
                </DropdownMenuItem>
                <DropdownMenuItem @click="() => updateLocale('zh')">
                  🇹🇼 繁體中文
                </DropdownMenuItem>
                <DropdownMenuItem @click="() => updateLocale('zh-CN')">
                  🇨🇳 简体中文
                </DropdownMenuItem>
                <DropdownMenuItem @click="() => updateLocale('ja')">
                  🇯🇵 日本語
                </DropdownMenuItem>
                <DropdownMenuItem @click="() => updateLocale('vi')">
                  🇻🇳 Tiếng Việt
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
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
      

      <!-- Gallery view -->
      <div 
        v-if="isGallery && posts && posts.data && posts.data.length > 0" 
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <Card 
          v-for="post in posts.data" 
          :key="post.id" 
          class="overflow-hidden flex flex-col hover:shadow-xl transition-all duration-300 rounded-xl border-none bg-card cursor-pointer"
          @click="navigateToPost(post.slug)"
        >
          <!-- Featured image -->
          <div v-if="post.featured_image" class="h-56 overflow-hidden">
            <img :src="/storage/ + post.featured_image" :alt="post.title" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" />
          </div>
          <div v-else class="h-56 bg-gradient-to-br from-primary/5 to-primary/20 dark:from-primary/10 dark:to-primary/30 flex items-center justify-center">
            <Avatar class="h-20 w-20 bg-primary/20 text-primary dark:bg-primary/30">
              <AvatarFallback class="text-2xl font-semibold">{{ post.title.substring(0, 2).toUpperCase() }}</AvatarFallback>
            </Avatar>
          </div>

          <!-- Post content -->
          <CardContent class="p-6 lg:p-8 flex-grow flex flex-col">
            <!-- Category -->
            <div v-if="post.category" class="mb-3">
              <Button 
                :as="Link" 
                :href="route('blog.category', post.category.slug)" 
                variant="outline"
                size="sm"
                class="rounded-full bg-primary/10 text-primary hover:bg-primary/20 hover:text-primary text-xs"
                @click.stop
              >
                {{ post.category.name }}
              </Button>
            </div>

            <!-- Title -->
            <h2 class="text-2xl font-bold mb-3 text-card-foreground line-clamp-2">
              {{ post.title }}
            </h2>

            <!-- Summary -->
            <p class="text-muted-foreground mb-5 flex-grow line-clamp-3 text-base">
              {{ post.summary || translations.blog.no_summary }}
            </p>

            <!-- Meta info - Using relative_updated_at from backend -->
            <div class="flex justify-between items-center text-sm mt-auto pt-4 border-t border-muted/60">
              <span class="text-muted-foreground">{{ translations.blog.last_updated }} {{ post.relative_updated_at }}</span>
              <span class="text-muted-foreground bg-muted/40 px-2 py-0.5 rounded-full text-xs">{{ post.views }} {{ translations.blog.views }}</span>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- List view -->
      <div v-if="isList && posts && posts.data && posts.data.length > 0">
        <div class="space-y-4">
          <div 
            v-for="post in posts.data" 
            :key="post.id" 
            class="border border-border rounded-lg p-4 hover:shadow-md transition-all flex gap-4 cursor-pointer"
            @click="navigateToPost(post.slug)"
          >
            <!-- Featured image (small) -->
            <div v-if="post.featured_image" class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md">
              <img :src="/storage/ + post.featured_image" :alt="post.title" class="w-full h-full object-cover" />
            </div>
            <div v-else class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md bg-gradient-to-br from-primary/5 to-primary/20 dark:from-primary/10 dark:to-primary/30 flex items-center justify-center">
              <Avatar class="h-12 w-12 bg-primary/20 text-primary dark:bg-primary/30">
                <AvatarFallback class="text-lg font-semibold">{{ post.title.substring(0, 2).toUpperCase() }}</AvatarFallback>
              </Avatar>
            </div>
            
            <!-- Content -->
            <div class="flex-1 flex flex-col min-w-0">
              <div class="flex justify-between items-start gap-2">
                <!-- Title and category -->
                <div>
                  <div v-if="post.category" class="mb-1">
                    <Button 
                      :as="Link" 
                      :href="route('blog.category', post.category.slug)" 
                      variant="outline"
                      size="sm"
                      class="rounded-full bg-primary/10 text-primary hover:bg-primary/20 hover:text-primary text-xs"
                      @click.stop
                    >
                      {{ post.category.name }}
                    </Button>
                  </div>
                  <h2 class="text-xl font-bold text-card-foreground line-clamp-1">
                    {{ post.title }}
                  </h2>
                </div>
                
                <!-- Meta info -->
                <div class="flex gap-3 text-xs text-muted-foreground flex-shrink-0">
                  <span class="whitespace-nowrap">{{ post.relative_updated_at }}</span>
                  <span class="bg-muted/40 px-2 py-0.5 rounded-full whitespace-nowrap">{{ post.views }} {{ translations.blog.views }}</span>
                </div>
              </div>
              
              <!-- Summary -->
              <p class="text-muted-foreground mt-2 line-clamp-2 text-sm">
                {{ post.summary || translations.blog.no_summary }}
              </p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Empty state -->
      <Card v-if="!posts || !posts.data || posts.data.length === 0" class="text-center py-16 bg-muted/20 rounded-xl border-dashed">
        <CardContent>
          <p class="text-muted-foreground text-xl">{{ translations.blog.no_posts }}</p>
        </CardContent>
      </Card>

      <!-- Pagination -->
      <div v-if="posts && posts.last_page > 1" class="mt-16 flex justify-center">
        <nav class="flex items-center space-x-2 p-2 rounded-lg bg-card shadow-sm">
          <!-- Previous page button -->
          <Button 
            v-if="posts.prev_page_url"
            :as="Link"
            :href="posts.prev_page_url"
            variant="outline"
            size="sm"
            class="w-auto px-3 rounded-md"
          >
            &larr; {{ translations.blog.previous }}
          </Button>
          
          <!-- Page numbers -->
          <template v-if="posts.links && posts.links.length">
            <Button 
              v-for="(link, i) in posts.links.filter(link => !link.label.includes('Previous') && !link.label.includes('Next'))" 
              :key="i"
              :as="Link"
              :href="link.url"
              :variant="link.active ? 'default' : 'ghost'"
              size="sm"
              class="w-10 h-10 rounded-md"
              v-html="link.label"
            />
          </template>
          
          <!-- Next page button -->
          <Button 
            v-if="posts.next_page_url"
            :as="Link"
            :href="posts.next_page_url"
            variant="outline"
            size="sm"
            class="w-auto px-3 rounded-md"
          >
            {{ translations.blog.next }} &rarr;
          </Button>
        </nav>
      </div>
    </div>
  </DefaultLayout>
</template>