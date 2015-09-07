<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:45
 */

namespace Admin\Controller;


use Think\Controller;

class OrderInfoController extends BaseController
{
    protected $meta_title = '订单';


    /**
     * 根据订单的id发货
     * @param $id
     */
    public function send($id){
        $orderInfoModel = D('OrderInfo');
        $result = $orderInfoModel->send($id);
        if($result===false){
            $this->error('发货失败!'.showModelError($orderInfoModel));
        }else{
            $this->success('发货成功!',U('index'));
        }
    }
}