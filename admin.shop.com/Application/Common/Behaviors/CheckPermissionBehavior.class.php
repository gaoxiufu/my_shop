<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 21:17
 */

namespace Common\Behaviors;


use Think\Behavior;

class CheckPermissionBehavior extends Behavior
{
    public function run(&$params)
    {

        // 获取路径
        $rul = MODULE_NAME . "/" . CONTROLLER_NAME . '/' . ACTION_NAME;
        // 可以访问的权限路径
        $paths = session('PERMISSION_PATH');
        if (!is_array($paths)) {
            $paths = [];
        }

        // 忽略的路径
        $ignore_setting = [
            'Admin/Admin/login',
            'Admin/Captcha/captcha',
        ];

        // 所有用户可见列表
        $user_ignore = [
            'Admin/Index/index',
            'Admin/Index/top',
            'Admin/Index/menu',
            'Admin/Index/main',
            'Admin/Admin/logout',
            'Admin/Admin/alter',
        ];

        // session中获取用户信息
        $userinfo = session('USERINFO');
        //如果没有登陆,就自动登陆
        if (!$userinfo) {
            $userinfo = D('Admin')->autoLogin();
        }
        // 允许访问的路径,获取的路径和忽略的路径
        $urls = array_merge($paths, $ignore_setting);
        if ($userinfo['username'] != 'admin') { // 如果是超级管理员就不需要限制访问
            if ($userinfo) {
                $urls = array_merge($urls, $user_ignore);
            }

            if (!in_array($rul, $urls)) {
                header('Content-Type: text/html;charset=utf-8');
                echo '<script type="text/javascript">top.location.href="' . U('Admin/Admin/login') . '"</script>';
//               redirect(U('Admin/Admin/login'),2,'无权访问');
            }
        }


    }

}