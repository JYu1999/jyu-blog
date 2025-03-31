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

import { ref } from 'vue';
import BlogFilters from '@/components/blog/BlogFilters.vue';
import PostList from '@/components/blog/PostList.vue';
import BlogPagination from '@/components/blog/BlogPagination.vue';

// Create a ref to access the BlogFilters component
const blogFilters = ref();
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
      <BlogFilters 
        :filters="filters" 
        routeName="blog.index" 
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