<?php

namespace App\Actions;

use Illuminate\Process\ProcessResult;
use Illuminate\Support\Facades\Process;

class Token
{
    public function __invoke(string $path): string
    {
        $cmd = [
            'composer',
            'config',
            '-g',
            'github-oauth.github.com',
            env('GITHUB_TOKEN'),
        ];

        /** @var ProcessResult $result */
        $result = Process::composer($path)->run($cmd);

        if (filled($result->output())) {
            return trim($result->output()); // @codeCoverageIgnore
        }

        return trim($result->errorOutput());
    }
}
