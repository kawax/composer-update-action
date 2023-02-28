<?php

namespace App\Providers;

use CzProject\GitPhp\Git;
use Github\Client;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            'git',
            fn ($app) => (new Git())->open(env('GITHUB_WORKSPACE'))
        );

        $this->app->singleton(Client::class, Client::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Process::macro('composer', function (string $path): PendingProcess {
            return Process::path($path)
                          ->timeout(600)
                          ->env([
                              'COMPOSER_MEMORY_LIMIT' => '-1',
                          ]);
        });
    }
}
