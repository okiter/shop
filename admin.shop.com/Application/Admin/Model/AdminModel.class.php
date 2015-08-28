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

class AdminModel extends BaseModel
{
    // 自动验证定义   $fields 表中的每个字段信息
    protected $_validate        =   array(
        array('username','require','用户名不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('password','require','密码不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
    );

    /**
     * 将请求中的数据分别保存到admin,admin_role,admin_permission表中
     * @param mixed|string $requestData
     */
    public function add($requestData){
        $this->startTrans();

        //>>1.将请求数据保存到admin表中
        $id = parent::add();
        if($id===false){
            $this->rollback();
            return false;
        }
        //>>2.将角色数据保存到admin_role
        $result = $this->handleRole($id,$requestData['role_ids']);
        if($result===false){
            $this->error = '保存角色时失败!';
            $this->rollback();
            return false;
        }


        //>>2.将额外权限保存到admin_permission表中
        $result = $this->handlePermission($id,$requestData['permission_ids']);
        if($result===false){
            $this->error = '保存权限时失败!';
            $this->rollback();
            return false;
        }
        return $this->commit();
    }

    public function save($requestData){
        $this->startTrans();
        $result =  parent::save();
        if($result===false){
            $this->rollback();
            return false;
        }

        $result = $this->handleRole($requestData['id'],$requestData['role_ids']);
        if($result===false){
            $this->error = '保存角色时失败!';
            $this->rollback();
            return false;
        }


        //>>2.将额外权限保存到admin_permission表中
        $result = $this->handlePermission($requestData['id'],$requestData['permission_ids']);
        if($result===false){
            $this->error = '保存权限时失败!';
            $this->rollback();
            return false;
        }
       return $this->commit();
    }

    /**
     * 将额外权限保存到admin_permission表中
     * @param $admin_id
     * @param $permission_ids
     */
    private function handlePermission($admin_id,$permission_ids){
        $adminPermissionModel = M('AdminPermission');
        $adminPermissionModel->where(array('admin_id'=>$admin_id))->delete();
        if(!empty($permission_ids)){
            $rows = array();
            foreach($permission_ids as $permission_id){
                $rows[] =  array('permission_id'=>$permission_id,'admin_id'=>$admin_id);
            }
            return M('AdminPermission')->addAll($rows);
        }
    }


    private function handleRole($admin_id,$role_ids){
        $adminRoleModel = M('AdminRole');
        $adminRoleModel->where(array('admin_id'=>$admin_id))->delete();

        if(!empty($role_ids)){
            $rows = array();
            foreach($role_ids as $role_id){
                $rows[] =  array('role_id'=>$role_id,'admin_id'=>$admin_id);
            }
            return M('AdminRole')->addAll($rows);
        }
    }


    public function find($id){
            $user = parent::find($id);
            if(!empty($user)){
                //>>1.当前用户的角色id
                $adminRoleModel = M('AdminRole');
                $role_ids = $adminRoleModel->field('role_id')->where(array('admin_id'=>$id))->select();
                $role_ids = array_column($role_ids,'role_id');
                $user['role_ids'] = json_encode($role_ids);
                //>>2.找到当前用户的额外权限
                $adminPermissonModel = M('AdminPermission');
                $permission_ids = $adminPermissonModel->field('permission_id')->where(array('role_id'=>$id))->select();
                $permission_ids = array_column($permission_ids,'permission_id');
                $user['permission_ids'] = json_encode($permission_ids);
            }
            return $user;
    }


}