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
    /**
     * @return void
     */
    public function testUpdateCommand()
    {
        GitHub::shouldReceive('authenticate')->once();

        Git::shouldReceive('setRemoteUrl')->once();
        Git::shouldReceive('execute')->twice();
        Git::shouldReceive('createBranch')->once();

        File::shouldReceive('exists')->twice()->andReturnTrue();

        $this->instance(
            'process.install',
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

        GitHub::shouldReceive('pullRequest->create')->once();

        $this->artisan('update')
             ->expectsOutput('init')
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
