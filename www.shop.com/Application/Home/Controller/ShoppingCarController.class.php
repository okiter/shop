<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/2
 * Time: 14:17
 */

namespace Home\Controller;


use Think\Controller;

class ShoppingCarController extends Controller
{

    public function add(){
        /**
         * 控制器接收请求参数
         */
       $goods_id = I('post.goods_id');
       $amount = I('post.amount');

        /**
         * 将商品和数量添加到购物车中
         */
       $shoppingCarModel = D('ShoppingCar');
       $shoppingCarModel->add($goods_id,$amount);


        $this->success('添加成功!',U('lst'));
    }


    /**
     * 购物车列表显示出来
     */
    public function lst(){
        $shoppingCarModel = D('ShoppingCar');
        $shoppingCar = $shoppingCarModel->lst();
        $this->assign('shoppingCar',$shoppingCar);
        $this->display('lst');
    }


}