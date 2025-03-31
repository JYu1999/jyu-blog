<script setup lang="ts">
import { Head, usePage, router } from '@inertiajs/vue3';
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import { computed, ref } from 'vue';
import { Card, CardContent } from '@/components/ui/card';
import TableOfContents from '@/components/blog/TableOfContents.vue';
import LanguageSelector from '@/components/LanguageSelector.vue';
import MarkdownRenderer from '@/components/blog/MarkdownRenderer.vue';
import { useLanguage } from '@/composables/useLanguage';

interface Props {
  title: string;
  content: string;
}

const props = defineProps<Props>();

// Get translations reactively
const page = usePage();
const translations = computed(() => page.props.translations);

// Use the language composable
const { currentLang } = useLanguage();

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
        <LanguageSelector />
      </div>
      
      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">{{ title }}</h1>
      </div>
      
      <!-- Main Content with Table of Contents -->
      <div class="flex flex-col lg:flex-row gap-8 max-w-7xl mx-auto">
        <!-- Post Content -->
        <Card class="flex-1">
          <CardContent class="p-6 lg:p-8" v-if="content">
            <MarkdownRenderer :content="content" />
          </CardContent>
          <CardContent v-else class="text-center py-12">
            <p class="text-muted-foreground text-xl">No content available yet.</p>
          </CardContent>
        </Card>
        
        <!-- Table of Contents (Desktop) - Using the component -->
        <div class="w-64 hidden lg:block sticky top-24 self-start">
          <TableOfContents 
            contentSelector=".prose" 
            :title="translations?.blog?.contents || 'Contents'"
          />
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

