<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\PostFile;

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
        //
        Paginator::useBootstrap(config('app.url'));

        View::composer(['layouts.nav', 'layouts.app2', 'profile.popup'], function ($view) {
            $user = Auth::user();
            $img = null;

            if ($user && $user->profile_img) {
                $img = [
                    'pathDate'     => explode('_', $user->profile_img->savename)[0],
                    'savename'     => $user->profile_img->savename,
                ];
            }

            $view->with('img', $img);
        });
    }
}
