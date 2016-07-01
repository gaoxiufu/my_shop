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


class permissionModel extends Model
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
        return $nestedsets->insert($this->data['parent_id'], $this->data, 'bottom');
    }

    /**
     * 修改权限
     * @return bool
     */
    public function savePermission()
    {
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

    public function deletePermission(){

    }
}