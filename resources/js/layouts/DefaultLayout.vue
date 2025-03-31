<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Moon, Sun, Menu, X, Globe } from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';
import { 
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu';

// Get translations reactively
const page = usePage();
const pageTranslations = computed(() => page.props.translations);

const mobileNavOpen = ref(false);
const currentLocale = ref('en');
const { appearance, updateAppearance } = useAppearance();
const darkMode = ref(appearance.value === 'dark');

// Check if code is running in browser environment
const isBrowser = typeof window !== 'undefined';

// Initialize locale from localStorage or use browser default
const initializeLocale = () => {
  if (!isBrowser) return; // Skip in SSR context
  
  try {
    // Try to get from localStorage first
    const savedLocale = localStorage.getItem('locale');
    if (savedLocale) {
      currentLocale.value = savedLocale;
      document.documentElement.lang = savedLocale;
    } else {
      // Try to detect from browser
      const browserLang = navigator.language;
      // Set a default locale based on browser language
      if (browserLang.startsWith('zh')) {
        if (browserLang.includes('CN') || browserLang.includes('Hans')) {
          currentLocale.value = 'zh-CN';
        } else {
          currentLocale.value = 'zh';
        }
      } else if (browserLang.startsWith('ja')) {
        currentLocale.value = 'ja';
      } else if (browserLang.startsWith('vi')) {
        currentLocale.value = 'vi';
      } else {
        currentLocale.value = 'en'; // Default to English
      }
      
      document.documentElement.lang = currentLocale.value;
      localStorage.setItem('locale', currentLocale.value);
    }
  } catch (error) {
    console.error('Error setting locale:', error);
  }
};

onMounted(() => {
  if (!isBrowser) return;
  
  try {
    // Watch for appearance changes
    watch(appearance, (newValue) => {
      darkMode.value = newValue === 'dark';
    });
    
    // Initialize locale
    initializeLocale();
  } catch (error) {
    console.error('Error during initialization:', error);
  }
});

const toggleDarkMode = () => {
  const newMode = darkMode.value ? 'light' : 'dark';
  updateAppearance(newMode);
  darkMode.value = !darkMode.value;
};

const toggleMobileNav = () => {
  mobileNavOpen.value = !mobileNavOpen.value;
};

const setLocale = (locale: string) => {
  currentLocale.value = locale;
  
  if (isBrowser) {
    document.documentElement.lang = locale;
    localStorage.setItem('locale', locale);
    
    // Redirect to the home page with the new locale parameter
    const currentPath = window.location.pathname;
    const currentQuery = new URLSearchParams(window.location.search);
    
    // Set the new locale
    currentQuery.set('locale', locale);
    
    // Create the new URL
    const newUrl = `${currentPath}?${currentQuery.toString()}`;
    
    // Navigate to the new URL
    window.location.href = newUrl;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <!-- Logo -->
            <Link :href="route('home')" class="flex-shrink-0 flex items-center">
              <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ pageTranslations.blog.blog_title }}</h1>
            </Link>
          </div>

          <!-- Desktop Navigation -->
          <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-8">
            <Link :href="route('blog.index')" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
              {{ pageTranslations.blog.blog }}
            </Link>
            <Link :href="route('about')" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
              {{ pageTranslations.blog.about }}
            </Link>

            <!-- Language Dropdown -->
            <div class="relative">
              <DropdownMenu>
                <DropdownMenuTrigger class="flex items-center px-3 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-md text-sm font-medium">
                  <Globe class="h-5 w-5 mr-1" />
                  <span class="sr-only">{{ pageTranslations.blog.language }}</span>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                  <DropdownMenuItem @click="() => setLocale('en')">
                    <span class="flex items-center">
                      <span class="mr-2">🇺🇸</span> English
                    </span>
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="() => setLocale('zh')">
                    <span class="flex items-center">
                      <span class="mr-2">🇹🇼</span> 繁體中文
                    </span>
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="() => setLocale('zh-CN')">
                    <span class="flex items-center">
                      <span class="mr-2">🇨🇳</span> 简体中文
                    </span>
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="() => setLocale('ja')">
                    <span class="flex items-center">
                      <span class="mr-2">🇯🇵</span> 日本語
                    </span>
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="() => setLocale('vi')">
                    <span class="flex items-center">
                      <span class="mr-2">🇻🇳</span> Tiếng Việt
                    </span>
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>

            <!-- Dark mode toggle -->
            <button @click="toggleDarkMode" class="ml-4 p-2 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
              <Moon v-if="!darkMode" class="h-5 w-5" />
              <Sun v-else class="h-5 w-5" />
            </button>
          </div>

          <!-- Mobile menu button -->
          <div class="flex items-center sm:hidden">
            <button @click="toggleMobileNav" class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
              <Menu v-if="!mobileNavOpen" class="h-6 w-6" />
              <X v-else class="h-6 w-6" />
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <div v-if="mobileNavOpen" class="sm:hidden py-2 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div class="px-2 pt-2 pb-3 space-y-1">
          <Link :href="route('blog.index')" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
            {{ pageTranslations.blog.blog }}
          </Link>
          <Link :href="route('about')" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
            {{ pageTranslations.blog.about }}
          </Link>
          
          <!-- Language selector -->
          <div class="border-t border-gray-200 dark:border-gray-700 mt-2 pt-2">
            <div class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400">
              {{ pageTranslations.blog.language }}
            </div>
            
            <button 
              @click="() => setLocale('en')"
              class="w-full flex justify-start px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <span class="mr-2">🇺🇸</span> English
            </button>
            <button 
              @click="() => setLocale('zh')"
              class="w-full flex justify-start px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <span class="mr-2">🇹🇼</span> 繁體中文
            </button>
            <button 
              @click="() => setLocale('zh-CN')"
              class="w-full flex justify-start px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <span class="mr-2">🇨🇳</span> 简体中文
            </button>
            <button 
              @click="() => setLocale('ja')"
              class="w-full flex justify-start px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <span class="mr-2">🇯🇵</span> 日本語
            </button>
            <button 
              @click="() => setLocale('vi')"
              class="w-full flex justify-start px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <span class="mr-2">🇻🇳</span> Tiếng Việt
            </button>
          </div>

          <!-- Dark mode toggle (mobile) -->
          <button @click="toggleDarkMode" class="w-full flex justify-start px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 border-t border-gray-200 dark:border-gray-700 mt-2">
            <Moon v-if="!darkMode" class="h-5 w-5 mr-2" />
            <Sun v-else class="h-5 w-5 mr-2" />
            <span>{{ darkMode ? pageTranslations.blog.light_mode : pageTranslations.blog.dark_mode }}</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main>
      <slot></slot>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-8">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <div class="mb-4 md:mb-0">
            <p class="text-gray-600 dark:text-gray-300">
              &copy; {{ new Date().getFullYear() }} {{ pageTranslations.blog.blog_title }}. {{ pageTranslations.blog.copyright }}
            </p>
          </div>
          <div class="flex space-x-6">
            <a href="#" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
              <!-- Github icon could go here -->
              {{ pageTranslations.blog.github }}
            </a>
            <a href="#" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
              <!-- LinkedIn icon could go here -->
              {{ pageTranslations.blog.linkedin }}
            </a>
            <a href="#" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
              <!-- Twitter/X icon could go here -->
              {{ pageTranslations.blog.twitter }}
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
