<?php
namespace app\index\controller;

use Workerman\Connection\AsyncTcpConnection;

class AsyncTcp extends AsyncTcpConnection
{
    //存放实例
    private static $_instance = null;
    private static $socket = 'ws://iosfilter.com:8888';

    //私有化克隆方法
    private function __clone()
    {

    }

    //公有化获取实例方法
    public static function getInstance()
    {
        if (!(self::$_instance instanceof AsyncTcp)) {
            self::$_instance = new AsyncTcp(self::$socket);
        }
        return self::$_instance;
    }
}