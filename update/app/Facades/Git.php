<?php

namespace App\Facades;

use CzProject\GitPhp\GitRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string[] execute(...$cmd)
 * @method static bool hasChanges()
 * @method static GitRepository setRemoteUrl(string $string, string $string1)
 * @method static GitRepository createBranch(string $new_branch, bool $true)
 * @method static GitRepository addAllChanges()
 */
class Git extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'git';
    }
}
