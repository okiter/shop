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

class MemberLevelModel extends BaseModel
{
    // 自动验证定义   $fields 表中的每个字段信息
    protected $_validate        =   array(
        array('name','require','名称不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('bottom','require','积分下限不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('top','require','积分上限不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('discount','require','折扣率不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('status','require','状态不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('sort','require','排序不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
    );
}