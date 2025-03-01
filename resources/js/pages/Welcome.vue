<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-gradient-to-br from-background to-muted p-6 text-foreground lg:justify-center lg:p-8">
        <header class="mb-6 w-full max-w-[335px] text-sm lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Button 
                    v-if="$page.props.auth.user"
                    :as="Link"
                    :href="route('dashboard')"
                    variant="outline"
                    size="sm"
                >
                    Dashboard
                </Button>
                <template v-else>
                    <Button 
                        :as="Link"
                        :href="route('login')"
                        variant="ghost"
                        size="sm"
                    >
                        Log in
                    </Button>
                    <Button 
                        :as="Link"
                        :href="route('register')"
                        variant="outline"
                        size="sm"
                    >
                        Register
                    </Button>
                </template>
            </nav>
        </header>
        <div class="flex w-full flex-col items-center justify-center lg:flex-row lg:grow">
            <div class="text-center mb-12 lg:mb-0 lg:text-left lg:mr-16 max-w-lg">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-primary to-primary/80">JYu's Blog</h1>
                <p class="text-xl text-muted-foreground mb-6">Welcome to my personal space where I share my thoughts, projects and explorations.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <Button
                        :as="Link"
                        :href="route('blog.index')"
                        size="lg"
                        class="rounded-full"
                    >
                        Read Blog
                    </Button>
                    <Button
                        :as="Link"
                        :href="route('about')"
                        variant="outline"
                        size="lg"
                        class="rounded-full"
                    >
                        About Me
                    </Button>
                </div>
            </div>
            <div
                class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary/5 to-primary/30 dark:from-primary/10 dark:to-primary/20 aspect-square w-full max-w-md shadow-lg border border-primary/10"
            >
                <!-- Blog Decorator Image -->
                <div class="absolute inset-0 flex items-center justify-center opacity-70">
                    <svg width="240" height="240" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-primary">
                        <path d="M4 19.5V4.5C4 3.67157 4.67157 3 5.5 3H18.5C19.3284 3 20 3.67157 20 4.5V19.5C20 20.3284 19.3284 21 18.5 21H5.5C4.67157 21 4 20.3284 4 19.5Z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M8 7H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M8 12H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M8 17H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                
                <!-- Decorative circles -->
                <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-primary/10 blur-2xl"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full bg-primary/10 blur-2xl"></div>
            </div>
        </div>
        <footer class="mt-12 text-center text-muted-foreground text-sm">
            &copy; 2025 JYu's Blog. All rights reserved.
        </footer>
    </div>
</template>

<style>
/* Add some custom styling for the welcome page */
@media (prefers-color-scheme: dark) {
  .bg-gradient-glow {
    background-image: radial-gradient(circle at 50% 50%, rgba(var(--primary), 0.1), transparent 25%);
  }
}
</style>