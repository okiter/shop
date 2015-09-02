<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/2
 * Time: 14:19
 */

namespace Home\Model;


use Think\Model;

class ShoppingCarModel extends Model
{

    /**
     * 将商品和数量添加到购物车中
     */
    public function add($goods_id,$amount){
        if(!isLogin()){
            $this->toCookie($goods_id,$amount);
        }else{
            $this->toDB($goods_id,$amount);
        }
    }

    /**
     * create table shopping_car(
        id int unsigned primary key auto_increment,
        goods_id int unsigned  not null default 0 comment '商品',
        amount int unsigned not null default 0 comment '数量',
        member_id int unsigned not null default 0 comment '用户ID',
        Index (member_id)
    )engine=MyISAM comment '购物车'


     * 将购物数据保存到数据库表中
     * @param $goods_id
     * @param $amount
     */
    private function toDB($goods_id,$amount){

          //>>1.检查当前登录用户是否购买了该商品
            $row = $this->where(array('member_id'=>UID,'goods_id'=>$goods_id))->find();
            if($row){
                //>>2. 如果购买过了,改变数量
                $this->where(array('member_id'=>UID,'goods_id'=>$goods_id))->setInc('amount',$amount);
            }else{
                //>>2. 如果没有购买过了,直接添加到shopping_car表中
                parent::add(array('goods_id'=>$goods_id,'amount'=>$amount,'member_id'=>UID));
            }
    }

    /**
     * 将商品和数量添加到cookie中 , 以序列化的方式放到cookie中
     */
    private function toCookie($goods_id,$amount){
         //>>1.查看cookie中是否有数据
          $shoppingCar = cookie('shopping_car');
          if(empty($shoppingCar)){
              $shoppingCar = array();
          }else{
              $shoppingCar = unserialize($shoppingCar);
          }

        //>>2. 将商品和数量添加到 $shoppingCar 中
            $exist = false;
            foreach($shoppingCar as &$item){
                if($item['goods_id']==$goods_id){
                    $item['amount'] = $item['amount'] + $amount;
                    $exist = true;
                    break;
                }
            }
        if(!$exist){
            $shoppingCar[] = array('goods_id'=>$goods_id,'amount'=>$amount);
        }

        //>>3.将$shoppingCar放到cookie中
        cookie('shopping_car',serialize($shoppingCar),60*60*24*7);
    }



    public function cookie2db(){


        //必须定义UID,因为登录时没有定义UID
        $userinfo = login();
        define('UID',$userinfo['id']);

        //>>1.从cookie中取出购物数据
        $shoppingCar = cookie('shopping_car');
        if(!empty($shoppingCar)){
            $shoppingCar = unserialize($shoppingCar);
            foreach($shoppingCar as $item){
                //>>2.将购物数据保存到db
                $this->toDB($item['goods_id'],$item['amount']);
            }
            //>>3.清楚cookie
            cookie('shopping_car',null);
        }
    }



    public function lst(){
        if(isLogin()){
            //登录的情况下从数据库中查询出购物数据
            $shoppingCar = $this->field('goods_id,amount')->where(array('member_id'=>UID))->select();
        }else{
            //没有登录的情况下从cookie中查询出购物数据
            $shoppingCar = cookie('shopping_car');
            $shoppingCar = unserialize($shoppingCar);
        }
        $this->buildShoppingCar($shoppingCar);
        return $shoppingCar;
    }

    /**
     * 重构每个购物车明细. 根据商品id将name,logo,shop_price
     * @param $shoppingCar
     */
    private function buildShoppingCar(&$shoppingCar){
        foreach($shoppingCar as &$item){  //goods_id, amount
            $goodsModel = M('Goods');
            $goods = $goodsModel->field('logo,name,shop_price')->find($item['goods_id']);
            $item = array_merge($item,$goods);
        }
    }
}