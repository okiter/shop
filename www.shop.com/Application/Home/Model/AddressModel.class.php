<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/5
 * Time: 9:29
 */

namespace Home\Model;


use Think\Model;

class AddressModel extends Model
{


    public function add(){
        $requestData = $this->data;
        $requestData['member_id'] = UID;
        //>>1.判断当前添加的数据是否为 默认地址, 如果是默认地址的话先将其他地址变成非默认地址
         if(!empty($this->data['is_default'])){
             $this->where(array('member_id'=>UID))->setField('is_default',0);
         }
        //>>2.再将当前数据添加到数据库中
        $locationsModel = M('Locations');
        $requestData['province_name'] = $locationsModel->getFieldById($requestData['province_id'],'name');
        $requestData['city_name'] = $locationsModel->getFieldById($requestData['city_id'],'name');
        $requestData['area_name'] = $locationsModel->getFieldById($requestData['area_id'],'name');
        if(empty($requestData['id'])){
            return parent::add($requestData);
        }else{
            return parent::save($requestData);
        }
    }


    /**
     * 得到当前用户的所有地址
     */
    public function getList(){
        $this->where(array('member_id'=>UID));
        $this->field('id,name,province_name,city_name,area_name,tel,detail_address,is_default');
        return $this->select();
    }


    public function setDefault($id){
        //>>1.先将其他的设置为非默认
          $this->where(array('member_id'=>UID))->setField('is_default',0);
        //>>2.将当前id设置为默认
          return $this->where(array('id'=>$id))->setField('is_default',1);
    }
}