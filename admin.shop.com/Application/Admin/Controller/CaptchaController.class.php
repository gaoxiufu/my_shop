<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 17:46
 */

namespace Admin\Controller;


use Think\Controller;

class CaptchaController extends Controller
{
    /**
     * 使用验证码工具
     */
    public function captcha()
    {
        // 设置验证码长度
        $setting = [
            'length' => 4,
        ];
        // 调用验证码工具
        $verify = new \Think\Verify($setting);
        // 实现验证码
        $verify->entry();
    }

}