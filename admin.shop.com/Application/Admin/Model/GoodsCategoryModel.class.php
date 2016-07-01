<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/28
 * Time: 0:18
 */

namespace Admin\Model;

use Admin\Logic\NestedSets;
use Think\Model;

class GoodsCategoryModel extends Model
{

    protected $patchValidate = true; //开启批量验证
    /**
     * name 必填，不能重复
     * status 可选值0-1
     * sort 必须是数字
     * @var type
     */
    protected $_validate = [
        ['name', 'require', '商品分类名称不能为空'],
    ];

    /**
     * 排序显示信息
     * @return mixed
     */
    public function getList()
    {
        return $this->where(['status' => ['egt', 0]])->order("lft")->select();
    }


    /**
     * 新增数据(使用NestedSets插件计算左右节点 和深度)
     */
    public function addCategory()
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
     * 修改品牌分类(使用NestedSets插件计算左右节点和深度)
     * @return bool
     */
    public function saveCategory()
    {
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
        return $this->save();
    }

    /**
     * 逻辑删除分类(使用NestedSets插件计算左右节点和深度)
     * @param $id
     * @return bool
     */
    public function deleteCategory($id)
    {
        $cont['parent_id'] = $id;
        if ($this->where($cont)->select() == null) { // 如果有子类不能删除
            // 创建ORM对象
            $orm = D('Mysql', 'Logic');
            // 创建NestedSets对象 需要传参依次为 ORM对象、完整表名、左节点字段名、右节点字段名、父ID字段名、主键ID、深度
            $nestedsets = new NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
            // 物理删除数据,重新计算左右节点和深度,delete方法会删除分类下的所有类别
            return $nestedsets->delete($id);
        } else {
            $this->error = '不能删除有子类的分类!';
            return false;
        }
    }

}