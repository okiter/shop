<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:45
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Verify;

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



    public function initPassword($id){

        $result = $this->model->initPassword($id);
        if($result===false){
            $this->error('初始化密码失败!'.showModelError($this->model));
        }else{
            $this->success('初始化密码成功!',cookie('__forward__'));
        }
    }



    public function login(){
        if(IS_POST){
          /*  $captcha = I('post.captcha');
            $verify = new Verify();
            if(!$verify->check($captcha)){
                $this->error('验证码错误!',U('login'));  //加上第二个参数是能够保证 登录页面数据,验证码改变!
            }*/


            //>>1.进行用户名和密码的校验
            if($this->model->create('',3)!==false){
                if(($userinfo = $this->model->login())!==false){
                        //>>1.将用户放到session中
                         login($userinfo);
                        //>>2.需要根据用户找到用户拥有的权限对应的url地址
                        $permissions = $this->model->getPermissionByAdmin_id($userinfo['id']);
                        //>>3.将权限的url保存到session中
                        savePermissionURL(array_column($permissions,'url'));
                        //>>4.将权限的id保存到session中
                        savePermissionId(array_column($permissions,'id'));



                        //如果用户选中了保存登录信息, 将用户id和密码加密后保存到cookie中
                        cookie('uid',$userinfo['id'],60*60*24*7);
                        cookie('pk',myMd5($userinfo['password'],$userinfo['salt']),60*60*24*7);


                    $this->success('登录成功',U('Index/index'));
                    return;
                }
            };
            $this->error('验证失败!'.showModelError($this->model));
        }else{

            $this->display('login');
        }
    }

    public function logout(){
        logout();
        $this->success('注销成功!',U('login'));
    }


}