<?php

namespace App\Providers;

use CzProject\GitPhp\Git;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'git',
            fn ($app) => (new Git())->open(env('GITHUB_WORKSPACE'))
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
