<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:45
 */

namespace Admin\Controller;


use Think\Controller;

class RoleController extends BaseController
{
    protected $usePostParam = true;

    protected $meta_title = '角色';

    public function _before_edit_view(){
        //>>1.为页面准备所有的权限数据
        $permissionModel = D('Permission');
        $rows = $permissionModel->getList('id,name,parent_id');
        $this->assign('rows',json_encode($rows));
    }

}