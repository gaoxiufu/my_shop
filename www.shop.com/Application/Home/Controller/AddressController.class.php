<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/10
 * Time: 16:12
 */

namespace Home\Controller;


use Think\Controller;

class AddressController extends Controller
{

    /**
     * @var \Home\Model\AddressModel
     */
    private $model = null;

    protected function _initialize()
    {
        $this->model = D('Address');
    }

    /**
     * 新增收货地址
     */
    public function add()
    {
        if ($this->model->create() === false) {
            $this->error(getError($this->model));
        }
        if ($this->model->addLocation() === false) {
            $this->error(getError($this->model));
        }
        $this->success('地址添加成功', U('Index/location'));

    }

    /**
     * 修改用户收货地址
     * @param $id
     */
    public function modifyLocation($id)
    {
        if (IS_POST) {
            if ($this->model->create() === false) {
                $this->error(getError($this->model));
            }
            if ($this->model->saveLocation($id) === false) {
                $this->error(getError($this->model));
            }
            $this->success('地址修改成功', U('Index/location'));
        } else {
            // 获取省份
            $locations_model = D('Locations');
            $provinces = $locations_model->getParentId();
            $this->assign('provinces', $provinces);

            // 获取当前地址,用于回显
            $row = $this->model->getAddressInfo($id);
            $this->assign('row', $row);
            $this->display();
        }
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

    /**
     * 修改默认地址
     * @param $id
     */
    public function modifyDefault($id)
    {
        if ($this->model->saveDefault($id) === false) {
            $this->error('修改默认地址失败');
        } else {
            $this->success('修改默认地址成功', U('Index/location'));
        }
    }

    public function removeLocation($id)
    {
        if($this->model->delete($id)===false){
            $this->error('删除地址失败');
        }
         $this->success('删除地址成功', U('Index/location'));
    }
}