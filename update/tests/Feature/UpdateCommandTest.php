<?php

namespace Tests\Feature;

use App\Actions\PackagesUpdate;
use App\Actions\Token;
use App\Actions\Update;
use App\Facades\Git;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateCommandTest extends TestCase
{
    public function testUpdateCommand()
    {
        Git::shouldReceive('getCurrentBranchName')->once()->andReturn('');

        GitHub::shouldReceive('authenticate')->once();

        Git::shouldReceive('setRemoteUrl')->once();
        Git::shouldReceive('execute')->times(4);
        Git::shouldReceive('fetch')->once();
        Git::shouldReceive('createBranch')->once();

        File::shouldReceive('exists')->twice()->andReturnTrue();

        $this->mock(Update::class, function (MockInterface $mock) {
            $mock->allows('basePath->run')->once()->andReturn('test');
        });

        $this->mock(Token::class, function (MockInterface $mock) {
            $mock->allows('basePath->run')->once()->andReturnSelf();
        });

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
        Git::shouldReceive('execute')->times(4);
        Git::shouldReceive('fetch')->once();
        Git::shouldReceive('createBranch')->once();

        File::shouldReceive('exists')->twice()->andReturnTrue();

        $this->mock(PackagesUpdate::class, function (MockInterface $mock) {
            $mock->allows('basePath->run')->once()->andReturn('test');
        });

        $this->mock(Token::class, function (MockInterface $mock) {
            $mock->allows('basePath->run')->once()->andReturnSelf();
        });

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
        Git::shouldReceive('execute')->times(4);
        Git::shouldReceive('fetch')->once();
        Git::shouldReceive('getBranches')->once()->andReturn([
            'remotes/origin/test-updated',
        ]);

        Git::shouldReceive('checkout')->once();
        Git::shouldReceive('pull')->once();
        Git::shouldReceive('merge')->once();

        File::shouldReceive('exists')->twice()->andReturnTrue();

        $this->mock(Update::class, function (MockInterface $mock) {
            $mock->allows('basePath->run')->once()->andReturn('test');
        });

        $this->mock(Token::class, function (MockInterface $mock) {
            $mock->allows('basePath->run')->once()->andReturnSelf();
        });

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
        $str = Str::of(' - Updating laravel/framework (v7.0.0 => v7.1.0): Loading from cache')->beforeLast(
            ':'
        )->trim()->value();

        $this->assertEquals('- Updating laravel/framework (v7.0.0 => v7.1.0)', $str);
    }
}
