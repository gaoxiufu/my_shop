<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/11
 * Time: 18:45
 */

namespace Home\Model;


use Think\Model;

class DeliveryModel extends Model
{

    /**
     * 获取快递方式列表
     * @return mixed
     */
    public function getList()
    {
        return $this->where(['status'=>1])->order('sort')->select();
    }
}