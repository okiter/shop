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
        header('Content-Type: text/html;charset=utf-8');
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
                    foreach($fields as &$field){
                        //>>a.从注释中提取出元素的名字
                        $comment = $field['Comment'];
                       /*
                        $field['Comment'] =  strpos($comment,'@')===false?$comment:strstr($comment,'@',true);  //如果有@符号才截取
                        //>>b.从注释中提取出表单元素的类型
                        preg_match("/@([a-z]+)\|?/",$comment,$result);
                        if(!empty($result)){
                            $field['field_type'] = $result[1];
                        }
                        //>>c.从注释中提取出表单元素的需要的值,  实际上就是 | 后面的内容
                        //如果说 $comment 是  状态@radio|1=是,0=否 ,将会把 | 后面的内容变成 array(1=>是,0=>否)
                        */

                        //>>a.从注释中提取出元素的名字, 类型 , 值
                        preg_match("/(.+)@([a-z]*)\|?(.*)/",$comment,$result);
                        if(!empty($result)){
                            $field['Comment'] =  $result[1];
                            $field['field_type'] = $result[2];
                            $field['field_type'] = $result[2];
                            if(!empty($result[3])){
                                parse_str($result[3],$result);
                                $field['field_values'] = $result;
                            }
                        }
                    }
                    unset($field); //防止foreach出错
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



            //准备页面模板需要的文件夹
            $view_dir = MODULE_PATH.'View/'.$name.'/';
            if(!is_dir($view_dir)){
                mkdir($view_dir,0755,true);
            }

            //>>3.生成index模板
            ob_start();
            require TEMPLATE_PATH.'index.tpl';
            $index_content =  "<?php\r\n".ob_get_clean();
            //拼装模型的路径
            $index_path = $view_dir.'index.html';
            $result = file_put_contents($index_path,$index_content);

            //>>3.生成edit模板
            ob_start();
            require TEMPLATE_PATH.'edit.tpl';
            $edit_content =  "<?php\r\n".ob_get_clean();
            //拼装模型的路径
            $edit_path = $view_dir.'edit.html';
            $result = file_put_contents($edit_path,$edit_content);



            $this->success('成功生成!');
        }else{
            $this->display('index');
        }
    }
}