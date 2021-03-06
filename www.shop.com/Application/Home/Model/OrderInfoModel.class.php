<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/12
 * Time: 9:50
 */

namespace Home\Model;


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
     * 新增订单信息
     * 1.将基本信息保存到订单表
     * 2.将订单明细保存到订单明细表
     * 3.将发票信息保存到发票数据表
     * 4.清空购物车数据
     * 5.库存不足提示跳转,库存足减去库存
     */
    public function addOrder()
    {
        // 开启事务
        $this->startTrans();
        // 1.保存订单基本信息
        // 1.1获取收货人信息(会员ID 会员名字 省份 城市 区县 详细地址 手机号)
        $address_id = I('post.address_id');
        $address_model = D('Address');
        $address = $address_model->getAddressInfo($address_id, 'member_id,name,province_name,city_name,area_name,detail_address,tel');
        $this->data = array_merge($this->data, $address);

        // 1.2获取方式(送货方式ID 送货方式名字 运费)
        $delivery = M('Delivery')->field('name as delivery_name,price as delivery_price ')->where(['id' => I('post.delivery_id')])->find();
        $this->data = array_merge($this->data, $delivery);

        // 1.3获取支付方式(支付方式 支付方式名字)
        $payment = M('Payment')->field('name as pay_type_name')->where(['id' => I('post.pay_type_id')])->find();
        $this->data = array_merge($this->data, $payment);

        // 1.4商品金额 getShoppingCar
        $ShoppingCar = D('ShoppingCar')->getShoppingCar();
        $this->data['price'] = $ShoppingCar['total_price'];

        // 1.5订单状态
        $this->data['status'] = 1;
        $this->data['inputtime'] = NOW_TIME;

        // 1.6数据新增到订单表
        if (($order_id = $this->add()) === false) {
            $this->rollback();
            $this->error = '订单基本信息保存失败!';
            return false;
        }

        // 2. 保存订单明细
        // 2.1 准备数据
        $data = [];
        foreach ($ShoppingCar['goods_info'] as $goods) {
            $data[] = [
                'order_info_id' => $order_id,
                'goods_id'      => $goods['id'],
                'goods_name'    => $goods['name'],
                'logo'          => $goods['logo'],
                'amount'        => $goods['amount'],
                'total_price'   => $goods['sub_total'],
                'price'         => $goods['shop_price'],
            ];
        }

        // 2.2 将数据保存到订单详情表
        $order_info_item_model = M('OrderInfoItem');
        if ($order_info_item_model->addAll($data) === false) {
            $this->error = '保存订单详情失败';
            $this->rollback();
            return false;
        }


        // 3. 保存发发票
        // 3.1 获取发票数据
        $receipt_type = I('post.receipt_type'); // 判断发票抬头
        if ($receipt_type == 1) {
            $receipt_title = $address['name'];
        } else {
            $receipt_title = I('post.company_name');
        }
        // 3.2 拼接发票内容
        $receipt_content = I('post.receipt_content');
        /**
         * 抬头
         * 商品 199.00 * 1  199.00
         * 总计:199.00
         */
        $receipt_intro = '';
        switch ($receipt_content) {
            case 1;
                foreach ($ShoppingCar['goods_info'] as $goods) {
                    $receipt_intro = $goods['name'] . "\t" . $goods['shop_price'] . '×' . $goods['amount'] . "\t" . $goods['sub_total'];
                }
                break;
            case 2;
                $receipt_intro = '办公用品';
                break;
            case 3;
                $receipt_intro = '体育休闲';
                break;
            default:
                $receipt_intro = '耗材';
                break;
        }
        $content = $receipt_title . "\r\n" . $receipt_intro . "\r\n总计：" . $ShoppingCar['total_price'];
        // 3.3 准备数据
        $data = [
            'name'          => $receipt_title,
            'content'       => $content,
            'price'         => $ShoppingCar['total_price'],
            'inputtime'     => NOW_TIME,
            'member_id'     => $address['member_id'],
            'order_info_id' => $order_id,
        ];
        // 3.4 保存发票数据
        if (M('Invoice')->add($data) === false) {
            $this->error = '保存发票失败';
            $this->rollback();
            return false;
        }

        // 5. 下单同时减去库存
        // 5.1 判断库存是否充足
        $cond['_logic'] = 'OR';
        foreach ($ShoppingCar['goods_info'] as $goods) {
            $cond[] = [
                'id'    => $goods['id'],
                'stock' => ['egt', $goods['amount'],]
            ];
        }
        $goods_model = M('Goods');
        $rst = $goods_model->where($cond)->count();

        if ($rst < count($ShoppingCar['goods_info'])) { // 如果库存不存在,则返回false
            $this->error = '商品库存不足!';
            $this->rollback();
            return false;
        }
        // 5.2 减去库存数据 $User->where('id=5')->setDec('score',5);
        $re = $goods_model->where(['id' => $goods['id']])->setDec('stock', $goods['amount']);
        if ($re === false) {
            $this->error = '库存更新失败!';
            $this->rollback();
            return false;
        }

        // 4. 清空购物车数据 clearShoppingCar
        if (D('ShoppingCar')->clearShoppingCar() === false) {
            $this->error = '清空购物车失败';
            $this->rollback();
            return false;
        }

        // 提交事务
        $this->commit();
    }

    /**
     * 获取订单列表
     * @return mixed
     */
    public function getList()
    {
        $userinfo = session('USERINFO');
        $rows = $this->where(['member_id' => $userinfo['id']])->select();

        $order_info_item_model = M('OrderInfoItem');
        foreach ($rows as $key => $value) {
            $array = $order_info_item_model->field('goods_id,goods_name,logo')->where(['order_info_id' => $value['id']])->select();
            $rows[$key]['goods_list'] = $array;
        }
        return $rows;
    }

    /**
     * 获取订单信息
     * @param $id
     * @return mixed
     */
    public function getOrderInfoById($id)
    {
        return $this->find($id);
    }

}