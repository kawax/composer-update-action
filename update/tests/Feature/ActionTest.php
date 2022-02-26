<?php

namespace Tests\Feature;

use App\Actions\PackagesUpdate;
use App\Actions\Token;
use App\Actions\Update;
use Mockery as m;
use Mockery\MockInterface;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class ActionTest extends TestCase
{
    public function test_update()
    {
        $this->instance('process.update',
            m::mock(Process::class, function (MockInterface $mock) {
                $mock->allows('setWorkingDirectory')->once();
                $mock->allows('setTimeout')->once();
                $mock->allows('setEnv')->once();
                $mock->allows('mustRun')->once();
                $mock->allows('getOutput')->once()->andReturn('test');
            })
        );

        $action = new Update();
        $output = $action->basePath(base_path())->run();

        $this->assertSame('test', $output);
    }

    public function test_packages_update()
    {
        $this->instance('process.update-packages',
            m::mock(Process::class, function (MockInterface $mock) {
                $mock->allows('setWorkingDirectory')->once();
                $mock->allows('setTimeout')->once();
                $mock->allows('setEnv')->once();
                $mock->allows('mustRun')->once();
                $mock->allows('getOutput')->once()->andReturn('');
                $mock->allows('getErrorOutput')->once()->andReturn('error');
            })
        );

        $action = new PackagesUpdate();
        $output = $action->basePath(base_path())->run();

        $this->assertSame('error', $output);
    }

    public function test_token()
    {
        $_ENV['GITHUB_TOKEN'] = 'token';

        $this->instance('process.token',
            m::mock(Process::class, function (MockInterface $mock) {
                $mock->allows('setWorkingDirectory')->once();
                $mock->allows('setTimeout')->once();
                $mock->allows('setEnv')->once();
                $mock->allows('mustRun')->once();
                $mock->allows('getOutput')->once()->andReturn('test');
            })
        );

        $action = new Token();
        $output = $action->basePath(base_path())->run();

        $this->assertSame('test', $output);
    }
}
