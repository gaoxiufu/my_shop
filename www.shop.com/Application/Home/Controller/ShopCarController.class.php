<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/8
 * Time: 22:31
 */

namespace Home\Controller;


use Think\Controller;

class ShopCarController extends Controller
{
    /**
     * @var \Home\Model\ShoppingCarModel
     */
    private $model = null;

    protected function _initialize() {
        $this->model = D('ShoppingCar');
    }

    public function shopcar($id, $amount)
    {
        $userinfo = session('USERINFO');
        if (!$userinfo) {  // 用户未登陆
            $key = 'SHOP_CAR'; // 设置键
            $car_list = cookie($key); // 获取cooKie 中的值
            if (isset($car_list[$id])) { // 如果cookie中有数量,那么就把商品数量相加
                $car_list[$id] += $amount;
            } else { // 如果cookie中没有数量,那么商品数量就等于获取的数量
                $car_list[$id] = $amount;
            }
            cookie($key, $car_list, 604800); // 保存到cookie中
        } else {// 用户登陆
            // 获取当前用户商品数量
            $shop_amount =  $this->model->getAmount($id);
            if ($shop_amount) {
                // 如果商品数量存在就增加数据
                 $this->model->addUp($id, $amount);
            } else {
                // 如果商品数量不存在就新增数据
                 $this->model->addCar($id, $amount);
            }
        }
        $this->success('添加成功', U('flow1'));
    }

    /**
     * 购物车
     */
    public function flow1()
    {
       $this->model->getShoppingCar();
        $this->display();
    }
}