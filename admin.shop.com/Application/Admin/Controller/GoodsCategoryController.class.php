<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/28
 * Time: 0:17
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsCategoryController extends Controller
{

    /**
     * 自动创建model对象
     * @var \Admin\Model\GoodsCategoryModel
     */
    private $model = null;

    protected function _initialize()
    {
        $this->model = D('GoodsCategory');
    }

    /**
     * 展示商品类别
     */
    public function index(){
        $rows=$this->model->getList();
        $this->assign('rows',$rows);
        $this->display();
    }

    public function add(){
        if (IS_POST) {
            //收集数据
            if($this->model->create()===false){
                $this->error(get_error($this->model));
            }
            if($this->model->add() === false){
                $this->error(get_error($this->model));
            }
            $this->success('添加成功',U('index'));
        } else {
            $goods_categories = json_encode($this->model->getList());
            $this->assign('goods_categories', $goods_categories);
            $this->display();
        }

    }

    public function edit($id){
        if (IS_POST) {
            //收集数据
            if($this->model->create()===false){
                $this->error(get_error($this->model));
            }
            if($this->model->save() === false){
                $this->error(get_error($this->model));
            }
            $this->success('修改成功',U('index'));
        } else {
            //展示数据
            $row = $this->model->find($id);
            $this->assign('row', $row);
            //获取所有的分类
            $goods_categories = json_encode($this->model->getList());
            $this->assign('goods_categories', $goods_categories);
            $this->display('add');
        }

    }

    public function delete(){

    }


}