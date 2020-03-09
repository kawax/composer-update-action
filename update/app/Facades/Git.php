<?php

namespace App\Facades;

use Cz\Git\IGit;
use Illuminate\Support\Facades\Facade;

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
