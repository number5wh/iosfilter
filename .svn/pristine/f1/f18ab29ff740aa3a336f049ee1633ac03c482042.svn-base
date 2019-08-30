<?php

namespace app\index\controller;

class Index extends Main
{
    public function index()
    {
        $this->assign('mobile', session('userinfo')['mobile']);
        return $this->fetch();
    }
}
