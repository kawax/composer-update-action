<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use Cz\Git\IGit;

class Git extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return IGit::class;
    }
}
