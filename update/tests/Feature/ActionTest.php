<?php

namespace Tests\Feature;

use App\Actions\PackagesUpdate;
use App\Actions\Token;
use App\Actions\Update;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class ActionTest extends TestCase
{
    public function test_update()
    {
        Process::fake([
            '*' => Process::result(
                output: '',
                errorOutput: 'test',
                exitCode: 0,
            ),
        ]);

        $action = new Update();
        $output = $action(base_path());

        $this->assertSame('test', $output);
    }

    public function test_packages_update()
    {
        Process::fake([
            '*' => Process::result(
                output: '',
                errorOutput: 'test',
                exitCode: 0,
            ),
        ]);

        $action = new PackagesUpdate();
        $output = $action(base_path());

        $this->assertSame('test', $output);
    }

    public function test_token()
    {
        $_ENV['GITHUB_TOKEN'] = 'token';

        Process::fake([
            '*' => Process::result(
                output: '',
                errorOutput: 'test',
                exitCode: 0,
            ),
        ]);

        $action = new Token();
        $output = $action(base_path());

        $this->assertSame('test', $output);
    }
}
