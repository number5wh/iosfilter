<?php
namespace app\client\controller;
use think\Db;
use Workerman\Worker;
use think\Controller;
use Workerman\Connection\AsyncTcpConnection;

class Client
{
    protected $socket = 'ws://iosfilter.com:8888';
    protected $name= '';
    public function __construct()
    {

        $worker = new Worker();
        $worker->onWorkerStart = function($worker) {

            $con = new AsyncTcpConnection($this->socket);

            $con->onConnect = function($con) {
                $con->send('testetst');
            };

            $con->onMessage = function($con, $data) {
                Db::name('test')->insert(['mobile' => $data]);
            };

            $con->connect();
        };

        Worker::runAll();
    }
}