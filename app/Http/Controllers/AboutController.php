<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function index(): Response
    {
        $title = Cache::get('about_title', 'About Me');
        $content = Cache::get('about_content', '');
        
        return Inertia::render('About', [
            'title' => $title,
            'content' => $content,
        ]);
    }
}
