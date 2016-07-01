<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/25
 * Time: 9:33
 */

namespace Admin\Model;


use Think\Model;

class BrandModel extends Model
{

    protected $patchValidate = true;//开启批量验证
    /**
     * 自动验证
     */
    protected $_validate = [
        ['name', 'require', '品牌名称不能为空'],
        ['name', '', '品牌已存在', self::EXISTS_VALIDATE, 'unique'],
        ['status', '0,1', '品牌状态不合法', self::EXISTS_VALIDATE, 'in'],
        ['sort', 'number', '排序必须为数字']
    ];


    /**
     * 分页
     * @param array $cont 搜索条件
     * @return array 混合数组
     */
    public function getPage(array $cont)
    {
        // 获取总条数
        $count = $this->where($cont)->count();
        // 调用分页工具
        $page = new \Think\Page($count, 2);
        // 获取分页样式
        $page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        // 获取翻页代码
        $html = $page->show();
        $num = ceil($count / 2);

        // 获取分页数据
        $rows = $this->where($cont)->page(I('get.p', 1), 2)->select();
        return compact('html', 'rows', 'num');
    }

    /**
     * 展示下拉数据
     * @return mixed
     */
    public function getList()
    {
        return $this->where(['status' => ['gt', 0]])->select();
    }

}