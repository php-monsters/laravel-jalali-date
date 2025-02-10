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
    const CAL_GREGORIAN = 0;
    const CAL_JALALI = 1;
    const CAL_HIJRI = 2;
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor ()
    {
        return 'PhpMonsters\Zaman\Helpers\ZamanDateHelper';
    }
}