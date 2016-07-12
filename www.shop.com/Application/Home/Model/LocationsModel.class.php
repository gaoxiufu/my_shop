<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/10
 * Time: 14:53
 */

namespace Home\Model;


use Think\Model;

class LocationsModel extends Model
{

    /**
     * 获取父级id对应的子级城市
     * @param int $parent_id
     * @return mixed
     */
    public function getParentId($parent_id = 0)
    {
        return $this->where(['parent_id'=>$parent_id])->select();

    }

}