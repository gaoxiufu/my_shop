<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/2
 * Time: 12:57
 */

namespace Admin\Model;


use Think\Model;

class RoleModel extends Model
{

    /**
     *列表分页
     */
    public function getPageList(array $cond = [])
    {
        // 获取总条数
        $count = $this->where($cond)->count();
        // 获取配置参数
        $page_setting = C('PAGE_SETTING');
        // 获取分页工具
        $page = new \Think\Page($count, $page_setting['PAGE_SIZE']);
        // 设置主题
        $page->setConfig('theme', $page_setting['PAGE_THEME']);
        // 获取分页代码
        $page_html = $page->show();
        // 获取分页数据
        $rows = $this->where($cond)->page(I('get.p', 1), $page_setting['PAGE_SIZE'])->select();
        // 返回数据
        return compact('rows', 'page_html');
    }

    /**
     * 新增角色,并把数据保存到角色表和权限关联表
     * @return bool
     */
    public function addRole()
    {
        // 开启事务
        $this->startTrans();

        // 把数据新增到角色表
        if (($role_id = $this->add()) === false) {
            $this->error = '新增角色失败';
            $this->rollback();// 回滚事务
            return false;
        }

        // 把数据现在到权限和角色关联表
        // 准备数据
        $permission_ids = I('post.permission_id');
        $data = [];
        foreach ($permission_ids as $permission_id) {
            $data[] = [
                'role_id'       => $role_id,
                'permission_id' => $permission_id
            ];
        }
        // 新增关联
        if ($data) {
            $role_permission_model = M('RolePermission');
            if ($role_permission_model->addAll($data) === false) {
                $this->error = '新增关联数据失败';
                $this->rollback();// 回滚事务
                return false;
            }

        }
        // 提交事务
        $this->commit();
        return true;
    }


    /**
     * 获取修改角色需要的回显数据
     * @param $id
     * @return mixed
     */
    public function getPermissionInfo($id)
    {
        // 获取角色信息
        $row = $this->find($id);
        // 获取关联信息
        $role_permission_model = M('RolePermission');
        $row['permission_ids'] = json_encode($role_permission_model->where(['role_id' => $id])->getField('permission_id', true));
        return $row;
    }

    /**
     * 更新角色信息和关联数据
     * @param $id
     * @return bool
     */
    public function saveRoleInfo($id)
    {
        $id = $this->data['id'];
        // 开启事务
        $this->startTrans();
        // 更新角色信息
        if ($this->save() === false) {
            $this->error = '更新角色信息失败';
            $this->rollback(); // 回滚事务
            return false;
        }

        // 删除原有的关联
        $role_permission_model = M('RolePermission');
        if ($role_permission_model->where(['role_id' => $id])->delete() === false) {
            $this->error = '删除历史关联数据失败';
            $this->rollback(); // 回滚事务
            return false;
        }

        // 插入新的关联数据
        // 准备数据

        $permission_ids = I('post.permission_id');
        $data = [];
        foreach ($permission_ids as $permission_id) {
            $data[] = [
                'role_id'       => $id,
                'permission_id' => $permission_id
            ];
        }
        // 新增关联
        if ($data) {
            $role_permission_model = M('RolePermission');
            if ($role_permission_model->addAll($data) === false) {
                $this->error = '更新关联数据失败';
                $this->rollback();// 回滚事务
                return false;
            }

        }
        // 提交事务
        $this->commit();
        return true;
    }

    /**
     * 删除角色.删除权限关联.删除管理员关联
     * @param $id
     * @return bool
     */
    public function deleteRole($id)
    {
        // 开启事务
        $this->startTrans();
        // 删除角色信息
        if ($this->delete($id) === false) {
            $this->error = '删除角色失败';
            $this->rollback();// 回滚事务
            return false;
        }

        // 删除关联的数据
        $role_permission_model = M('RolePermission');
        if ($role_permission_model->where(['role_id' => $id])->delete() === false) {
            $this->error = '删除权限关联失败';
            $this->rollback();// 回滚事务
            return false;
        }
        // 删除管理员关联

        // 提交事务
        $this->commit();
        return true;
    }

    /**
     * 回显数据使用
     * @return mixed
     */
    public function getList() {
        return $this->where(['status'=>1])->select();
    }
}