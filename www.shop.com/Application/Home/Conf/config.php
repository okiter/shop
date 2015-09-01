<?php
defined('SRC_URL') or define('SRC_URL','http://www.shop.com');

return array(
    //'配置项'=>'配置值'
    'TMPL_PARSE_STRING'  =>array(
        '__CSS__' => SRC_URL.'/Public/Home/css', // 更改默认的/Public 替换规则
        '__JS__' => SRC_URL.'/Public/Home/js', // 更改默认的/Public 替换规则
        '__IMG__' => SRC_URL.'/Public/Home/images', // 更改默认的/Public 替换规则
        '__LAYER__' => SRC_URL.'/Public/Home/layer/layer.js', // 更改默认的/Public 替换规则
        '__UPLOADIFY__' => SRC_URL.'/Public/Home/uploadify', // 更改默认的/Public 替换规则
        '__TREEGRID__' => SRC_URL.'/Public/Home/treegrid', // treegrid的路径
        '__ZTREE__' => SRC_URL.'/Public/Home/ztree', // ztree的路径
        '__UEDITOR__' => SRC_URL.'/Public/Home/ueditor', // ztree的路径
        '__BRAND__' => "http://itsource-brand.b0.upaiyun.com", //访问itsource_brand空间的url
        '__GOODS__' => "http://itsource-goods.b0.upaiyun.com", //访问itsource_goods空间的url
    ),
);