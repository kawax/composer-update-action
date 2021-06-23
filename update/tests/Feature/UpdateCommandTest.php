<?php

namespace Tests\Feature;

use App\Facades\Git;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Mockery as m;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class UpdateCommandTest extends TestCase
{
    public function testUpdateCommand()
    {
        Git::shouldReceive('getCurrentBranchName')->once()->andReturn('');

        GitHub::shouldReceive('authenticate')->once();

        Git::shouldReceive('setRemoteUrl')->once();
        Git::shouldReceive('execute')->twice();
        Git::shouldReceive('fetch')->once();
        Git::shouldReceive('createBranch')->once();

        File::shouldReceive('exists')->twice()->andReturnTrue();

        $this->instance(
            'process.update',
            m::mock(
                Process::class,
                function ($mock) {
                    $mock->shouldReceive('setWorkingDirectory->setTimeout->setEnv->mustRun->getOutput')->once()->andReturn(
                        'test'
                    );
                }
            )
        );

        $this->instance(
            'process.token',
            m::mock(
                Process::class,
                function ($mock) {
                    $mock->shouldReceive('setWorkingDirectory->setTimeout->mustRun')->once()->andReturnSelf();
                }
            )
        );

        Git::shouldReceive('hasChanges')->andReturnTrue();
        Git::shouldReceive('addAllChanges->commit->push')->once();

        GitHub::shouldReceive('pullRequest->create')->once()->andReturn([
            'html_url' => 'https://',
        ]);

        $this->artisan('update')
             ->expectsOutput('Initializing ...')
             ->assertExitCode(0);
    }

    public function testUpdatePackagesCommand()
    {
        $_ENV['COMPOSER_PACKAGES'] = 'laravel/*';

        Git::shouldReceive('getCurrentBranchName')->once()->andReturn('');

        GitHub::shouldReceive('authenticate')->once();

        Git::shouldReceive('setRemoteUrl')->once();
        Git::shouldReceive('execute')->twice();
        Git::shouldReceive('fetch')->once();
        Git::shouldReceive('createBranch')->once();

        File::shouldReceive('exists')->twice()->andReturnTrue();

        $this->instance(
            'process.update-packages',
            m::mock(
                Process::class,
                function ($mock) {
                    $mock->shouldReceive('setWorkingDirectory->setTimeout->setEnv->mustRun->getOutput')->once()->andReturn(
                        'test'
                    );
                }
            )
        );

        $this->instance(
            'process.token',
            m::mock(
                Process::class,
                function ($mock) {
                    $mock->shouldReceive('setWorkingDirectory->setTimeout->mustRun')->once()->andReturnSelf();
                }
            )
        );

        Git::shouldReceive('hasChanges')->andReturnTrue();
        Git::shouldReceive('addAllChanges->commit->push')->once();

        GitHub::shouldReceive('pullRequest->create')->once()->andReturn([
            'html_url' => 'https://',
        ]);

        $this->artisan('update')
             ->expectsOutput('Initializing ...')
             ->assertExitCode(0);
    }

    public function testUpdateSingleCommand()
    {
        $_ENV['APP_SINGLE_BRANCH'] = true;

        Git::shouldReceive('getCurrentBranchName')->once()->andReturn('test');

        GitHub::shouldReceive('authenticate')->once();

        Git::shouldReceive('setRemoteUrl')->once();
        Git::shouldReceive('execute')->twice();
        Git::shouldReceive('fetch')->once();
        Git::shouldReceive('getBranches')->once()->andReturn([
            'remotes/origin/test-updated',
        ]);

        Git::shouldReceive('checkout')->once();
        Git::shouldReceive('pull')->once();
        Git::shouldReceive('merge')->once();

        File::shouldReceive('exists')->twice()->andReturnTrue();

        $this->instance(
            'process.update',
            m::mock(
                Process::class,
                function ($mock) {
                    $mock->shouldReceive('setWorkingDirectory->setTimeout->setEnv->mustRun->getOutput')->once()->andReturn(
                        'test'
                    );
                }
            )
        );

        $this->instance(
            'process.token',
            m::mock(
                Process::class,
                function ($mock) {
                    $mock->shouldReceive('setWorkingDirectory->setTimeout->mustRun')->once()->andReturnSelf();
                }
            )
        );

        Git::shouldReceive('hasChanges')->andReturnTrue();
        Git::shouldReceive('addAllChanges->commit->push')->once();

        GitHub::shouldReceive('pullRequest->all')->once()->andReturn([
            [
                'html_url' => 'https://',
            ],
        ]);

        $this->artisan('update')
             ->expectsOutput('Initializing ...')
             ->assertExitCode(0);
    }

    public function testFluentStrings()
    {
        $str = (string) Str::of(' - Updating laravel/framework (v7.0.0 => v7.1.0): Loading from cache')->beforeLast(
            ':'
        )->trim();

        $this->assertEquals('- Updating laravel/framework (v7.0.0 => v7.1.0)', $str);
    }
}
