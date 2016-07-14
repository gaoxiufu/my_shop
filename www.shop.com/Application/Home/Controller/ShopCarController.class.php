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

    protected function _initialize()
    {
        $this->model = D('ShoppingCar');
    }

    /**
     * 将购物数据保存到购物车
     * @param $id
     * @param $amount
     */
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
            $shop_amount = $this->model->getAmount($id);
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
        // 获取商品信息
        $car_info = $this->model->getShoppingCar();
        $this->assign($car_info);
        $this->display();
    }

    /**
     * 结算页面
     */
    public function flow2()
    {
        // 获取用户session信息
        $userinfo = session('USERINFO');
        // 判断用户是否登陆
        if (!$userinfo) { // 如果没登陆就跳转到登陆页面,登陆后再跳转到订单页面
            cookie('__FORWARD__', __SELF__); // 将当前页面地址保存到cookie中，登陆后跳转
            $this->error('请先登陆', U('Member/login'));
        } else { // 如果登陆就直接引入订单信息视图
            // 获取收货人信息数据
            $address_model = D('Address');
            $addresses = $address_model->getList();
            $this->assign('addresses', $addresses);

            // 获取送货方式
            $delivery_model = D('Delivery');
            $deliverys = $delivery_model->getList();
            $this->assign('deliverys', $deliverys);

            // 获取支付方式
            $payment_model = D('Payment');
            $payments = $payment_model->getList();
            $this->assign('payments', $payments);

            // 获取购物车清单列表
            $car_info = $this->model->getShoppingCar();
            $this->assign($car_info);

            // 渲染视图
            $this->display();
        }
    }

    public function flow3()
    {
        $this->display();
    }

    /**
     * 删除购物车数据 待做
     * @param $id
     */
    public function deleteCar($id)
    {
        dump($id);
        exit;
        $this->model->delete($id);
    }
}