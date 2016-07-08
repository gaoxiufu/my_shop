<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/7
 * Time: 22:30
 */

namespace Home\Model;


use Think\Model;

class GoodsCategoryModel extends Model
{
    /**
     * 获取商品分类
     * @param string $field
     * @return mixed
     */
    public function getList($field='*')
    {
        return $this->field($field)->where(['status'=>1])->select();
    }
}