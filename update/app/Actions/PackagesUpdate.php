<?php

namespace App\Actions;

use Symfony\Component\Process\Process;

class PackagesUpdate extends BaseAction
{
    public function run(): string
    {
        /**
         * @var Process $process
         */
        $process = app('process.update-packages');

        return $this->getOutput($process);
    }
}

