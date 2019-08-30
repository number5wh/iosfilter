<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use  app\model\Account;
use  app\model\Sms;

class Login extends Controller
{
    public function index()
    {
        return $this->fetch('login');
    }


    /**
     * 登录
     */
    public function login()
    {

        $post = $this->request->post();
        $mobile = $post['mobile'];
        $userInfo = Db::name('account')
            ->where('mobile', $mobile)
            ->find();
        if (!$userInfo) {
            return json(['code' => 3, 'msg' => '用户不存在']);
        }
        if (md5($userInfo['salt'] . $post['password']) !== $userInfo['password']) {
            return json(['code' => 3, 'msg' => '密码错误']);
        } else {
            session('username', $userInfo['username']);
            session('mobile', $userInfo['mobile']);
            session('userid', $userInfo['id']);
            session('userinfo', $userInfo);
            return json(['code' => 0]);
        }

    }

    public function reg()
    {
        return $this->fetch();
    }

    //增加用户
    public function addUser()
    {
        $request = $this->request;
        if ($request->isPost()) {
            $post = $this->request->post();
            if ($post['yzm'] != session('yzm')) {
                return json(['status' => 1, 'msg' => '请输入正确的验证码', 'data' => 6]);
            }
            $salt = $this->generateSalt();
            $inserData['username'] = $this->randstr();
            $inserData['password'] = md5($salt . $post['password']);
            $inserData['salt'] = $salt;
            $inserData['mobile'] = $post['mobile'];
            $isuser = Db::name('account')
                ->where('mobile', $post['mobile'])
                ->find();
            if($isuser){
                return json(['status' => 2, 'msg' => '用户已注册', 'data' => 6]);
            }
            $inserData['lastlogin'] = date('Y-m-d h:i:s', time());
            $inserData['addtime'] = date('Y-m-d h:i:s', time());
            $res = Db::name('account')->insert($inserData);
            if($res){
                return json(['status' => 0, 'msg' => '注册成功', 'data' => 6]);
            }else{
                return json(['status' => 1, 'msg' => '新增用户失败', 'data' => 6]);
            }
        }
    }

    function generateSalt()
    {
        $str = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
        $salt = strtolower(substr(str_shuffle($str), 10, 6));
        return $salt;
    }


    public function sendcode()
    {
        $mobile = input("mobile");
        if (!$mobile) {
            return json(['status' => -1, 'msg' => '参数错误！', 'data' => 5]);
        }
        $num = $this->randnum();
        $content = $num . "有效期2分钟，验证码请不要随意告知他人，工作人员不会向您索取。";
        $ret = sendSms($mobile, $content);
        if ($ret == 'true') {
            session("yzm", $num);
            return json(['status' => 0, 'msg' => '短信发送成功', 'data' => 6]);
        } else {
            return json(['status' => -1, 'msg' => '短信发送成功', 'data' => 6]);
        }

    }

    public function randnum()
    {
        $arr = array();
        while (count($arr) < 4) {
            $arr[] = rand(1, 9);
            $arr = array_unique($arr);
        }
        return implode("", $arr);
    }

    public function randstr()
    {
        //取随机10位字符串
//        $strs="QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        $strs="QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm";
        $name=substr(str_shuffle($strs),mt_rand(0,strlen($strs)-11),6);
        return $name;
    }

    //注销
    public function logOut()
    {
        session('username', null);
        session('mobile', null);
        session('userid', null);
        session('userinfo', null);
        session(null);
        $this->redirect('index/login/index');
    }

    public function findpassword(){
        $newpwd =input("password");
        $phone =input("phone");
        $code =input("code");
        if(session('yzm')){
            if ($code != session('yzm')) {
                return json(['status' => 1, 'msg' => '请输入正确的验证码', 'data' => 6]);
            }
        }else{
            return json(['status' => 3, 'msg' => '密码修改失败', 'data' => 6]);
        }
        $isuser = Db::name('account')
            ->where('mobile', $phone)
            ->find();
        if($isuser){
            $where =["mobile"=>$phone];
            $salt =$isuser["salt"];
            $password = md5($salt.$newpwd);
            $data = array("password"=>$password);
            $res=Db::name("account")->where($where)->update($data);
            if($res){
                return json(['status' => 0, 'msg' => '密码重置成功，请重新登录', 'data' => 6]);
            }else{
                return json(['status' => 3, 'msg' => '密码修改失败', 'data' => 6]);
            }
        }else{
            return json(['status' => 2, 'msg' => '用户未注册', 'data' => 6]);
        }



    }

    public function sendcodecheck()
    {
        $mobile = input("mobile");
        $isuser = Db::name('account')
            ->where('mobile', $mobile)
            ->find();
        if($isuser){
            if (!$mobile) {
                return json(['status' => -1, 'msg' => '参数错误！', 'data' => 5]);
            }
            $num = $this->randnum();
            $content = $num . "有效期2分钟，验证码请不要随意告知他人，工作人员不会向您索取。";
            $ret = sendSms($mobile, $content);
            if ($ret == 'true') {
                session("yzm", $num);
                return json(['status' => 0, 'msg' => '短信发送成功', 'data' => 6]);
            } else {
                return json(['status' => -1, 'msg' => '短信发送成功', 'data' => 6]);
            }

        }else{
            return json(['status' => 2, 'msg' => '用户未注册,请先注册！', 'data' => 6]);
        }



    }


}
