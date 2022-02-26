<?php

namespace App\Actions;

use Symfony\Component\Process\Process;

class Update extends BaseAction
{
    public function run(): string
    {
        /**
         * @var Process $process
         */
        $process = app('process.update');

        return $this->getOutput($process);
    }
}
