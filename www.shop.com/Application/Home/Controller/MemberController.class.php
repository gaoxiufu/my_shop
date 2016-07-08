<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/5
 * Time: 16:36
 * https://www.guerrillamail.com/zh/
 */

namespace Home\Controller;


use Think\Controller;

class MemberController extends Controller
{
    /**
     * 自动创建对象
     * @var \Home\Model\MemberModel
     */
    private $model = null;

    protected function _initialize()
    {
        $this->model = D('Member');
    }

    /**
     * 用户注册
     */
    public function reg()
    {
        if (IS_POST) {
            if ($this->model->create('', 'add') === false) {
                $this->error(getError($this->model));
            }
            if ($this->model->addMember() === false) {
                $this->error(getError($this->model));
            }
            $this->success('注册成功请到邮箱激活账户', U('index'));
        } else {
            $this->display();

        }
    }


    /**
     * 邮箱激活
     * @param $email
     * @param $register_token
     */
    public function active($email, $user_token)
    {
        //查询邮箱和token和传过来的一致的,如果有则改变状态为1 激活
        $cond = [
            'email'          => $email,
            'register_token' => $user_token,
            'status'         => 0,
        ];
        if ($this->model->where($cond)->count()) {
            //修改status状态
            $this->model->where($cond)->setField('status', 1);
            $this->success('激活成功', U('Index/index'));
        } else {
            $this->error('验证失败', U('Index/index'));
        }
    }

    /**
     * 接收前端ajax请求数据验证信息是否存在
     */
    public function checkParam()
    {
        // 查询是否有匹配数据
        if ($this->model->where(I('get.'))->count()) {
            $this->ajaxReturn(false);
        } else {
            $this->ajaxReturn(true);
        }
    }

    /**
     * 用户登陆
     */
    public function login()
    {
        if (IS_POST) {
            if ($this->model->create() == false) {
                $this->error(getError($this->model));
            }
            if ($this->model->longinMember() == false) {
                $this->error(getError($this->model));
            }
            $this->success('登陆成功', U('Index/index'));
        } else {
            $this->display();
        }
    }

    /**
     * 获取用户名绑定到哦前端
     */
    public function userinfo()
    {
        $userinfo = session('USERINFO');
        if($userinfo){
            $this->ajaxReturn($userinfo['username']);
        }else{
            $this->ajaxReturn(false);
        }
    }


    /**
     *用户退出
     */
    public function logout()
    {
        session(null);
        cookie(null);
        $this->success('退出成功', U('Index/index'));
    }


}