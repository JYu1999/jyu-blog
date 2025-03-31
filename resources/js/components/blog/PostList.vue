<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';

interface Category {
  id: number;
  name: string;
  slug: string;
}

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
  category: Category | null;
}

interface Props {
  posts: Post[];
  viewMode: string;
  translations: any;
}

const props = defineProps<Props>();

// Computed properties for view mode
const isList = computed(() => props.viewMode === 'list');
const isGallery = computed(() => props.viewMode === 'gallery');

// Method to navigate to post
const navigateToPost = (slug: string) => {
  window.location.href = route('blog.show', slug);
};
</script>

<template>
  <!-- Gallery view -->
  <div 
    v-if="isGallery && posts && posts.length > 0" 
    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <Card 
      v-for="post in posts" 
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
  <div v-if="isList && posts && posts.length > 0">
    <div class="space-y-4">
      <div 
        v-for="post in posts" 
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
  <Card v-if="!posts || posts.length === 0" class="text-center py-16 bg-muted/20 rounded-xl border-dashed">
    <CardContent>
      <p class="text-muted-foreground text-xl">{{ translations.blog.no_posts }}</p>
    </CardContent>
  </Card>
</template>