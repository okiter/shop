<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:45
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsController extends BaseController
{
    protected $meta_title = '商品';

    /**
     * 钩子方法:
     * 主要被子类覆盖.. 在编辑页面展示之前执行该方法..
     */
    protected function _before_edit_view()
    {
        //>>1.为ztree准备商品分类数据
        $goodsCategoryModel = D('GoodsCategory');
        $goodsCategories = $goodsCategoryModel->getList('id,name,parent_id');
        $this->assign('goodsCategories',json_encode($goodsCategories));

        //>>2.为页面提供品牌数据
        $brandModel = D('Brand');
        $brandds = $brandModel->getList('id,name');
        $this->assign('brands',$brandds);
        //>>3.为页面提供供货商的数据
        $supplierModel = D('Supplier');
        $suppliers = $supplierModel->getList('id,name');
        $this->assign('suppliers',$suppliers);

    }


}