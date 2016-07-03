<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/1
 * Time: 21:46
 */

namespace Admin\Model;


use Admin\Logic\NestedSets;
use Think\Model;


class PermissionModel extends Model
{
    // 自动验证权限名称
    protected $_validate = [
        ['name', 'require', '权限名称不能为空']
    ];

    /**
     * 获取权限列表
     * @return mixed
     */
    public function getList()
    {
        return $this->where(['status' => 1])->order('lft')->select();

    }

    /**
     * 新增权限
     * @return false|int
     */
    public function addPermission()
    {

        // 删除主键字段
        unset($this->data[$this->getPk()]);
        // 创建ORM对象
        $orm = D('Mysql', 'Logic');
        // 创建NestedSets对象 需要传参依次为 ORM对象、完整表名、左节点字段名、右节点字段名、父ID字段名、主键ID、深度
        $nestedsets = new NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        // 调用NestedSets对象对象下insert方法，需要传参 父级ID 所有数据 和数据插入的位置
        if ($nestedsets->insert($this->data['parent_id'], $this->data, 'bottom') === false) {
            $this->error = '添加权限失败';
            return false;
        }
        return true;

    }

    /**
     * 修改权限
     * @return bool
     */
    public function savePermission()
    {
        // 判断是否修改了父级菜单  如果没有就不创建NestedSets对象
        $parent_id = $this->getFieldById($this->data['id'], 'parent_id');
        if ($this->data['parent_id'] != $parent_id) {
            // 创建ORM对象
            $orm = D('Mysql', 'Logic');
            // 创建NestedSets对象 需要传参依次为 ORM对象、完整表名、左节点字段名、右节点字段名、父ID字段名、主键ID、深度
            $nestedsets = new NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
            // moveUnder方法计算左右节点和层级 参数为 当前ID 要移动到的父级ID 插入位置
            $result = $nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom');
            if ($result === false) {
                $this->error = '不能将父类移动到子类下!';
                return false;
            }
        }
        return $this->save();
    }

    /**
     *删除权限和子类权限.同时删除角色关联. 删除菜单关联
     * @param $id
     * @return bool
     */
    public function deletePermission($id)
    {
        // 开启事务
        $this->startTrans();
        // 获取后代权限
        $permission_lr = $this->field('lft,rght')->find($id); // 获取左右边界
        $cond = [
            'lft'  => ['egt', $permission_lr['lft']],
            'rght' => ['elt', $permission_lr['rght']],
        ];
        $permission_ids = $this->where($cond)->getField('id', true); // 获取边界类的使用权限ID

        //删除相关的权限关联
        $role_permission_model = M('RolePermission');
        if ($role_permission_model->where(['permission_id' => ['in', $permission_ids]])->delete() === false) {
            $this->error = '删除关联角色失败';
            $this->rollback(); // 回滚事务
        } else {
            echo 1;
        }


        // 删除关联的菜单
        $menu_permission_model = M('MenuPermission');
        if ($menu_permission_model->where(['permission_id' => $id])->delete() === false) {

            $this->error = '删除菜单关联失败';
            $this->rollback(); // 回滚事务
        } else {
            echo 2;
        }

        // 删除权限和子类权限
        // 创建ORM对象
        $orm = D('Mysql', 'Logic');
        // 创建NestedSets对象 需要传参依次为 ORM对象、完整表名、左节点字段名、右节点字段名、父ID字段名、主键ID、深度
        $nestedsets = new NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        // 物理删除数据,重新计算左右节点和深度,delete方法会删除分类下的所有类别
        if ($nestedsets->delete($id) === false) {
            $this->rollback(); // 回滚事务
        } else {
            echo 3;
            // 提交事务
            $this->commit();
            return true;
        }

    }


}