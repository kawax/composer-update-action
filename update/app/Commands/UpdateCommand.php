<?php

namespace App\Commands;

use App\Facades\Git;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class UpdateCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'update';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'composer update';

    /**
     * @var string
     */
    protected string $repo;

    /**
     * @var string
     */
    protected string $base_path;

    /**
     * @var string
     */
    protected string $composer_path;

    /**
     * @var string
     */
    protected string $branch;

    /**
     * @var string
     */
    protected string $out;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->init();

        if (! $this->exists()) {
            return;
        }

        $this->process('install');

        $output = $this->process('update');

        $this->output($output);

        if (! Git::hasChanges()) {
            $this->info('no changes');

            return;
        }

        $this->commitPush();

        $this->createPullRequest();
    }

    /**
     * init.
     */
    protected function init()
    {
        $this->info('init');

        $this->repo = env('GITHUB_REPOSITORY', '');

        $this->base_path = env('GITHUB_WORKSPACE', '');
        $this->composer_path = env('COMPOSER_PATH', '');

        $this->branch = 'cu/'.Str::random(8);

        $token = env('GITHUB_TOKEN');

        GitHub::authenticate($token, 'http_token');

        Git::setRemoteUrl(
            'origin',
            'https://'.$token.'@github.com/'.$this->repo.'.git'
        );

        Git::execute(['config', '--local', 'user.name', env('GIT_NAME', 'cu')]);
        Git::execute(['config', '--local', 'user.email', env('GIT_EMAIL', 'cu@composer-update')]);

        Git::createBranch($this->branch, true);
    }

    /**
     * @return bool
     */
    protected function exists(): bool
    {
        $path = $this->base_path.$this->composer_path;

        if (! File::exists($path.'/composer.json')) {
            return false;
        }

        if (! File::exists($path.'/composer.lock')) {
            return false;
        }

        return true;
    }

    /**
     * @param  string  $command
     *
     * @return string
     * @throws ProcessFailedException
     */
    protected function process(string $command): string
    {
        $this->info($command);

        /**
         * @var Process $process
         */
        $process = app('process.'.$command)
            ->setWorkingDirectory($this->base_path.$this->composer_path)
            ->setTimeout(600)
            ->mustRun();

        $output = $process->getOutput();
        if (blank($output)) {
            $output = $process->getErrorOutput();
        }

        return $output ?? '';
    }

    /**
     * @param  string|null  $output
     */
    protected function output(?string $output)
    {
        $output = explode(PHP_EOL, $output);

        $this->out = collect($output)
                ->filter(fn ($item) => Str::contains($item, ' - '))
                ->map(fn ($item) => trim($item))
                ->implode(PHP_EOL).PHP_EOL;
    }

    /**
     * Commit and Push.
     */
    protected function commitPush()
    {
        $this->info('commit');

        Git::addAllChanges()
           ->commit('composer update '.today()->toDateString().PHP_EOL.PHP_EOL.$this->out)
           ->push('origin', [$this->branch]);
    }

    /**
     * Create Pull Request.
     */
    protected function createPullRequest()
    {
        $this->info('Pull Request');

        $pullData = [
            'base'  => Str::afterLast(env('GITHUB_REF'), '/'),
            'head'  => $this->branch,
            'title' => 'composer update '.today()->toDateString(),
            'body'  => $this->out,
        ];

        GitHub::pullRequest()->create(
            Str::before($this->repo, '/'),
            Str::afterLast($this->repo, '/'),
            $pullData
        );
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
