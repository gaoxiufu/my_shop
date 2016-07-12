<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/8
 * Time: 18:18
 */

namespace Home\Controller;


use Think\Controller;

class GoodsController extends Controller
{
    /**
     * 计算商品的浏览次数
     * 1.如果没有浏览次数就新增一条
     * 2.如果有浏览次数就在原次数上+1
     * @param $id
     */
    public function clockNum($id)
    {
        $goods_click_model = M('GoodsClick');
        // 通过ID获取当前商品的点击次数.
        $num = $goods_click_model->getFieldByGoodsId($id, 'click_times');
        if (!$num) { // 如果没有点击次数,就默认为1,存入数据库.
            $num = 1;
            $data = [
                'goods_id'    => $id,
                'click_times' => $num,
            ];
            $goods_click_model->add($data);
        } else {   // 如果有点击次数,就在原来的次数上+1.
            ++$num;
            $data = [
                'goods_id'    => $id,
                'click_times' => $num,
            ];
            $goods_click_model->save($data);
        }
//        return $num;
        $this->ajaxReturn($num);
    }

    /**
     * 增加浏览次数
     * @param $id
     */
    public function clickNumRedis($id)
    {
        $redis = get_redis();
        $key = 'goods_clicks';
        // zIncrBy 在指定的键上增加,
        $this->ajaxReturn($redis->zIncrBy($key, 1, $id)); // 将最新数据返回到前端
    }

    /**
     * 将redis中的浏览记录保存到数据表
     */
    public function syncClick()
    {
        // 获取redis
        $redis = get_redis();
        $key = 'goods_clicks';
        // 获取redis中的数据 zRange返回所有数据
        $click_times = $redis->zRange($key, 0, -1, true);
        // 判断变量是否存
        if (empty($click_times)) {
            return true;
        }
        //zRange goods_clicks 0 -1 WITHSCORES

        $goods_ids = array_keys($click_times);
        // 删除原有的数据
        $goods_click_model = M('GoodsClick');
        $goods_click_model->where(['goods_id' => ['in', $goods_ids]])->delete();
        // 新增数据
        $data = [];
        foreach ($click_times as $key => $val) {
            $data[] = [
                'goods_id'    => $key,
                'click_times' => $val,
            ];
        }
        echo '<script type="text/javascript">window.close();</script>';
        $goods_click_model->addAll($data);
    }



}