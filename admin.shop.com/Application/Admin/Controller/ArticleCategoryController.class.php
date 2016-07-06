<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/25
 * Time: 11:39
 */

namespace Admin\Controller;


use Think\Controller;

class ArticleCategoryController extends Controller
{
    /**
     * 自动创建model对象
     * @var \Admin\Model\ArticleCategoryModel
     */
    private $model = null;

    protected function _initialize()
    {
        $this->model = D('ArticleCategory');
    }

    /**
     * 文章信息列表展示
     * 1.模糊搜索
     * 2.分页
     */
    public function index()
    {
        // 获取模糊搜索条件
        $name = I('get.name');
        // 获取总条数
        $cont['status'] = ['egt', 0];
        if ($name) {
            $cont['name'] = ['like', '%' . $name . '%'];
        }
        //$rows=$this->model->where($cont)->select();
        $rows = $this->model->getPage($cont);
        $this->assign($rows);
        $this->display();
    }

    /**
     * 添加文章分类
     */
    public function add()
    {
        if (IS_POST) {
            // 获取数据
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }
            if ($this->model->add() === false) {
                $this->error(getError($this->model));
            } else {
                $this->success('文章分类添加成功', U('index'));
            }
        } else {
            $this->display();
        }
    }

    /**
     * 通过ID修改文章类信息
     * @param int $id 当前修改的数据的ID
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
                $this->success('修改成功', U('index'));
            }

        } else {
            // 通过ID查询数据
            $row = $this->model->find($id);
            $this->assign($row);
            $this->display('add');
        }
    }

    /**
     * 逻辑删除文章分类
     * @param int $id
     */
    public function remove($id){
        $data=[
            'id'=>$id,
            'name'=>['exp','concat(name,"_del")'],
            'status'=>-1
        ];
        if($this->model->setField($data)===false){
            $this->error(getError($this->model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }

}