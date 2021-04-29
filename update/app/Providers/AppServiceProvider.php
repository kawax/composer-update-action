<?php

namespace App\Providers;

use Cz\Git\GitRepository;
use Cz\Git\IGit;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Process\Process;

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
            IGit::class,
            fn ($app) => new GitRepository(env('GITHUB_WORKSPACE'))
        );

        $this->app->bind(
            'process.install',
            fn ($app) => new Process($this->command('install'))
        );

        $this->app->bind(
            'process.update',
            fn ($app) => new Process($this->command('update'))
        );

        $this->app->bind(
            'process.token',
            fn ($app) => new Process($this->token())
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
            '--no-autoloader',
            '--no-scripts',
        ];
    }

    /**
     * @return array
     */
    private function token(): array
    {
        return [
            'composer',
            'config',
            '-g',
            'github-oauth.github.com',
            env('GITHUB_TOKEN'),
        ];
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
