<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/28
 * Time: 15:16
 */

namespace Admin\Logic;


class MysqlLogic implements DbMysql
{
    public function connect()
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    public function disconnect()
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    public function free($result)
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    /**
     * 执行一条更新语句
     * @param string $sql
     * @param array $args
     * @return false|int
     */
    public function query($sql, array $args = array())
    {
        // 获取所有参数.
        $args = func_get_args();
        // 获取sql语句.(array_shift函数弹出一个数据)
        $sql = array_shift($args);

        // 将sql语句 用正则表达式分.
        $rows = preg_split('/\?[FTN]/', $sql);
        // 删除最后一个元素.(array_pop弹出最后一个而数据)
        array_pop($rows);
        // 循环数据,拼凑sql语句.
        $sql = '';
        foreach ($rows as $k => $val) {
            $sql .= $val . $args[$k];
        }

//        echo $sql;exit;
        // 写入数据
        return M()->execute($sql);

    }

    /**
     * 新增一条记录
     * @param string $sql
     * @param array $args
     * @return bool|string
     */
    public function insert($sql, array $args = array())
    {
        // 获取所有数据
        $args = func_get_args();
        // 获取sql语句
        $sql = $args[0];
        // 获取表名
        $tableName = $args[1];
        // 获数据
        $datas = $args[2];
        // 循环数据存入数组
        $temp = [];
        foreach ($datas as $k => $val) {
            $temp[] = '`' . $k . '`' . '="' . $val . '"';
        }
        // 用逗号分隔数组
        $temp = implode(',', $temp);
        // 替换值的占位符
        $sql = str_replace('?%', $temp, $sql);
        // 替换表名占位符
        $sql = str_replace('?T', $tableName, $sql);
        if (M()->execute($sql) !== false) {
            return M()->getLastInsID();
        } else {
            return false;
        }

    }

    public function update($sql, array $args = array())
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    public function getAll($sql, array $args = array())
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    public function getAssoc($sql, array $args = array())
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    /**
     * 返回一行数据
     * @param string $sql
     * @param array $args
     * @return mixed
     */
    public function getRow($sql, array $args = array())
    {
        // 获取所有参数.
        $args = func_get_args();
        // 获取sql语句.(array_shift函数弹出一个数据)
        $sql = array_shift($args);

        // 将sql语句 用正则表达式分.
        $rows = preg_split('/\?[FTN]/', $sql);
        // 删除最后一个元素.(array_pop弹出最后一个而数据)
        array_pop($rows);
        // 循环数据,拼凑sql语句.
        $sql = '';
        foreach ($rows as $k => $val) {
            $sql .= $val . $args[$k];
        }
        // 获取返回的二维数据
        $row = M()->query($sql);
        // 取出第一行数据
        return array_shift($row);
    }

    public function getCol($sql, array $args = array())
    {

        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    /**
     * 返回节点一个字段最大的值
     * @param string $sql
     * @param array $args
     * @return mixed
     */
    public function getOne($sql, array $args = array())
    {
        // 获取所有参数.
        $args = func_get_args();
        // 获取sql语句.(array_shift函数弹出一个数据)
        $sql = array_shift($args);

        // 将sql语句 用正则表达式分隔.
        $rows = preg_split('/\?[FTN]/', $sql);
        // 删除最后一个元素.(array_pop弹出最后一个而数据)
        array_pop($rows);
        // 循环数据,拼凑sql语句.
        $sql = '';
        foreach ($rows as $k => $val) {
            $sql .= $val . $args[$k];
        }
        // 获取返回的二维数据
        $row = M()->query($sql);
        // 取出第一行数据
        return array_shift($row[0]);
    }


}