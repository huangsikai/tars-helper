<?php

namespace Hsk\TarsHelper\Util;

class Fun
{
    private $f__;

    public function __construct(\Closure $fun)
    {
        $this->f__ = $fun;
    }

    public function call()
    {
        return ($this->f__)();
    }

    public static function invoke($serialize)
    {
        (\Opis\Closure\unserialize($serialize))->call();
    }
}
