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
     * 该方法主要是被子类覆盖.
     */
    protected function _setModel()
    {
        $this->field('obj.*,gc.name as goods_category_name,b.name as brand_name,s.name as supplier_name');
        $this->join('__GOODS_CATEGORY__ as  gc on  obj.goods_category_id=gc.id');
        $this->join('__BRAND__ as  b on obj.brand_id = b.id');
        $this->join('__SUPPLIER__ as  s on obj.supplier_id = s.id');
    }

    /**
     * 主要是对查询出来的数据列表进一步处理
     */
    protected function _handleRows(&$rows){
        foreach($rows as &$row){
            $goods_status = $row['goods_status'];
            //是否为精品
            $row['is_best'] = $goods_status & 1==0?0:1;
            //是否为新品
            $row['is_new'] = $goods_status & 2==0?0:1;
            //是否为热销
            $row['is_hot'] = $goods_status & 4==0?0:1;
        }
    }

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
            $this->error = '添加简介信息失败!';
            $this->rollback();
            return false;
        }
        //>>4.处理商品相册中的图片
        $result = $this->handlerGallery($id,$requestData['gallery_path']);
        if($result===false){
            $this->error = '添加商品图片失败!';
            $this->rollback();
            return false;
        }
        //>>5.处理商品相关文章
        $result = $this->handleArticle($id,$requestData['article_ids']);
        if($result===false){
            $this->error = '添加商品文章失败!';
            $this->rollback();
            return false;
        }
        //>>6.处理会员价格
        $result = $this->handleMemberPrice($id,$requestData['member_goods_price']);
        if($result===false){
            $this->error = '处理会员价格失败!';
            $this->rollback();
            return false;
        }
        //>>7.处理商品属性
        $result = $this->handleGoodsAttribute($id,$requestData['goods_attribute']);
        if($result===false) {
            $this->error = '处理商品属性!';
            $this->rollback();
            return false;
        }

        return $this->commit();//提交事务
    }


    /**
     * 保存商品属性
     * @param $id
     * @param $goods_attribute
     */
    private function handleGoodsAttribute($id,$goods_attributes){
        if(empty($goods_attributes)){
            return false;
        }

        //构建数据
        $rows = array();
        foreach($goods_attributes as $attribute_id =>$value){
            if(is_array($value)){
                //多值属性的话,需要将每一个值都保存为一行记录
                foreach($value as $v){
                    $rows[] = array('goods_id'=>$id,'attribute_id'=>$attribute_id,'value'=>$v);
                }
            }else{
                //单值属性的话, 直接将值保存到一行记录中
                $rows[] = array('goods_id'=>$id,'attribute_id'=>$attribute_id,'value'=>$value);
            }
        }

        //保存
        if(!empty($rows)){
            $goodsAttributeModel = M('GoodsAttribute');
            return $goodsAttributeModel->addAll($rows);
        }

    }


    /**
     * 将商品的会员价格保存到数据库中
     * @param $goods_id
     * @param $member_goods_prices
     *
     *
     *  ["member_goods_price"] => array(3) {
            [1] => string(3) "100"    键: 会员级别id  值: 会员价格
            [2] => string(2) "90"
            [3] => string(2) "80"
    }
     */
    private function handleMemberPrice($goods_id,$member_goods_prices){
        //>>1.删除会员价格
        $memberGoodsPriceModel = M('MemberGoodsPrice');
        $memberGoodsPriceModel->where(array('goods_id'=>$goods_id))->delete();


        //>>2.再将会员价格添加到数据库中
        $rows = array();
        foreach($member_goods_prices as $member_level_id=>$price){
            $rows[] =  array('member_level_id'=>$member_level_id,'price'=>$price,'goods_id'=>$goods_id);
        }
        if(!empty($rows)){
            return $memberGoodsPriceModel->addAll($rows);
        }
    }

    private function handleArticle($goods_id,$article_ids){
        if(!empty($article_ids)){
            $goodsArticleModel = M('GoodsArticle');
            //先删除当前商品的相关文章
            $goodsArticleModel->where(array('goods_id'=>$goods_id))->delete();

            //再将这次的文章保存.
            $rows = array();
            foreach($article_ids as $article_id){
                $rows[] = array('goods_id'=>$goods_id,'article_id'=>$article_id);
            }
            return$goodsArticleModel->addAll($rows);
        }

    }

    /**
     * 将相册图片路径保存到goods_gallery表中
     * @param $goods_id
     * @param $gallery_paths
     * @return bool|string
     */
    private function handlerGallery($goods_id,$gallery_paths){
         if(!empty($gallery_paths)){
             $rows = array();
             foreach($gallery_paths as $gallery_path){
                 $rows[] =  array('path'=>$gallery_path,'goods_id'=>$goods_id);
             }

            return M('GoodsGallery')->addAll($rows);
         }
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


            //>>2.goods_gallery表中获取当前商品的图片路径
            $gallerys  = M('GoodsGallery')->field('id,path')->where(array('goods_id'=>$id))->select();
            $goods['gallerys'] =  $gallerys;

            //>>3.从goods_article表中获取相关文章的id, 再到article表中找到相关文章的name
            $articles = M()->query("SELECT a.id,a.name FROM `goods_article` as obj  join article as a on obj.article_id=a.id  where obj.goods_id = $id");
            $goods['articles']=$articles;


            //>>4.需要根据商品的id查询出该商品的会员价格
            $memberGoodsPriceModel = M('MemberGoodsPrice');
            $memberGoodsPrices = $memberGoodsPriceModel->field('member_level_id,price')->where(array('goods_id'=>$id))->select();
            if(!empty($memberGoodsPrices)){
                //将$memberGoodsPrices中的member_level_id 作为索引, price作为索引的值
                $member_level_ids = array_column($memberGoodsPrices,'member_level_id');
                $prices = array_column($memberGoodsPrices,'price');
                $memberGoodsPrices = array_combine($member_level_ids,$prices);
                $goods['memberGoodsPrices'] = $memberGoodsPrices;

                /**
                 * $memberGoodsPrices的数据结构为:
                 *
                 * array('member_level_id的值'=>price)
                 */
            }


            //>>5.根据当前商品的属性类型的id,查询出来从attribute表中查询出属性
            $attributes  = D('Attribute')->getByGoodsTypeId($goods['goods_type_id']);
            $goods['attributes'] = json_encode($attributes);

            //>>6.查询出当前商品的属性值
            /**
             *[
                array( 'attribute'=>1, value=>S,)
                array('attribute'=>1, value=>M,)
                array(attribute'=>2, value=>黑色,)
                array(attribute'=>2, value=>蓝色,)
                array(attribute'=>3, value=>蚕丝,)
            ]
             将上面的数组转换为 下面的数组
                1=>['S','M'],
                2=>['黑色','蓝色']
                3=>['蚕丝']
             */
            $goodsAttributes = M('GoodsAttribute')->field('attribute_id,value')->where(array('goods_id'=>$id))->select();
            $temps = array();
            foreach($goodsAttributes as $goodsAttribute){
                $temps[$goodsAttribute['attribute_id']][] = $goodsAttribute['value'];
            }
            $goods['goods_attributes'] = json_encode($temps);
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

        //>>3.将新上传的图片添加到goods_gallery表中
        $result = $this->handlerGallery($requestData['id'],$requestData['gallery_path']);
        if($result===false){
            $this->error = '更新相册失败!';
            $this->rollback();
            return false;
        }


        //>>5.处理商品相关文章
        $result = $this->handleArticle($requestData['id'],$requestData['article_ids']);
        if($result===false){
            $this->error = '更新商品文章失败!';
            $this->rollback();
            return false;
        }
        //>>6.处理会员商品价格
        $result = $this->handleMemberPrice($requestData['id'],$requestData['member_goods_price']);
         if($result===false){
             $this->error = '更新会员价格失败!';
             $this->rollback();
             return false;
         }


        //>>7.更新时处理商品属性
        $result = $this->handleUpdateGoodsAttribute($requestData['id'],$requestData['goods_attribute']);
        if($result===false){
            $this->error = '更新属性失败!';
            $this->rollback();
            return false;
        }

        return $this->commit();
    }


    /**
     * 单值属性的处理方式
     *   直接将值更新到当前商品的属性值上.
     *
     * 多值属性的处理方式
     *   请求中有,数据库中没有,  需要将请求中的其添加到数据库中
     *   请求中有,数据库中也有,  不需要更新
     *   请求中没有有,数据库有,  需要从数据库中删除
     *   请求中没有有,数据库没有有, 不需要管..
     *
     * 更新商品属性
     * @param $goods_id
     * @param $goods_attributes
     */
    private function handleUpdateGoodsAttribute($goods_id,$goods_attributes){
        $goodsAttributeModel = M('GoodsAttribute');


        //数据库中的多值属性的值
        $sql = "SELECT obj.attribute_id,obj.value FROM `goods_attribute` as obj join attribute as a on obj.attribute_id = a.id where a.attribute_type=2 and obj.goods_id = $goods_id";
        $dbGoodsAttributes = $this->query($sql);

        //>>1.循环每个属性, 检查是否为单值或者是多值
        foreach($goods_attributes as $attribute_id => $requestValues){
            //>>2.判断values是否为数组,如果是数组说明是多值, 如果是非数组是单值属性
            if(!is_array($requestValues)){
                //>>3.单值属性直接更新到数据库中
                $result = $goodsAttributeModel->where(array('goods_id'=>$goods_id,'attribute_id'=>$attribute_id))->save(array('value'=>$requestValues));
                if($result===false){
                    return false;
                }
            }else{
                //>>4.多值属性需要,进一步处理
                    foreach($requestValues as $requestValue){
                        //>>4.1 请求中有,数据库中没有,  需要将请求中的其添加到数据库中
                            //>>a.检查该值是否中数据库中存在
                            $result = $goodsAttributeModel->where(array('goods_id'=>$goods_id,'attribute_id'=>$attribute_id,'value'=>$requestValue))->find();
                            //>>b.不存在保存到数据库中
                            if(empty($result)){
                               $result =  $goodsAttributeModel->add(array('goods_id'=>$goods_id,'attribute_id'=>$attribute_id,'value'=>$requestValue));
                                if($result===false){
                                    return false;
                                }
                            }else{
                               //>> 请求中有,数据库中也有,  不需要更新
                            }
                    }




                //>>4.2 请求中没有,数据库有,  需要从数据库中删除 , 使用数据库中的值和请求中的值进行对比.
                //>>a.需要将数据库中的多值属性查询出来
//                                  $dbGoodsAttributes
                //>>b.和请求中的多值属性进行对比
                foreach($dbGoodsAttributes as $dbGoodsAttribute){
                     //要和请求中的属性对应的值进行对比..
                    if($attribute_id == $dbGoodsAttribute['attribute_id']){
                        if(!in_array($dbGoodsAttribute['value'],$requestValues)){
                            $result = $goodsAttributeModel->where(array('goods_id'=>$goods_id,'attribute_id'=>$attribute_id,'value'=>$dbGoodsAttribute['value']))->delete();
                            if($result===false){
                                return false;
                            }
                        }
                    }
                }
            }
        }


        //>>4.解决请求中没有选中的多值属性
            //>>a.数据库中的多值属性id
             $db_attribute_ids =  array_column($dbGoodsAttributes,'attribute_id');
            //>>b.请求中的属性的id
             $request_attribute_ids = array_keys($goods_attributes);
           /*  foreach($db_attribute_ids as $db_attribute_id){
                    if(!in_array($db_attribute_id,$request_attribute_ids)){
                        //>>c.将该属性的所有的值从数据库中删除
                        $result = $goodsAttributeModel->where(array('goods_id'=>$goods_id,'attribute_id'=>$db_attribute_id))->delete();
                        if($result===false){
                            return false;
                        }
                    }
             }*/
            $diff_attribute_ids = array_diff($db_attribute_ids,$request_attribute_ids);
            if(!empty($diff_attribute_ids)){
                $result = $goodsAttributeModel->where(array('goods_id'=>$goods_id,'attribute_id'=>array('in',$diff_attribute_ids)))->delete();
                if($result===false){
                    return false;
                }
            }




    }
}