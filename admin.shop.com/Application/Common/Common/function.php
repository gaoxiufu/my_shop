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
 * @param $salt     盐
 * @return string 字符串
 */
function password_salt($password, $salt)
{
    return md5(md5($password) . $salt);
}


/**
 * 邮件发送
 * @param $email
 * @param $subject
 * @param $content
 * @return array
 * @throws Exception
 * @throws phpmailerException
 */
function sendMail($email,$subject,$content)
{
    Vendor('PHPMailer.PHPMailerAutoload');
    $mail = new \PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.126.com';  //填写发送邮件的服务器地址
    $mail->SMTPAuth = true;                               // 使用smtp验证
    $mail->Username = 'kunx_edu@126.com';                 // 发件人账号名
    $mail->Password = 'iam4ge';                           // 密码
    $mail->SMTPSecure = 'ssl';                            // 使用协议,具体是什么根据你的邮件服务商来确定
    $mail->Port = 465;                                    // 使用的端口

    $mail->setFrom('kunx_edu@126.com', 'ayiyayo');//发件人,注意:邮箱地址必须和上面的一致
    $mail->addAddress($email);     // 收件人

    $mail->isHTML(true);                                  // 是否是html格式的邮件

    $mail->Subject = $subject;//标题
    $mail->Body = $content;//正文
    $mail->CharSet = 'UTF-8';

    if ($mail->send()) {
        return [
            'status' => 1,
            'msg'    => '发送成功',
        ];
    } else {
        return [
            'status' => 0,
            'msg'    => $mail->ErrorInfo,
        ];

    }
}

