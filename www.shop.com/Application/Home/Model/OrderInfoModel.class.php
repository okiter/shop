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


    /**
     * 根据订单的id进行支付
     * @param $id
     */
    public function doPay($id){
        //>>1.根据订单的id查询出需要的参数
             $orderInfo = $this->find($id);


          //>>1.1查询出订单的明细
            $orderInfoItems = M('OrderInfoItem')->where(array('order_info_id'=>$id))->select();

        //>>2.根据参数进行支付
        /**************************请求参数**************************/

        //支付类型
        $payment_type = "1";
        //必填，不能修改

        //服务器异步通知页面路径
        $notify_url = "http://www.shop.com/index.php/OrderInfo/notify_url.html";
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径,  支付成功之后跳转的页面
        $return_url = "http://www.shop.com/index.php/OrderInfo/lst.html";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        //商户订单号
        $out_trade_no = $orderInfo['sn'];
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject = '源代码商城的订单';  // 所用订单中的一个商品的名字作为订单的名称
        //必填

        //付款金额
        $price = $orderInfo['price'];
        //必填


        //商品数量
        $quantity = 1;
        //必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
        //物流费用
        $logistics_fee = $orderInfo['delivery_price'];
        //必填，即运费
        //物流类型
        $logistics_type = "EXPRESS";
        //必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
        //物流支付方式
        $logistics_payment = "BUYER_PAY";
        //必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        //订单描述
        $body = '源代码商城的订单描述';
        //商品展示地址
        $show_url = "http://www.shop.com/index.php/OrderInfo/show/id/"+$orderInfo['id']+".html";
        //需以http://开头的完整路径，如：http://www.商户网站.com/myorder.html

        //收货人姓名
        $receive_name = $orderInfo['name'];
        //如：张三

        //收货人地址
        $receive_address = $orderInfo['province_name'].$orderInfo['city_name'].$orderInfo['area_name'].$orderInfo['detail_address'];
        //如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号

        //收货人邮编
        $receive_zip = '123456';
        //如：123456

        //收货人电话号码
        $receive_phone = $orderInfo['tel'];
        //如：0571-88158090

        //收货人手机号码
        $receive_mobile = $orderInfo['tel'];
        //如：13312341234


        /************************************************************/
        $alipay_config = C('ALIPAY_CONFIG');

//构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_partner_trade_by_buyer",
            "partner" => trim($alipay_config['partner']),
            "seller_email" => trim($alipay_config['seller_email']),
            "payment_type"	=> $payment_type,
            "notify_url"	=> $notify_url,
            "return_url"	=> $return_url,
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "price"	=> $price,
            "quantity"	=> $quantity,
            "logistics_fee"	=> $logistics_fee,
            "logistics_type"	=> $logistics_type,
            "logistics_payment"	=> $logistics_payment,
            "body"	=> $body,
            "show_url"	=> $show_url,
            "receive_name"	=> $receive_name,
            "receive_address"	=> $receive_address,
            "receive_zip"	=> $receive_zip,
            "receive_phone"	=> $receive_phone,
            "receive_mobile"	=> $receive_mobile,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );

        header('Content-Type: text/html;charset=utf-8');
        //建立请求,引入AlipaySubmit所在的文件
        vendor('Alipay.lib.alipay_submit#class');
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
        exit;
    }
}