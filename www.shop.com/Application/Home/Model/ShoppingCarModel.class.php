<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/8
 * Time: 23:01
 */

namespace Home\Model;


use Think\Model;

class ShoppingCarModel extends Model
{

    /**
     * 获取当前商品在购物车数据库中的数量
     * @param $goods_id
     * @return mixed
     */
    public function getAmount($goods_id)
    {
        // 从session中获取数据
        $userinfo = session('USERINFO');
        // 准备数据
        $cond = [
            'member_id' => $userinfo['id'],
            'goods_id'  => $goods_id,
        ];
        // 查询商品数量
        return $this->where($cond)->getField('amount');
    }

    /**
     * 累加当前商品在购物车中的数量
     * @param $amount
     * @param $goods_id
     * @return bool
     */
    public function addUp($goods_id,$amount)
    {
        // 从session中获取数据
        $userinfo = session('USERINFO');
        // 准备数据
        $cond = [
            'member_id' => $userinfo['id'],
            'goods_id'  => $goods_id,
        ];

        return $this->where($cond)->setInc('amount', $amount);
//        echo $this->getLastSql();exit;
    }

    /**
     * 新增商品在购物车数据表中
     * @param mixed|string $amount
     * @param array $goods_id
     * @return mixed
     */
    public function addCar($goods_id,$amount)
    {
        $userinfo = session('USERINFO');
        // 准备数据
        $data = [
            'member_id' => $userinfo['id'],
            'goods_id'  => $goods_id,
            'amount'    => $amount,
        ];
        return $this->add($data);
    }
}