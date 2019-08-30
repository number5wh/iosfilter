<?php

namespace app\index\controller;

use app\client\controller\Client;
use think\Db;
use Workerman\Worker;
use think\Controller;
use Workerman\Connection\AsyncTcpConnection;

class  Test extends Controller
{
    public function index()
    {
        if ($this->request->isAjax()) {
            $worker = new Worker();
            $worker->onWorkerStart = function($worker) {

                $con = new AsyncTcpConnection('ws://iosfilter.com:8888');

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
        return $this->fetch();
    }
}
