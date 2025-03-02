<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';

interface HeadingItem {
  id: string;
  text: string;
  level: number;
}

interface Props {
  contentSelector: string;
  title?: string;
  observerOptions?: IntersectionObserverInit;
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Contents',
  observerOptions: () => ({ rootMargin: '-100px 0px -80% 0px' })
});

const tableOfContents = ref<HeadingItem[]>([]);
const activeHeading = ref<string | null>(null);

const generateTableOfContents = () => {
  setTimeout(() => {
    const contentEl = document.querySelector(props.contentSelector);
    if (!contentEl) return;
    
    const headings = contentEl.querySelectorAll('h1, h2, h3, h4, h5, h6');
    const toc: HeadingItem[] = [];
    
    headings.forEach((heading, index) => {
      const headingElement = heading as HTMLElement;
      const text = headingElement.textContent || '';
      const level = parseInt(headingElement.tagName.substring(1));
      
      // Create ID from heading text if none exists
      if (!headingElement.id) {
        const id = `heading-${index}-${text.toLowerCase().replace(/[^\w]+/g, '-')}`;
        headingElement.id = id;
      }
      
      toc.push({ 
        id: headingElement.id, 
        text, 
        level 
      });
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
      props.observerOptions
    );
    
    headings.forEach((heading) => observer.observe(heading));
  }, 100); // Small delay to ensure content is rendered
};

onMounted(() => {
  generateTableOfContents();
});

// Re-generate TOC when content changes
watch(() => props.contentSelector, () => {
  generateTableOfContents();
});

// Expose data for parent components
defineExpose({
  tableOfContents,
  activeHeading
});
</script>

<template>
  <div v-if="tableOfContents.length > 0" class="rounded-lg bg-card p-4 shadow-sm border">
    <h3 class="text-lg font-semibold mb-3 text-card-foreground">{{ title }}</h3>
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
</template>

<style scoped>
/* Table of contents styling */
.toc a {
  @apply text-gray-700 dark:text-gray-300;
}

.toc a:hover {
  @apply text-primary dark:text-primary;
}
</style>