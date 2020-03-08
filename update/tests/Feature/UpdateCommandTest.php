<?php

namespace Tests\Feature;

use Tests\TestCase;

class UpdateCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testUpdateCommand()
    {
        $this->artisan('update')
             ->expectsOutput('Only on GitHub Actions')
             ->assertExitCode(0);
    }
}
