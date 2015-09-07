<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:45
 */

namespace Admin\Controller;


use Think\Controller;

class AttributeController extends BaseController
{
    protected $meta_title = '属性';

    /**
     * 该方法主要是被子类覆盖,用来忘wheres中添加条件
     * @param $wheres
     */
    protected function _setWheres(&$wheres)
    {
        $goods_type_id = I('get.goods_type_id');
        if(!empty($goods_type_id)){
            $wheres['obj.goods_type_id'] = $goods_type_id;
        }
    }


    /**
     * 在列表展示之前
     */
    public function _before_index_view(){
        $goodsTypeModel = D('GoodsType');
        $goodsTypes = $goodsTypeModel->getList('id,name');
        $this->assign('goodsTypes',$goodsTypes);
    }


    /**
     * 编辑页面展示之前
     */
    public function _before_edit_view(){
        $goodsTypeModel = D('GoodsType');
        $goodsTypes = $goodsTypeModel->getList();
        $this->assign('goodsTypes',$goodsTypes);

    }



    public function add()
    {
        if (IS_POST) {
            if ($this->model->create() !== false) { //1.接收数据  2.自动验证  3.自动完成
                if ($this->model->add($this->usePostParam?I('post.'):'') !== false) {
                    $this->success('保存成功!', cookie('__forward__'));
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


    /**
     * 根据商品类型id查询出属性
     * @param $goods_type_id
     */
    public function getByGoodsTypeId($goods_type_id){
           $rows = $this->model->getByGoodsTypeId($goods_type_id);
           $this->ajaxReturn($rows);
    }

}