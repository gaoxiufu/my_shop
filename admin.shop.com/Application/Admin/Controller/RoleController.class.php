<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/2
 * Time: 12:57
 */

namespace Admin\Controller;


use Think\Controller;

class RoleController extends Controller
{
    /**
     * 自动创建model对象
     * @var \Admin\Model\RoleModel
     */
    private $model = null;

    protected function _initialize()
    {
        $this->model = D('Role');
    }

    /**
     * 展示角色列表
     */
    public function index()
    {
        // 获取模糊搜索条件
        $name = I('get.name');
        $cond = [];
        if ($name) {
            $cond['name'] = ['like', '%' . $name . '%'];
        }
        $rows = $this->model->getPageList($cond);
        $this->assign($rows);
        $this->display();
    }

    /**
     * 新增角色
     */
    public function add()
    {
        if (IS_POST) {
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }
            if ($this->model->addRole() === false) {
                $this->error(getError($this->model));
            }
            $this->success('添加成功', U('index'));
        } else {
            // 获取权限数据
            $this->load_data();
            $this->display();
        }
    }

    /**
     *删除权限.并同时删除角色关联
     */
    public function edit($id)
    {
        if (IS_POST) {
            // 获取数据
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }
            // 修改数据
            if ($this->model->saveRoleInfo() === false) {
                $this->error(getError($this->model));
            }
            $this->success('修改成功', U('index'));

        } else {
            // 获取回显数据
            $row = $this->model->getPermissionInfo($id);
            $this->assign('row', $row);
            $this->load_data();
            $this->display('add');
        }

    }

    /**
     * 删除角色
     * @param $id
     */
    public function remove($id)
    {
        if($this->model->deleteRole($id)===false){
            $this->error(getError($this->model));
        }
        $this->success('删除成功',U('index'));
    }

    /**
     * 获取视图需要绑定的数据
     */
    public function load_data()
    {
        // 获取权限列表,前端使用ztree展示所以转换成json数据
        $permission_model = D('Permission');
        $permissions = $permission_model->getList();
        $this->assign('permissions', json_encode($permissions));
    }
}