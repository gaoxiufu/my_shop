<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/10
 * Time: 16:12
 */

namespace Home\Model;


use Think\Model;

class AddressModel extends Model
{
    protected $patchValidate = true;

    protected $_validate = [
        ['name', 'require', '收货人姓名不能为空'],
        ['province_id', 'require', '省份不能为空'],
        ['city_id', 'require', '市级城市不能为空'],
        ['area_id', 'require', '区县不能为空'],
        ['detail_address', 'require', '详细地址不能为空'],
        ['tel', 'require', '手机不能为空'],
    ];

    /**
     * 添加一条用户收货地址
     */
    public function addLocation()
    {
        // 获取用户session信息
        $userinfo = session('USERINFO');
        if ($this->data['is_default']) {
            // 如果有勾选默认地址,那么先将其他默认地址改为非默认
            $this->where(['member_id' => $userinfo['id']])->setField('is_default', 0);
        }
        $this->data['member_id'] = $userinfo['id'];
        $this->add();
    }

    /**
     * 获取用户所有的收货地址
     * @return mixed
     */
    public function getlist()
    {
        // 获取用户session信息
        $userinfo = session('USERINFO');
        return $this->where(['member_id' => $userinfo['id']])->select();
    }

    /**
     * 获取当前用户收货地址信息
     * @param $id
     * @return mixed
     */
    public function getAddressInfo($id,$field='*')
    {
        // 获取用户session数据
        $userinfo = session('USERINFO');
        $cond = [
            'member_id' => $userinfo['id'],
            'id'        => $id,
        ];
        return $this->field($field)->where($cond)->find();
    }

    /**
     * 修改收货地址
     * @param $id
     * @return bool
     */
    public function saveLocation($id)
    {
        // 获取用户session信息
        $userinfo = session('USERINFO');

        if ($this->data['is_default']) {
            // 如果有勾选默认地址,那么先将其他默认地址改为非默认
            $this->where(['member_id' => $userinfo['id'],])->setField('is_default', 0);
        }
        $cond = [
            'member_id' => $userinfo['id'],
            'id'        => $id,
        ];
        if ($this->where($cond)->save() === false) {
            return false;
        }
    }

    /**
     * 设置默认地址
     * @param $id
     * @return bool
     */
    public function saveDefault($id)
    {
        // 获取用户session信息
        $userinfo = session('USERINFO');
        // 那么先将其他默认地址改为非默认
        $this->where(['member_id' => $userinfo['id'],])->setField('is_default', 0);

        // 将指定的地址修改为默认
        $cond = [
            'member_id' => $userinfo['id'],
            'id'        => $id,
        ];
        if ($this->where($cond)->setField('is_default', 1) === false) {
            return false;
        }
    }


}