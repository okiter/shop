<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/1
 * Time: 11:58
 */

namespace Home\Model;


use Think\Model;

class GoodsCategoryModel extends Model
{
    public function getList(){

        return $this->field('id,name,level,parent_id')->where('status=1')->order('lft')->select();
    }

}