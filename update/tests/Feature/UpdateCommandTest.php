<?php

namespace Tests\Feature;

use App\Facades\Git;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Support\Facades\File;
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

        $this->artisan('update')
             ->expectsOutput('Only on GitHub Actions')
             ->assertExitCode(0);
    }
}
