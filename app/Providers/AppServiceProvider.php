<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure Carbon's diffForHumans for Traditional Chinese
        Carbon::setLocale(App::getLocale());
        
        // Override Traditional Chinese (zh) localization to ensure it uses traditional characters
        if (App::getLocale() === 'zh') {
            Carbon::setTranslations('zh', [
                'year' => '年',
                'years' => '年',
                'month' => '個月',
                'months' => '個月',
                'week' => '週',
                'weeks' => '週',
                'day' => '天',
                'days' => '天',
                'hour' => '小時',
                'hours' => '小時',
                'minute' => '分鐘',
                'minutes' => '分鐘',
                'second' => '秒',
                'seconds' => '秒',
                'ago' => '前',
                'from_now' => '後',
                'after' => '後',
                'before' => '前',
            ]);
        }
    }
}
