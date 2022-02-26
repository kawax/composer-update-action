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
        $process = app(Process::class, ['command' => $this->update()]);

        return $this->getOutput($process);
    }

    /**
     * @return array
     */
    private function update(): array
    {
        return [
            'composer',
            'update',
            '--no-interaction',
            '--no-progress',
            '--no-autoloader',
            '--no-scripts',
        ];
    }
}
