<?php
defined('SRC_URL') or define('SRC_URL','http://admin.shop.com');
return array(
	//'配置项'=>'配置值'
    'TMPL_PARSE_STRING'  =>array(
        '__CSS__' => SRC_URL.'/Public/Admin/css', // 更改默认的/Public 替换规则
        '__JS__' => SRC_URL.'/Public/Admin/js', // 更改默认的/Public 替换规则
        '__IMG__' => SRC_URL.'/Public/Admin/images', // 更改默认的/Public 替换规则
        '__LAYER__' => SRC_URL.'/Public/Admin/layer/layer.js', // 更改默认的/Public 替换规则
        '__UPLOADIFY__' => SRC_URL.'/Public/Admin/uploadify', // 更改默认的/Public 替换规则
        '__TREEGRID__' => SRC_URL.'/Public/Admin/treegrid', // treegrid的路径
        '__ZTREE__' => SRC_URL.'/Public/Admin/ztree', // ztree的路径
        '__UEDITOR__' => SRC_URL.'/Public/Admin/ueditor', // ztree的路径
        '__BRAND__' => "http://itsource-brand.b0.upaiyun.com", //访问itsource_brand空间的url
        '__GOODS__' => "http://itsource-goods.b0.upaiyun.com", //访问itsource_goods空间的url
    ),
    'UPLOAD'=>array(
        'mimes'         =>  array(), //允许上传的文件MiMe类型
        'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
        'exts'          =>  array(), //允许上传的文件后缀
        'rootPath'      =>  './', // 必须写为./ 保存根路径
    ),
    'DRIVERCONFIG'=>array(
        'host'     => 'v0.api.upyun.com', //又拍云服务器
        'username' => 'itsource', //又拍云操作员的账号
        'password' => 'itsource', //又拍云操作员的密码
        'timeout'  => 90, //超时时间
    )
);