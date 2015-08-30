<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/24
 * Time: 9:11
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Upload;

/**
 * 专门用来处理上传的类
 * Class UploadController
 * @package Admin\Controller
 */
class UploadController extends Controller
{

    public function index()
    {

          $dir = I('post.dir');
            //>>1.接收上传文件
          $config =  C('UPLOAD');


         //驱动的配置
         $driverConfig = C('DRIVERCONFIG');
         $driverConfig['bucket'] = 'itsource-'.$dir;//空间名称

        //>>2.将图片上传到又拍云上
            $uploader = new Upload($config,'Upyun',$driverConfig);
            $info =  $uploader->uploadOne($_FILES['Filedata']);
            if($info!==false){
                echo $info['savepath'].$info['savename'];  //info中保存了在又拍云上的文件夹地址
            }else{
                echo $uploader->getError();
            }

        exit;
    }

}