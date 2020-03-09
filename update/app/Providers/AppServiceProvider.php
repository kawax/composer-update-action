<?php

namespace App\Providers;

use Cz\Git\GitRepository;
use Cz\Git\IGit;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Process\Process;

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

        $this->app->bind(
            'process.install',
            function ($app) {
                return new Process($this->command('install'));
            }
        );

        $this->app->bind(
            'process.update',
            function ($app) {
                return new Process($this->command('update'));
            }
        );
    }

    /**
     * @param  string  $cmd
     *
     * @return array
     */
    private function command(string $cmd): array
    {
        return [
            'composer',
            $cmd,
            '--no-interaction',
            '--no-progress',
            '--no-suggest',
            '--no-autoloader',
            '--no-scripts',
        ];
    }
}
