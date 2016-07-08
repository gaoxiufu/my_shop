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
     * ������Ʒ���������
     * 1.���û���������������һ��
     * 2.����������������ԭ������+1
     * @param $id
     */
    public function clockNum($id)
    {
        $goods_click_model = M('GoodsClick');
        // ͨ��ID��ȡ��ǰ��Ʒ�ĵ������.
        $num = $goods_click_model->getFieldByGoodsId($id, 'click_times');
        if (!$num) { // ���û�е������,��Ĭ��Ϊ1,�������ݿ�.
            $num = 1;
            $data = [
                'goods_id'    => $id,
                'click_times' => $num,
            ];
            $goods_click_model->add($data);
        } else {   // ����е������,����ԭ���Ĵ�����+1.
            ++$num;
            $data = [
                'goods_id'    => $id,
                'click_times' => $num,
            ];
            $goods_click_model->save($data);
        }
        $this->ajaxReturn($num);
    }

    /**
     * ��ȡ�������
     * @param $id
     */
    public function clickNumRedis($id)
    {
        $redis = get_redis();
        $key = 'goods_clicks';
        $this->ajaxReturn($redis->zIncrBy($key, 1, $id));
    }

    /**
     * ��redis�е������¼���浽���ݱ�
     */
    public function syncClick()
    {
        // ��ȡredis
        $redis = get_redis();
        $key = 'goods_clicks';
        // ��ȡredis�е�����
        $click_times = $redis->zRange($key, 0, -1, true);
        // �жϱ����Ƿ��
        if (empty($click_times)) {
            return true;
        }

        $goods_ids = array_keys($click_times);
        // ɾ��ԭ�е�����
        $goods_click_model = M('GoodsClick');
        $goods_click_model->where(['goods_id' => ['in', $goods_ids]])->delete();
        // ��������
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