<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/14
 * Time: 0:07
 */

namespace Admin\Model;


use Think\Model;

class OrderInfoModel extends Model
{
    public $statuses = [
        0 => '已取消',
        1 => '待支付',
        2 => '待发货',
        3 => '待收货',
        4 => '完成',
    ];

    /**
     * 获取订单信息,降序排列
     * @return mixed
     */
    public function getList()
    {
        return $this->order('inputtime desc')->select();
    }
}