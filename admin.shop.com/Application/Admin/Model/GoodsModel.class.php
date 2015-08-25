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

class GoodsModel extends BaseModel
{
    // 自动验证定义   $fields 表中的每个字段信息
    protected $_validate_1 = array(
        array('name', 'require', '名称不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('sn', 'require', '货号不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('goods_category_id', 'require', '商品分类不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('brand_id', 'require', '品牌不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('supplier_id', 'require', '供货商不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('market_price', 'require', '市场价格不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('shop_price', 'require', '本店价格不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('stock', 'require', '库存不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('goods_status', 'require', '商品状态不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('is_on_sale', 'require', '是否上架不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('inputtime',NOW_TIME),
        array('goods_status','handler_good_status',self::MODEL_BOTH,'callback'),
    );

    /**
     * 自动处理商品状态
     * @param $goods_status  用户选择的商品状态
     */
    public function handler_good_status($goods_status){
        $init_status = 0;  //初始化状态 0
        if(!empty($goods_status)){
           foreach($goods_status as $v){
               $init_status = $init_status |$v;
           }
           return $init_status;
       }
        return $init_status;
    }


    /**
     * 将请求中的数据保存到数据库中
     * @param mixed|string $requestData   请求中的所有数据
     * @return bool
     */
    public function add($requestData){
        //$this->data: create收集到的当前表中的数据
        //$requestData: 请求中的所有数据

        $this->startTrans();  //开启事务

        //>>1.将请求中的数据保存到数据库中
        $id = parent::add();
        if($id===false){
            $this->rollback();
            return false;
        }
        //>>2.生成sn之后将sn更新到sn字段上
             //2015082500000001
             //2015082500000011
             //2015082500000111
        $sn =  date('Ymd').str_pad($id,8, "0", STR_PAD_LEFT);  ;
        $result = parent::save(array('id'=>$id,'sn'=>$sn));
        if($result===false){
            $this->rollback();
            return false;
        }

        //>>3.处理商品描述
        $result  = $this->handleGoodsIntro($id,$requestData['intro']);
        if($result===false){
            $this->error = '更新简介信息失败!';
            $this->rollback();
            return false;
        }

        return $this->commit();//提交事务
    }

    /**
     * 将goods_id和intro保存到goods_intro表中
     * @param $goods_id
     * @param $intro
     */
    private function handleGoodsIntro($goods_id,$intro){
        $goodsIntro = M('GoodsIntro');
        //>>1.删除原来的简介内容
        $goodsIntro->where(array('goods_id'=>$goods_id))->delete();
        //>>2.再将新的内容添加进去
        $data =array('goods_id'=>$goods_id,'content'=>$intro);
        return $goodsIntro->add($data);
    }



    public function find($id){
        $goods = parent::find($id);
        if(!empty($goods)){
            //说明根据id找到一行记录, 需要把这行记录中其他的信息找到

            //>>1.从goods_intro表中找到简介
            $intro  = M('GoodsIntro')->getFieldByGoods_id($id,'content');
            $goods['intro'] = $intro;

        }

        return $goods;
    }


    /**
     * 需要将请求中的数据更新到 goods表和goods_intro表中
     */
    public function save($requestData){
        //>>1.将$this->data的数据更新到goods表中
        $this->startTrans();
        $result = parent::save();
        if($result===false){
            $this->rollback();
            return false;
        }

        //>>2.将简介数据更新到goods_intro表中
        $result = $this->handleGoodsIntro($requestData['id'],$requestData['intro']);
        if($result===false){
            $this->error = '更新简介信息失败!';
            $this->rollback();
            return false;
        }
        return $this->commit();
    }
}