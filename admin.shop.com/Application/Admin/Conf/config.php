<?php
defined('SRC_URL') or define('SRC_URL','http://admin.shop.com');
return array(
	//'配置项'=>'配置值'
    'TMPL_PARSE_STRING'  =>array(
        '__CSS__' => SRC_URL.'/Public/Admin/css', // 更改默认的/Public 替换规则
        '__JS__' => SRC_URL.'/Public/Admin/js', // 更改默认的/Public 替换规则
        '__IMG__' => SRC_URL.'/Public/Admin/images', // 更改默认的/Public 替换规则
    )
);