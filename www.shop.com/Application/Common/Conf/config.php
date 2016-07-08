<?php
// 定义网站路径
define('BASE_URL', 'http://www.shop.com/');
return array(
    // 定义常用文件引入路径
    'TMPL_PARSE_STRING' => [
        '__CSS__'             => BASE_URL . 'Public/css', // css路径
        '__JS__'              => BASE_URL . 'Public/js', // js路径
        '__IMG__'             => BASE_URL . 'Public/images', // 图片路径
        '__UPLOADIFY__'       => BASE_URL . 'Public/ext/uploadify', // uploadify插件路径
        '__LAYER__'           => BASE_URL . 'Public/ext/layer', // layer插件路径
        '__ZTREE__'           => BASE_URL . 'Public/ext/ztree', // ztree插件路径
        '__TREEGRID__'        => BASE_URL . 'Public/ext/treegrid', // treegrid插件路径
        '__UEDITOR__'         => BASE_URL . 'Public/ext/ueditor', // ueditor插件路径
        '__JQUERY_VALIDATE__' => BASE_URL . 'Public/ext/jquery-validate', // ueditor插件路径
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
    //分页相关的配置
    'PAGE_SETTING'      => [
        'PAGE_SIZE'  => 5,
        'PAGE_THEME' => '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    ],
    // 设置cookie前缀
    'COOKIE_PREFIX'     => 'www_shop_com_',
    //Redis Session配置
    'SESSION_AUTO_START' => true,    // 是否自动开启Session
    'SESSION_TYPE' => 'Redis',    //session类型
    'SESSION_PERSISTENT' => 1,        //是否长连接(对于php来说0和1都一样)
    'SESSION_CACHE_TIME' => 1,        //连接超时时间(秒)
    'SESSION_EXPIRE' => 0,        //session有效期(单位:秒) 0表示永久缓存
    'SESSION_PREFIX' => 'sess_',        //session前缀
    'SESSION_REDIS_HOST' => '127.0.0.1', //分布式Redis,默认第一个为主服务器
    'SESSION_REDIS_PORT' => '6379',           //端口,如果相同只填一个,用英文逗号分隔
    'SESSION_REDIS_AUTH' => '',    //Redis auth认证(密钥中不能有逗号),如果相同只填一个,用英文逗号分隔

    // 静态缓存配置
    'HTML_CACHE_ON' => true, // 开启静态缓存
    'HTML_CACHE_TIME' => 60,   // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX' => '.shtml', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES' => array(
        // 定义静态缓存规则     // 定义格式1 数组方式
        //'Index:index' => array('index'),
        'Index:' => array('{:controller}_{:action}_{id}'),
        // 定义格式2 字符串方式
        //'静态地址' => '静态规则',
    ),
    // redis配置
    'DATA_CACHE_TYPE'=>'Redis',
    'REDIS_HOST'=>'127.0.0.1',
    'REDIS_PORT'=>6379,

);