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
     * * 将购物信息展示到购物车
     * 1.用户登陆
     * >>1.1从购物车表中获取商品id 和 amount
     * >>1.2从goods表中获取logo shop_price name
     * >>1.3将数据发送到前端
     * 2.用户未登陆
     * >>1.1从cookie中获取商品id 和 amount
     * >>1.2从goods表中获取logo shop_price name
     * >>1.3将数据发送到前端
     * @return array 商品信息
     */
    public function getShoppingCar()
    {
        // 获取用户session数据
        $userinfo = session('USERINFO');
        // 判断用户是否登陆
        if ($userinfo) { // 如果用户登陆就从数据中获取数据
            $car_list = $this->where(['member_id' => $userinfo['id']])->getField('goods_id,amount');
        } else { // 如果用户没有登陆就在cookie获取数据
            $car_list = cookie("SHOP_CAR");
        }

        if (!$car_list) { // 如果购物中没有数据,就将初始为零
            return [
                'total_price'     => '0.00',
                'goods_info_list' => [],
            ];
        }

        // 获取商品信息
        $goods_model = M('Goods');
        $cond = [
            'id'         => ['in', array_keys($car_list)],
            'is_on_sale' => 1,
            'status'     => 1,
        ];

        $goods_info = $goods_model->where($cond)->getField('id,logo,shop_price,name');
        $total_price = 0;

        // 获取当前会员积分
        $score = M('Member')->where(['id' => $userinfo['id']])->getField('score');

        // 获取当前会员等级/折扣
        $cond = [
            'bottom' => ['elt', $score],
            'top'    => ['egt', $score],
        ];
        $member_level = M('MemberLevel')->where($cond)->field('id,discount')->find();
        $member_level_id = $member_level['id']; //会员等级ID
        $discount = $member_level['discount']; // 会员折扣
        // 当前会员商品价格
        $member_goods_price_model = M('MemberGoodsPrice');
        foreach ($car_list as $key => $value) { // $key为商品ID,$value为商品数量
            // 获取会员单价
            $cond = [
                'goods_id'        => $key,
                'member_level_id' => $member_level_id,
            ];
            $member_price = $member_goods_price_model->where($cond)->getField('price');
            if ($member_price) {
                $goods_info[$key]['shop_price'] = $member_price;
            } elseif($userinfo){ // 判断是否登陆
                $goods_info[$key]['shop_price'] = get_number_format($goods_info[$key]['shop_price'] * $discount / 100);
            }else{
                $goods_info[$key]['shop_price'] = get_number_format($goods_info[$key]['shop_price']);
            }

            // 将商品数量放入商品信息数组中
            $goods_info[$key]['amount'] = $value;
            // 获取小计价格 sub_total
            $goods_info[$key]['sub_total'] = get_number_format($goods_info[$key]['shop_price'] * $value);
            // 获取合计价格 $total_price
            $total_price += $goods_info[$key]['sub_total'];
        }
        $total_price = get_number_format($total_price);

        return compact('goods_info', 'total_price');
    }

    /**
     * 情况购物车
     * @return mixed
     */
    public function clearShoppingCar()
    {
        // 获取用户session数据
        $userinfo = session('USERINFO');
        return $this->where(['member_id' => $userinfo['id']])->delete();
    }

}