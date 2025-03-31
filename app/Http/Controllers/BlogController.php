<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogController extends Controller
{
    /**
     * Get common filter parameters from request
     * 
     * @param Request $request
     * @return array
     */
    private function getFilterParams(Request $request): array
    {
        return [
            'sort' => $request->input('sort', 'updated'),
            'direction' => $request->input('direction', 'desc'),
            'search' => $request->input('search', ''),
            'view' => $request->input('view', 'gallery'),
            'locale' => $request->input('locale', app()->getLocale()),
        ];
    }
    
    public function index(Request $request)
    {
        $filters = $this->getFilterParams($request);
        
        $posts = Post::with(['category'])
            ->published()
            ->applyBlogFilters($filters)
            ->paginate(12)
            ->withQueryString();
            
        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'filters' => $filters,
            'availableLocales' => config('app.supported_locales'),
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
    
    public function byCategory($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $filters = $this->getFilterParams($request);
        
        $posts = Post::with(['category'])
            ->published()
            ->where('category_id', $category->id)
            ->applyBlogFilters($filters)
            ->paginate(12)
            ->withQueryString();
            
        return Inertia::render('Blog/Category', [
            'category' => $category,
            'posts' => $posts,
            'filters' => $filters,
            'availableLocales' => config('app.supported_locales'),
        ]);
    }
    
    public function byTag($slug, Request $request)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        $filters = $this->getFilterParams($request);
        
        $posts = $tag->posts()
            ->with(['category'])
            ->published()
            ->applyBlogFilters($filters)
            ->paginate(12)
            ->withQueryString();
            
        return Inertia::render('Blog/Tag', [
            'tag' => $tag,
            'posts' => $posts,
            'filters' => $filters,
            'availableLocales' => config('app.supported_locales'),
        ]);
    }
}
