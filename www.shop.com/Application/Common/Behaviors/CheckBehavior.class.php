<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 21:17
 */

namespace Common\Behaviors;


use Think\Behavior;

class CheckBehavior extends Behavior
{
    public function run(&$params)
    {

//        // session中获取用户信息
//        $userinfo = session('USERINFO');
//        //如果没有登陆,就自动登陆
//        if (!$userinfo) {
//            D('Member')->autoLogin();
//        }
        /**
         * 自动登陆
         * @return bool|mixed
         */

        $data = cookie('USER_TOKEN');
        if (!$data) {
            return false;
        }

        // 比较cookie中的数据和数据库中的数据
        $user_token_model = M('UserToken');
        if (!$user_token_model->where($data)->count()) {
            return false;
        }
//        echo $user_token_model->getLastSql();
            // 自动登陆后 删除历史token数据
        $user_token_model->delete($data['user_id']);

        // 重新生成cookie数据
        $data = [
            'user_id' => $data['user_id'],
            'token'   => \Org\Util\String::randString(40),
        ];
        // 保存cookie并设置过时间
        cookie('USER_TOKEN', $data, 3360);

        // 将新的token记录存入库中
        $user_token_model->add($data);
        // 保存信息到session中
        $member_model=M('Member');
        $userinfo = $member_model->find($data['user_id']);
        session('USERINFO', $userinfo);
        return $userinfo;
    }



}