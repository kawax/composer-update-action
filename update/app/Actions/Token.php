<?php

namespace App\Actions;

use Symfony\Component\Process\Process;

class Token extends BaseAction
{
    public function run(): string
    {
        /**
         * @var Process $process
         */
        $process = app('process.token');

        return $this->getOutput($process);
    }
}
