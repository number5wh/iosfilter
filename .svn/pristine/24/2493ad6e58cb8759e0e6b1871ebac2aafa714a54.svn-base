<?php
namespace app\index\controller;

use think\Controller;


class Main extends Controller
{
    /**
     * 初始化
    */
    public function _initialize()
    {
        $userid  = session('userid');
        if (empty($userid)) {
            $this->redirect('index/login/index');
        }
    }

    /**
     * Notes: 接口数据返回
     * @param $code
     * @param array $data
     * @param string $msg
     * @param int $count
     * @param array $other
     * @return mixed
     */
    public function apiReturn($code, $data = [], $msg = '', $count = 0, $other = [])
    {
        return json([
            'code' => $code,
            'data' => $data,
            'msg'  => $msg,
            'count' => $count,
            'other' => $other
        ]);
    }
}
