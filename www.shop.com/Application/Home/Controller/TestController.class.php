<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/5
 * Time: 17:42
 */

namespace Home\Controller;


use Think\Controller;

class TestController extends Controller
{

    public function msg()
    {
        Vendor('Alidayu.TopSdk');
        $c = new \TopClient;
        $c->appkey = '23399673';
        $c->secretKey = '7658f6b6ad2e22f30cd405834372f7cc';
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("高先生");
        $req->setSmsParam("{'product':'赵川钢板','code':'1234'}");
        $req->setRecNum("15982344303");
        $req->setSmsTemplateCode("SMS_11490267");
        $resp = $c->execute($req);
        dump($resp);
    }

    public function sendEmail() {
        Vendor('PHPMailer.PHPMailerAutoload');

        $mail = new \PHPMailer;


        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host       = 'smtp.126.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                               // Enable SMTP authentication
        $mail->Username   = 'aimafandehen@126.com';                 // SMTP username
        $mail->Password   = '010203gao';                           // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 465;                                    // TCP port to connect to

        $mail->setFrom('aimafandehen@126.com', 'ayiyayo');
        $mail->addAddress('aimafan@qq.com', 'brother four');     // Add a recipient

        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = '欢迎注册啊咿呀哟母婴商城';
        $url = U('Member/Active',['email'=>'kunx-eud@qq.com'],true,true);
        $mail->Body    = '欢迎您注册我们的网站,请点击<a href="'.$url.'">链接</a>激活账号.如果无法点击,请复制以下链接粘贴到浏览器窗口打开!<br />' . $url;
        $mail->CharSet = 'UTF-8';

        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }


}