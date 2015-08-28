<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:46
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class RoleModel extends BaseModel
{
    // 自动验证定义   $fields 表中的每个字段信息
    protected $_validate        =   array(
        array('name','require','名称不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('status','require','状态不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('sort','require','排序不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
    );



    public function add($requestData){
        //>>1.将请求中需要的role表中数据添加role表中
        $id = parent::add();
        //>>2.将请求中的permissonIds保存到role_permission表中
        $this->handlePermission($id,$requestData);

        return $id;
    }

    /**
     * 将请求中的role_id和permission_id保存到role_permission表中
     * @param $role_id
     * @param $requestData
     */
    private function handlePermission($role_id,$requestData){
        $permission_ids = $requestData['permission_ids'];
        $rolePermissionModel = M('RolePermission');
        //先删除原来的权限
        $rolePermissionModel->where(array('role_id'=>$role_id))->delete();
        //再添加
        if(!empty($permission_ids)){
            $rows = array();
            foreach($permission_ids as $permission_id){
                $rows[] = array('role_id'=>$role_id,'permission_id'=>$permission_id);
            }
            $rolePermissionModel->addAll($rows);
        }
    }

    public function save($requestData){
        //>>1.根据role表中的数据
        $result = parent::save();
        //>>2将请求中的permissonIds更新到role_permission表中
        $this->handlePermission($requestData['id'],$requestData);
        return $result;
    }

    /**
     * 根据id找到对应的角色
     * @param array|mixed $id
     */
    public function find($id)
    {
        $role = parent::find($id);
        if(!empty($role)){
                $rolePermissonModel = M('RolePermission');
                $permission_id = $rolePermissonModel->field('permission_id')->where(array('role_id'=>$id))->select();
                $permission_ids = array_column($permission_id,'permission_id');
                $role['permission_ids'] = json_encode($permission_ids);
        }

        return $role;
    }
}