<?php

namespace App\Actions;

use Illuminate\Process\ProcessResult;
use Illuminate\Support\Facades\Process;

class PackagesUpdate
{
    public function __invoke(string $path): string
    {
        $cmd = [
            'composer',
            'update',
            env('COMPOSER_PACKAGES'),
            '--with-dependencies',
            '--no-interaction',
            '--no-progress',
            '--no-autoloader',
            '--no-scripts',
        ];

        /** @var ProcessResult $result */
        $result = Process::composer($path)->run($cmd);

        if (filled($result->output())) {
            return trim($result->output()); // @codeCoverageIgnore
        }

        return trim($result->errorOutput());
    }
}
