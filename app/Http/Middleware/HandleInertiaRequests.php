<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        
        // Get locale from (in order of priority):
        // 1. Request 'locale' query parameter
        // 2. Session
        // 3. X-Locale header
        // 4. Default app locale
        $locale = $request->query('locale'); 
        
        if (!$locale) {
            $locale = $request->session()->get('locale');
        }
        
        if (!$locale) {
            $locale = $request->header('X-Locale', config('app.locale'));
        }
        
        // Set application locale and save to session if it's a valid locale
        if (in_array($locale, ['en', 'zh', 'zh-CN', 'ja', 'vi'])) {
            app()->setLocale($locale);
            $request->session()->put('locale', $locale);
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'locale' => app()->getLocale(),
            'availableLocales' => [
                'en' => 'English',
                'zh' => '繁體中文',
                'zh-CN' => '简体中文',
                'ja' => '日本語',
                'vi' => 'Tiếng Việt',
            ],
            'translations' => [
                'blog' => trans('blog'),
            ],
        ];
    }
}
