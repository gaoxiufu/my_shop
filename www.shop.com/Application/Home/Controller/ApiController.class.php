<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/6
 * Time: 10:28
 */

namespace Home\Controller;


use Think\Controller;

class ApiController extends Controller
{
    /**
     * 手机验证码
     * @param $tel
     */
    public function regSms($tel) {
        //发送短信
        //引入topSdk.php
        Vendor('Alidayu.TopSdk');
        $c            = new \TopClient;
        $c->appkey    = '23399673';
        $c->secretKey = '7658f6b6ad2e22f30cd405834372f7cc';
        $req          = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("高先生");

        // 生成随机数字验证码
        $code = \Org\Util\String::randNumber(1000, 9999);
//        $code='起床撒尿了';
        // 将随机验证码保存到session中
        session('tel_code',$code);
        $data = [
            'product'=>'[啊咿呀哟]',
            'code'=> $code,
        ];
        $req->setSmsParam(json_encode($data)); // 要发送的消息
        $req->setRecNum($tel); // 接收验证码的手机号码
        $req->setSmsTemplateCode("SMS_11490267"); // 阿里大鱼模块
        $resp         = $c->execute($req);
    }
}