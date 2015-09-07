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



    protected function _setWheres(&$wheres){
        $keyword = I('get.keyword', '');
        if(!empty($keyword)){
            //清空之前在BaseController中的条件
            $wheres=array();

            //拼装   ( obj.name LIKE '1%' OR obj.sn LIKE '1%' )
            $orWheres['obj.name'] = array('like',$keyword.'%');
            $orWheres['obj.sn'] = array('like',$keyword.'%');
            $orWheres['_logic'] = 'or';
            $wheres['_complex'] = $orWheres;
        }



        $goods_category_id = I('get.goods_category_id', '');
        if(!empty($goods_category_id)){
            $wheres['obj.goods_category_id'] = $goods_category_id;
        }

        $supplier_id = I('get.supplier_id', '');
        if(!empty($supplier_id)){
            $wheres['obj.supplier_id'] = $supplier_id;
        }

        $brand_id = I('get.brand_id', '');
        if(!empty($brand_id)){
            $wheres['obj.brand_id'] = $brand_id;
        }

    }

    /**
     * 在index页面展示之前为页面分配分类数据, 供货商数据, 品牌数据
     */
    protected function _before_index_view(){
        //>>1.准备品牌数据
        $brandModel = D('Brand');
        $brands = $brandModel->getList('id,name');
        $this->assign('brands',$brands);
        //>>2.准备供货商数据
        $supplierModel = D('Supplier');
        $suppliers = $supplierModel->getList('id,name');
        $this->assign('suppliers',$suppliers);
        //>>3.准备所有的分类数据
        $goodsCategoryModel = D('GoodsCategory');
        $goodsCategorys = $goodsCategoryModel->getList('id,name');
        $this->assign('goodsCategorys',$goodsCategorys);

    }



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

        //>>4.查询出会员级别的数据
        $memberLevelModel = D('MemberLevel');
        $memberLevels = $memberLevelModel->getList('id,name');
        $this->assign('memberLevels',$memberLevels);

    }



    public function add()
    {
        if (IS_POST) {
            if ($this->model->create() !== false) { //1.接收数据  2.自动验证  3.自动完成

                $requestData = I('post.');
                $requestData['intro'] = I('post.intro','',false);

                if ($this->model->add($requestData) !== false) {
                    $this->success('保存成功!', U('index'));
                    return;
                }
            }
            $this->error('保存失败!' . showModelError($this->model));
        } else {
            $this->assign('meta_title','添加'.$this->meta_title);

            $this->_before_edit_view();
            $this->display('edit');
        }
    }


    public function edit($id)
    {
        if (IS_POST) {
            if ($this->model->create() !== false) { //1.接收数据  2.自动验证  3.自动完成

                $requestData = I('post.');
                $requestData['intro'] = I('post.intro','',false);

                if ($this->model->save($requestData) !== false) {
                    $this->success('更新成功!', cookie('__forward__'));
                    return;
                }
            }
            $this->error('更新失败!' . showModelError($this->model));
        } else {
            //根据id只找到当前表中id对应的记录
            $row = $this->model->find($id);
            $this->assign($row);
            $this->assign('meta_title','编辑'.$this->meta_title);
            $this->_before_edit_view();
            $this->display('edit');
        }
    }


    /**
     * 根据gallery_id删除goods_gallery表中的一行记录
     * @param $gallery_id
     */
    public function deleteGallery($gallery_id)
    {
        $galleryModel = M('GoodsGallery');
        if($galleryModel->delete($gallery_id)===false){
            $this->error('删除失败!'.showModelError($galleryModel));
        }else{
            $this->success('删除成功!'.showModelError($galleryModel));
        }
    }
}