<?php

namespace App\Facades;

use CzProject\GitPhp\GitRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array execute(...$cmd)
 * @method static bool hasChanges()
 * @method static GitRepository setRemoteUrl(string $string, string $string1)
 * @method static GitRepository createBranch(string $new_branch, bool $true)
 * @method static GitRepository addAllChanges()
 * @method static string getCurrentBranchName()
 * @method static array getBranches()
 * @method static GitRepository fetch($remote = null, array $options = null)
 * @method static GitRepository checkout(string $name)
 * @method static GitRepository pull($remote = null, array $options = null)
 * @method static GitRepository merge($branch, $options = null)
 *
 * @mixin GitRepository
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
