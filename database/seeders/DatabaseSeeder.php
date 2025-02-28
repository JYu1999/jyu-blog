<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        
        // Create categories
        $categories = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Articles about technology, programming, and digital innovations.',
            ],
            [
                'name' => 'Personal',
                'slug' => 'personal',
                'description' => 'Personal thoughts, experiences, and reflections.',
            ],
            [
                'name' => 'Tutorials',
                'slug' => 'tutorials',
                'description' => 'Step by step guides and tutorials on various subjects.',
            ],
        ];
        
        foreach ($categories as $category) {
            Category::create($category);
        }
        
        // Create tags
        $tags = [
            ['name' => 'Laravel', 'slug' => 'laravel'],
            ['name' => 'Vue', 'slug' => 'vue'],
            ['name' => 'PHP', 'slug' => 'php'],
            ['name' => 'JavaScript', 'slug' => 'javascript'],
            ['name' => 'Web Development', 'slug' => 'web-development'],
            ['name' => 'DevOps', 'slug' => 'devops'],
        ];
        
        foreach ($tags as $tag) {
            Tag::create($tag);
        }
        
        // Create sample posts
        $posts = [
            [
                'title' => 'Getting Started with Laravel and Vue',
                'slug' => 'getting-started-with-laravel-and-vue',
                'content' => "# Getting Started with Laravel and Vue

Laravel and Vue make a powerful combination for full-stack development. In this post, we'll explore the basics of setting up a Laravel project with Vue.js integration.

## Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js and npm

## Laravel Installation

First, let's install Laravel using Composer:

```bash
composer create-project laravel/laravel example-app
cd example-app
```

## Vue.js Integration

Laravel now comes with Inertia.js, which makes it easy to build single-page Vue applications:

```bash
php artisan inertia:install vue
```

## Building Your First Component

Create your Vue components in the `resources/js/Pages` directory, and use them through Inertia in your controllers.

Example component:

```vue
<script setup>
import { ref } from 'vue';

const message = ref('Hello World!');
</script>

<template>
  <h1>{{ message }}</h1>
</template>
```

## Conclusion

Laravel and Vue together offer a seamless development experience for modern web applications, combining the best of back-end and front-end technologies.
",
                'summary' => 'A beginner-friendly guide to setting up a Laravel project with Vue.js integration for full-stack development.',
                'category_id' => 3, // Tutorials
                'status' => 'published',
                'views' => 42,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(4),
            ],
            [
                'title' => 'Deploying Laravel Applications with GitHub Actions',
                'slug' => 'deploying-laravel-applications-with-github-actions',
                'content' => "# Deploying Laravel Applications with GitHub Actions

In this tutorial, we'll walk through setting up a CI/CD pipeline for a Laravel application using GitHub Actions.

## Why GitHub Actions?

GitHub Actions provides powerful automation capabilities right within your GitHub repository. It allows you to:

- Run tests automatically on each push
- Deploy your application when changes are merged into specific branches
- Maintain code quality through automated checks

## Setting Up Your Workflow

Create a `.github/workflows/deploy.yml` file in your repository:

```yaml
name: Deploy Application

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          
      - name: Install dependencies
        run: composer install --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist
        
      - name: Run tests
        run: php artisan test
        
      - name: Deploy to production
        uses: appleboy/ssh-action@master
        with:
          host: \${{ secrets.HOST }}
          username: \${{ secrets.USERNAME }}
          key: \${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /path/to/your/project
            git pull origin main
            composer install --no-interaction --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan optimize
```

## Securing Your Secrets

Make sure to add your deployment secrets in your GitHub repository settings:

1. Go to Settings > Secrets and variables > Actions
2. Add your `HOST`, `USERNAME`, and `SSH_PRIVATE_KEY` values

## Conclusion

With GitHub Actions, you can automate your Laravel deployment process, ensuring that only properly tested code makes it to production.
",
                'summary' => 'Learn how to set up continuous integration and deployment for Laravel applications using GitHub Actions.',
                'category_id' => 1, // Technology
                'status' => 'published',
                'views' => 78,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(9),
            ],
            [
                'title' => 'Why I Switched to Vue from React',
                'slug' => 'why-i-switched-to-vue-from-react',
                'content' => "# Why I Switched to Vue from React

After years of using React for front-end development, I recently made the switch to Vue.js. Here's my personal journey and the reasons behind this decision.

## My Background

I've been working with React since 2017, building various applications from small business websites to complex enterprise dashboards. React served me well for years, but I kept hearing about Vue's simplicity and elegant design.

## The Turning Point

The release of Vue 3 with the Composition API was what finally convinced me to give it a serious try. The Composition API offers similar capabilities to React Hooks, but in my opinion, with a cleaner and more intuitive design.

## What I Love About Vue

### 1. Simplicity and Readability

Vue templates are HTML-like and very intuitive. The separation of concerns between template, script, and style makes code organization straightforward:

```vue
<template>
  <div>{{ message }}</div>
</template>

<script setup>
import { ref } from 'vue'
const message = ref('Hello Vue!')
</script>

<style scoped>
div {
  color: green;
}
</style>
```

### 2. Official Supporting Libraries

Vue's core team maintains Vue Router and Pinia (state management), ensuring they work seamlessly with the framework. In React, you often need to choose between multiple third-party solutions.

### 3. Documentation

Vue's documentation is exceptionally clear and beginner-friendly, making the learning curve much gentler.

## Still Appreciating React

This isn't to say React isn't excellent - it absolutely is. React:
- Has a larger ecosystem
- Is more widely used in enterprises
- Offers more job opportunities

## Conclusion

Both frameworks are powerful tools for building modern web applications. My switch to Vue was based on personal preference for its design philosophy and developer experience. The best framework is ultimately the one that makes you most productive and happy as a developer.

What's your experience with different JavaScript frameworks? I'd love to hear your thoughts!
",
                'summary' => 'My personal journey switching from React to Vue.js, exploring the features that made me prefer Vue for new projects.',
                'category_id' => 2, // Personal
                'status' => 'published',
                'views' => 124,
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(14),
            ],
        ];
        
        foreach ($posts as $post) {
            Post::create($post);
        }
        
        // Assign tags to posts
        $post1 = Post::where('slug', 'getting-started-with-laravel-and-vue')->first();
        $post1->tags()->attach([1, 2, 3, 4]); // Laravel, Vue, PHP, JavaScript
        
        $post2 = Post::where('slug', 'deploying-laravel-applications-with-github-actions')->first();
        $post2->tags()->attach([1, 5, 6]); // Laravel, Web Development, DevOps
        
        $post3 = Post::where('slug', 'why-i-switched-to-vue-from-react')->first();
        $post3->tags()->attach([2, 4, 5]); // Vue, JavaScript, Web Development
    }
}
