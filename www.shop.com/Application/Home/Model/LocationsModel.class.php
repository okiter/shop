<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/2
 * Time: 16:56
 */

namespace Home\Model;


use Think\Model;

class LocationsModel extends Model
{
    /**
     * 根据parent_id找到 子地区
     * @param $parent_id
     */
    public function getChildren($parent_id){
        return $this->field('id,name')->where(array('parent_id'=>$parent_id))->select();
    }

}