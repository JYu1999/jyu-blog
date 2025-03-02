<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';

interface Category {
  id: number;
  name: string;
  slug: string;
  description: string | null;
}

interface Post {
  id: number;
  title: string;
  slug: string;
  summary: string | null;
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
  category: Category;
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
}

const props = defineProps<Props>();

const navigateToPost = (slug: string) => {
  window.location.href = route('blog.show', slug);
};
</script>

<template>
  <Head>
    <title>{{ category.name }} - JYu's Blog</title>
    <meta name="description" :content="`Articles in the ${category.name} category`" />
  </Head>

  <DefaultLayout>
    <div class="container mx-auto py-16 px-4 sm:px-6 lg:px-8">
      <div class="mb-16 max-w-2xl mx-auto">
        <div class="bg-primary/10 dark:bg-primary/20 rounded-full px-4 py-1 inline-block mb-4">
          <span class="text-primary text-sm font-medium">Category</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">{{ category.name }}</h1>
        <p v-if="category.description" class="text-xl text-muted-foreground leading-relaxed">{{ category.description }}</p>
        <p class="mt-6">
          <Button 
            :as="Link"
            :href="route('blog.index')"
            variant="outline"
            size="sm"
            class="rounded-full"
          >
            &larr; Back to all articles
          </Button>
        </p>
      </div>

      <!-- Posts grid -->
      <div v-if="posts && posts.data && posts.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
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
            <!-- Title -->
            <h2 class="text-2xl font-bold mb-3 text-card-foreground line-clamp-2">
              {{ post.title }}
            </h2>

            <!-- Summary -->
            <p class="text-muted-foreground mb-5 flex-grow line-clamp-3 text-base">
              {{ post.summary || 'No summary available.' }}
            </p>

            <!-- Meta info - Using relative_updated_at from backend -->
            <div class="flex justify-between items-center text-sm mt-auto pt-4 border-t border-muted/60">
              <span class="text-muted-foreground">Last updated: {{ post.relative_updated_at }}</span>
              <span class="text-muted-foreground bg-muted/40 px-2 py-0.5 rounded-full text-xs">{{ post.views }} views</span>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Empty state -->
      <Card v-else class="text-center py-16 bg-muted/20 rounded-xl border-dashed">
        <CardContent>
          <p class="text-muted-foreground text-xl">No posts found in this category. Check back soon!</p>
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
            &larr; 上一頁
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
            下一頁 &rarr;
          </Button>
        </nav>
      </div>
    </div>
  </DefaultLayout>
</template>