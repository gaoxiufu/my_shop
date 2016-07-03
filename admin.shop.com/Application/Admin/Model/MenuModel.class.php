<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3
 * Time: 14:43
 */

namespace Admin\Model;

use Admin\Logic\NestedSets;
use Think\Model;

class MenuModel extends Model
{
    // 自动验证
    protected $_validate = [
        ['name', 'require', '菜单名称不能为空'],
    ];

    /**
     * 获取所有的商品菜单。
     * @return array
     */
    public function getList()
    {
        return $this->where(['status' => ['egt', 0]])->order('lft')->select();
    }

    /**
     * 新增菜单信息,使用NestedSets计算左右节点和深度
     * 同时现在关联数据
     * @return bool
     */
    public function addMenu()
    {
        // 开启事务
        $this->startTrans();
        // 将数据插入到菜单表
        unset($this->data[$this->getPk()]); // 删除主键字段
        // 创建ORM对象
        $orm = D('Mysql', 'Logic');
        // 创建NestedSets对象 需要传参依次为 ORM对象、完整表名、左节点字段名、右节点字段名、父ID字段名、主键ID、深度
        $nestedsets = new NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        // 调用NestedSets对象对象下insert方法，需要传参 父级ID 所有数据 和数据插入的位置 返回最后一条的ID
        if (($menu_id = $nestedsets->insert($this->data['parent_id'], $this->data, 'bottom')) === false) {
            $this->error = '添加菜单失败';
            $this->rollback(); // 回滚事务
            return false;
        }

        // 将数据插入到菜单权限关联表
        $menu_permission_model = M('MenuPermission');
        $permission_ids = I('post.permission_id');
        $data = [];
        foreach ($permission_ids as $permission_id) {
            $data[] = [
                'menu_id'       => $menu_id,
                'permission_id' => $permission_id,
            ];
        }
        if ($menu_permission_model->addAll($data) === false) {
            $this->error = '新增菜单关联失败';
            $this->rollback(); // 回滚事务
            return false;
        }
        // 提交事务
        $this->commit();
        return true;
    }

    /**
     * 获取菜单基本数据
     * 获取菜单权限关联数据并且装换成json数据
     */
    public function getMenuInfo($id)
    {
        // 获取菜单基本信息
        $row = $this->find($id);
        // 获取关联权限 ,装换成json数据
        $menu_permission_model = M('MenuPermission');
        $row['permission_ids'] = json_encode($menu_permission_model->where(['menu_id' => $id])->getField('permission_id', true));
        // 返回数据
        return $row;
    }

    /**
     * 修改菜单信息
     * 1.保存菜单基本信息
     * 2.删除历史关联数据
     * 3.更新关联数据
     * @param $id
     * @return bool
     */
    public function saveMenu($id)
    {
        // 开启事务
        $this->startTrans();
        // 判断不让自身移动到其自身子类下
        // 获取原父级ID
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
        // 保存菜单基本数据
        if ($this->save() === false) {
            $this->rollback(); // 回滚事务
            return false;
        }

        // 删除原有的菜单关联权限
        $menu_permission_model = M('MenuPermission');
        if ($menu_permission_model->where(['menu_id' => $id])->delete() === false) {
            $this->error = '删除历史关联失败';
            $this->rollback(); // 回滚事务
            return false;
        }

        // 插入新的菜单关联
        $permission_ids = I('post.permission_id');
        $data = [];
        foreach ($permission_ids as $permission_id) {
            $data[] = [
                'menu_id'       => $id,
                'permission_id' => $permission_id,
            ];
        }
        if ($menu_permission_model->addAll($data) === false) {
            $this->error = '更新关联数据失败';
            $this->rollback(); // 回滚事务
            return false;
        }
        // 提交事务
        $this->commit();
        return true;
    }

    /**
     * 删除菜单
     * 删除基本信息
     * 删除管理数据
     * @param $id
     * @return bool
     */
    public function deleteMenu($id){
        // 开启事务
        $this->startTrans();
        // 获取后代权限
        $menu_lr = $this->field('lft,rght')->find($id); // 获取左右边界
        $cond = [
            'lft'  => ['egt', $menu_lr['lft']],
            'rght' => ['elt', $menu_lr['rght']],
        ];
        $menu_ids = $this->where($cond)->getField('id', true); // 获取边界类的使用权限ID
        //删除相关的权限关联
        $menu_permission_model = M('MenuPermission');
        $res = $menu_permission_model->where(['menu_id' => ['in', $menu_ids]])->delete();
        if ($res === false) {
            $this->error = '删除关联失败';
            $this->rollback(); // 回滚事务
        }

        // 删除权限和子类权限
        // 创建ORM对象
        $orm = D('Mysql', 'Logic');
        // 创建NestedSets对象 需要传参依次为 ORM对象、完整表名、左节点字段名、右节点字段名、父ID字段名、主键ID、深度
        $nestedsets = new NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        // 物理删除数据,重新计算左右节点和深度,delete方法会删除分类下的所有类别
        if ($nestedsets->delete($id) === false) {
            $this->rollback(); // 回滚事务
        }

        // 提交事务
        $this->commit();
        return true;


    }

}