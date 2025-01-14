<?php

namespace Hsk\TarsHelper\Commands;

use Illuminate\Support\Facades\Log;
use Tars\cmd\Stop;
use Lxj\Laravel\Tars\Util;
use Tars\route\RouteFactory;
use Lxj\Laravel\Tars\Registries\Registry;
use Lxj\Laravel\Tars\Route\TarsRouteFactory;

class Tars extends \Lxj\Laravel\Tars\Commands\Tars
{
//    public function handle()
//    {
//        $cmd = $this->option('cmd');
//        $cfg = $this->option('config_path');
//
//        class_alias(TarsRouteFactory::class, RouteFactory::class);
//
//        list($hostname, $port, $appName, $serverName) = Util::parseTarsConfig($cfg);
//
//        config(['tars.deploy_cfg' => $cfg]);
//
//        Registry::register($hostname, $port);
//
//        if (!function_exists('exec')) {
//            echo 'Function `exec` is not exist, please check php.ini. ' . PHP_EOL;
//            exit;
//        }
//
//        switch ($cmd) {
//            case 'stop':
//                $class = new Stop($cfg);
//                $class->execute();
//                break;
//            case 'restart':
//                $class = new Stop($cfg);
//                $class->execute();
//                $class = new Start($cfg);
//                $class->execute();
//                break;
//            default:
//                // 默认其实就是start
//                $class = new Start($cfg);
//                $class->execute();
//                break;
//
//        }
//    }
}
