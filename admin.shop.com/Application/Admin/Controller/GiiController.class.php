<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/23
 * Time: 9:20
 */

namespace Admin\Controller;


use Think\Controller;

class GiiController extends Controller
{

    public function index(){
        if(IS_POST){
            //>>4.接收表单参数(表名)
            $table_name = I('post.table_name');
            if(empty($table_name)){
                $this->error('必须填写表的名称');
            }
            //>>3.准备模板文件夹路径
            defined('TEMPLATE_PATH') or define('TEMPLATE_PATH',ROOT_PATH.'Template/');

            //>>2.为模板准备数据
                //>>2.1 需要将表名变成thinkphp中定义的规范名字  brand==>Brand  goods_type==>GoodsType
                    $name = parse_name($table_name,1);

                //>>2.2 需要通过表的注释作为meta_title的值
                    $sql = "show table status where name = '{$table_name}'";
                    $rows = M()->query($sql);
                    $meta_title = $rows[0]['Comment'];

                //>>2.2.得到当前表中的所有字段信息
                    $sql = "show full columns from {$table_name}";
                    $fields = M()->query($sql);

            //>>1.生成控制器
            ob_start();
            require TEMPLATE_PATH.'Controller.tpl';
            $controller_content = "<?php\r\n".ob_get_clean();
            //拼装控制器的路径
            $controller_dir = MODULE_PATH.'Controller/';
            $controller_path = $controller_dir.$name.'Controller.class.php';
            file_put_contents($controller_path,$controller_content);


            //>>2.生成模型(Model)
            ob_start();
            require TEMPLATE_PATH.'Model.tpl';
            $model_content =  "<?php\r\n".ob_get_clean();
            //拼装模型的路径
            $model_dir = MODULE_PATH.'Model/';
            $model_path = $model_dir.$name.'Model.class.php';
            file_put_contents($model_path,$model_content);


            $this->success('成功生成!');
        }else{
            $this->display('index');
        }
    }
}