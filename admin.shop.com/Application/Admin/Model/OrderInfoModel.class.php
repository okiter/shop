<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:46
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class OrderInfoModel extends BaseModel
{
    // 自动验证定义   $fields 表中的每个字段信息
    protected $_validate        =   array(
        array('sn','require','订单号，唯一不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('member_id','require','会员ID不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('name','require','收货人不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('province_name','require','省份不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('city_name','require','城市不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('area_name','require','区县不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('detail_address','require','详细地址不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('tel','require','手机号不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('delivery_id','require','配送方式的ID不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('delivery_name','require','配送方式的名字不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('delivery_price','require','运费不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('pay_type','require','支付方式不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('invoice_id','require','发票ID不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('price','require','发票金额不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('inputtime','require','发票时间不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('order_status','require','订单状态不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('shipping_status','require','物流状态不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('pay_status','require','支付状态不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('trade_no','require','支付宝交易号不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
    );


    /**
     * 根据id发货. 告诉支付宝,用户已经发货
     * @param $id
     */
    public function send($id){

             //支付宝交易号
              $trade_no =  $this->getFieldById($id,'trade_no');


            $alipay_config  = C('ALIPAY_CONFIG');
            vendor("Alipay.lib.alipay_submit#class");
            /**************************请求参数**************************/


//            $trade_no = $_POST['WIDtrade_no'];
            //必填
            //物流公司名称
            $logistics_name = '顺丰物流';
            //必填
            //物流发货单号
            $invoice_no = '200085775555';
            //物流运输类型
            $transport_type = 'EXPRESS';
            //三个值可选：POST（平邮）、EXPRESS（快递）、EMS（EMS）


            /************************************************************/

    //构造要请求的参数数组，无需改动
            $parameter = array(
                "service" => "send_goods_confirm_by_platform",
                "partner" => trim($alipay_config['partner']),
                "trade_no"	=> $trade_no,
//                "logistics_name"	=> $logistics_name,
//                "invoice_no"	=> $invoice_no,
                "transport_type"	=> $transport_type,
                "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
            );

    //建立请求 , 发送请求给支付宝 系统
            $alipaySubmit = new \AlipaySubmit($alipay_config);
            $html_text = $alipaySubmit->buildRequestHttp($parameter);  //自付宝系统返回响应内容
            if($html_text===false){
                 $this->error = '当前网站运行环境不满足需求.. 请安装ssl,openssl';
                return false;
            }

            $simpleXML = simplexml_load_string($html_text);
            $is_success =  ((string)$simpleXML->is_success);
            if($is_success=='T'){
              return true;
            }elseif($is_success=='F'){
                $this->error = '改变发货状态失败!';
                return false;
            }





        exit;
    //解析XML
    //注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
            $doc = new DOMDocument();
            $doc->loadXML($html_text);

    //请在这里加上商户的业务逻辑程序代码

    //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

    //解析XML
            if(!empty($doc->getElementsByTagName( "alipay" )->item(0)->nodeValue) ) {
                $alipay = $doc->getElementsByTagName( "alipay" )->item(0)->nodeValue;
                echo $alipay;
            }

    }
}