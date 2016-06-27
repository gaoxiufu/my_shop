<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/25
 * Time: 9:32
 */

namespace Admin\Controller;


use Think\Controller;

class BrandController extends Controller
{
    /**
     * 自动创建model对象
     * @var \Admin\Model\BrandModel
     */
    private $model = null;

    protected function _initialize()
    {
        $this->model = D('Brand');
    }

    /**
     * 品牌列表
     * 1.展示品牌信息
     * 2.分页显示信息
     * 3.模糊搜索信息
     */
    public function index()
    {
        // 获取模糊搜索条件
        $name = I('get.name');
        if ($name) {
            $cont['name'] = ['like', '%' . $name . '%'];
        }
        $cont['status'] = ['egt', 0];
        // 获取数据站列表
        //$rows = $this->model->where($cont)->select();
        $rows = $this->model->getPage($cont);
        $this->assign($rows);
        $this->display();
    }

    /**
     * 添加品牌信息
     */
    public function add()
    {
        if (IS_POST) {
            // 获取数据
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }
dump($this->model->data());
            // 添加数据
            if ($this->model->add() === false) {
                $this->error(getError($this->model));
            } else {
                $this->success('添加成功！', U('index'));
            }
        } else {
            // 渲染视图
            $this->display();
        }
    }

    /**
     * * 修改品牌信息
     * 1.回显品牌信息
     * 2.提交修改
     * @param int $id
     */
    public function edit($id)
    {
        if (IS_POST) {
            // 获取数据
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }

            // 修改数据
            if ($this->model->save() === false) {
                $this->error(getError($this->model));
            } else {
                $this->success('修改成功！', U('index'));
            }

        } else {
            // 通过ID查询一条数据
            $row=$this->model->find($id);
            // 传送数据
            $this->assign($row);
            // 渲染视图
            $this->display('add');
        }
    }

    /**
     * 逻辑删除数据
     * @param $id
     */
    public function remove($id){
        $cont=[
            'id'=>$id,
            'name'=>['exp','concat(name,"_del")'],
            'status'=>-1
        ];
        if($this->model->setField($cont)===false){
            $this->error(getError($this->model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }

}