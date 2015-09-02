<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/2
 * Time: 14:54
 */

namespace Home\Behavior;


use Think\Behavior;

class CheckLoginBehavior extends Behavior
{
    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @access public
     * @param mixed $params 行为参数
     * @return void
     */
    public function run(&$params)
    {
        if(isLogin()){
            $userinfo = login();
            defined('UID') or define('UID',$userinfo['id']);
        }
    }


}