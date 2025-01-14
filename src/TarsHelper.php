<?php

namespace Sky\TarsHelper;

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
}
