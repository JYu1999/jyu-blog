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
  filters?: {
    sort: string;
    direction: string;
    search: string;
    view: string;
    locale: string;
  };
  availableLocales?: Record<string, string>;
}

const props = defineProps<Props>();

import { ref } from 'vue';
import BlogFilters from '@/components/blog/BlogFilters.vue';
import PostList from '@/components/blog/PostList.vue';
import BlogPagination from '@/components/blog/BlogPagination.vue';

// Create a ref to access the BlogFilters component
const blogFilters = ref();
</script>

<template>
  <Head>
    <title>{{ category.name }} - JYu's Blog</title>
    <meta name="description" :content="`Articles in the ${category.name} category`" />
  </Head>

  <DefaultLayout>
    <div class="container mx-auto py-16 px-4 sm:px-6 lg:px-8">
      <div class="mb-10 max-w-2xl mx-auto">
        <div class="bg-primary/10 dark:bg-primary/20 rounded-full px-4 py-1 inline-block mb-4">
          <span class="text-primary text-sm font-medium">{{ translations.blog.category }}</span>
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
&larr; {{ translations.blog.back_to_all }}
          </Button>
        </p>
      </div>
      
      <!-- Filter controls -->
      <BlogFilters 
        :filters="filters" 
        routeName="blog.category" 
        :routeParams="{ slug: category.slug }" 
        :translations="translations" 
        ref="blogFilters"
      />
      
      <!-- Post list -->
      <PostList 
        :posts="posts.data" 
        :viewMode="blogFilters?.viewMode" 
        :translations="translations" 
      />

      <!-- Pagination -->
      <BlogPagination 
        :pagination="posts" 
        :translations="translations" 
      />
    </div>
  </DefaultLayout>
</template>