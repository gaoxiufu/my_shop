<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/11
 * Time: 19:00
 */

namespace Home\Model;


use Think\Model;

class PaymentModel extends Model
{
    /**
     * 获取支付方式列表
     * @return mixed
     */
    public function getList()
    {
        return $this->where(['status'=>1])->order('sort')->select();
    }
}