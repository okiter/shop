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