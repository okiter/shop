<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/5
 * Time: 15:49
 */

namespace Home\Model;


use Think\Model;

class OrderInfoModel extends Model
{

    /**
     * 将请求中的数据保存到order_info表中
     * @param mixed|string $requestData
     */
    public function add($requestData)
    {
        $this->startTrans();
        //>>1.先构建order_info表中需要的数据
             $orderInfo = array();

            //>>1.1 使用redis生成订单号码.
              //例如:  2015090500000000100
                  $redis = getRedis();
                  $sn = $redis->incr('order_sn');
                  $sn  = date('Ymd').str_pad($sn, 10, "0", STR_PAD_LEFT);
              $orderInfo['sn'] = $sn;

            //>>1.2 准备会员ID
              $orderInfo['member_id'] = UID;

            //>>1.3 准备收货人信息
                   $address_id = $requestData['address_id'];
                   $addressModel = D('Address');
                   $address = $addressModel->field('name,province_name,city_name,area_name,detail_address,tel')->find($address_id);
              $orderInfo = array_merge($orderInfo,$address);  //将收货人信息合并到orderInfo中
            //>>1.4.准备配送方式信息
                   $deliveryModel = M('Delivery');
                   $delivery = $deliveryModel->field('id as delivery_id,name as delivery_name,price as delivery_price')->find($requestData['delivery_id']);
              $orderInfo = array_merge($orderInfo,$delivery);
             //>>1.5 支付方式
              $orderInfo['pay_type'] = $requestData['pay_type'];

             //>>1.6 计算出当前购买货品的总费用
                   //取出购物车中每个商品的价格和数量进行相加
                    $shoppingCarModel  = D('ShoppingCar');
                    $shoppingCar = $shoppingCarModel->lst();
                   $price = 0;
                    foreach($shoppingCar as $item){
                        $price+= ($item['shop_price'] * $item['amount']);
                    }
                   //再加上运费
                  $total_price  = $price + $delivery['delivery_price'];

              $orderInfo['price'] = $total_price;
              $orderInfo['inputtime']   = NOW_TIME;


        //>>2.将orderInfo中的数据保存到orderInfo表中
            $id = parent::add($orderInfo);
            if($id===false){
                $this->rollback();
                return false;
            }
             $this->commit();


        //>>3.构建订单明细表order_info_item 中需要的数据, 保存到order_info_item表中      : 实际上就是将购物车中的明细变为订单的明细
            $orderInfoItems = array();
            foreach($shoppingCar as $item){
                $orderInfoItems[] = array('order_info_id'=>$id,'goods_id'=>$item['goods_id'],'logo'=>$item['logo'],'price'=>$item['shop_price'],'amount'=>$item['amount'],'total_price'=>$item['shop_price']*$item['amount']);
            }
            $orderInfoItemModel = M('OrderInfoItem');
            $result = $orderInfoItemModel->addAll($orderInfoItems);
            if($result===false){
                $this->error = '保存明细失败!';
                $this->rollback();
                return false;
            }
        //>>4.构建发票表invoice 中需要的数据. 将数据保存到inovice表中
            $invoice = array();
            //>>4.1 准备发票的名字
            $invoice_name ='';
            if($requestData['invoice_type']==1){ //表示个人
                $userinfo  = login();
                $invoice_name = $userinfo['username'];
            }else{  //表示公司
                $invoice_name = $requestData['invoice_name'];
            }

            $invoice['name'] = $invoice_name;

        //>>4.2 准备发票的内容
            $invoice_content = '';
            if($requestData['invoice_content']==1){
                 //需要将购买数据拼成一个字符串作为发票的内容
                foreach($orderInfoItems as $item){
                    $invoice_content.=$item['name'].'   '.$item['price'].'    '.$item['amount'].'    '.$item['total_price'].'\r\n';
                }
            }else{
                $invoice_content = $requestData['invoice_content'];
            }

            $invoice['content'] = $invoice_content;
            $invoice['price'] = $price;
            $invoice['inputtime'] = NOW_TIME;
            $invoice['member_id'] = UID;
            $invoice['order_info_id'] = $id;

            $invoiceModel = M('Invoice');
            $invoice_id = $invoiceModel->add($invoice);
            if($invoice_id===false){
                $this->error = '保存发票失败!';
                $this->rollback();
                return false;
            }

        //>>5. 需要将发票的id更新到订单中
            $result = parent::save(array('id'=>$id,'invoice_id'=>$invoice_id));
            if($result===false){
                $this->error = '订单和发票关联失败!';
                $this->rollback();
                return false;
            }
        $this->commit();
        return $id;
    }
}