<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/7
 * Time: 23:56
 */

namespace Home\Model;


use Think\Model;

class GoodsModel extends Model
{
    /**
     * 获取推荐商品信息
     * @param $goods_status
     * @return mixed
     */
    public function getGoodsStatus($goods_status) {
        $cond = [
            'status'=>1,
            'is_on_sale'=>1,
            'goods_status & '.$goods_status,
        ];
        return $this->where($cond)->limit(5)->select();
    }

    public function getGoods($id)
    {
//        $row= $this->field('g.*,b.`name` as bname,gi.`content`')->alias('g')->where(['g.is_on_sale'=>1,'g.status'=>1,'g.id'=>$id])->join('__BRAND__ AS b ON g.`brand_id`=b.`id`')->join('__GOODS_INTRO__ AS gi ON  g.`id`=gi.`goods_id`')->select();
//        echo $this->getLastSql();
        $row = $this->field('g.*,b.name as bname,gi.content')->alias('g')->where(['is_on_sale'=>1,'g.status'=>1,'g.id'=>$id])->join('__BRAND__ as b ON g.brand_id=b.id')->join('__GOODS_INTRO__ as gi ON gi.goods_id=g.id')->find();
        $row['galleries'] = M('GoodsGallery')->where(['goods_id'=>$id])->getField('path',true);

        return $row;
    }
}