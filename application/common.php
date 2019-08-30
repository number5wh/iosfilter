<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//记录log
if (!function_exists('save_log')) {
    function save_log($path, $content, $mode = 'day')
    {
        $path = strval($path);
        $path = str_replace('\\', '/', trim($path, '/'));

        $content = strval($content);

        if (!$path || !$content) {
            return false;
        }
        $mode = in_array($mode, array('day', 'month', 'year')) ? $mode : 'day';
        $tempPath = config('log_dir') . '/' . $path . '/';

        if ($mode == 'day') {
            $tempPath .= date('Y') . '/' . date('m') . '/';
            $fileName  = date('d'). '.log';
        } elseif ($mode == 'month') {
            $tempPath .= date('Y') . '/';
            $fileName  = date('m'). '.log';
        } else {
            $fileName = date('Y') . 'log';
        }

        if (!file_exists($tempPath)) {
            if (!mkdir($tempPath, 0777, true)) {
                return false;
            }
        }

        $file    = $tempPath . $fileName;
        $content = date('Y-m-d H:i:s') . '#' . $content . "\r\n";
        $res     = @file_put_contents($file, $content, FILE_APPEND);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
}


/*
 * 生成随机数
 */
if (!function_exists('randomkeys')) {
    function randomkeys($length,$isnumber=false)
    {
        $arr     = array_merge(range(0, 9), range('a', 'z'));
        if($isnumber){
            $arr     = array_merge(range(0, 9));
        }

        $count = count($arr);
        $key ="";
        for($i=0;$i<$length;$i++)
        {
            if ($isnumber) {
                $key .= $arr[mt_rand(0, $count-1)];    //生成php随机数
            } else {
                $key .= $arr[mt_rand(0, $count-1)];    //生成php随机数
            }

        }
        return $key;
    }
}

//验证手机号
if (!function_exists('ismobile')) {
    function ismobile($mobile) {
        if(preg_match("/^1\d{10}$/",$mobile)){
            return true;
        }else{
            return false;
        }
    }
}

//发送短信
if (!function_exists('sendSms')) {
      function sendSms($mobile,$content){
        $url="http://182.254.136.167:8009/sys_port/gateway/index.asp?";
        $data = "id=%s&pwd=%s&to=%s&Content=%s&time=";
        $id = urlencode(iconv("utf-8","gb2312","itaiyang"));
        $pwd = 'wf123520';
        $to = $mobile;
        $content = urlencode(iconv("UTF-8","GB2312",$content));
        $rdata = sprintf($data, $id, $pwd, $to, $content);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$rdata);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $result = curl_exec($ch);
        curl_close($ch);
        $code = substr($result,0,3);
        if($code==='000'){
            return true;
        }
        else{
            return fasle;
        }
    }
}
