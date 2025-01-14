<?php

namespace Hsk\TarsHelper\Util;

class Trace
{
//    const ROLE_CLIENT = 1;
//    const ROLE_SERVER = 2;
//
//    const EX_TYPE_SEND    = 'send';
//    const EX_TYPE_RECEIVE = 'receive';

//    public $trace_id;
//    public $trace_num;
//    public $cs_key;
//    public $ser_link_hash;
//    public $server_ip;
//    public $server_port;
//    public $server_name;
//    public $func_name;
//    public $params;
//    public $role;
//    public $ex_type;
//    public $client_name = '';
//    public $client_ip   = '';


    protected static function generateRandomCode_($length = 10)
    {
        $returnStr = '';

        $pattern = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

        $l = count($pattern);

        for ($i = 0; $i < $length; ++$i) {
            $returnStr .= $pattern[mt_rand(0, $l - 1)];
        }

        return $returnStr;
    }

    public static function buildHashKey()
    {
        return md5(microtime(true) . self::generateRandomCode_());
    }

    /**
     * @return mixed
     */
//    public function save()
//    {
//        //暂时这样测试
//
//        (new Db())->insert('trace_server', [
//            'trace_id'           => $this->trace_id,
//            'cs_key'             => $this->cs_key,
//            'ser_link_hash'      => $this->ser_link_hash,
//            'trace_num'          => $this->trace_num,
//            'server_ip'          => $this->server_ip,
//            'server_port'        => $this->server_port,
//            'server_name'        => $this->server_name,
//            'func_name'          => $this->func_name,
//            'params'             => json_encode($this->params, JSON_UNESCAPED_UNICODE),
//            'role'               => $this->role,
//            'ex_type'            => $this->ex_type,
//            'client_name'        => $this->client_name,
//            'client_ip'          => $this->client_ip,
//            'ex_time'            => intval(microtime(true) * 1000),
//            'created_at'         => date('Y-m-d H:i:s'),
//        ]);
//    }

    public static function insert($datas){
        try {
            $datas['ex_time'] = intval(microtime(true) * 1000);
            $datas['created_at'] = date('Y-m-d H:i:s');
            return (new Db())->insert('trace_servers', $datas);
        }catch (\Exception $e){

            echo "写入trace_servers表失败\r\n";
            echo $e->getMessage()."\r\n";

        }

    }

    public static function update($ser_hash, $datas){
        try {
            return (new Db())->update('trace_servers', ['ser_hash' => $ser_hash],$datas);
        }catch (\Exception $e){
            echo "更新trace_servers表失败\r\n";
            echo $e->getMessage()."\r\n";
        }

    }
}
