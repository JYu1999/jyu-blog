<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function index(Request $request): Response
    {
        $locale = $request->input('locale', app()->getLocale());
        
        $title = Setting::getValue('about_title', 'About Me', $locale);
        $content = Setting::getValue('about_content', '', $locale);
        
        return Inertia::render('About', [
            'title' => $title,
            'content' => $content,
        ]);
    }
}
