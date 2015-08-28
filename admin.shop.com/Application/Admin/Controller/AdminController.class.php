<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:45
 */

namespace Admin\Controller;


use Think\Controller;

class AdminController extends BaseController
{
    /**
     * 是否是有post中的所有参数传递给model
     * @var bool
     */
    protected $usePostParam = true;

    protected $meta_title = '管理员';


    public function _before_edit_view(){
        //>>1.准备角色数据
        $roleModel = D('Role');
        $roles = $roleModel->getList('id,name');
        $this->assign('roles',$roles);
        //>>2.准备权限的json数据
        $permissionModel  = D('Permission');
        $permissions = $permissionModel->getList('id,name,parent_id');
        $this->assign('permissions',json_encode($permissions));
    }
}