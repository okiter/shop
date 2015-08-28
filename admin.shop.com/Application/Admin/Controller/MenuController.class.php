<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:45
 */

namespace Admin\Controller;


use Think\Controller;

class MenuController extends BaseController
{
    protected $meta_title = '菜单表';
    /**
     * 是否是有post中的所有参数传递给model
     * @var bool
     */
    protected $usePostParam = true;


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
        //>>1.获取所有的供选择的父菜单
        $rows = $this->model->getList('id,name,parent_id');
        $this->assign('rows',json_encode($rows));

        //>>2.获取所有的权限. 供菜单选择
        $permissionModel = D('Permission');
        $permissions = $permissionModel->getList('id,name,parent_id');
        $this->assign('permissions',json_encode($permissions));

    }
}