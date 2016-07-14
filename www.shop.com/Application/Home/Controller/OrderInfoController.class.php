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
        if (IS_POST) {
            // 收集数据
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }
            // 新增数据
            if ($this->model->addOrder() === false) {
                $this->error(getError($this->model));
            }
            // 成功跳转
            $this->success('创建订单成功', U('ShopCar/flow3'));
        } else {
            $this->error('拒绝直接访问');
        }

    }

    /**
     * 订单列表展示
     */
    public function index()
    {
        // 获取商品分类,并将数据保存到缓存中减少数据库读取压力.
        if (!$goods_categorys = S('goods_categorys')) {
            $goods_category_model = D('GoodsCategory');
            $goods_categorys = $goods_category_model->getList('id,name,parent_id');
            S('goods_categorys', $goods_categorys, 3600);
        }
        $this->assign('goods_categorys', $goods_categorys);


        // 获取帮助类文档,并将数据保存到缓存中减少数据库读取压力.
        if (!$help_articles = S('help_articles')) {
            $article_category_model = D('Article');
            $help_articles = $article_category_model->getHelpArticle();
            S('help_articles', $help_articles, 3600);
        }
        $this->assign('help_articles', $help_articles);

        // 获取订单数据
        $rows = $this->model->getList();
        $this->assign('rows', $rows);
        $this->assign('statuses', $this->model->statuses);
        $this->display();
    }


    /**
     * 完成交易 ,订单状态修改为 完成
     * @param $id
     */
    public function finish($id)
    {
        $order_info_model = M('OrderInfo');
        if ($order_info_model->where(['id' => $id])->setField('status', 4) === false) {
            $this->error(getError($order_info_model));
        } else {
            $this->success('交易完成', U('index'));
        }
    }
}