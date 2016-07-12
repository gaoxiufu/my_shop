<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3
 * Time: 9:44
 */

namespace Admin\Model;


use Think\Model;

class AdminModel extends Model
{
    // 开启批量验证
    protected $patchValidate = true;

    /**
     * 自动验证
     * @var array
     */
    protected $_validate = [
        ['username', 'require', '用户名不能为空'],
        ['username', '', '用户名已被占用', self::EXISTS_VALIDATE, 'unique', 'add'],
        ['password', 'require', '密码不能为空', self::EXISTS_VALIDATE, '', 'add'],
        ['password', '6,16', '密码长度不合法', self::EXISTS_VALIDATE, 'length', 'add'],
        ['repassword', 'password', '两次密码不一致', self::EXISTS_VALIDATE, 'confirm'],
        ['email', 'require', '邮箱不能为空'],
        ['email', 'email', '邮箱格式不合法', self::EXISTS_VALIDATE],
        ['email', '', '邮箱已被占用', self::EXISTS_VALIDATE, 'unique'],
//        ['captcha', 'checkCaptcha', '验证码不正确', self::EXISTS_VALIDATE, 'callback'],
    ];

    /**
     * 自动完成
     * 1. 注册时间
     * 2. 自动生成随机盐
     * @var type
     */
    protected $_auto = [
        ['add_time', NOW_TIME, 'add'],
        ['salt', '\Org\Util\String::randString', self::MODEL_BOTH, 'function',]
    ];

    /**
     * 验证验证码
     * @return bool
     */
    public function checkCaptcha($code)
    {
        $verify = new \Think\Verify();
        return $verify->check($code);
    }

    /**
     * 分页代码
     * @param array $cond
     * @return array
     */
    public function getPageList(array $cond = [])
    {
        // 获取总条数
        $count = $this->where($cond)->count();
        // 获取分页配置
        $page_setting = C('PAGE_SETtING');
        // 获取分页工具
        $page = new \Think\Page($count, $page_setting['PAGE_SIZE']);
        // 获取主题
        $page->setConfig('theme', $page_setting['PAGE_THEME']);
        // 获取分页代码
        $page_html = $page->show();
        // 获取分页数据
        $rows = $this->where($cond)->page(I('get.p', 1), $page_setting['PAGE_SIZE'])->select();
        return compact('rows', 'page_html');
    }


    /**
     * 新增管理员
     * @return bool
     */
    public function addAdmin()
    {

        // 开启事务
        $this->startTrans();
        // 把数据现在到管理员表
        $this->data['password'] = password_salt($this->data['password'], $this->data['salt']); // 密码加盐加密
        if (($admin_id = $this->add()) === false) {
            $this->rollback(); // 回滚事务
            return false;
        }
        // 把数据新增到角色关联表
        $role_ids = I('post.role_id');
        $cond = [];
        foreach ($role_ids as $role_id) {
            $cond[] = [
                'admin_id' => $admin_id,
                'role_id'  => $role_id,
            ];
        }
        $admin_role_model = M('AdminRole');
        if ($cond) {
            if ($admin_role_model->addAll($cond) === false) {
                $this->error = '保存关联角色失败';
                $this->rollback(); // 回滚事务
                return false;
            }
        }
        // 提交事务
        $this->commit();
        return true;
    }

    /**
     * 获取管理员基本信息
     * 通过管理员ID查询出相关联的角色ID
     * @param int $id 管理员ID
     * @return mixed
     */
    public function getAdminInfo($id)
    {
        // 获取基本信息
        $row = $this->find($id);
        // 获取关联角色
        $admin_role_model = M('AdminRole');
        $row['role_ids'] = json_encode($admin_role_model->where(['admin_id' => $id])->getField('role_id', true));
        return $row;
    }

    /**
     * 修改管理员权限
     *1.先删除原有的权限,在插入新的权限
     * * @param $id
     * @return bool
     */
    public function saveAdmin($id)
    {
        // 开启事务
        $this->startTrans();
        // 删除原有的角关联
        $admin_role_model = M('AdminRole');
        if ($admin_role_model->where(['admin_id' => $id])->delete() === false) {
            $this->error = '删除关联角色失败';
            $this->rollback(); // 回滚事务
            return false;
        }

        // 保存新的管理角色
        $role_ids = I('post.role_id');
        $cond = [];
        foreach ($role_ids as $role_id) {
            $cond[] = [
                'admin_id' => $id,
                'role_id'  => $role_id,
            ];
        }

        if ($cond) {
            if ($admin_role_model->addAll($cond) === false) {
                $this->error = '保存关联角色失败';
                $this->rollback(); // 回滚事务
                return false;
            }
        }
        // 提交事务
        $this->commit();
        return true;
    }


