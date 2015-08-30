<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/30
 * Time: 15:24
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Verify;

class VerifyController extends Controller
{

    public function index(){
        $verify = new Verify();
        $verify->entry();
    }
}