<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            // Force root URL to use HTTPS
            $this->app['url']->forceRootUrl(config('app.url'));
        }

        // Share canonical URL to all views
        view()->composer('*', function ($view) {
            $canonicalUrl = url()->current();
            $view->with('canonicalUrl', $canonicalUrl);
        });
    }
}
