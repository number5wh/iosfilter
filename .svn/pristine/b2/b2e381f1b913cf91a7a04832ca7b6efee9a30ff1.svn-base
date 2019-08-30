<?php

namespace app\index\controller;


use think\Db;

class User extends Main
{
    public function info()
    {
        return $this->fetch();
    }

    public function detail()
    {
        return $this->fetch();
    }

    public function recharge()
    {
        return $this->fetch();
    }

    public function changePass()
    {
        if ($this->request->isAjax()) {
            $request = $this->request->request();
            $pass2     = trim(input('pass2')) ? trim(input('pass2')) : '';
            $pass3     = trim(input('pass3')) ? trim(input('pass3')) : '';
            $pass4     = trim(input('pass4')) ? trim(input('pass4')) : '';
            $userInfo = Db::name('account')
                ->where('mobile', session('mobile'))
                ->find();
            if (md5($userInfo['salt'] . $pass2) !== $userInfo['password']) {
                return json(['status' => 2, 'msg' => '密码不正确', 'data' => 6]);

            }

            $salt =$userInfo["salt"];
            $password = md5($salt.$pass3);
            $data = array("password"=>$password);
            $res=Db::name("account") ->where('mobile', session('mobile'))->update($data);
            if($res){
                return json(['status' => 0, 'msg' => '密码修改成功', 'data' => 6]);
            }else{
                return json(['status' => 2, 'msg' => '密码修改失败', 'data' => 6]);
            }
        }
    }
}
