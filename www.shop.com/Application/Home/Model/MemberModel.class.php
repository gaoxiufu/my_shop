<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/5
 * Time: 16:37
 */

namespace Home\Model;


use Think\Model;

class MemberModel extends Model
{
    // 开启批量验证
    protected $patchValidate = true;
    // 自动验证
    protected $_validate = [
        ['username', 'require', '用户名不能为空'],
        ['username', '', '用户名已存在', self::EXISTS_VALIDATE, 'unique'],
        ['password', 'require', '密码不能为空'],
        ['password', '6,16', '密码必须6-16位', self::EXISTS_VALIDATE, 'length'],
        ['repassword', 'password', '两次密码不一致', self::EXISTS_VALIDATE, 'confirm'],
        ['email', 'require', '邮箱不能为空'],
        ['email', 'email', '邮箱不合法'],
        ['email', '', '邮箱已存在', self::EXISTS_VALIDATE, 'unique'],
        ['tel', 'require', '手机号码不能为空'],
        ['tel', '/^1[34578]\d{9}$/', '手机号码不合法', self::EXISTS_VALIDATE, 'regex'],
        ['email', '', '邮箱已存在', self::EXISTS_VALIDATE, 'unique'],
        ['checkcode', 'require', '图片验证码不能为空'],
        ['checkcode', 'checkImgCode', '图片验证码不正确', self::EXISTS_VALIDATE, 'callback'],
        ['captcha', 'require', '手机验证码不能为空'],
        ['captcha', 'checkTelCode', '手机验证码不正确', self::EXISTS_VALIDATE, 'callback'],
    ];

    // 自动完成
    protected $_auto = [
        ['add_time', NOW_TIME],
        ['salt', '\Org\Util\String::randString', self::MODEL_INSERT, 'function'],
        ['user_token', '\Org\Util\String::randString', self::MODEL_INSERT, 'function', 32],
        ['status', 0],//没有通过邮件验证的账号是禁用账户
    ];


    /**
     * 验证图片验证码
     * @param $code
     * @return bool
     */
    public function checkImgCode($code)
    {
        $verify = new \Think\Verify();
        return $verify->check($code);
    }

    /**
     * 验证手机验证码
     * @param $code
     * @return bool
     */
    public function checkTelCode($code)
    {
        if ($code == session('tel_code')) {
            session('tel_code', null);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 新增用户
     * 密码加盐加密
     * 设置令牌
     */
    public function addMember()
    {
        // 准备数据
        $user_token = $this->data['user_token'];
        $email = $this->data['email'];
        // 加盐加密
        $this->data['password'] = password_salt($this->data['password'], $this->data['salt']);
        $this->add();

        //发送激活邮件
        $url = U('Member/active',['email'=>$email,'user_token'=>$user_token],true,true);
        $subject = '欢迎注册啊咿呀哟母婴商城';
        $content = '欢迎您注册我们的网站,请点击<a href="'.$url.'">链接</a>激活账号.如果无法点击,请复制以下链接粘贴到浏览器窗口打开!<br />' . $url;

        $rst = sendMail($email,$subject,$content);
        if($rst['status']){
            return true;
        }else{
            $this->error = $rst['msg'];
            return false;
        }
    }
}