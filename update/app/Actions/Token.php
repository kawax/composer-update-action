<?php

namespace App\Actions;

use Symfony\Component\Process\Process;

class Token extends BaseAction
{
    public function run()
    {
        /**
         * @var Process $process
         */
        $process = app(Process::class, ['command' => $this->token()]);

        $process->setWorkingDirectory($this->base_path)
                ->setTimeout(60)
                ->mustRun();
    }

    /**
     * @return array
     */
    private function token(): array
    {
        return [
            'composer',
            'config',
            '-g',
            'github-oauth.github.com',
            env('GITHUB_TOKEN'),
        ];
    }
}
