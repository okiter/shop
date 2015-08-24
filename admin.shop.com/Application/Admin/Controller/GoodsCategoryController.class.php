<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:45
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsCategoryController extends BaseController
{
    protected $meta_title = '商品分类';

    public function index()
    {
        //查询出没有被删除的分类数据
        $rows = $this->model->getList();
        $this->assign('rows',$rows);

        //显示出当前列表的标题
        $this->assign('meta_title',$this->meta_title);
        $this->display('index');
    }

    protected function _before_edit_view(){
        $rows = $this->model->getList();
        $this->assign('rows',json_encode($rows));
    }

}