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

class SupplierModel extends BaseModel
{
    // 自动验证定义
    protected $_validate        =   array(
        array('name','require','名称不能够为空!'),
        array('name','','名称已经存在,请更改!',self::EXISTS_VALIDATE,'unique')
    );
}