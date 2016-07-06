<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3
 * Time: 9:43
 */

namespace Admin\Controller;


use Think\Controller;

class AdminController extends Controller
{
    /**
     * 自动创建model对象
     * @var \Admin\Model\AdminModel
     */
    private $model = null;

    protected function _initialize()
    {
        $this->model = D('Admin');
    }

    /**
     * 展示管理员列表
     */
    public function index()
    {
        // 获取模糊搜索条件
        $name = I('get.name');
        $cond = [];
        if ($name) {
            $cond['username'] = ['like', '%' . $name . '%'];
        }
        $rows = $this->model->getPageList($cond);
        $this->assign($rows);
        $this->display();
    }

    /**
     * 新增管理员
     */
    public function add()
    {
        if (IS_POST) {
            // 获取数据
            if ($this->model->create('', 'add') === false) {
                $this->error(getError($this->model));
            }
            // 新增数据
            if ($this->model->addAdmin() === false) {
                $this->error(getError($this->model));
            }
            $this->success('添加成功', U('index'));
        } else {
            $this - $this->load_data();
            $this->display();
        }

    }

    /**
     * 修改管理权限
     * @param $id
     */
    public function edit($id)
    {

        if (IS_POST) {
            // 获取数据
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }
            // 新增数据
            if ($this->model->saveAdmin($id) === false) {
                $this->error(getError($this->model));
            }
            $this->success('修改成功', U('index'));
        } else {
            // 获取管理员所有管理的角色
            $row = $this->model->getAdminInfo($id);
            $this->assign('row', $row);
            $this->load_data();
            $this->display('add');
        }
    }

    /**
     * 删除用户
     * @param $id
     */
    public function remove($id)
    {
        if ($this->model->deleteAdmin($id) === false) {
            $this->error(getError($this->model));
        }
        $this->success('删除成功', U('index'));


    }

    /**
     * 获取视图需要绑定的数据
     */
    public function load_data()
    {
        // 获取角色列表,前端使用ztree展示所以转换成json数据
        $role_model = D('Role');
        $roles = $role_model->getList();
        $this->assign('roles', json_encode($roles));
    }

    /**
     * 重置密码
     */
    public function alter()
    {
        // 获取session中的数据
        $userinfo=session('USERINFO');
        if (IS_POST) {
            // 获取数据
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }
            // 新增数据
            if (($password = $this->model->savePassword($userinfo['id'])) === false) {
                $this->error(getError($this->model));
            }
            $this->success('密码修改成功,您的新密码是  ' . $password, U('index'), 5);
        } else {
            // 获取管理员基本数据
            $row = $this->model->find($userinfo['id']);
            $this->assign('row', $row);
            $this->display();
        }
    }

    /**
     * 用户登陆
     */
    public function login()
    {
        if (IS_POST) {
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }
            if ($this->model->adminLogin() === false) {
                $this->error(getError($this->model));
            }
            $this->success('登陆成功', U('Index/index'));
        } else {
            $this->display();
        }
    }

    /**
     * 用户退出
     */
    public function logout()
    {
        session(null);
        cookie(null);
        $this->success('退出成功', U('login'));
    }


}