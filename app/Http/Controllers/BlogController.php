<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::with(['category'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        return Inertia::render('Blog/Index', [
            'posts' => $posts,
        ]);
    }
    
    public function show($slug)
    {
        $post = Post::with(['category', 'tags'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
            
        $post->incrementViews();
        
        return Inertia::render('Blog/Show', [
            'post' => $post,
        ]);
    }
    
    public function byCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $posts = Post::with(['category'])
            ->where('category_id', $category->id)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        return Inertia::render('Blog/Category', [
            'category' => $category,
            'posts' => $posts,
        ]);
    }
    
    public function byTag($slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        
        $posts = $tag->posts()
            ->with(['category'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        return Inertia::render('Blog/Tag', [
            'tag' => $tag,
            'posts' => $posts,
        ]);
    }
}
