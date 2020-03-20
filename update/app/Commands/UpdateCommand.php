<?php

namespace App\Commands;

use App\Facades\Git;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
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
    protected string $new_branch;

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
     * @return void
     */
    protected function init(): void
    {
        $this->info('init');

        $this->repo = env('GITHUB_REPOSITORY', '');

        $this->base_path = env('GITHUB_WORKSPACE', '').env('COMPOSER_PATH', '');

        $this->new_branch = 'cu/'.Str::random(8);

        $token = env('GITHUB_TOKEN');

        GitHub::authenticate($token, 'http_token');

        Git::setRemoteUrl(
            'origin',
            "https://{$token}@github.com/{$this->repo}.git"
        );

        Git::execute(['config', '--local', 'user.name', env('GIT_NAME', 'cu')]);
        Git::execute(['config', '--local', 'user.email', env('GIT_EMAIL', 'cu@composer-update')]);

        Git::createBranch($this->new_branch, true);
    }

    /**
     * @return bool
     */
    protected function exists(): bool
    {
        return File::exists($this->base_path.'/composer.json')
            && File::exists($this->base_path.'/composer.lock');
    }

    /**
     * @param  string  $command
     *
     * @return string
     */
    protected function process(string $command): string
    {
        $this->info($command);

        /**
         * @var Process $process
         */
        $process = app('process.'.$command)
            ->setWorkingDirectory($this->base_path)
            ->setTimeout(600)
            ->mustRun();

        $output = $process->getOutput();
        if (blank($output)) {
            $output = $process->getErrorOutput();
        }

        return $output ?? '';
    }

    /**
     * @param  string  $output
     *
     * @return void
     */
    protected function output(string $output): void
    {
        $this->out = Str::of($output)
                        ->explode(PHP_EOL)
                        ->filter(fn ($item) => Str::contains($item, ' - '))
                        ->map(fn ($item) => (string) Str::of($item)->beforeLast(':')->trim())
                        ->implode(PHP_EOL).PHP_EOL;

        $this->line($this->out);
    }

    /**
     * @return void
     */
    protected function commitPush(): void
    {
        $this->info('commit');

        Git::addAllChanges()
           ->commit('composer update '.today()->toDateString().PHP_EOL.PHP_EOL.$this->out)
           ->push('origin', [$this->new_branch]);
    }

    /**
     * @return void
     */
    protected function createPullRequest(): void
    {
        $this->info('Pull Request');

        $pullData = [
            'base'  => Str::afterLast(env('GITHUB_REF'), '/'),
            'head'  => $this->new_branch,
            'title' => 'composer update '.today()->toDateString(),
            'body'  => $this->out,
        ];

        GitHub::pullRequest()->create(
            Str::before($this->repo, '/'),
            Str::afterLast($this->repo, '/'),
            $pullData
        );
    }
}
