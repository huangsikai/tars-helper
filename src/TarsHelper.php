<?php

namespace Hsk\TarsHelper;

use Hsk\TarsHelper\Core\Task;
use Hsk\TarsHelper\Util\Fun;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Log;

class TarsHelper
{
    protected $config;
//    public function __construct(Repository $config)
//    {
//        $this->config = $config;
//    }

    public function Test(){
//        Log::info(json_encode($this->config));
        return __METHOD__;
    }

    public function Async(\Closure $fun){
        Task::dispatch(Fun::class, 'invoke', \Opis\Closure\serialize(new Fun($fun)));
    }
}
