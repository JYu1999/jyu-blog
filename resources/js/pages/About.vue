<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import { marked } from 'marked';
import { computed } from 'vue';

interface Props {
  title: string;
  content: string;
}

const props = defineProps<Props>();

// Convert markdown to HTML
const renderedContent = computed(() => {
  return props.content ? marked(props.content, { 
    breaks: true,
    gfm: true // Enable GitHub Flavored Markdown
  }) : '';
});
</script>

<template>
  <Head>
    <title>{{ title }} - JYu's Blog</title>
    <meta name="description" :content="title">
  </Head>

  <DefaultLayout>
    <div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ title }}</h1>
      </div>
      
      <div class="max-w-3xl mx-auto">
        <div v-if="content" class="prose prose-lg dark:prose-invert max-w-none" v-html="renderedContent"></div>
        <div v-else class="text-center py-12">
          <p class="text-gray-600 dark:text-gray-300 text-lg">No content available yet.</p>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<style>
/* Mirror Markdown styling from Blog/Show.vue */
.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
  @apply text-gray-900 dark:text-white;
}

.prose p, .prose li {
  @apply text-gray-700 dark:text-gray-300;
}

.prose pre {
  @apply bg-gray-800 text-gray-100 dark:bg-gray-900 dark:text-gray-100 p-4 rounded-md overflow-x-auto my-4;
}

/* Fix code blocks rendering issue */
.prose pre code {
  @apply bg-transparent p-0 text-gray-100 dark:text-gray-100 inline-block w-full;
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