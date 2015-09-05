<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/2
 * Time: 16:51
 */

namespace Home\Controller;


use Think\Controller;

class AddressController extends BaseController
{

    public function index(){
        //>>1.准备所有的省份数据
        $locationsModel =  D('Locations');
        $provinces = $locationsModel->getChildren(0); //找到parent_id = 0 的省份
        $this->assign('provinces',$provinces);

        //>>2.为页面准备当前所有的收货地址
        $addressModel = D('Address');
        $addresses = $addressModel->getList();
        $this->assign('addresses',$addresses);

        $this->display('index');
    }




    public function add(){
        $addressModel = D('Address');
        if($addressModel->create()!==false){
            if(($id = $addressModel->add())!==false){
                $this->ajaxReturn($id);
            }
        }
        $this->error('添加失败!');
    }

    public function setDefault($id){
        $addressModel = D('Address');
        $result = $addressModel->setDefault($id);
        if($result!==false){
            $this->success('设置成功!',U('index'));
        }else{
            $this->success('设置失败!');
        }
    }


    /**
     * 根据id查询一行记录
     * @param $id
     */
    public function edit($id){
        $addressModel = D('Address');
        $row = $addressModel->find($id);
        $this->ajaxReturn($row);
    }

}