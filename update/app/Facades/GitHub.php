<?php

namespace App\Facades;

use Github\Api\AbstractApi;
use Github\Client;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void authenticate($tokenOrLogin, $password = null, $authMethod = null)
 * @method static AbstractApi api(string $name)
 *
 * @see  Client
 */
class GitHub extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return Client::class;
    }
}
