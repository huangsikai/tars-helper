<?php

namespace Hsk\TarsHelper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void Async(\Closure $fun)
 */
class TarsHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tars-helper';
    }
}
