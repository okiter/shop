<?php
return array(
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址(直接使用127.0.0.1 效率高)
    'DB_NAME'               =>  'shop',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'admin',          // 密码
    'DB_PORT'               =>  '',        // 端口
    'DB_PREFIX'             =>  '',    // 数据库表前缀
//    'DB_PARAMS'          	=>  array(), // 数据库连接参数
    'DB_PARAMS'    =>    array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),

    'PAGE_SIZE'             =>  10, //分页中每页显示的条数
    'SHOW_PAGE_TRACE'       =>  true,
    'SUPER_MANAGER_NAME'    =>  'admin',
    'NO_LOGIN_CHECK_URLS'   => array('Admin/Admin/login','Admin/Verify/index'),

    'COOKIE_DOMAIN'         =>  '.shop.com',      // Cookie有效域名


    'ALIPAY_CONFIG'  =>array(
        //合作身份者id，以2088开头的16位纯数字
        'partner' => '2088002155956432',
        //收款支付宝账号
        'seller_email' => 'guoguanzhao520@163.com',

        //安全检验码，以数字和字母组成的32位字符
        'key' => 'a0csaesgzhpmiiguif2j6elkyhlvf4t9',
        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        //签名方式 不需修改
        'sign_type' => strtoupper('MD5'),
        //字符编码格式 目前支持 gbk 或 utf-8
        'input_charset' => strtolower('utf-8'),
        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        'cacert' => getcwd() . '/cacert.pem',
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        'transport' => 'http'
    )

);