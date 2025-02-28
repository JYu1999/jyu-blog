<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/layouts/DefaultLayout.vue';

interface Tag {
  id: number;
  name: string;
  slug: string;
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
  category: {
    id: number;
    name: string;
    slug: string;
  } | null;
}

interface Props {
  tag: Tag;
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
</script>

<template>
  <Head>
    <title>{{ tag.name }} - JYu's Blog</title>
    <meta name="description" :content="`Articles tagged with ${tag.name}`" />
  </Head>

  <DefaultLayout>
    <div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Tag: {{ tag.name }}</h1>
        <p class="mt-4 text-sm text-blue-600 dark:text-blue-400">
          <Link :href="route('blog.index')" class="hover:underline">&larr; Back to all articles</Link>
        </p>
      </div>

      <!-- Posts grid -->
      <div v-if="posts && posts.data && posts.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div v-for="post in posts.data" :key="post.id" 
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden flex flex-col hover:shadow-lg transition-shadow duration-300">
          <!-- Featured image -->
          <div v-if="post.featured_image" class="h-48 overflow-hidden">
            <img :src="post.featured_image" :alt="post.title" class="w-full h-full object-cover" />
          </div>
          <div v-else class="h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
            <span class="text-gray-400 dark:text-gray-500">No image</span>
          </div>

          <!-- Post content -->
          <div class="p-6 flex-grow flex flex-col">
            <!-- Category -->
            <div v-if="post.category" class="mb-2">
              <Link :href="route('blog.category', post.category.slug)" 
                  class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                {{ post.category.name }}
              </Link>
            </div>

            <!-- Title -->
            <h2 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">
              <Link :href="route('blog.show', post.slug)" class="hover:text-blue-600 dark:hover:text-blue-400">
                {{ post.title }}
              </Link>
            </h2>

            <!-- Summary -->
            <p class="text-gray-600 dark:text-gray-300 mb-4 flex-grow">
              {{ post.summary || 'No summary available.' }}
            </p>

            <!-- Meta info -->
            <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mt-auto">
              <span>{{ new Date(post.created_at).toLocaleDateString() }}</span>
              <span>{{ post.views }} views</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else class="text-center py-12">
        <p class="text-gray-600 dark:text-gray-300 text-lg">No posts found with this tag. Check back soon!</p>
      </div>

      <!-- Pagination -->
      <div v-if="posts && posts.meta && posts.meta.last_page > 1" class="mt-12 flex justify-center">
        <nav class="flex items-center space-x-2">
          <Link v-for="page in posts.meta.last_page" :key="page"
              :href="route('blog.tag', { slug: tag.slug, page })"
              class="px-4 py-2 rounded-md"
              :class="{
                'bg-blue-600 text-white': page === posts.meta.current_page,
                'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600': page !== posts.meta.current_page
              }">
            {{ page }}
          </Link>
        </nav>
      </div>
    </div>
  </DefaultLayout>
</template>