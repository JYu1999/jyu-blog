<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $sort = $request->input('sort', 'updated');
        $direction = $request->input('direction', 'desc');
        $search = $request->input('search', '');
        $view = $request->input('view', 'gallery');
        $locale = $request->input('locale', app()->getLocale());
        
        $postsQuery = Post::with(['category'])
            ->where('status', 'published');
            
        // Apply language filter
        if ($locale) {
            $postsQuery->where('locale', $locale);
        }
            
        // Apply search if provided
        if (!empty($search)) {
            // Check if search is purely numeric (for ID search)
            if (is_numeric($search)) {
                $postsQuery->where(function($query) use ($search) {
                    $query->where('id', $search)
                          ->orWhere('title', 'like', "%{$search}%")
                          ->orWhere('content', 'like', "%{$search}%")
                          ->orWhere('summary', 'like', "%{$search}%");
                });
            } else {
                $postsQuery->where(function($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                          ->orWhere('content', 'like', "%{$search}%")
                          ->orWhere('summary', 'like', "%{$search}%");
                });
            }
        }
        
        // Apply sorting with direction
        $sortDirection = $direction === 'asc' ? 'asc' : 'desc';
        
        switch ($sort) {
            case 'views':
                $postsQuery->orderBy('views', $sortDirection);
                break;
            case 'created':
                $postsQuery->orderBy('created_at', $sortDirection);
                break;
            case 'updated':
            default:
                if ($sortDirection === 'asc') {
                    $postsQuery->orderBy('content_updated_at', 'asc')
                             ->orderBy('created_at', 'asc');
                } else {
                    $postsQuery->orderBy('content_updated_at', 'desc')
                             ->orderBy('created_at', 'desc');
                }
                break;
        }
        
        $posts = $postsQuery->paginate(12)->withQueryString();
            
        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'filters' => [
                'sort' => $sort,
                'direction' => $direction,
                'search' => $search,
                'view' => $view,
                'locale' => $locale,
            ],
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
        
        // Get filter parameters
        $sort = $request->input('sort', 'updated');
        $direction = $request->input('direction', 'desc');
        $search = $request->input('search', '');
        $view = $request->input('view', 'gallery');
        $locale = $request->input('locale', app()->getLocale());
        
        $postsQuery = Post::with(['category'])
            ->where('category_id', $category->id)
            ->where('status', 'published');
            
        // Apply language filter
        if ($locale) {
            $postsQuery->where('locale', $locale);
        }
            
        // Apply search if provided
        if (!empty($search)) {
            // Check if search is purely numeric (for ID search)
            if (is_numeric($search)) {
                $postsQuery->where(function($query) use ($search) {
                    $query->where('id', $search)
                          ->orWhere('title', 'like', "%{$search}%")
                          ->orWhere('content', 'like', "%{$search}%")
                          ->orWhere('summary', 'like', "%{$search}%");
                });
            } else {
                $postsQuery->where(function($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                          ->orWhere('content', 'like', "%{$search}%")
                          ->orWhere('summary', 'like', "%{$search}%");
                });
            }
        }
        
        // Apply sorting with direction
        $sortDirection = $direction === 'asc' ? 'asc' : 'desc';
        
        switch ($sort) {
            case 'views':
                $postsQuery->orderBy('views', $sortDirection);
                break;
            case 'created':
                $postsQuery->orderBy('created_at', $sortDirection);
                break;
            case 'updated':
            default:
                if ($sortDirection === 'asc') {
                    $postsQuery->orderBy('content_updated_at', 'asc')
                             ->orderBy('created_at', 'asc');
                } else {
                    $postsQuery->orderBy('content_updated_at', 'desc')
                             ->orderBy('created_at', 'desc');
                }
                break;
        }
        
        $posts = $postsQuery->paginate(12)->withQueryString();
            
        return Inertia::render('Blog/Category', [
            'category' => $category,
            'posts' => $posts,
            'filters' => [
                'sort' => $sort,
                'direction' => $direction,
                'search' => $search,
                'view' => $view,
                'locale' => $locale,
            ],
            'availableLocales' => config('app.supported_locales'),
        ]);
    }
    
    public function byTag($slug, Request $request)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        
        // Get filter parameters
        $sort = $request->input('sort', 'updated');
        $direction = $request->input('direction', 'desc');
        $search = $request->input('search', '');
        $view = $request->input('view', 'gallery');
        $locale = $request->input('locale', app()->getLocale());
        
        $postsQuery = $tag->posts()
            ->with(['category'])
            ->where('status', 'published');
            
        // Apply language filter
        if ($locale) {
            $postsQuery->where('locale', $locale);
        }
            
        // Apply search if provided
        if (!empty($search)) {
            // Check if search is purely numeric (for ID search)
            if (is_numeric($search)) {
                $postsQuery->where(function($query) use ($search) {
                    $query->where('id', $search)
                          ->orWhere('title', 'like', "%{$search}%")
                          ->orWhere('content', 'like', "%{$search}%")
                          ->orWhere('summary', 'like', "%{$search}%");
                });
            } else {
                $postsQuery->where(function($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                          ->orWhere('content', 'like', "%{$search}%")
                          ->orWhere('summary', 'like', "%{$search}%");
                });
            }
        }
        
        // Apply sorting with direction
        $sortDirection = $direction === 'asc' ? 'asc' : 'desc';
        
        switch ($sort) {
            case 'views':
                $postsQuery->orderBy('views', $sortDirection);
                break;
            case 'created':
                $postsQuery->orderBy('created_at', $sortDirection);
                break;
            case 'updated':
            default:
                if ($sortDirection === 'asc') {
                    $postsQuery->orderBy('content_updated_at', 'asc')
                             ->orderBy('created_at', 'asc');
                } else {
                    $postsQuery->orderBy('content_updated_at', 'desc')
                             ->orderBy('created_at', 'desc');
                }
                break;
        }
        
        $posts = $postsQuery->paginate(12)->withQueryString();
            
        return Inertia::render('Blog/Tag', [
            'tag' => $tag,
            'posts' => $posts,
            'filters' => [
                'sort' => $sort,
                'direction' => $direction,
                'search' => $search,
                'view' => $view,
                'locale' => $locale,
            ],
            'availableLocales' => config('app.supported_locales'),
        ]);
    }
}
