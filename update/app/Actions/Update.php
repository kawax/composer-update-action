<?php

namespace App\Actions;

use Illuminate\Process\ProcessResult;
use Illuminate\Support\Facades\Process;

class Update
{
    public function __invoke(string $path): string
    {
        $cmd = [
            'composer',
            'update',
            '--no-interaction',
            '--no-progress',
            '--no-autoloader',
            '--no-scripts',
        ];

        /** @var ProcessResult $result */
        $result = Process::composer($path)->run($cmd);

        if (filled($result->errorOutput())) {
            return trim($result->errorOutput()); // @codeCoverageIgnore
        }

        return trim($result->output());
    }
}
