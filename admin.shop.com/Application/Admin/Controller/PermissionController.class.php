<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:45
 */

namespace Admin\Controller;


use Think\Controller;

class PermissionController extends BaseController
{
    protected $meta_title = '权限';


    public function index()
    {
        $rows = $this->model->getList();
        $this->assign('rows',$rows);

        //保存当前请求的url地址到cookie中,为了做其他操作再通过该url回去...
        cookie('__forward__', $_SERVER['REQUEST_URI']);

        //显示出当前列表的标题
        $this->assign('meta_title',$this->meta_title);

        $this->_before_index_view();
        $this->display('index');
    }


    public function _before_edit_view(){
        $rows = $this->model->getList('id,name,parent_id');
        $this->assign('rows',json_encode($rows));
    }
}