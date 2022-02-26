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
        $process = app(Process::class, ['command' => $this->packages()]);

        return $this->getOutput($process);
    }

    /**
     * @return array
     */
    private function packages(): array
    {
        return [
            'composer',
            'update',
            env('COMPOSER_PACKAGES'),
            '--with-dependencies',
            '--no-interaction',
            '--no-progress',
            '--no-autoloader',
            '--no-scripts',
        ];
    }
}