    public function deleteAdmin($id)
    {
        // 开启事务
        $this->startTrans();
        // 删除管理员信息
        if ($this->delete($id) === false) {
            $this->error = '删除管理员失败';
            $this->rollback(); // 回滚事务
            return false;
        }

        // 删除原有的角关联
        $admin_role_model = M('AdminRole');
        if ($admin_role_model->where(['admin_id' => $id])->delete() === false) {
            $this->error = '删除关联失败';
            $this->rollback(); // 回滚事务
            return false;
        }
        // 提交事务
        $this->commit();
        return true;
    }

    /**
     * 重置密码
     */
    public function savePassword($id)
    {
        $password = $this->data['password'];
        // 获取加盐加密的新密码
        $this->data['password'] = password_salt($this->data['password'], $this->data['salt']); // 密码加盐加密
        if ($this->save()) {
            return $password;
        }
    }

    /**
     * 验证用户登陆
     */
    public function adminLogin()
    {
        // 获取用户名
        $username = $this->data['username'];
        // 获取用密码
        $password = $this->data['password'];
        // 对比用户名是否在数据库内
        $userinfo = $this->where(['username' => $username])->find();
        if (!$userinfo) {
            $this->error = '用户名或者密码错误!';
            return false;
        }
        // 如果存在取出盐和密码,将获取密码加盐机密进行对比
        if ($userinfo['password'] != password_salt($password, $userinfo['salt'])) {
            $this->error = '用户名或者密码错误!';
            return false;
        }
        // 保存用登陆的时间和IP
        $data = [
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
            'id'              => $userinfo['id']
        ];

        $this->save($data);
        $this->getPermission($userinfo['id']);
        // 用户和密码对比通过了就保存用户信息到session
        session('USERINFO', $userinfo);

        // 自动登陆

        //删除用户相关的token记录
        $admin_token_model = M('AdminToken');
        $admin_token_model->delete($userinfo['id']);

        if (I('post.remember')) { // 如果勾选了标记,就讲用户信息保到cookie
            $data = [
                'admin_id' => $userinfo['id'],
                'token'    => \Org\Util\String::randString(40),
            ];
            // 保存cookie并设置过时间
            cookie('USER_TOKEN', $data, 3360);

            // 将新的token记录存入库中
            $admin_token_model->add($data);

        }
        return $userinfo;
    }


    /**
     * 获取用户相关路径
     * @param $admin_id
     * @return bool
     */
    private function getPermission($admin_id)
    {
        //SELECT DISTINCT path FROM admin_role AS ar JOIN role_permission AS rp ON ar.`role_id`=rp.`role_id` JOIN permission AS p ON p.`id`=rp.`permission_id` WHERE path<>'' AND admin_id=8;
        $cond = [
            'path'     => ['neq', ''],
            'admin_id' => $admin_id,
        ];
        $permissions = M()->distinct(true)->field('permission_id,path')->table('admin_role')->alias('ar')->join('__ROLE_PERMISSION__ as rp ON ar.`role_id`=rp.`role_id`')->join('__PERMISSION__ as p ON p.`id`=rp.`permission_id`')->where($cond)->select();

        $pids = []; // 权限ID
        $paths = []; // 路径
        foreach ($permissions as $permission) {
            $pids[] = $permission['permission_id'];
            $paths[] = $permission['path'];
        }

        // 保存到session中
        session('PERMISSION_ID', $pids);
        session('PERMISSION_PATH', $paths);
        return true;
    }

    /**
     * 验证用户自动登陆
     */
    public function autoLogin()
    {

//        dump(cookie('USER_TOKEN'));exit;
        // 获取cookie数据
        $data = cookie('USER_TOKEN');
        if (!$data) {
            return false;
        }

        // 比较cookie中的数据和数据库中的数据
        $admin_token_model = M('AdminToken');
        if (!$admin_token_model->where($data)->count()) {
            return false;
        }
        // 自动登陆后 删除历史token数据
        $admin_token_model->delete($data['admin_id']);

        // 重新生成cookie数据
        $data = [
            'admin_id' => $data['admin_id'],
            'token'    => \Org\Util\String::randString(40),
        ];
        // 保存cookie并设置过时间
        cookie('USER_TOKEN', $data, 3360);

        // 将新的token记录存入库中
        $admin_token_model->add($data);
        // 保存信息到session中
        $userinfo = $this->find($data['admin_id']);
        session('USERINFO', $userinfo);
        // 获取用户权限
        $this->getPermission($userinfo['id']);
        return $userinfo;

    }

}