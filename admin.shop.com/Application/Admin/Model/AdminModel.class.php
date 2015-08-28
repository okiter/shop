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

class AdminModel extends BaseModel
{
    // 自动验证定义   $fields 表中的每个字段信息
    protected $_validate        =   array(
        array('username','require','用户名不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('password','require','密码不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
    );
}