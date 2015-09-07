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


    'MAIL_CONFIG' =>array(
        'Host'=>'smtp.126.com',  //邮件服务器地址
        'Username'=>'itsource520@126.com', //账号
        'Password'=>'qqitsource520',  //密码
        'From'=>'itsource520@126.com', //发送人的地址
        'FromName'=>'itsource'  //发送人的名称
    ),


    //关于session的配置
    'SESSION_TYPE'			=>  'Redis',	//session类型
    'SESSION_PERSISTENT'    =>  1,		//是否长连接(对于php来说0和1都一样)
    'SESSION_CACHE_TIME'	=>  1,		//连接超时时间(秒)
    'SESSION_EXPIRE'		=>  0,		//session有效期(单位:秒) 0表示永久缓存
//    'SESSION_PREFIX'		=>  'sess_',		//session前缀
    'SESSION_REDIS_HOST'	=>  '127.0.0.1', //分布式Redis,默认第一个为主服务器
    'SESSION_REDIS_PORT'	=>  '6379',	       //端口,如果相同只填一个,用英文逗号分隔
//    'SESSION_REDIS_AUTH'    =>  'redis123',    //Redis auth认证(密钥中不能有逗号),如果相同只填一个,用英文逗号分隔


    //配置缓存
    'DATA_CACHE_TYPE'       =>  'Redis',  //指定驱动的名字
    'REDIS_HOST'            =>   '127.0.0.1',
    'REDIS_PORT'            =>   6379,


    //静态缓存的配置
    'HTML_CACHE_ON'     =>    true, // 开启静态缓存
    'HTML_CACHE_TIME'   =>    60,   // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX'  =>    '.shtml', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES'  =>     array(  // 定义静态缓存规则
        /**
         * 静态地址:  访问thinkphp的控制器的方法地址
         *  1. Index:index: 当访问IndexController中的index方法时, 使用对应的静态规则
         *  2. 后面的规则是指定静态页面的地址
         */
        'Index:index'    =>     array('index', 60*60*24),
        'Index:goods'    =>     array('goods/{id}', 60*60*24),
    ),

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
        'cacert' => getcwd() . '\\cacert.pem',
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        'transport' => 'http'
    )


);


