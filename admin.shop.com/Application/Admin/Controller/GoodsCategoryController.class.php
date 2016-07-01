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

    /**
     * 新增商品类别
     */
    public function add(){
        if (IS_POST) {
            //收集数据
            if($this->model->create()===false){
                $this->error(getError($this->model));
            }
            if($this->model->addCategory() === false){
                $this->error(getError($this->model));
            }
            $this->success('添加成功',U('index'));
        } else {
            $this->view();
            $this->display();
        }

    }

    /**
     * 修改商品类别
     * @param $id
     */
    public function edit($id){
        if (IS_POST) {
            //收集数据
            if($this->model->create()===false){
                $this->error(getError($this->model));
            }
            if($this->model->saveCategory() === false){
                $this->error(getError($this->model));
            }
            $this->success('修改成功',U('index'));
        } else {
            //展示数据
            $row = $this->model->find($id);
            $this->assign('row', $row);
            //获取所有的分类
            $this->view();
            $this->display('add');
        }

    }

    /**
     * 删除商品类别
     * @param $id
     */
    public function remove($id){
        if($this->model->deleteCategory($id)===false){
            $this->error(getError($this->model));
        }else{
            $this->success('删除成功',U('index'));
        }

    }



    /**
     * 在视图上增加一个顶级分类
     */
    public function view(){
        // 获取所有的商品分类
        $goods=$this->model->getList();
        // 将等级分开放入商品分类数组中.
        array_unshift($goods,['id'=>0,'name'=>'顶级分类','parent_id'=>0]);
        $goods = json_encode($goods);
        $this->assign('goods', $goods);
    }
}