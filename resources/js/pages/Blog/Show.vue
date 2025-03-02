<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import { computed } from 'vue';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import TableOfContents from '@/components/blog/TableOfContents.vue';
import MarkdownRenderer from '@/components/blog/MarkdownRenderer.vue';

// Get translations reactively
const page = usePage();
const translations = computed(() => page.props.translations);

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
  relative_updated_at: string;
  category: Category | null;
  tags: Tag[];
}

interface Props {
  post: Post;
}

const props = defineProps<Props>();
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
        
        <!-- Meta info - Using relative_updated_at from backend -->
        <div class="text-sm text-muted-foreground mb-8 bg-muted/50 inline-block px-4 py-1.5 rounded-full">
          <span>{{ translations.blog.last_updated }} {{ post.relative_updated_at }}</span>
          <span class="mx-2">•</span>
          <span>{{ post.views }} {{ translations.blog.views }}</span>
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
            <MarkdownRenderer :content="post.content" />
            
            <!-- Tags -->
            <div v-if="post.tags && post.tags.length > 0" class="mt-12">
              <Separator class="my-6" />
              <h2 class="text-lg font-semibold text-card-foreground mb-4">{{ translations.blog.tag }}</h2>
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
        
        <!-- Table of Contents (Desktop) - Using the component -->
        <div class="w-64 hidden lg:block sticky top-24 self-start">
          <TableOfContents 
            contentSelector=".prose" 
            :title="translations.blog.contents || 'Contents'"
          />
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
          &larr; {{ translations.blog.back_to_all }}
        </Button>
      </div>
    </article>
  </DefaultLayout>
</template>

