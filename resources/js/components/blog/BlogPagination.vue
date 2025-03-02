<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

interface PaginationLinks {
  first: string;
  last: string;
  prev: string | null;
  next: string | null;
}

interface PaginationMeta {
  current_page: number;
  from: number;
  last_page: number;
  path: string;
  per_page: number;
  to: number;
  total: number;
}

interface PaginationData {
  links: PaginationLinks;
  meta: PaginationMeta;
  prev_page_url: string | null;
  next_page_url: string | null;
  last_page: number;
}

interface Props {
  pagination: PaginationData;
  translations: any;
}

const props = defineProps<Props>();
</script>

<template>
  <div v-if="pagination && pagination.last_page > 1" class="mt-16 flex justify-center">
    <nav class="flex items-center space-x-2 p-2 rounded-lg bg-card shadow-sm">
      <!-- Previous page button -->
      <Button 
        v-if="pagination.prev_page_url"
        :as="Link"
        :href="pagination.prev_page_url"
        variant="outline"
        size="sm"
        class="w-auto px-3 rounded-md"
      >
        &larr; {{ translations.blog.previous }}
      </Button>
      
      <!-- Page numbers -->
      <template v-if="pagination.links && pagination.links.length">
        <Button 
          v-for="(link, i) in pagination.links.filter(link => !link.label.includes('Previous') && !link.label.includes('Next'))" 
          :key="i"
          :as="Link"
          :href="link.url"
          :variant="link.active ? 'default' : 'ghost'"
          size="sm"
          class="w-10 h-10 rounded-md"
          v-html="link.label"
        />
      </template>
      
      <!-- Next page button -->
      <Button 
        v-if="pagination.next_page_url"
        :as="Link"
        :href="pagination.next_page_url"
        variant="outline"
        size="sm"
        class="w-auto px-3 rounded-md"
      >
        {{ translations.blog.next }} &rarr;
      </Button>
    </nav>
  </div>
</template>