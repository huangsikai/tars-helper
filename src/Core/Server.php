<?php

namespace Hsk\TarsHelper\Core;

use Hsk\TarsHelper\Util\Db;
use Hsk\TarsHelper\Util\Trace;
use Illuminate\Support\Facades\Log;
use Tars\App;
use Tars\Code;
use Tars\core\Request;
use Tars\core\Response;
use Tars\core\TarsPlatform;
use Tars\protocol\ProtocolFactory;

class Server extends \Tars\core\Server
{
    public function onTask($server, $taskId, $fromId, $data)
    {
        //不能直接使用$taskId, $taskId对于每个工作进程只是一个从0开始的自增数

        $task_work_id = $server->worker_id; //当前任务进程ID 工作进程数+投递的任务进程号（0开始）
        $worker_num   = $server->setting['worker_num']; //工作进程配置数量

        $i = $task_work_id - $worker_num;

        if ($i > 0) {
            Task::callback($data);
        } else {
            //0号任务进程框架上报服务
            parent::onTask($server, $taskId, $fromId, $data);
        }
    }

    public function onWorkerStart($server, $workerId)
    {
        parent::onWorkerStart($server, $workerId);
        $server->isHskTarsHelper = true;
    }

    public function onReceive($server, $fd, $fromId, $data)
    {
        $unpackResult    = \TUPAPI::decodeReqPacket($data);
        $server->traceId = $unpackResult['status']['traceId'] ?? Trace::buildHashKey(); //tars工具测试接口时第一个接口不会走TCP发起 没有跟踪ID自己生成一个
        $server->csKey = $unpackResult['status']['csKey'] ?? '';
        $server->traceNum = $unpackResult['status']['traceNum'] ?? 1;
        $server->pSerHash = $unpackResult['status']['serHash'] ?? '';
        $server->serHash = Trace::buildHashKey();

        $resp         = new Response();
        $resp->fd     = $fd;
        $resp->fromFd = $fromId;
        $resp->server = $server;

        // 处理管理端口的特殊逻辑
        $unpackResult = \TUPAPI::decodeReqPacket($data);
        $sServantName = $unpackResult['sServantName'];
        $sFuncName    = $unpackResult['sFuncName'];

        $objName = explode('.', $sServantName)[2];



        if (!isset(self::$paramInfos[$objName]) || !isset(self::$impl[$objName])) {
            App::getLogger()->error(__METHOD__ . " objName $objName not found.");
            $resp->send('');
            //TODO 这里好像可以直接返回一个taf error code 提示obj 不存在的
            return;
        }

        $req             = new Request();
        $req->reqBuf     = $data;
        $req->paramInfos = self::$paramInfos[$objName];
        $req->impl       = self::$impl[$objName];
        // 把全局对象带入到请求中,在多个worker之间共享
        $req->server = $this->sw;

        // 处理管理端口相关的逻辑
        if ('AdminObj' === $sServantName) {
            TarsPlatform::processAdmin($this->tarsConfig, $unpackResult, $sFuncName, $resp, $this->sw->master_pid);
        }

        $impl       = $req->impl;
        $paramInfos = $req->paramInfos;
        $protocol   = ProtocolFactory::getProtocol($this->servicesInfo[$objName]['protocolName']);
        try {
            // 这里通过protocol先进行unpack
            $result = $protocol->route($req, $resp, $this->tarsConfig);
            if (is_null($result)) {
                return;
            } else {
                $sFuncName    = $result['sFuncName'];
                $args         = $result['args'];
                $unpackResult = $result['unpackResult'];


//                Trace::insert([
//                    'trace_id' => $server->traceId,
//                    'trace_num' => $server->traceNum,
//                    'p_ser_hash' => $server->pSerHash,
//                    'ser_hash' => $server->serHash,
//                    'server_ip' => $server->host,
//                    'server_port' => $server->port,
//                    'server_name' => $this->application.'.'.$this->serverName.'.'.$objName,
//                    'func_name' => $sFuncName,
//                    'rec_data' => json_encode($args,JSON_UNESCAPED_UNICODE)
//                ]);


                if (method_exists($impl, $sFuncName)) {
                    $returnVal = $impl->$sFuncName(...$args);
                } else {
                    throw new \Exception(Code::TARSSERVERNOFUNCERR);
                }
                $paramInfo = $paramInfos[$sFuncName];
                $rspBuf    = $protocol->packRsp($paramInfo, $unpackResult, $args, $returnVal);
                $resp->send($rspBuf);

//                Trace::update($server->serHash, ['send_data' => json_encode($returnVal, JSON_UNESCAPED_UNICODE)]);


                return;
            }
        } catch (\Exception $e) {
            $unpackResult['iVersion'] = 1;
            $rspBuf                   = $protocol->packErrRsp($unpackResult, $e->getCode(), $e->getMessage());
            $resp->send($rspBuf);

            return;
        }
    }


    public function onClose($server, $fd, $fromId)
    {
        $server->traceId = '';
    }

    public function onRequest($request, $response)
    {
        $this->sw->traceId = Trace::buildHashKey();
        parent::onRequest($request, $response);
    }

}
