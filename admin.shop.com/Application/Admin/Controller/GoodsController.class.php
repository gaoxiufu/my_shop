<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/29
 * Time: 17:52
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsController extends Controller
{

    /**
     * 自动创建model对象
     * @var \Admin\Model\GoodsModel
     */
    private $model = null;

    protected function _initialize()
    {
        $this->model = D('Goods');
    }


    /**
     * 展示商品列表
     */
    public function index()
    {
        // 按照商品类名搜索
        $name = I('get.name');
        $cond = [];
        if ($name) {
            $cond['name'] = ['like', '%' . $name . '%'];
        }

        // 按照商品分类搜索
        $goods_category_id = I('get.goods_category_id');
        if ($goods_category_id) {
            $cond['goods_category_id'] = $goods_category_id;
        }

        // 按照品牌进行搜索
        $brand_id = I('get.brand_id');
        if ($brand_id) {
            $cond['brand_id'] = $brand_id;
        }

        // 按照推荐状态搜索
        $goods_status = I('get.goods_status');
        if ($goods_status) {
            $cond[] = 'goods_status &'.$goods_status;
        }

        // 上架状态搜索
        $is_on_sale = I('get.is_on_sale');
        if (strlen($is_on_sale)) {
            $cond['is_on_sale'] = $is_on_sale;
        }
//
//        if($is_on_sale==='0'){
//            $cond['is_on_sale'] = $is_on_sale;
//        }
//        if($is_on_sale==='1'){
//            $cond['is_on_sale'] = $is_on_sale;
//        }

        // 回显所有商品分类
        $goods_category_model = D('GoodsCategory');
        $goods_categorys = $goods_category_model->getList();
        $this->assign('goods_categorys', $goods_categorys);

        // 回显所有品牌
        $brand_model = D('Brand');
        $brands = $brand_model->getList();
        $this->assign('brands', $brands);

        // 商品促销状态
        $goods_statuses = [
            ['id' => 1, 'name' => '精品'],
            ['id' => 2, 'name' => '新品'],
            ['id' => 4, 'name' => '热销'],
        ];
        $this->assign('goods_statuses', $goods_statuses);

        // 商品上架状态
        $is_on_sales = [
            ['id' => 1, 'name' => '上架'],
            ['id' => 0, 'name' => '下架'],
        ];
        $this->assign('is_on_sales', $is_on_sales);



        // 分页
        $data = $this->model->getPage($cond);
        $this->assign($data);
        $this->display();
    }

    /**
     * 新增商品
     */
    public function add()
    {
        if (IS_POST) {
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }
            if ($this->model->addGoods() === false) {
                $this->error(getError($this->model));
            } else {
                $this->success('添加成功', U('index'));
            }
        } else {

            // 传送数据
            $this->load_data();
            // 渲染视图
            $this->display();
        }

    }

    /**
     * 编辑商品
     * @param $id
     */
    public function edit($id)
    {
        if (IS_POST) {
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }
            if ($this->model->saveGoodsInfo($id) === false) {
                $this->error(getError($this->model));
            } else {
                $this->success('修改成功', U('index'));
            }

        } else {
            $row = $this->model->getGoodsInfo($id);
            $this->assign('row', $row);
            $this->load_data();
            $this->display('add');
        }
    }

    /**
     * 逻辑删除商品信息
     */
    public function remove($id)
    {
        if ($this->model->saveGoos($id) === false) {
            $this->error(getError($this->model));
        } else
            $this->success('删除成功', U('index'));
    }

    /**
     * 获取视图需要绑定的数据
     */
    public function load_data()
    {
        // 获取商品分类 传送JOSN数据
        $goods_category_model = D('GoodsCategory');
        $goods_categorys = $goods_category_model->getList();
        $this->assign('goods_categorys', json_encode($goods_categorys));

        // 获取商品品牌
        $brand_model = D('Brand');
        $brands = $brand_model->getList();
        $this->assign('brands', $brands);

        // 获取商品分类
        $supplier_model = D('Supplier');
        $suppliers = $supplier_model->getList();
        $this->assign('suppliers', $suppliers);
        // 商品编码
        $sn = $this->model->createSn();
        $this->assign('sn', $sn);
    }

    /**
     * 删除相册
     */
    public function removeGallery($id)
    {
        $goods_gallery_model = M('GoodsGallery');
        if ($goods_gallery_model->delete($id) === false) {
            $this->error('删除失败');
        } else {
            $this->success('删除成功');
        }
    }
}