<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{

    /**
     * 构造函数
     */
    protected function _initialize()
    {
        //判断是否需要展示商品分类
        if (ACTION_NAME == 'index') {
            $show_categroy = true;
        } else {
            $show_categroy = false;
        }
        $this->assign('show_category', $show_categroy);

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

        //获取用户登陆信息
        $userinfo = session('USERINFO');
        $this->assign('userinfo', $userinfo);

    }


    /**
     * 网站首页
     */
    public function index()
    {
        // 新品,精品,热销展示
        $goods_model = D('Goods');
        $data = [
            'goods_best_list' => $goods_model->getGoodsStatus(1),
            'goods_new_list'  => $goods_model->getGoodsStatus(2),
            'goods_hot_list'  => $goods_model->getGoodsStatus(4),
        ];
        $this->assign($data);

        $this->display();
    }

    /**
     * 获取商品信息
     * @param $id
     */
    public function goods($id)
    {
        $goods_model = D('Goods');
        $row = $goods_model->getGoods($id);
        if (!$row) {
            $this->error('商品已经下架', U('index'));
        }
        $this->assign('row', $row);
        $this->display();
    }

    /**
     * 收货地址管理
     */
    public function location()
    {
        // 获取省份
        $locations_model = D('Locations');
        $provinces = $locations_model->getParentId();
        $this->assign('provinces', $provinces);

        // 获取所有收货地址
        $address_model = D('Address');
        $addresses = $address_model->getlist();
        $this->assign('addresses', $addresses);
        $this->display();
    }


    /**
     * 获取市级或区县
     * @param $parent_id
     */
    public function getSubclass($parent_id)
    {
        $locations_model = D('Locations');
        $provinces = $locations_model->getParentId($parent_id);
        $this->ajaxReturn($provinces);
    }


}