<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/31
 * Time: 9:20
 */

namespace Home\Model;


use Think\Model;

class MemberModel extends Model
{
    protected $_auto            =   array(
        array('salt','\Org\Util\String::randString',self::MODEL_INSERT,'function'),
        array('add_time',NOW_TIME,self::MODEL_INSERT)
    );  // 自动完成定义


    public function add(){
        $email = $this->data['email'];
        $username = $this->data['username'];
        $salt = $this->data['salt'];
        //>>1.主要是对密码进行加密
        $this->data['password'] = myMd5($this->data['password'],$this->data['salt']);
        $id = parent::add();

        $vcode = myMd5($username,$salt);

        //>>2.如果id不为false表示注册成功!
        if($id!==false){
            //>>2.1发送邮件
            $sendMailerThread = new SendMailThread($id,$email,$vcode);
            $sendMailerThread->start();
//            $sendMailerThread->detach(); phpmailer不支持...
        }

        return $id;
    }

    /**
     * 验证value是否在field字段上存在
     * @param $field 字段
     * @param $value  值
     * @return bool
     */
    public function checkField($field,$value){  //username  zhangsan
        $row = $this->where(array($field=>$value))->find();
        return empty($row);
    }


    public function fire($id,$vcode){
        //>>1.根据$vcode验证是否正确  username,salt
         $row = $this->field('username,salt,status')->find($id);
         if($row['status']==1){
            $this->error = '已经激活过,不需要再次激活!';
            return false;
         }
          $dbVcode = myMd5($row['username'],$row['salt']);
         if($dbVcode==$vcode){
             //>>2.根据id改变当前行的状态为 1
           return parent::save(array('id'=>$id,'status'=>1));
         }else{
             //>>3.激活的信息不正确
             $this->error = '激活信息已经被篡改';
             return false;
         }

    }


    public function login(){

        //>>1.根据用户名查询是否有这个人
        $username = $this->data['username'];
        $password = $this->data['password'];

        //设置查询的列和条件
        $this->field('id,username,password,salt')->where(array('username'=>$username));
        $row = parent::find();  //防止他使用到当前类中的find方法
        if(!empty($row)){
            //>>2.对比密码
            if($row['password']==myMd5($password,$row['salt'])){
                //>>3.密码对比上之后将登录时间和登录IP放到数据库中
                $last_login_time = NOW_TIME;
                $last_login_ip = ip2long(get_client_ip());
                $data = array('last_login_time'=>$last_login_time,'last_login_ip'=>$last_login_ip,'id'=>$row['id']);
                parent::save($data);
                //row中包含了id,username
                return $row;
            }else{
                $this->error = '密码出错!';
                return false;
            }
        }else{
            $this->error = '用户名出错!';
            return false;
        }

    }
}


class SendMailThread extends \Thread{
    private $id;
    private $email;
    private $vcode;
    public function __construct($id,$email,$vcode){
        $this->id = $id;
        $this->email = $email;
        $this->vcode = $vcode;
    }
    public function run()
    {
        //发送邮件的代码
        $address = $this->email;  //发送给谁
        $subject = '欢迎注册源代码商城';  //标题
        $content = "<h1>激活账号</h1>
            <a href=\"http://www.shop.com/index.php/Member/fire/id/{$this->id}/vcode/{$this->vcode}\">激活</a>
            ";   //内容
        $result = sendMail($address,$subject,$content);
        if($result!==true){
            $this->error = $result;
            return false;
        }
    }

}