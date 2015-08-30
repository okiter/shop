<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {


    public function menu(){
        $userinfo = login();
        $menuModel = D('Menu');
        //如果是超级管理员查询出所有的菜单
        if($userinfo['username']==C('SUPER_MANAGER_NAME')){
            //>>1.查询出所有菜单数据
            $menus = $menuModel->getList('name,url,level,parent_id,id');
        }else{
            //不是超级管理员,需要根据用户的权限,查询出能够访问的菜单
           $permission_ids =  savePermissionId();
           $permission_ids = arr2str($permission_ids);
           $sql = "SELECT DISTINCT m.level,m.id,m.name,m.parent_id,m.url FROM `menu` as m join menu_permission as mp on m.id=mp.menu_id  where mp.permission_id  in ($permission_ids);";
           $menus = $menuModel->query($sql);
        }

        $this->assign('menus',$menus);
        $this->display('menu');
    }

}