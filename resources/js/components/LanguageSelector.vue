<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="px-2 h-8">
        <span class="text-lg">{{ getCurrentLanguageFlag }}</span>
        <span class="sr-only">Language</span>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuLabel>Language</DropdownMenuLabel>
      <DropdownMenuItem
        v-for="lang in availableLanguages"
        :key="lang.value"
        @click="updateLocale(lang.value)"
        :disabled="currentLang === lang.value"
      >
        <span class="mr-2">{{ lang.flag }}</span>
        {{ lang.label }}
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useLanguage } from '@/composables/useLanguage';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

const { currentLang, availableLanguages, updateLocale } = useLanguage();

const getCurrentLanguageFlag = computed(() => {
  const lang = availableLanguages.find(l => l.value === currentLang.value);
  return lang ? lang.flag : '🌐';
});
</script>