<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/14
 * Time: 21:32
 */

namespace Admin\Controller;


use Think\Controller;

class TestController extends Controller
{
    public function sendMail()
    {
        $obj = new MyMailThread('aimafan@qq.com', '这里是邮箱测试', '邮件成功发送了!');
        $rts = $obj->start();
        var_dump($rts);
    }

    public function coreseek($key = '长城')
    {
        header('Content-Type:text/html; charset=utf-8');
        //引入sphinx的类库
        vendor('Sphinx.sphinxapi');
        $spinx = new \SphinxClient();
        $spinx->SetServer('127.0.0.1', 9312);
        $spinx->SetLimits(0, 50);
        $spinx->SetMatchMode(SPH_MATCH_ANY);
        $rst = $spinx->Query($key);
        var_dump($rst);
    }

}

class MyMailThread extends \Thread
{
    private $email, $subject, $content;

    public function __construct($email, $subject, $content)
    {
        $this->email = $email;
        $this->ubject = $subject;
        $this->content = $content;
    }

    public function run()
    {
        $this->res = sendMail($this->email, $this->ubject, $this->content);
    }



}