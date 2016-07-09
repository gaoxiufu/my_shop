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
    public function addUp($goods_id, $amount)
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
    public function addCar($goods_id, $amount)
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

    /**
     * 将cookie中的购物车保存到数据库
     * @return bool
     */
    public function cookie2db()
    {
        // 获取用户session数据
        $userinfo = session('USERINFO');
        // 获取cookie购物车信息
        $cookie_car = cookie('SHOP_CAR');
        if (!$cookie_car) {
            return true;
        }
        // 准备数据
        $cond = [
            'member_id' => $userinfo['id'],
            'goods_id'  => [
                'in', array_keys($cookie_car),
            ],
        ];
        // 删除数据库购物车相同商品的数据
        if ($this->where($cond)->delete() === false) {
            return false;
        }
        // 准备数据
        $data = [];
        foreach ($cookie_car as $key => $value) {
            $data[] = [
                'member_id' => $userinfo['id'],
                'amount'    => $value,
                'goods_id'  => $key,
            ];
        }
        // 清空cookie
        cookie('SHOP_CAR', null);
        // 现在购物车数据到数据库
        return $this->addAll($data);
    }

    /**
     * 将购物信息展示到购物车
     * 1.用户登陆
     * >>1.1从购物车表中获取商品id 和 amount
     * >>1.2从goods表中获取logo shop_price name
     * >>1.3将数据发送到前端
     * 2.用户未登陆
     * >>1.1从cookie中获取商品id 和 amount
     * >>1.2从goods表中获取logo shop_price name
     * >>1.3将数据发送到前端
     */
    public function getShoppingCar()
    {
        // 获取用户session数据
        $userinfo=session('USERINFO');
        // 判断用户是否登陆
        if($userinfo){ // 如果用户登陆就从数据中获取数据
            $car_list=$this->where(['member_id'=>$userinfo['id']])->getField('goods_id,amount');
        }else{ // 如果用户没有登陆就在cookie获取数据
            $car_list=cookie("SHOP_CAR");
        }
        // 获取商品信息

    }

}