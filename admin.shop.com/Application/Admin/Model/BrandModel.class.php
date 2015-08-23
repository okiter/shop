<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:46
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class BrandModel extends BaseModel
{
    // 自动验证定义   $fields 表中的每个字段信息
    protected $_validate        =   array(
        array('name','require','名称不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('site_url','require','网址不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('logo','require','LOGO@file不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('sort','require','排序不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('status','require','状态@radio|1=是,0=否不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
    );
}