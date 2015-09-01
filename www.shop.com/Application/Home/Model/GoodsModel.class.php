<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/1
 * Time: 15:29
 */

namespace Home\Model;




use Think\Model;

class GoodsModel extends Model
{

    /*
     * 查询出正常并且上架的商品
     *
     * 根据指定的状态查询出$num个商品
     */
    public function getGoodsByStatus($status,$num){
        return $this->field('id,name,shop_price,logo')->where("goods_status & $status > 0 and is_on_sale = 1 and status = 1")->limit($num)->select();
    }



    public function find($id){
        $goods = parent::find($id);
        if(!empty($goods)){
            //>>1.从goods_gallery表中找到商品相册图片
            $goodsGalleryModel = M('GoodsGallery');
            $paths = $goodsGalleryModel->field('path')->where(array('goods_id'=>$id))->select();
            $paths = array_column($paths,'path');

            //>>2.需要将logo作为相册的第一张图片  [logo,1,2,3,4,5]
            array_unshift($paths,$goods['logo']);

            $goods['galleryPaths'] = $paths;
        }
        return $goods;
    }
}