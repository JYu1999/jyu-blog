<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function index(): Response
    {
        $title = Setting::getValue('about_title', 'About Me');
        $content = Setting::getValue('about_content', '');
        
        return Inertia::render('About', [
            'title' => $title,
            'content' => $content,
        ]);
    }
}
