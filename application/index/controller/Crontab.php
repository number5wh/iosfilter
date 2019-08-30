<?php

namespace app\index\controller;

use app\model\Mobilestore;
use app\model\Order;
use app\model\Usermobile;
use think\Controller;
use think\Db;
use Workerman\Lib\Timer;
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Autoloader;


class Crontab extends Controller
{
    protected $socket = 'ws://iosfilter.com:8888';
    public function index()
    {
        set_time_limit(0);
        $orderModel       = new Order();
        $usermobileModel  = new Usermobile();
        $mobilestoreModel = new Mobilestore();

        $orderList = $orderModel->getListAll(['isrun' => 0, 'ispay' => 1]);
        if (!$orderList) {
            exit;
        }
        foreach ($orderList as $v) {
            //查找文件
            $filepath = ROOT_PATH . 'public' . DS . 'uploads' . DS . $v['filename'];
            if (!file_exists($filepath)) {
                $orderModel->updateById($v['id'], ['status' => 2, 'updatetime' => date('Y-m-d H:i:s')]);
            }

            $fp       = fopen($filepath, 'r');
            $str      = '';
            $phoneArr = [];
            //按行读取
            while (!feof($fp)) {
                $str        = fgets($fp);
                $str        = str_replace(PHP_EOL, '', $str);
                $phoneArr[] = $str;
            }
            Db::startTrans();
            try {
                //插入订单表
                $orderno          = $v['orderno'];
                $userid           = $v['userid'];

                //去重
                //$phoneArr  = array_unique($phoneArr);
                $count     = count($phoneArr);
                $limit     = 1000;
                $times     = ceil($count / $limit);
                for ($i = 0; $i < $times; $i++) {
                    $offset = ($i - 1) * $limit;
                    $tmpArr = array_slice($phoneArr, $offset, $limit);
                    //插入手机号表(判断是否已存在)
                    $hasMobile = $mobilestoreModel->getListAll(['mobile' => ['in', $tmpArr]], 'mobile');
                    $hasMobile = array_column($hasMobile, 'mobile');
                    $phoneinfo = [];
                    foreach ($tmpArr as $p) {
                        if (in_array($p, $hasMobile)) {
                            $phoneinfo[] = [
                                'orderno' => $orderno,
                                'userid'  => $userid,
                                'mobile'  => $p,
                                'status'  => 2 //已开通
                            ];
                        } else {
                            $phoneinfo[] = [
                                'orderno' => $orderno,
                                'userid'  => $userid,
                                'mobile'  => $p,
                                'status'  => 0
                            ];
                        }
                    }
                    $usermobileModel->insertAll($phoneinfo);
                }
                save_log('uploadcrontab', 'orderid:'.$v['orderno']);
                $orderModel->updateById($v['id'], ['isrun' => 1, 'updatetime' => date('Y-m-d H:i:s')]);
                Db::commit();
            } catch (\Exception $e) {
                save_log('uploadcrontab', $e->getMessage());
                Db::rollback();
            }
        }
    }


    //向服务端发送数据
    public function sendMobile()
    {

        $worker = new Worker();
        $worker->count = 4;
        $worker->onWorkerStart = function($worker) {

            $timeinterval = 3;
            $usermobileModel = new Usermobile();
            $m = $usermobileModel->getList(['status' =>0], 1, 50,'mobile');
            if (!$m) {
                exit;
            }
            $m = array_column($m, 'mobile');

            Timer::add($timeinterval, function() use($m) {
                echo "start";
                $con = new AsyncTcpConnection($this->socket);
                $con->onConnect = function($con) use($m) {
                    foreach ($m as $v) {
                        $con->send($v);
                    }
                };
                $con->onMessage = function($con, $data) {
                    Db::name('test')->insert(['mobile' => $data]);
                };
                $con->onClose = function($con) {
                    echo 'closed';
                };
                $con->connect();
                echo 'end##';
            });

        };

        Worker::runAll();
    }


    public function startAsync()
    {
        $http_worker = new Worker("websocket://iosfilter.com:8888");

// 启动4个进程对外提供服务
        $http_worker->count = 4;

// 接收到浏览器发送的数据时回复hello world给浏览器
        $http_worker->onMessage = function($connection, $data)
        {
            // 向浏览器发送hello world
            $connection->send(json_encode(['code' => 0, 'data' => $data]));
        };

// 运行worker
        Worker::runAll();
    }
}