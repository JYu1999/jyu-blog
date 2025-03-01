<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import { marked } from 'marked';
import { ref, computed, onMounted } from 'vue';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';

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
const tableOfContents = ref<{id: string, text: string, level: number}[]>([]);
const activeHeading = ref<string | null>(null);

// Convert markdown to HTML
const renderedContent = computed(() => {
  return props.post && props.post.content ? marked(props.post.content, { 
    breaks: true,
    gfm: true // Enable GitHub Flavored Markdown
  }) : '';
});

// Generate table of contents and add IDs to headings
onMounted(() => {
  setTimeout(() => {
    const contentEl = document.querySelector('.prose');
    if (!contentEl) return;
    
    const headings = contentEl.querySelectorAll('h1, h2, h3, h4, h5, h6');
    const toc: {id: string, text: string, level: number}[] = [];
    
    headings.forEach((heading, index) => {
      const headingElement = heading as HTMLElement;
      const text = headingElement.textContent || '';
      const level = parseInt(headingElement.tagName.substring(1));
      
      // Create ID from heading text
      const id = `heading-${index}-${text.toLowerCase().replace(/[^\w]+/g, '-')}`;
      headingElement.id = id;
      
      toc.push({ id, text, level });
    });
    
    tableOfContents.value = toc;
    
    // Set up intersection observer for active heading
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            activeHeading.value = entry.target.id;
          }
        });
      },
      { rootMargin: '-100px 0px -80% 0px' }
    );
    
    headings.forEach((heading) => observer.observe(heading));
  }, 100); // Small delay to ensure content is rendered
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
      <header class="mb-12 text-center">
        <!-- Category -->
        <div v-if="post.category" class="mb-4">
          <Button 
            :as="Link" 
            :href="route('blog.category', post.category.slug)" 
            variant="outline"
            size="sm"
            class="rounded-full bg-primary/10 text-primary hover:bg-primary/20 hover:text-primary"
          >
            {{ post.category.name }}
          </Button>
        </div>
        
        <!-- Title -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
          {{ post.title }}
        </h1>
        
        <!-- Meta info - Changed to display updated_at instead of created_at -->
        <div class="text-sm text-muted-foreground mb-8 bg-muted/50 inline-block px-4 py-1.5 rounded-full">
          <span>Last updated: {{ new Date(post.updated_at).toLocaleDateString() }}</span>
          <span class="mx-2">•</span>
          <span>{{ post.views }} views</span>
        </div>
        
        <!-- Featured image -->
        <div v-if="post.featured_image" class="mb-10 max-h-[500px] overflow-hidden rounded-xl shadow-lg">
          <img :src="/storage/ + post.featured_image" :alt="post.title" class="w-full object-cover" />
        </div>
      </header>
      
      <!-- Main Content with Table of Contents -->
      <div class="flex flex-col lg:flex-row gap-8 max-w-7xl mx-auto relative">
        <!-- Post Content -->
        <Card class="flex-1">
          <CardContent class="p-6 lg:p-8">
            <!-- Summary if available -->
            <div v-if="post.summary" class="mb-8 text-xl text-muted-foreground border-l-4 border-primary/60 pl-4 py-2 bg-muted/20 rounded-r-md">
              {{ post.summary }}
            </div>
            
            <!-- Main content -->
            <div class="prose prose-lg md:prose-xl dark:prose-invert max-w-none" v-html="renderedContent"></div>
            
            <!-- Tags -->
            <div v-if="post.tags && post.tags.length > 0" class="mt-12">
              <Separator class="my-6" />
              <h2 class="text-lg font-semibold text-card-foreground mb-4">Tags</h2>
              <div class="flex flex-wrap gap-2">
                <Button 
                  v-for="tag in post.tags" 
                  :key="tag.id" 
                  :as="Link" 
                  :href="route('blog.tag', tag.slug)"
                  variant="outline"
                  size="sm"
                  class="rounded-full bg-secondary/40 hover:bg-secondary/60"
                >
                  # {{ tag.name }}
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
        
        <!-- Table of Contents (Desktop) - Now on the right side -->
        <div v-if="tableOfContents.length > 0" class="w-64 hidden lg:block sticky top-24 self-start">
          <div class="rounded-lg bg-card p-4 shadow-sm border">
            <h3 class="text-lg font-semibold mb-3 text-card-foreground">Contents</h3>
            <nav class="toc">
              <ul class="space-y-2">
                <li v-for="heading in tableOfContents" :key="heading.id" 
                    :class="{'pl-2': heading.level === 2, 'pl-4': heading.level === 3, 'pl-6': heading.level > 3}">
                  <a :href="`#${heading.id}`" 
                     class="text-sm hover:text-primary transition-colors block py-1 border-l-2 pl-2"
                     :class="activeHeading === heading.id ? 'border-primary text-primary font-medium' : 'border-transparent'">
                    {{ heading.text }}
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
      
      <!-- Back to blog -->
      <div class="max-w-3xl mx-auto mt-12 text-center">
        <Separator class="my-6" />
        <Button 
          :as="Link" 
          :href="route('blog.index')" 
          variant="outline"
          size="lg"
          class="mt-4"
        >
          &larr; Back to all articles
        </Button>
      </div>
    </article>
  </DefaultLayout>
