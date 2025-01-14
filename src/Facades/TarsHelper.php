<?php

namespace Hsk\TarsHelper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string Test()
 */
class TarsHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tars-helper';
    }
}
