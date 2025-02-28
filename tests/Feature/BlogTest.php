<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_displays_blog_posts(): void
    {
        // Create test data
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test category description',
        ]);

        $post = Post::create([
            'title' => 'Test Blog Post',
            'slug' => 'test-blog-post',
            'content' => 'This is a test blog post content.',
            'summary' => 'Test summary',
            'category_id' => $category->id,
            'status' => 'published',
        ]);

        // Visit the homepage
        $response = $this->get('/');

        // Assert successful response
        $response->assertStatus(200);
        
        // Assert that the page contains our post title
        $response->assertSee('Test Blog Post');
    }

    public function test_single_post_page_displays_correctly(): void
    {
        // Create test data
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test category description',
        ]);

        $post = Post::create([
            'title' => 'Test Blog Post',
            'slug' => 'test-blog-post',
            'content' => 'This is a test blog post content.',
            'summary' => 'Test summary',
            'category_id' => $category->id,
            'status' => 'published',
        ]);

        // Visit the single post page
        $response = $this->get('/blog/test-blog-post');

        // Assert successful response
        $response->assertStatus(200);
        
        // Assert that the page contains post data
        $response->assertSee('Test Blog Post');
        $response->assertSee('This is a test blog post content.');
    }

    public function test_category_page_displays_correctly(): void
    {
        // Create test data
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test category description',
        ]);

        $post = Post::create([
            'title' => 'Test Blog Post',
            'slug' => 'test-blog-post',
            'content' => 'This is a test blog post content.',
            'summary' => 'Test summary',
            'category_id' => $category->id,
            'status' => 'published',
        ]);

        // Visit the category page
        $response = $this->get('/category/test-category');

        // Assert successful response
        $response->assertStatus(200);
        
        // Assert that the page contains category and post data
        $response->assertSee('Test Category');
        $response->assertSee('Test Blog Post');
    }

    public function test_tag_page_displays_correctly(): void
    {
        // Create test data
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $post = Post::create([
            'title' => 'Test Blog Post',
            'slug' => 'test-blog-post',
            'content' => 'This is a test blog post content.',
            'summary' => 'Test summary',
            'category_id' => $category->id,
            'status' => 'published',
        ]);

        $tag = Tag::create([
            'name' => 'Test Tag',
            'slug' => 'test-tag',
        ]);

        $post->tags()->attach($tag->id);

        // Visit the tag page
        $response = $this->get('/tag/test-tag');

        // Assert successful response
        $response->assertStatus(200);
        
        // Assert that the page contains tag and post data
        $response->assertSee('Test Tag');
        $response->assertSee('Test Blog Post');
    }
}