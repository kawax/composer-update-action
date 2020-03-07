<?php

namespace App\Commands;

use Cz\Git\GitException;
use Cz\Git\GitRepository;
use Symfony\Component\Process\Process;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
     * @var GitRepository
     */
    protected $git;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $repo;

    /**
     * @var string
     */
    protected $repo_owner;

    /**
     * @var string
     */
    protected $repo_name;

    /**
     * @var string
     */
    protected $random;

    /**
     * @var string
     */
    protected $base_path;

    /**
     * @var string
     */
    protected $composer_path;

    /**
     * @var string
     */
    protected $branch;

    /**
     * @var string
     */
    protected $target_branch;

    /**
     * @var string
     */
    protected $out;

    /**
     * Execute the console command.
     *
     * @throws GitException
     */
    public function handle()
    {
        $this->init();

        $this->process('install');

        $output = $this->process('update');

        $this->output($output);

        if (! $this->git->hasChanges()) {
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

        $this->token = env('GITHUB_TOKEN');
        GitHub::authenticate($this->token, 'http_token');

        $this->repo = env('GITHUB_REPOSITORY');

        $this->repo_owner = Str::before($this->repo, '/');
        $this->repo_name = Str::afterLast($this->repo, '/');

        $this->base_path = env('GITHUB_WORKSPACE');
        $this->composer_path = env('COMPOSER_PATH', '');

        $this->random = Str::random(8);
        $this->branch = 'cu/'.$this->random;

        $this->target_branch = Str::afterLast(env('GITHUB_REF'), '/');

        try {
            $this->git = new GitRepository($this->base_path);

            $this->git->setRemoteUrl(
                'origin',
                'https://'.env('GITHUB_ACTOR').':'.$this->token.'@github.com/'.$this->repo.'.git'
            );

            $this->git->execute(['config', '--local', 'user.name', env('GIT_NAME', 'cu')]);
            $this->git->execute(['config', '--local', 'user.email', env('GIT_EMAIL', 'cu@composer-update')]);

            $this->git->createBranch($this->branch, true);
        } catch (GitException $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @param  string  $command
     *
     * @return string
     * @throws ProcessFailedException
     */
    protected function process(string $command)
    {
        $this->info($command);

        $exec = implode(
            ' ',
            [
                'composer',
                $command,
                '--no-interaction',
                '--no-progress',
                '--no-suggest',
                '--no-autoloader',
                '--no-scripts',
            ]
        );

        $process = Process::fromShellCommandline($exec)
                          ->setWorkingDirectory($this->base_path.$this->composer_path)
                          ->setTimeout(600)
                          ->mustRun();

        $output = $process->getOutput();
        if (blank($output)) {
            $output = $process->getErrorOutput();
        }

        return $output;
    }

    /**
     * @param  string|null  $output
     */
    protected function output(?string $output)
    {
        $output = explode(PHP_EOL, $output);

        $this->out = collect($output)
                ->filter(fn($item) => Str::contains($item, ' - '))
                ->map(fn($item) => trim($item))
                ->implode(PHP_EOL).PHP_EOL;
    }

    /**
     * Commit and Push.
     *
     * @throws GitException
     */
    protected function commitPush()
    {
        $this->info('commit');

        $this->git->addAllChanges()
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
            'base'  => $this->target_branch,
            'head'  => $this->branch,
            'title' => 'composer update '.today()->toDateString(),
            'body'  => $this->out,
        ];

        GitHub::pullRequest()->create(
            $this->repo_owner,
            $this->repo_name,
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
