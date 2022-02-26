<?php

namespace App\Providers;

use CzProject\GitPhp\Git;
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
            'git',
            fn ($app) => (new Git())->open(env('GITHUB_WORKSPACE'))
        );

        $this->app->bind(
            'process.update',
            fn ($app) => new Process($this->update())
        );

        $this->app->bind(
            'process.update-packages',
            fn ($app) => new Process($this->packages())
        );

        $this->app->bind(
            'process.token',
            fn ($app) => new Process($this->token())
        );
    }

    /**
     * @return array
     */
    private function update(): array
    {
        return [
            'composer',
            'update',
            '--no-interaction',
            '--no-progress',
            '--no-autoloader',
            '--no-scripts',
        ];
    }

    /**
     * @return array
     */
    private function packages(): array
    {
        return [
            'composer',
            'update',
            env('COMPOSER_PACKAGES'), // @codeCoverageIgnore
            '--with-dependencies',
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
            env('GITHUB_TOKEN'), // @codeCoverageIgnore
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
