<?php

namespace Tartan\Zaman\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Zaman
 * @package Tartan\Zaman
 * @author  Aboozar Ghaffari <aboozar.ghf@gmail.com>
 */
class Zaman extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor ()
    {
        return 'Tartan\Zaman\Helpers\PersianDateHelper';
    }
}