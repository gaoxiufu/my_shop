<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/14
 * Time: 0:06
 */

namespace Admin\Controller;


use Think\Controller;

class OrderInfoController extends Controller
{
    /**
     * 展示订单信息
     */
    public function index()
    {
        $order_info_model = D('OrderInfo');
        $rows = $order_info_model->getList();
        $this->assign('rows', $rows);
        $this->assign('statuses', $order_info_model->statuses);
        $this->display();
    }

    /**
     * 发货,并将订单状态修改为 待收货
     * @param $id
     */
    public function send($id)
    {
        $order_info_model = D('OrderInfo');
        if ($order_info_model->where(['id' => $id])->setField('status', 3) === false) {
            $this->error(getError($order_info_model));
        } else {
            $this->success('发货成功', U('index'));
        }
//        $this->display();
    }

    /**
     * 订单超时清除
     * 1.获取订单生成时间
     * 2.超时时间=订单生成时间+15分钟
     * 3.如果超时时间>当前时间.定单就超时
     * 4.将订单状态修改为0(已取消) ,前台后台同步显示
     */
    public function autoRemoveOrder()
    {
        // 获取订单生成时间
        $order_info_model = D('OrderInfo');
        $cond = [
            'inputtime' => ['lt', NOW_TIME - 900],
            'status'    => 1
        ];
        $order_ids = $order_info_model->where($cond)->getField('id', true);
        // 超时订单,修改状态
        $order_info_model->where(['id' => ['in', $order_ids]])->setField('status', 0);
        $order_info_item_model = M('OrderInfoItem');
        $goods_list = $order_info_item_model->where(['goods_info_id' => ['in', $order_ids]])->getField('id,goods_id,amount');
        // 恢复库存
        $goods_model = M('Goods');
        $data = [];
        foreach ($goods_list as $goods) {
            if (isset($data[$goods['goods_id']])) {

                $data[$goods['goods_id']] += $goods['amount'];
            } else {
                $data[$goods['goods_id']] = $goods['amount'];
            }
        }
        foreach ($data as $goods_id => $amount) {
            $goods_model->where(['id' => $goods_id])->setInc('stock', $amount);
        }
    }

}