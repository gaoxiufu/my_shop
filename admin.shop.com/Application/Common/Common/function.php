<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/24
 * Time: 21:51
 */

/**
 * 把错误信息编辑成有序列表
 * @param \Think\Model $model
 * @return string 有序列表
 */
function getError(\Think\Model $model)
{
    $errors = $model->getError();
    if (!is_array($errors)) {
        $errors = [$errors];
    }

    $html = '<ol>';
    foreach ($errors as $error) {
        $html .= '<li>' . $error . '</li>';
    }
    $html .= '</ol>';
    return $html;

}


/**
 * 将一个关联数组转换成下拉列表
 * @param array $data 关联数组,二维数组.
 * @param string $name_field 提示文本的字段名.
 * @param string $value_field value数据的字段名.
 * @param string $name 表单控件的name属性.
 * @return string 下拉列表的html代码.
 */
function array_select(array $data, $name_filed = 'name', $value_filed = 'id', $name = '', $default_value = '')
{
    $html = '<select name="' . $name . '" class="' . $name . '" >';
    $html .= '<option value=""> 请选择 </option>';
    foreach ($data as $key => $value) {
        // 将遍历后的数据强制转换为string类型,然后进行比较,排除提交0和空字符串
        if ((string)$value[$value_filed] === $default_value) {
            $html .= '<option value="' . $value[$value_filed] . '"selected="selected">' . $value[$name_filed] . '</option>';
        } else {
            $html .= '<option value="' . $value[$value_filed] . '">' . $value[$name_filed] . '</option>';
        }
    }
    $html .= '</select>';
    return $html;
}


/**
 * 加盐加密
 * @param $password 原密码
 * @param $salt     堰
 * @return string 字符串
 */
function password_salt($password, $salt)
{
    return md5(md5($password) . $salt);
}

