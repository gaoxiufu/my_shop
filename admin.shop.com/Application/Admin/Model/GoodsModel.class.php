<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/29
 * Time: 19:25
 */

namespace Admin\Model;


use Think\Model;

class GoodsModel extends Model
{
    // 开启批量验证
    protected $patchValidate = true;
    // 自动验证
    /**
     * 1. 商品名必填
     * 2. 商品分类必填
     * 3. 品牌必填
     * 4. 供货商必填
     * 5. 市场价必填,必须是货币
     * 6. 商城价格必填,必须是货币
     * 7. 库存必填,必须是数字
     * ...
     */
    protected $_validate = [
        ['name', 'require', '商品名称不能为空'],
        ['sn', '', '货号已存在', self::VALUE_VALIDATE],
        ['goods_category_id', 'require', '商品分类不能为空'],
        ['brand_id', 'require', '品牌不能为空'],
        ['supplier_id', 'require', '供货商不能为空'],
        ['market_price', 'require', '市场价不能为空'],
        ['market_price', 'currency', '市场价不合法'],
        ['shop_price', 'require', '售价不能为空'],
        ['shop_price', 'currency', '售价不合法'],
        ['stock', 'require', '库存不能为空'],
    ];
    // 自动完成
    protected $_auto = [
        ['sn', 'createSn', self::MODEL_INSERT, 'callback'],
        ['goods_status', 'counts', self::MODEL_BOTH, 'callback'],
        ['inputtime', NOW_TIME, self::MODEL_INSERT],
    ];

    protected function counts($goods_status)
    {
        if (isset($goods_status)) {
            return array_sum($goods_status);
        } else {
            return 0;
        }
    }

    /**
     * 计算货号 并开启事务
     * @return string
     */
    public function createSn()
    {
        //开启事务
        $this->startTrans();
        // 货号使用SN2016040800001的格式
        // 获取数据
        $date = date('Ymd');
        $num_model = M('GoodsNum');
        if ($num = $num_model->getFieldByDate($date, 'num')) {
            ++$num;
            $data = [
                'date' => $date,
                'num'  => $num,
            ];
            $num_model->save($data);
        } else {
            $num = 1;
            $data = [
                'date' => $date,
                'num'  => $num,
            ];
            $num_model->add($data);
        }
        // 拼接SN
        $sn = 'SN' . $date . str_pad($num, 5, '0', STR_PAD_LEFT);
        return $sn;
    }

    /**
     * 分别向商品表和商品信息表/相册表插入数据,在所有完成后提交事务
     */
    public function addGoods()
    {
        $goods_id = $this->add();
        if ($goods_id === false) {
            $this->rollback();    // 回滚事务
            return false;
        }

        // 准备商品内容所需的数
        $data = [
            'goods_id' => $goods_id,
            'content'  => I('post.content', '', false),
        ];
        // 创建goods_intro对象
        $goods_intro_model = M('GoodsIntro');
        if ($goods_intro_model->add($data) === false) {
            $this->rollback();// 回滚事务
            return false;
        }

        // 新增相册设计
        $goods_gallery_model = M('GoodsGallery');
        // 准备数据
        $paths = I('post.path');
        $datas = [];
        foreach ($paths as $path) {
            $datas [] = [
                'goods_id' => $goods_id,
                'path'     => $path,
            ];
        }


        if ($datas && ($goods_gallery_model->addAll($datas) === false)) {
            $this->rollback();// 回滚事务
            return false;
        }

        // 提交事务
        $this->commit();
        return true;
    }

    /**
     * 分页
     * @return array
     */
    public function getPage(array $cond = [])
    {
        $cond = array_merge(['status' => 1], $cond);
        // 获取总条数
        $count = $this->where($cond)->count();
        // 实例化分页工具
        $page_setting = C('PAGE_SETTING');
        $page = new \Think\Page($count, $page_setting['PAGE_SIZE']);
        $page->setConfig('theme', $page_setting['PAGE_THEME']);
        $page_html = $page->show();
        // 获取分页数据
        $rows = $this->where($cond)->page(I('get.p', 1), $page_setting['PAGE_SIZE'])->select();
        // 将商品推荐精品 新品 热销 重新拼装好后返回前端
        foreach ($rows as $key => $value) {
            $value['is_best'] = $value['goods_status'] & 1 ? true : false;
            $value['is_new'] = $value['goods_status'] & 2 ? true : false;
            $value['is_hot'] = $value['goods_status'] & 4 ? true : false;
            $rows[$key] = $value;
        }
        return compact('rows', 'page_html');
    }

    /**
     * 获取商品信息和商品详细内容
     * @param $id
     * @return mixed
     */
    public function getGoodsInfo($id)
    {
        // 获取商品信息
        $row = $this->find($id);
        // 重新注重商品推荐状态
        $row['goods_status'];
        $temp = [];
        if ($row['goods_status'] & 1) {
            $temp[] = 1;
        }
        if ($row['goods_status'] & 2) {
            $temp[] = 2;
        }
        if ($row['goods_status'] & 4) {
            $temp[] = 4;
        }
        $row['goods_status'] = json_encode($temp);
        unset($temp); // 释放资源
        // 获取商品详细描述
        $goods_intro_model = M('GoodsIntro');
        $row['content'] = $goods_intro_model->getFieldByGoodsId($id, 'content');
        // 获取相册地址
        $goods_gallery_model = M('GoodsGallery');
        $row['paths'] = $goods_gallery_model->getFieldByGoodsId($id, 'id,path');
        // 返回数据
        return $row;
    }

    /**
     * 把数据更新到商品信息表和商品详情表
     */
    public function saveGoodsInfo()
    {
        $id = $this->data['id'];
        // 开启事务
        $this->startTrans();
        // 把数据更新到商品信息
        if ($this->save() === false) {
            $this->rollback(); // 数据更新失败,回滚事务
        }
        // 把数据更新到商品详细表
        // 准备数据
        $data = [
            'goods_id' => $id,
            'content'  => I('post.content', '', false),
        ];
        $goods_intro_model = M('GoodsIntro');
        if ($goods_intro_model->save($data) === false) {

            $this->rollback(); // 数据更新失败,回滚事务
            return false;
        }

        // 把数据更新到相册
        $paths = I('post.path');
        $datas = [];
        foreach ($paths as $path) {
            $datas [] = [
                'goods_id' => $id,
                'path'     => $path,
            ];
        }
        $goods_gallery_model = M('GoodsGallery');

        if ($datas && ($goods_gallery_model->addAll($datas) === false)) {
            $this->rollback(); // 数据更新失败,回滚事务
            return false;
        }
        // 提交事务
        $this->commit();
        return true;
    }

    /**
     * 修改商品状态,实现逻辑删除
     * @param $id
     */
    public function saveGoos($id)
    {
        // 装备数据
        $data = [
            'id'     => $id,
            'status' => 0,
        ];
        $this->save($data);
    }
}