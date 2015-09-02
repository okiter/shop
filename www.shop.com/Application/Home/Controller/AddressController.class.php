<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/2
 * Time: 16:51
 */

namespace Home\Controller;


use Think\Controller;

class AddressController extends Controller
{

    public function index(){
        //>>1.准备所有的省份数据
        $locationsModel =  D('Locations');
        $provinces = $locationsModel->getChildren(0); //找到parent_id = 0 的省份
        $this->assign('provinces',$provinces);


        $this->display('index');
    }
}