</template>

<style>
/* Enhanced typography for blog content */
.prose {
  @apply text-gray-800 dark:text-gray-200;
  font-size: 1.125rem;
  line-height: 1.8;
}

/* Headings */
.prose h1 {
  @apply text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mt-10 mb-6;
}

.prose h2 {
  @apply text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-8 mb-5;
}

.prose h3 {
  @apply text-xl md:text-2xl font-semibold text-gray-900 dark:text-white mt-7 mb-4;
}

.prose h4 {
  @apply text-lg md:text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3;
}

.prose h5, .prose h6 {
  @apply text-base md:text-lg font-semibold text-gray-900 dark:text-white mt-5 mb-2;
}

/* Paragraphs and lists */
.prose p, .prose ul, .prose ol {
  @apply my-5;
}

.prose li {
  @apply my-2;
}

/* Code blocks */
.prose pre {
  @apply bg-slate-950 text-slate-100 dark:bg-slate-900 dark:text-slate-100 p-5 rounded-lg overflow-x-auto my-6 shadow-md;
}

/* Fix code blocks rendering issue */
.prose pre code {
  @apply bg-transparent p-0 text-inherit font-mono text-sm md:text-base inline-block w-full;
}

.prose code {
  @apply bg-slate-100 dark:bg-transparent px-1.5 py-0.5 rounded text-slate-800 dark:text-slate-200 font-mono text-sm;
}

/* Blockquotes */
.prose blockquote {
  @apply border-l-4 border-primary/70 pl-5 py-2 bg-muted/30 dark:bg-muted/10 rounded-r-md text-gray-700 dark:text-gray-300 italic my-6;
}

.prose blockquote p {
  @apply my-2;
}

/* Links */
.prose a {
  @apply text-primary dark:text-blue-300 hover:underline decoration-2 underline-offset-2 font-medium;
}

/* Images */
.prose img {
  @apply rounded-lg mx-auto shadow-md my-8 max-w-full;
}

/* Tables */
.prose table {
  @apply w-full my-6 border-collapse;
}

.prose table th {
  @apply bg-muted px-4 py-2 text-left font-semibold border dark:border-gray-700;
}

.prose table td {
  @apply px-4 py-2 border dark:border-gray-700;
}

/* Horizontal rule */
.prose hr {
  @apply my-8 border-t border-gray-200 dark:border-gray-700;
}

/* Table of contents styling */
.toc a {
  @apply text-gray-700 dark:text-gray-300 hover:text-primary dark:hover:text-primary transition-colors;
}

.toc a.active {
  @apply text-primary font-medium;
}
</style>