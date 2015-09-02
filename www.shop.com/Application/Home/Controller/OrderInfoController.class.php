<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/2
 * Time: 16:02
 */

namespace Home\Controller;


use Think\Controller;

class OrderInfoController extends Controller
{

    public function index(){
        if(IS_POST){

        }else{
            if(!isLogin()){
                session('return_url',$_SERVER['HTTP_REFERER']);
                $this->success('你没有登录,必须先登录!',U('Member/login'));
                return;
            }
            $this->display('index');
        }
    }

}