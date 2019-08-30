<?php
namespace app\index\controller;

class Upload extends Main
{
    public function mobile()
    {
        $file = $this->request->file('file');
        $info = $file->validate(['size'=>10485760,'ext'=>'txt'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if ($info) {
            $savepath = $info->getSaveName();
            //验证文件手机号是否正确
            $filepath = ROOT_PATH . 'public' . DS . 'uploads'.DS.$savepath;
            if (!file_exists($filepath)) {
                return $this->apiReturn(2, [], '上传失败');
            }

            $fp = fopen($filepath, 'r');
            $str = '';
            $phoneArr = [];
            //按行读取
            while(!feof($fp)) {
                $str = fgets($fp);
                $str = str_replace(PHP_EOL, '', $str);
                if (!ismobile($str)) {
                    return $this->apiReturn(3, [], '上传的文件存在手机号格式有误的信息');
                }
                $phoneArr[] = $str;
            }
            return $this->apiReturn(0, ['path' => $savepath,'name' => $_FILES["file"]["name"]], '上传成功，请点击创建任务进行检验');

        } else {
            return $this->apiReturn(1, [], $file->getError());
        }
    }
}
