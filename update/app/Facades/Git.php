<?php

namespace App\Facades;

use CzProject\GitPhp\GitRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array execute(string ...$cmd)
 * @method static bool hasChanges()
 * @method static GitRepository setRemoteUrl(string $name, string $url, ?array $options = null)
 * @method static GitRepository createBranch(string $name, bool $checkout = false)
 * @method static GitRepository addAllChanges()
 * @method static string getCurrentBranchName()
 * @method static array getBranches()
 * @method static GitRepository fetch(?string $remote = null, ?array $options = null)
 * @method static GitRepository checkout(string $name)
 * @method static GitRepository pull(?string $remote = null, ?array $options = null)
 * @method static GitRepository merge(string $branch, ?array $options = null)
 *
 * @see GitRepository
 */
class Git extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'git';
    }
}
