<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/30
 * Time: 11:43
 */

namespace Admin\Behavior;


use Think\Behavior;

class CheckPermissionBehavior extends Behavior
{
    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @access public
     * @param mixed $params 行为参数
     * @return void
     */
    public function run(&$params)
    {
        /**
         * 不需要登录验证的直接退出
         */
        $requestURL = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        if(in_array($requestURL,C('NO_LOGIN_CHECK_URLS'))){
            return;
        }

        //>>1.判断用户是否登录
        if(!isLogin()){
              //如果没有登录的情况下,需要自动登录
               $adminModel = D('Admin');
               $result = $adminModel->autoLogin();
               if($result===false){
                   //没有自动登录成功,再去登录
                   redirect('/index.php?s=Admin/login');
               }
        }

        //>>2.如果用户登录了,该用户是超级管理员, 不需要进行权限的验证..
        $userinfo = login();
        if($userinfo['username']==C('SUPER_MANAGER_NAME')){
            return;
        }

        //>>2.判断用户现在访问的url地址是否为当前用户能够访问的url地址

         $urls = savePermissionURL();

        if(!in_array($requestURL,$urls)){
                echo 'forbidden... please ....';
                exit;
         }
    }


}