<?php
// 定义网站路径
define('BASE_URL', 'http://admin.shop.com/');
return array(
    // 定义常用文件引入路径
    'TMPL_PARSE_STRING' => [
        '__CSS__'       => BASE_URL . 'Public/css', // css路径
        '__JS__'        => BASE_URL . 'Public/js', // js路径
        '__IMG__'       => BASE_URL . 'Public/images', // 图片路径
        '__UPLOADIFY__' => BASE_URL . 'Public/ext/uploadify', // uploadify插件路径
        '__LAYER__'     => BASE_URL . 'Public/ext/layer', // layer插件路径
        '__ZTREE__'     => BASE_URL . 'Public/ext/ztree', // ztree插件路径
        '__TREEGRID__'  => BASE_URL . 'Public/ext/treegrid', // ztree插件路径
    ],

    //'配置项'=>'配置值'

    /*开启调试*/
    'SHOW_PAGE_TRACE'   => true,
    // U函数重定向模式
    'URL_MODEL'         => 2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    /* 数据库设置 */
    'DB_TYPE'           => 'mysql',     // 数据库类型
    'DB_HOST'           => '127.0.0.1', // 服务器地址
    'DB_NAME'           => 'shop',          // 数据库名
    'DB_USER'           => 'root',      // 用户名
    'DB_PWD'            => '12345',          // 密码
    'DB_PORT'           => '3306',        // 端口
    'DB_PREFIX'         => '',    // 数据库表前缀
    'DB_PARAMS'         => array(), // 数据库连接参数
    'DB_DEBUG'          => TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'   => false,        // 启用字段缓存
    'DB_CHARSET'        => 'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'    => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'    => false,       // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'     => 1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'       => '', // 指定从服务器序号

    // 图片上传配置
    'UPLOAD_SETTING'    => [
        'mimes'        => array(), //允许上传的文件MiMe类型
        'maxSize'      => 0, //上传的文件大小限制 (0-不做限制)
        'exts'         => array(), //允许上传的文件后缀
        'autoSub'      => true, //自动子目录保存文件
        'subName'      => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath'     => ROOT_PATH, //保存根路径
        'savePath'     => 'uploads/', //保存路径
        'saveName'     => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'      => '', //文件保存后缀，空则使用原后缀
        'replace'      => false, //存在同名是否覆盖
        'hash'         => true, //是否生成hash编码
        'callback'     => false, //检测文件是否存在回调，如果存在返回文件信息数组
        // 'driver'       => 'Qiniu', // 文件上传驱动
        'driverConfig' => array(), // 上传驱动配置
        // 七牛配置
        'driverConfig' => array(
            'secretKey' => 'FxqC84jMdI3q1yXN4atILHXrlIUXmCaXMGwdcLBz', //sk
            'accessKey' => 'jigPhjWfZHLBQss-xGOELpEs_jStmMQox72ljEMY', //AK
            'domain'    => 'o9fsk32jc.bkt.clouddn.com', //域名
            'bucket'    => 'shop', //空间名称
            'timeout'   => 300, //超时时间
        ),
    ],

);