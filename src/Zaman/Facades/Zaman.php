<?php

namespace PhpMonsters\Zaman\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Zaman
 * @package PhpMonsters\Zaman
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
        return 'PhpMonsters\Zaman\Helpers\PersianDateHelper';
    }
}