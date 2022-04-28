<?php

namespace App\Commands;

use App\Actions\PackagesUpdate;
use App\Actions\Token;
use App\Actions\Update;
use App\Facades\Git;
use CzProject\GitPhp\GitException;
use Github\AuthMethod;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

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
    protected string $parent_branch;

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
            return; // @codeCoverageIgnore
        }

        if (filled(env('COMPOSER_PACKAGES'))) {
            $output = app(PackagesUpdate::class)->basePath($this->base_path)->run();
        } else {
            $output = app(Update::class)->basePath($this->base_path)->run();
        }

        $this->output($output);

        if (! Git::hasChanges()) {
            $this->info('No changes after update.'); // @codeCoverageIgnore

            return; // @codeCoverageIgnore
        }

        $this->commitPush();

        $this->createPullRequest();
    }

    /**
     * @return void
     *
     * @throws GitException
     */
    protected function init(): void
    {
        $this->info('Initializing ...');

        $this->repo = env('GITHUB_REPOSITORY', '');

        $this->base_path = env('GITHUB_WORKSPACE', '').env('COMPOSER_PATH', '');

        $this->parent_branch = Git::getCurrentBranchName();

        $this->new_branch = 'cu/'.Str::random(8);
        if (env('APP_SINGLE_BRANCH')) {
            $this->new_branch = $this->parent_branch.env('APP_SINGLE_BRANCH_POSTFIX', '-updated');

            $this->info('Using single-branch approach. Branch name: "'.$this->new_branch.'"');
        }

        $token = env('GITHUB_TOKEN');

        GitHub::authenticate($token, AuthMethod::ACCESS_TOKEN);

        Git::setRemoteUrl(
            'origin',
            "https://{$token}@github.com/{$this->repo}.git"
        );

        Git::execute('config', '--local', 'user.name', env('GIT_NAME', 'cu'));
        Git::execute('config', '--local', 'user.email', env('GIT_EMAIL', 'cu@composer-update'));

        $this->info('Fetching from remote.');

        Git::fetch('origin');

        if (
            ! env('APP_SINGLE_BRANCH')
            || ! in_array('remotes/origin/'.$this->new_branch, Git::getBranches() ?? [])
        ) {
            $this->info('Creating branch "'.$this->new_branch.'".');

            Git::createBranch($this->new_branch, true);
        } elseif (env('APP_SINGLE_BRANCH')) {
            $this->info('Checking out branch "'.$this->new_branch.'".');

            Git::checkout($this->new_branch);

            $this->info('Pulling from origin.');

            Git::pull('origin');

            $this->info('Merging from "'.$this->parent_branch.'".');

            Git::merge($this->parent_branch, [
                '--strategy-option=theirs',
                '--quiet',
            ]);
        }

        $this->token();
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
     * Set GitHub token for composer.
     *
     * @return void
     */
    protected function token(): void
    {
        app(Token::class)->basePath($this->base_path)->run();
    }

    /**
     * @param  string  $output
     * @return void
     */
    protected function output(string $output): void
    {
        $this->out = Str::of($output)
                        ->explode(PHP_EOL)
                        ->filter(fn ($item) => Str::contains($item, ' - '))
                        ->reject(fn ($item) => Str::contains($item, 'Downloading '))
                        ->takeUntil(fn ($item) => Str::contains($item, ':'))
                        ->implode(PHP_EOL).PHP_EOL;

        $this->line($this->out);
    }

    /**
     * @return void
     *
     * @throws GitException
     */
    protected function commitPush(): void
    {
        $this->info('Committing changes ...');

        Git::addAllChanges()
           ->commit(env('GIT_COMMIT_PREFIX', '').'composer update '.today()->toDateString().PHP_EOL.PHP_EOL.$this->out)
           ->push(['origin', $this->new_branch]);
    }

    /**
     * @return void
     */
    protected function createPullRequest(): void
    {
        $this->info('Pull Request');

        $date = env('APP_SINGLE_BRANCH') ? '' : ' '.today()->toDateString();

        $pullData = [
            'base' => Str::afterLast(env('GITHUB_REF'), '/'),
            'head' => $this->new_branch,
            'title' => env('GIT_COMMIT_PREFIX', '').'Composer update with '
                .(count(explode(PHP_EOL, $this->out)) - 1).' changes'
                .$date,
            'body' => $this->out,
        ];

        $createPullRequest = true;

        if (env('APP_SINGLE_BRANCH')) {
            $pullRequests = Github::pullRequest()->all(
                Str::before($this->repo, '/'),
                Str::afterLast($this->repo, '/'),
                [
                    'head' => Str::before($this->repo, '/').':'.$this->new_branch,
                    'state' => 'open',
                ]
            );

            if (count($pullRequests) > 0) {
                $createPullRequest = false;
            }
        }

        if ($createPullRequest) {
            $result = GitHub::pullRequest()->create(
                Str::before($this->repo, '/'),
                Str::afterLast($this->repo, '/'),
                $pullData
            );

            $this->info('Pull request created for branch "'.$this->new_branch.'": '.$result['html_url']);
        } else {
            $this->info('Pull request already exists for branch "'.$this->new_branch.'": '.$pullRequests[0]['html_url']);
        }
    }
}
