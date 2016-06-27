<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 21:23
 */

namespace Admin\Controller;


use Think\Controller;

class UploadController extends Controller
{
    public function uploadImg()
    {
        //创建upload对象
        $options   = C('UPLOAD_SETTING');
        $upload = new \Think\Upload($options);
        // 获取上传文件信息
        $file_info = $upload->uploadOne($_FILES['file_data']);
       //var_dump($file_info);
        if ($file_info) {
            if ($upload->driver == 'Qiniu') {
                $file_url = $file_info['url'];
            } else {
                $file_url = BASE_URL . $file_info['savepath'] . $file_info['savename'];
            }
            $return = [
                'file_url' => $file_url,
                'msg'      => "上传成功",
                'status'   => 1,
            ];
        } else {
            $return = [
                'file_url' => '',
                'msg'      => $upload->getError(),
                'status'   => 0,
            ];
        }
        $this->ajaxReturn($return);
    }

}