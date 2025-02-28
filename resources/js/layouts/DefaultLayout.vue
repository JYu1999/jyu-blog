<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Moon, Sun, Menu, X } from 'lucide-vue-next';

const darkMode = ref(false);
const mobileNavOpen = ref(false);

onMounted(() => {
  try {
    // Check if the user prefers dark mode
    if (localStorage.getItem('darkMode') === 'true' ||
        (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      darkMode.value = true;
      document.documentElement.classList.add('dark');
    }
  } catch (error) {
    console.error('Error setting dark mode:', error);
  }
});

watch(darkMode, (newValue) => {
  try {
    if (newValue) {
      document.documentElement.classList.add('dark');
      localStorage.setItem('darkMode', 'true');
    } else {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('darkMode', 'false');
    }
  } catch (error) {
    console.error('Error updating dark mode:', error);
  }
});

const toggleDarkMode = () => {
  darkMode.value = !darkMode.value;
};

const toggleMobileNav = () => {
  mobileNavOpen.value = !mobileNavOpen.value;
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
              <h1 class="text-xl font-bold text-gray-900 dark:text-white">JYu's Blog</h1>
            </Link>
          </div>

          <!-- Desktop Navigation -->
          <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-8">
            <Link :href="route('blog.index')" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
              Blog
            </Link>
            <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
              About
            </a>
            <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
              Contact
            </a>

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
            Blog
          </Link>
          <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
            About
          </a>

          <!-- Dark mode toggle (mobile) -->
          <button @click="toggleDarkMode" class="w-full flex justify-start px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
            <Moon v-if="!darkMode" class="h-5 w-5 mr-2" />
            <Sun v-else class="h-5 w-5 mr-2" />
            <span>{{ darkMode ? 'Light Mode' : 'Dark Mode' }}</span>
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
              &copy; {{ new Date().getFullYear() }} JYu's Blog. All rights reserved.
            </p>
          </div>
          <div class="flex space-x-6">
            <a href="#" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
              <!-- Github icon could go here -->
              Github
            </a>
            <a href="#" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
              <!-- LinkedIn icon could go here -->
              LinkedIn
            </a>
            <a href="#" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
              <!-- Twitter/X icon could go here -->
              Twitter
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
