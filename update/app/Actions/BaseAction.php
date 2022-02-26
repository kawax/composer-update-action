<?php

namespace App\Actions;

use Symfony\Component\Process\Process;

abstract class BaseAction
{
    protected string $base_path;

    /**
     * @param  string  $base_path
     * @return $this
     */
    public function basePath(string $base_path): self
    {
        $this->base_path = $base_path;

        return $this;
    }

    /**
     * @param  Process  $process
     * @return string
     */
    protected function getOutput(Process $process): string
    {
        $output = $process->setWorkingDirectory($this->base_path)
                          ->setTimeout(600)
                          ->setEnv(
                              [
                                  'COMPOSER_MEMORY_LIMIT' => '-1',
                              ]
                          )
                          ->mustRun()
                          ->getOutput();

        if (blank($output)) {
            $output = $process->getErrorOutput();
        }

        return $output ?? '';
    }
}
