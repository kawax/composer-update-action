<?php

namespace App\Providers;

use Cz\Git\IGit;
use Cz\Git\GitRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            IGit::class,
            function ($app) {
                return new GitRepository(env('GITHUB_WORKSPACE'));
            }
        );
    }
}
