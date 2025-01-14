<?php

namespace Hsk\TarsHelper\Core;

use Illuminate\Support\Facades\Log;
use Tars\App;

class Task
{
    /**
     * @param $className
     * @param $methodName
     * @param $data
     *
     * @return false|int|mixed|void
     */
    public static function dispatch($className, $methodName, $data)
    {
        $sw =  App::getSwooleInstance();
        $isHskTarsHelper      = $sw->isHskTarsHelper ?? false;
        $task_worker_num = $sw->setting['task_worker_num'];

        if ($isHskTarsHelper && !$sw->taskworker && $task_worker_num > 1) {
            //此处待优化为空闲任务进程处理
            srand();
            $workerId = rand(1, $task_worker_num - 1);
            App::getSwooleInstance()->task(['className' => $className, 'methodName' => $methodName, 'data' => $data], $workerId);
        } else {
            //没有任务进程|非TARS帮助扩展|非工作进程 直接执行
            return self::callback(['className' => $className, 'methodName' => $methodName, 'data' => $data]);
        }
    }

    public static function callback($params)
    {
        $className  = $params['className']  ?? '';
        $methodName = $params['methodName'] ?? '';
        $data       = $params['data']       ?? '';

        if (!$className || !$methodName) {
            return 0;
        }

        if (!class_exists($className)) {
            return 0;
        }

        $ref = new \ReflectionClass($className);

        if (!$ref->hasMethod($methodName)) {
            return 0;
        }

        if (!$ref->getMethod($methodName)->isPublic()) {
            return 0;
        }

        if ($ref->getMethod($methodName)->isStatic()) {
            return call_user_func([$className, $methodName], $data);
        } else {
            return ($ref->newInstance())->$methodName($data);
        }

    }
}
