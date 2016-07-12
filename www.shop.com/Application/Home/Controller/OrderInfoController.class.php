<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/12
 * Time: 9:50
 */

namespace Home\Controller;


use Think\Controller;

class OrderInfoController extends Controller
{
    /**
     * @var \Home\Model\OrderInfoModel
     */
    private $model = null;

    protected function _initialize()
    {
        $this->model = D('OrderInfo');
    }

    /**
     * 新增订单信息
     * 1.将基本信息保存到订单表
     * 2.将订单明细保存到订单明细表
     * 3.将发票信息保存到发票数据表
     */
    public function add()
    {
        if(IS_POST){
            // 收集数据
            if($this->model->create()===false){
                $this->error(getError($this->model));
            }
            // 新增数据
            if($this->model->addOrder()===false){
                $this->error(getError($this->model));
            }
            // 成功跳转
            $this->success('创建订单成功',U('ShopCar/flow3'));
        }else{
            $this->error('拒绝直接访问');
        }

    }

}