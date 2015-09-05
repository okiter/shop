<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/2
 * Time: 16:02
 */

namespace Home\Controller;


use Think\Controller;

class OrderInfoController extends BaseController
{

    public function index(){
        if(IS_POST){
            //>>1.保存订单
            $orderInfoModel = D('OrderInfo');
            //add方法将订单中的数据保存到order_info表中
            $result = $orderInfoModel->add(I('post.'));
            if($result!==false){
                $this->success('下单成功!',U('orderSuccess'));
                return;
            }else{
                $this->success('下单失败!');
            }
        }else{

            //>>1.查询出当前登录用户的收货人信息
            $addressModel = D('Address');
            $addresses = $addressModel->getList();
            $this->assign('addresses',$addresses);

            //>>2.查询出所有的配送方式
            $deliveryModel = D('Delivery');
            $deliverys = $deliveryModel->getList();
            $this->assign('deliverys',$deliverys);



           //>>3.查询出购物车中的内容
            $shoppingCarModel=  D('ShoppingCar');
            $shoppingCar = $shoppingCarModel->lst();
            $this->assign('shoppingCar',$shoppingCar);


            $this->display('index');
        }
    }


    public function orderSuccess(){
        $this->display('success');
    }

}