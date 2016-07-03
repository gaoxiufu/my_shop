<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3
 * Time: 14:43
 */

namespace Admin\Controller;

use Think\Controller;

class MenuController extends Controller
{
    /**
     * 自动创建model对象
     * @var \Admin\Model\MenuModel
     */
    private $model = null;

    protected function _initialize()
    {
        $this->model = D('Menu');
    }

    /**
     * 展示菜单列表
     */
    public function index()
    {
        $this->assign('rows', $this->model->getList());
        $this->display();
    }

    /**
     * 新增菜单
     */
    public function add()
    {
        if(IS_POST){
            if($this->model->create()===false){
                $this->error(getError($this->model));
            }
            if($this->model->addMenu()===false){
                $this->error(getError($this->model));
            }
            $this->success('添加成功',U('index'));
        }else{
            $this->load_data();
            $this->display();
        }

    }

    /**
     * 修改菜单
     * @param $id
     */
    public function edit($id)
    {
        if(IS_POST){
            if($this->model->create()===false){
                $this->error(getError($this->model));
            }
            if($this->model->saveMenu($id)===false){
                $this->error(getError($this->model));
            }
            $this->success('修改成功',U('index'));

        }else{
            // 获取回显数据
            $row=$this->model->getMenuInfo($id);
            $this->assign('row',$row);
            $this->load_data();
            $this->display('add');
        }

    }

    /**
     * 删除菜单
     * @param $id
     */
    public function remove($id)
    {
        if($this->model->deleteMenu($id)===false){
            $this->error(getError($this->model));
        }
        $this->success('删除成功',U('index'));
    }

    /**
     * 获取视图需要绑定的数据
     */
    public function load_data()
    {
        // 获取所有菜单
        $menus = $this->model->getList();
        array_unshift($menus, ['id' => 0, 'name' => '顶级菜单', 'parent_id' => 0]);
        $menus = json_encode($menus);
        $this->assign('menus', $menus);

        // 获取权限列表,前端使用ztree展示所以转换成json数据
        $permission_model = D('Permission');
        $permissions = $permission_model->getList();
        $this->assign('permissions', json_encode($permissions));

    }
}