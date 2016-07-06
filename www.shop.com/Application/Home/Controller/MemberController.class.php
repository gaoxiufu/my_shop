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
            if ($this->model->create() === false) {
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

}