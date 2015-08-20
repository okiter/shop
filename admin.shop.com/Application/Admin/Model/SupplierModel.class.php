<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:46
 */

namespace Admin\Model;


use Think\Model;

class SupplierModel extends Model
{
    // 是否批处理验证
    protected $patchValidate    =   true;

    // 自动验证定义
    protected $_validate        =   array(
        array('name','require','名称不能够为空!'),
        array('name','','名称不能够为空!',self::EXISTS_VALIDATE,'unique')
    );
    /**
     * 查询出需要在列表中显示的数据
     * @return mixed
     */
    public function getList()
    {
        return $this->order('sort')->where("status>-1")->select();
    }

    /**
     * 根据id改变一行数据为status的状态值
     * @param $id
     * @param $status
     * @return bool
     */
    public function changeStatus($id, $status){
        return parent::save(array('id'=>$id,'status'=>$status));
    }
}