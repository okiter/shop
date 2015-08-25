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
    protected $_validate        =   array(
        array('name','require','名称不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('sn','require','货号不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('logo','require','商品LOGO不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('goods_category_id','require','商品分类不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('brand_id','require','品牌不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('supplier_id','require','供货商不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('market_price','require','市场价格不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('shop_price','require','本店价格不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('stock','require','本店价格不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('goods_status','require','商品状态不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('is_on_sale','require','是否上架不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('status','require','状态不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('sort','require','排序不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
array('inputtime','require','录入时间不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
    );
}