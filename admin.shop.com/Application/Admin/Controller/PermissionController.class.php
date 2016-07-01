<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/1
 * Time: 21:45
 */

namespace Admin\Controller;


use Think\Controller;

class permissionController extends Controller
{
    /**
     * 自动创建model对象
     * @var \Admin\Model\permissionModel
     */
    private $model = null;

    protected function _initialize()
    {
        $this->model = D('Permission');
    }

    public function index()
    {
        $rows = $this->model->getList();
        $this->assign('rows', $rows);
        $this->display();
    }

    public function add()
    {
        if (IS_POST) {
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }
            if ($this->model->addPermission()) {
                $this->error(getError($this->model));
            }
            $this->success('添加成功', U('index'));
        } else {
            $this->load_data();
            $this->display();
        }

    }

    public function edit($id)
    {
        if(IS_POST){
            // 收集数据
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }
            // 保存数据
            if ($this->model->savePermission() === false) {
                $this->error(getError($this->model));
            }

            $this->success('修改成功', U('index'));
        }else{
            // 获取数据
            $row = $this->model->find($id);
            // 传送数据
            $this->assign('row',$row);
            // json字符串,给ztree使用
            $this->load_data();
            $this->display('add');
        }
    }

    public function remove($id)
    {
        if($this->model->deletePermission($id) === false){
            $this->error(getError($this->model));
        }
        //跳转
        $this->success('删除成功', U('index'));

    }

    /**
     * 获取视图需要绑定的数据
     */
    public function load_data()
    {
        // 获取权限列表,前端使用ztree展示所以转换成json数据
        $permissions = $this->model->getList();
        array_unshift($permissions, ['id' => 0, 'name' => '顶级权限', 'parent_id' => null]);
        $this->assign('permissions', json_encode($permissions));
    }
}