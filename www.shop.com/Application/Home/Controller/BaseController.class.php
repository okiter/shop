<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/5
 * Time: 10:06
 */

namespace Home\Controller;


use Think\Controller;

class BaseController extends Controller
{
    public function _initialize(){
        //>>1.判断用户是否登录
        if(!isLogin()){
            session('return_url',$_SERVER['REQUEST_URI']);
            $this->error('没有登录,请登录',U('Member/login'));
        }
    }


}