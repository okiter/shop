<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/2
 * Time: 17:04
 */

namespace Home\Controller;


use Think\Controller;

class LocationsController extends Controller
{

    /**
     * 根据parent_id得到json数据,json是子地址
     * @param $parent_id
     */
    public function getChildren($parent_id){
        $locationsModel = D('Locations');
        $rows = $locationsModel->getChildren($parent_id);
        $this->ajaxReturn($rows);
    }

}