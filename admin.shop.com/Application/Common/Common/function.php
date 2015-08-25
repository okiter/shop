<?php


/**
 * 显示Model的错误信息
 * @param $model
 * @return string
 */
function showModelError($model)
{
    $errors = $model->getError();
    if(empty($errors)){  //没有错误信息下面的代码不需要执行
        return '';
    }
    $errorInfo = '<ul>';
    if (is_array($errors)) {
        foreach ($errors as $error) {
            $errorInfo .= "<li>{$error}</li>";
        }
    } else {
        $errorInfo .= "<li>{$errors}</li>";
    }
    $errorInfo .= '</ul>';
    return $errorInfo;
}

if(!function_exists('array_column')){
    /**
     * 取出二维数组中一列的值
     * @param $rows   二维数组
     * @param $column_name   一列的名字
     * @return array
     */
   function array_column($rows,$column_name){
       $temp = array();
        foreach($rows as $row){
            $temp[] =  $row[$column_name];
        }
       return $temp;
   }
}