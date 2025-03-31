<script setup lang="ts">
import { marked } from 'marked';
import { computed } from 'vue';

interface Props {
  content: string;
}

const props = defineProps<Props>();

// Convert markdown to HTML with proper rendering
const renderedContent = computed(() => {
  return props.content ? marked(props.content, { 
    breaks: true,
    gfm: true // Enable GitHub Flavored Markdown
  }) : '';
});
</script>

<template>
  <div class="markdown prose prose-lg md:prose-xl dark:prose-invert max-w-none" v-html="renderedContent"></div>
</template>

<style>
/* Enhanced typography for content */
.markdown.prose {
  @apply text-gray-800 dark:text-gray-200;
  font-size: 1.125rem;
  line-height: 1.8;
}

/* Headings */
.markdown.prose h1 {
  @apply text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mt-10 mb-6;
}

.markdown.prose h2 {
  @apply text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-8 mb-5;
}

.markdown.prose h3 {
  @apply text-xl md:text-2xl font-semibold text-gray-900 dark:text-white mt-7 mb-4;
}

.markdown.prose h4 {
  @apply text-lg md:text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3;
}

.markdown.prose h5, .markdown.prose h6 {
  @apply text-base md:text-lg font-semibold text-gray-900 dark:text-white mt-5 mb-2;
}

/* Paragraphs and lists */
.markdown.prose p, .markdown.prose ul, .markdown.prose ol {
  @apply my-5;
}

.markdown.prose li {
  @apply my-2;
}

/* Improve list rendering */
.markdown.prose ul {
  @apply list-disc pl-6;
}

.markdown.prose ol {
  @apply list-decimal pl-6;
}

.markdown.prose ul ul, .markdown.prose ol ul {
  @apply list-disc my-0 ml-6;
}

.markdown.prose ul ol, .markdown.prose ol ol {
  @apply list-decimal my-0 ml-6;
}

.markdown.prose li::marker {
  @apply text-gray-500 dark:text-gray-400;
}

/* Code blocks */
.markdown.prose pre {
  @apply bg-slate-950 text-slate-100 dark:bg-slate-900 dark:text-slate-100 p-5 rounded-lg overflow-x-auto my-6 shadow-md;
}

/* Fix code blocks rendering issue */
.markdown.prose pre code {
  @apply bg-transparent p-0 text-inherit font-mono text-sm md:text-base inline-block w-full;
}

.markdown.prose code {
  @apply bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-800 dark:text-slate-200 font-mono text-sm;
}

/* Blockquotes */
.markdown.prose blockquote {
  @apply border-l-4 border-primary/70 pl-5 py-2 bg-muted/30 dark:bg-muted/10 rounded-r-md text-gray-700 dark:text-gray-300 italic my-6;
}

.markdown.prose blockquote p {
  @apply my-2;
}

/* Links */
.markdown.prose a {
  @apply text-primary dark:text-blue-300 hover:underline decoration-2 underline-offset-2 font-medium;
}

/* Images */
.markdown.prose img {
  @apply rounded-lg mx-auto shadow-md my-8 max-w-full;
}

/* Tables */
.markdown.prose table {
  @apply w-full my-6 border-collapse;
}

.markdown.prose table th {
  @apply bg-muted px-4 py-2 text-left font-semibold border dark:border-gray-700;
}

.markdown.prose table td {
  @apply px-4 py-2 border dark:border-gray-700;
}

/* Horizontal rule */
.markdown.prose hr {
  @apply my-8 border-t border-gray-200 dark:border-gray-700;
}
</style>