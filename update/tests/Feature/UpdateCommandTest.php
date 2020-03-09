<?php

namespace Tests\Feature;

use App\Facades\Git;
use Tests\TestCase;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use GrahamCampbell\GitHub\Facades\GitHub;

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

        $this->artisan('update')
             ->expectsOutput('Only on GitHub Actions')
             ->assertExitCode(0);
    }
}
