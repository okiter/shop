<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/31
 * Time: 9:14
 */

namespace Home\Controller;


use Think\Controller;

class MemberController extends Controller
{
    public function register(){
        if(IS_POST){
            $memberModel  = D('Member');
            if($memberModel->create()!==false){
                if($memberModel->add()!==false){
                    $this->success('注册成功!',U('registerSuccess'));
                    return;
                }
            }
            $this->error('注册失败!'.showModelError($memberModel));
        }else{
            $this->display('register');
        }
    }


    public function check(){
        $params = $_GET;   //不能够使用I()方法
        $value = current($params);  //得到请求中的值
        $key = key($params);   //得到请求中的参数的名字  该名字将作为验证的字段

        $memberModel = D('Member');
        $result = $memberModel->checkField($key,$value);
        //因为result需要交给前台的json处理
        echo json_encode($result);  //true或者false
    }

    /**
     * 激活用户
     * @param $id
     * @param $vcode
     */
    public function fire($id,$vcode){
        $memberModel = D('Member');
        $result = $memberModel->fire($id,$vcode);
        if($result===false){
            $this->error('激活失败!'.showModelError($memberModel),U('login'));
        }else{
            $this->success('激活成功!',U('login'));
        }
    }


    public function login(){
        if(IS_POST){
            $memberModel = D('Member');
            if($memberModel->create()!==false){
                 if(($userinfo = $memberModel->login())!==false){
                        login($userinfo);


                        //将cookie中的购物车数据保存到数据库表shopping_car中
                        $shoppingCarModel = D('ShoppingCar');
                        $shoppingCarModel->cookie2db();


                        //登录之后原路返回
                        $return_url = session('return_url');
                        if(empty($return_url)){
                            $return_url = U('Index/index');
                        }else{
                            session('return_url',null);
                        }
                        $this->success('登录成功!',$return_url);
                     return ;
                 }
            }
            $this->error('登录失败!'.showModelError($memberModel));
        }else{
            $this->display('login');
        }
    }

    public function logout(){
         logout();
        $this->success('注销成功!',U('Index/index'));
    }

    public function getUserInfo(){
        if(isLogin()){
            $user = login();
            $this->ajaxReturn($user);
        }
    }
}