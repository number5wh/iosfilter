<?php

namespace app\index\controller;

use app\model\Account;
use app\model\Order;
use redis\Redis;
use think\Db;

class Task extends Main
{
    protected $permoney = 2;
    public function index()
    {
        if ($this->request->isAjax()) {
            $savepath = input('savepath') ? input('savepath') : '';
            $taskname = input('taskname') ? input('taskname') : '';

            if (!$savepath) {
                return $this->apiReturn(1, [], '任务创建失败，请稍后重试');
            }
            if (!$taskname) {
                return $this->apiReturn(2, [], '请输入任务名称');
            }

            //判断号码
            $filepath = ROOT_PATH . 'public' . DS . 'uploads' . DS . $savepath;
            if (!file_exists($filepath)) {
                return $this->apiReturn(3, [], '上传文件已失效，请重新上传');
            }

            $fp       = fopen($filepath, 'r');
            $phoneArr = [];
            //按行读取
            while (!feof($fp)) {
                $str        = fgets($fp);
                $str        = str_replace(PHP_EOL, '', $str);
                if (!ismobile($str)) {
                    return $this->apiReturn(3, [], '上传的文件存在手机号格式有误的信息');
                }
                $phoneArr[] = $str;
            }
            //去重
            $phoneArr  = array_unique($phoneArr);
            $num = count($phoneArr);
            $ordermoney = $num * $this->permoney;

            //插入订单表
            $orderModel = new Order();
            while (true) {
                $orderno = date('YmdHis') . randomkeys(10, true);
                if (!$orderModel->getCount(['orderno' => $orderno])) {
                    break;
                }
            }
            $data = [
                'userid'   => session('userid'),
                'orderno'  => $orderno,
                'taskname' => $taskname,
                'filename' => $savepath,
                'num'      => $num,
                'ordermoney'  => $ordermoney,
                'ispay'  => 0,
                'addtime'  => date('Y-m-d H:i:s')
            ];
            $res = $orderModel->insert($data);
            if ($res) {
                return $this->apiReturn(0, [], '任务创建成功');
            } else {
                return $this->apiReturn(3, [], '任务创建失败，请稍后重试');
            }
        }

        return $this->fetch();
    }

    //查询列表
    public function getlist()
    {
        if ($this->request->isAjax()) {
            $page    = intval(input('page')) ? intval(input('page')) : 1;
            $limit   = intval(input('limit')) ? intval(input('limit')) : 15;
            $orderModel = new Order();
            $count = $orderModel->getCount();
            $list = $orderModel->getList(['userid' => session('userid')], $page, $limit);
            return $this->apiReturn(0, $list, '', $count);
        }
        return $this->fetch();
    }


    //支付
    public function pay()
    {
        $orderno = input('orderno') ? input('orderno') : '';
        if (!$orderno) {
            return $this->apiReturn(1, [], '订单不存在');
        }

        $lockkey = session('userid').'_'.$orderno;
        if (!Redis::getInstance()->lock($lockkey)) {
            return $this->apiReturn(2, [], '请勿重复操作');
        }
        $orderModel = new Order();
        $orderInfo = $orderModel->getRow(['orderno' => $orderno, 'userid' => session('userid')]);
        if (!$orderInfo) {
            Redis::getInstance()->rm($lockkey);
            return $this->apiReturn(3, [], '不存在该笔订单');
        }
        if ($orderInfo['ispay'] == 1) {
            Redis::getInstance()->rm($lockkey);
            return $this->apiReturn(4, [], '订单已支付，请勿重复支付');
        }
        $accountModel = new Account();
        $userInfo = $accountModel->getRowById(session('userid'));
        $paymoney = $orderInfo['ordermoney'];
        if ($userInfo['balance'] < $paymoney) {
            Redis::getInstance()->rm($lockkey);
            return $this->apiReturn(5, [], '您的余额不足，请先充值');
        }

        Db::startTrans();
        try {
            $accountModel->updateById(session('userid'), [
                "balance"   => Db::raw("balance-" . $paymoney),
            ]);
            $orderModel->updateById($orderInfo['id'], [
                'ispay' => 1
            ]);
            Db::commit();
            Redis::getInstance()->rm($lockkey);
            return $this->apiReturn(0, [], '支付成功，请稍后看处理结果');
        } catch (\Exception $e) {
            Db::rollback();
            Redis::getInstance()->rm($lockkey);
            return $this->apiReturn(6, [], '充值失败，请稍后重试');
        }
    }
}
