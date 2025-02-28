<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import { marked } from 'marked';
import { ref, computed } from 'vue';

interface Tag {
  id: number;
  name: string;
  slug: string;
}

interface Category {
  id: number;
  name: string;
  slug: string;
}

interface Post {
  id: number;
  title: string;
  slug: string;
  content: string;
  summary: string | null;
  featured_image: string | null;
  category_id: number | null;
  status: string;
  views: number;
  created_at: string;
  updated_at: string;
  category: Category | null;
  tags: Tag[];
}

interface Props {
  post: Post;
}

const props = defineProps<Props>();

// Convert markdown to HTML
const renderedContent = computed(() => {
  return props.post && props.post.content ? marked(props.post.content, { breaks: true }) : '';
});
</script>

<template>
  <Head>
    <title>{{ post && post.title ? post.title : 'Loading...' }} - JYu's Blog</title>
    <meta name="description" :content="post && post.summary ? post.summary : post && post.title ? post.title : 'Loading post content...'">
  </Head>

  <DefaultLayout>
    <article class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <!-- Post Header -->
      <header class="mb-8 text-center">
        <!-- Category -->
        <div v-if="post.category" class="mb-4">
          <Link :href="route('blog.category', post.category.slug)" 
              class="text-blue-600 dark:text-blue-400 hover:underline">
            {{ post.category.name }}
          </Link>
        </div>
        
        <!-- Title -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
          {{ post.title }}
        </h1>
        
        <!-- Meta info -->
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-8">
          <span>{{ new Date(post.created_at).toLocaleDateString() }}</span>
          <span class="mx-2">•</span>
          <span>{{ post.views }} views</span>
        </div>
        
        <!-- Featured image -->
        <div v-if="post.featured_image" class="mb-8 max-h-96 overflow-hidden rounded-lg">
          <img :src="post.featured_image" :alt="post.title" class="w-full object-cover" />
        </div>
      </header>
      
      <!-- Post Content -->
      <div class="max-w-3xl mx-auto">
        <!-- Summary if available -->
        <div v-if="post.summary" class="mb-8 text-xl text-gray-600 dark:text-gray-300 italic border-l-4 border-blue-500 pl-4 py-2">
          {{ post.summary }}
        </div>
        
        <!-- Main content -->
        <div class="prose prose-lg dark:prose-invert max-w-none" v-html="renderedContent"></div>
        
        <!-- Tags -->
        <div v-if="post.tags && post.tags.length > 0" class="mt-12 pt-6 border-t border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tags</h2>
          <div class="flex flex-wrap gap-2">
            <Link 
              v-for="tag in post.tags" 
              :key="tag.id" 
              :href="route('blog.tag', tag.slug)"
              class="px-4 py-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
            >
              {{ tag.name }}
            </Link>
          </div>
        </div>
      </div>
      
      <!-- Back to blog -->
      <div class="max-w-3xl mx-auto mt-12 pt-8 border-t border-gray-200 dark:border-gray-700 text-center">
        <Link :href="route('blog.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          &larr; Back to all articles
        </Link>
      </div>
    </article>
  </DefaultLayout>
</template>

<style>
/* Additional styling for markdown content if needed */
.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
  @apply text-gray-900 dark:text-white;
}

.prose p, .prose li {
  @apply text-gray-700 dark:text-gray-300;
}

.prose pre {
  @apply bg-gray-800 text-gray-100 dark:bg-gray-900 dark:text-gray-100 p-4 rounded-md overflow-x-auto;
}

.prose code {
  @apply bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded;
}

.prose blockquote {
  @apply border-l-4 border-blue-500 pl-4 py-1 text-gray-600 dark:text-gray-400 italic;
}

.prose a {
  @apply text-blue-600 dark:text-blue-400 hover:underline;
}

.prose img {
  @apply rounded-lg max-h-96 mx-auto;
}
</style>