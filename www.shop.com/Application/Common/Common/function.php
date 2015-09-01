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


/**
 * 将数组连接起来
 * @param $arr
 * @param string $limit
 */
function arr2str($arr,$limit = ','){
     return implode($limit,$arr);
}

/**
 * 字符串变为数组
 * @param $str
 * @param string $limit
 */
function str2arr($str,$limit = ','){
    return explode($limit,$str);
}


/**
 * 将传入的数据生成一个下拉列表的html.
 * @param $name  下拉列表的名字
 * @param $rows  下拉列表中的数据
 * @param $defaultValue  默认选中的值
 * @param $valueField  需要指定的列表的名字作为  下拉框的值
 * @param $textField  需要指定的列表的名字作为  下拉框的文本内容
 */
function arr2select($name,$rows,$defaultValue='',$valueField='id',$textField='name'){
    $select_html = "<select name='{$name}' class='{$name}'>";
          $select_html.="<option value=''>--请选择--</option>";
           foreach($rows as $row){

               //选中默认的
               $selected = '';
                if($row[$valueField]==$defaultValue){
                    $selected = "selected='selected'";
                }
               //输出option
                $select_html.="<option {$selected}  value='{$row[$valueField]}'>{$row[$textField]}</option>";
           }
    $select_html .= "</select>";
    echo $select_html;
}


function myMd5($str,$salt){
    return md5($str.md5($salt));
}

/**
 * 和用户相关的函数
 */

function login($userinfo=null){
    if(!empty($userinfo)){
        session('USERINFO',$userinfo);
    }else{
        return  session('USERINFO');
    }
}

/**
 * 判断用户是否登录
 * @return bool
 */
function isLogin(){
    return login()!=null;
}

/**
 * 退出登录
 */
function logout(){
//    session('USERINFO',null);
    session('[destroy]');
}

/**
 * 保存用户权限的URL
 * @param null $urls
 * @return mixed
 */
function savePermissionURL($urls=null){
    if(!empty($urls)){
        session('URLS',$urls);
    }else{
        return  session('URLS');
    }
}
/**
 * 保存用户权限的ID
 * @param null $ids
 * @return mixed
 */
function savePermissionId($id=null){
    if(!empty($id)){
        session('PERMISSION_IDS',$id);
    }else{
        return  session('PERMISSION_IDS');
    }
}


/**
 * 发送邮件
 * @param $address 发送的地址
 * @param $subject  发送的标题
 * @param $content  内容
 * @return bool|string   是否发送成功. true表示成功, 字符串表示错误信息
 * @throws \Exception
 * @throws \phpmailerException
 */
function sendMail($address,$subject,$content){
    //>>1.加载邮件类
    vendor('PHPMailer.PHPMailerAutoload');
    //>>2.使用邮件类发送邮件
    $mail = new \PHPMailer;

    $mail_config = C('MAIL_CONFIG');

    //$mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->CharSet = 'utf-8';  //设置发送邮件的编码
    //>>1.连接到服务器上面
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = $mail_config['Host'];  // 指定发送邮件的服务器
    $mail->SMTPAuth = true;          //是否需要认证
    $mail->Username = $mail_config['Username'];                 // SMTP username
    $mail->Password = $mail_config['Password'];                           // SMTP password
    //$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    //$mail->Port = 587;                                    // TCP port to connect to


    //>>2.设置邮件的内容
    //>>2.1 设置发件人
    $mail->From = $mail_config['From'];   //发件人的地址
    $mail->FromName = $mail_config['FromName'];    //发件人的名字

    //>>2.2. 设置收件人
    $mail->addAddress($address);     // 收件人

    //$mail->addReplyTo('info@example.com', 'Information');

//                $mail->addCC('itsource520@126.com');  //抄送
//                $mail->addBCC('itsource520@126.com'); //密送

    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments 添加附件
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->isHTML(true);                                  // 指定邮件是什么格式的.

    $mail->Subject = $subject;
    $mail->Body    = $content;
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  //以非html的形式发送


    //>>3.发送邮件(将邮件内容保存到邮件服务器上)
    if(!$mail->send()) {
        return $mail->ErrorInfo;
    } else {
        return true;
    }
}