<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/1
 * Time: 14:27
 */

namespace Home\Model;


use Think\Model;

class BaseModel extends Model
{

    public function getList($field='*',$wheres = array()){
        return $this->field($field)->where($wheres)->order('sort')->where('status=1')->select();
    }
}