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
        array('username','','用户名不能够重复!',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
        array('password','require','密码不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('repassword','password','确认密码必须和密码一致!',self::EXISTS_VALIDATE,'confirm',self::MODEL_BOTH),
        array('email','require','Email不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('email','','Email不能够重复!',self::EXISTS_VALIDATE,'unique',self::MODEL_BOTH)
    );


    protected $_auto = array(
        array('add_time',NOW_TIME),
        array('salt','\Org\Util\String::randString',self::MODEL_INSERT,'function')
    );

    /**
     * 将请求中的数据分别保存到admin,admin_role,admin_permission表中
     * @param mixed|string $requestData
     */
    public function add($requestData){
        $this->startTrans();

        $this->data['password'] = myMd5($this->data['password'],$this->data['salt']);
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


    public function initPassword($id){
        $password = '111111';
        //>>1.得到当前用户的盐
        $salt = $this->getFieldById($id,'salt');  //select salt from 表名 where id = $id;
        //>>2.生成加密后的密码
        $password  = myMd5($password,$salt);
        //>>3.将加密后的密码更新到数据库中
        return parent::save(array('password'=>$password,'id'=>$id));
    }


    public function login(){
       //>>1.根据用户名查询是否有这个人
            $username = $this->data['username'];
            $password = $this->data['password'];

            //设置查询的列和条件
            $this->field('id,username,password,salt')->where(array('username'=>$username));
            $row = parent::find();  //防止他使用到当前类中的find方法
            if(!empty($row)){
                //>>2.对比密码
                if($row['password']==myMd5($password,$row['salt'])){
                    //>>3.密码对比上之后将登录时间和登录IP放到数据库中
                    $last_login_time = NOW_TIME;
                    $last_login_ip = ip2long(get_client_ip());
                    $data = array('last_login_time'=>$last_login_time,'last_login_ip'=>$last_login_ip,'id'=>$row['id']);
                    parent::save($data);

                    //row中包含了id,username
                    return $row;
                }else{
                    $this->error = '密码出错!';
                    return false;
                }
            }else{
                $this->error = '用户名出错!';
                return false;
            }

    }

    /**
     * 根据用户得到用户的权限对应的url地址和id
     * @param $admin_id
     */
    public function getPermissionByAdmin_id($admin_id){
        $sql = "
        select distinct p.url,p.id from permission as p  where p.id in(select  permission_id from admin_permission where admin_id = {$admin_id})
		or p.id in
(select rp.permission_id from admin_role as ar join role_permission as rp on ar.role_id =rp.role_id where ar.admin_id = {$admin_id})";

        $rows = $this->query($sql);
        return $rows;
    }


    /**
     * 需要自动登录
     */
    public function autoLogin(){
         //>>1.先取出cookie中的
            $pk = cookie('pk');
            $uid = cookie('uid');

         //>>2. 根据uid查找一行数据
            $this->field('id,password,salt,username');
            $this->where("status=1");
            $userinfo = parent::find($uid);
           if($userinfo){
               if($pk==myMd5($userinfo['password'],$userinfo['salt'])){
                   //自动登录成功之后也需要将用户保存到 session, 权限信息也要保存到session中

                   login($userinfo);
                   //>>2.需要根据用户找到用户拥有的权限对应的url地址
                   $permissions = $this->getPermissionByAdmin_id($userinfo['id']);
                   //>>3.将权限的url保存到session中
                   savePermissionURL(array_column($permissions,'url'));
                   //>>4.将权限的id保存到session中
                   savePermissionId(array_column($permissions,'id'));
                   return;
               }
           }
             return false;
    }

}