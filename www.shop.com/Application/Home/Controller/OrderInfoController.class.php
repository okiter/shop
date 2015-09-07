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
                $this->success('下单成功!',U('orderSuccess',array('id'=>$result)));
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


    /**
     * 根据订单的id显示订单的编号,金额,支付方式
     * @param $id
     */
    public function orderSuccess($id){
        //>>1.根据订单
        $orderInfo = M('OrderInfo');
        $row = $orderInfo->field('sn,price,id,pay_type')->find($id);
        $this->assign($row);
        $this->display('success');
    }


    /**
     * 订单ID
     * @param $id
     */
    public function doPay($id){
        $orderInfoModel = D('OrderInfo');
        $result = $orderInfoModel->doPay($id);
        if($result===false){
            echo 'not pay';
        }else{
            echo 'pay success';
        }
    }



    public function lst(){
        echo 'order list...';
    }



    public function notify_url(){
        //>>1.更改订单的状态...
        /* *
     * 功能：支付宝服务器异步通知页面
     * 版本：3.3
     * 日期：2012-07-23
     * 说明：
     * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
     * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


     *************************页面功能说明*************************
     * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
     * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
     * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
     * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
     */

        $alipay_config = C('ALIPAY_CONFIG');
        require_once("Alipay.lib.alipay_notify#class");

        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();  //验证是否为支付宝服务器的请求
        if($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代


            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];   //得到当前订单号

            //支付宝交易号
            $trade_no = $_POST['trade_no'];

            $orderInfoModel = D('OrderInfo');

            //交易状态
            $trade_status = $_POST['trade_status'];
            if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
                //该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                //在支付宝中产生了记录,就将交易号保存到  订单表中..
                $result  = $orderInfoModel->where(array('sn'=>$out_trade_no))->save(array('trade_no'=>$trade_no));
                if($result===false){
                    echo  'fail';
                    exit;
                }

                echo "success";		//请不要修改或删除

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
                //该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货

                  $result  = $orderInfoModel->where(array('sn'=>$out_trade_no))->save(array('order_status'=>1,'pay_status'=>2));
                  if($result===false){
                      echo  'fail';
                      exit;
                  }
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                echo "success";		//请不要修改或删除

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
                //该判断表示卖家已经发了货，但买家还没有做确认收货的操作

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                $result  = $orderInfoModel->where(array('sn'=>$out_trade_no))->save(array('order_status'=>5,'pay_status'=>2,'shipping_status'=>1));
                if($result===false){
                    echo  'fail';
                    exit;
                }

                echo "success";		//请不要修改或删除

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if($_POST['trade_status'] == 'TRADE_FINISHED') {
                //该判断表示买家已经确认收货，这笔交易完成

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                echo "success";		//请不要修改或删除

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else {
                //其他状态判断
                echo "success";

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult ("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            echo "fail";
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }
}