<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/2
 * Time: 10:43
 */

namespace Home\Controller;


use Think\Controller;

class GoodsClickController extends Controller
{


    public function click($id){
        //>>1.连接上redis
        $redis = getRedis();
        //>>2.再去操作redis
        $redis->incr('goods_view_times:'.$id);
        /*
        $row = M('GoodsClick')->find($id);
        if($row){
            M('GoodsClick')->where(array('goods_id'=>$id))->setInc('click_times',1);
        }else{
            //>>1.根据id更改商品的点击数
            M('GoodsClick')->add(array('goods_id'=>$id,'click_times'=>1));
        }*/
    }




    public function redisToMysql(){
        //>>1.连接上redis
        $redis = getRedis();
        //>>2.需要从redis中得到所有商品的浏览次数
        $keys = $redis->keys('goods_view_times:*');
        $values = $redis->mget($keys);
        //>>3.将浏览次数保存到数据库表goods_click中
         foreach($keys as $i=>$key){
             $goods_id = str2arr($key,':')[1];  //从goods_view_times:10中取出商品的id
             $view_times = $values[$i];  //对应的浏览次数

             $row = M('GoodsClick')->find($goods_id);
             if($row){
                 M('GoodsClick')->where(array('goods_id'=>$goods_id))->setInc('click_times',$view_times);
             }else{
                 //>>1.根据id更改商品的点击数
                 M('GoodsClick')->add(array('goods_id'=>$goods_id,'click_times'=>$view_times));
             }
         }
        //>>4.将redis中的键删除
        $redis->del($keys);


    }

}