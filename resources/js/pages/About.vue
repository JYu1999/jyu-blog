<script setup lang="ts">
import { Head, usePage, router } from '@inertiajs/vue3';
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import { marked } from 'marked';
import { computed, onMounted, ref } from 'vue';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { 
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu';

interface Props {
  title: string;
  content: string;
}

const props = defineProps<Props>();
const tableOfContents = ref<{id: string, text: string, level: number}[]>([]);
const activeHeading = ref<string | null>(null);

// Get translations reactively
const page = usePage();
const translations = computed(() => page.props.translations);

// For language selector
const currentLang = ref(page.props.locale || 'en');

// Update locale and reload page
const updateLocale = (locale: string) => {
  currentLang.value = locale;
  if (typeof localStorage !== 'undefined') {
    localStorage.setItem('locale', locale);
    document.documentElement.lang = locale;
  }
  
  // Reload with new locale
  router.get(route('about'), { locale }, {
    preserveState: true,
    replace: true
  });
};

// Convert markdown to HTML
const renderedContent = computed(() => {
  return props.content ? marked(props.content, { 
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
    <title>{{ title }} - JYu's Blog</title>
    <meta name="description" :content="title">
  </Head>

  <DefaultLayout>
    <div class="container mx-auto py-16 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-end mb-4">
        <!-- Language selector -->
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="outline" size="sm" class="gap-2">
              <span v-if="currentLang === 'en'">🇺🇸 English</span>
              <span v-else-if="currentLang === 'zh'">🇹🇼 繁體中文</span>
              <span v-else-if="currentLang === 'zh-CN'">🇨🇳 简体中文</span>
              <span v-else-if="currentLang === 'ja'">🇯🇵 日本語</span>
              <span v-else-if="currentLang === 'vi'">🇻🇳 Tiếng Việt</span>
              <span v-else>🌐 {{ translations?.blog?.language || 'Language' }}</span>
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
      
      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">{{ title }}</h1>
      </div>
      
      <!-- Main Content with Table of Contents -->
      <div class="flex flex-col lg:flex-row gap-8 max-w-7xl mx-auto">
        <!-- Post Content -->
        <Card class="flex-1">
          <CardContent class="p-6 lg:p-8" v-if="content">
            <div class="prose prose-lg md:prose-xl dark:prose-invert max-w-none" v-html="renderedContent"></div>
          </CardContent>
          <CardContent v-else class="text-center py-12">
            <p class="text-muted-foreground text-xl">No content available yet.</p>
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
    </div>
  </DefaultLayout>
</template>

<style>
/* Enhanced typography for content */
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