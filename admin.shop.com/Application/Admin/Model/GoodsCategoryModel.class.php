<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:46
 */

namespace Admin\Model;


use Admin\Service\NestedSetsService;
use Think\Model;
use Think\Page;

class GoodsCategoryModel extends BaseModel
{
    // 自动验证定义   $fields 表中的每个字段信息
    protected $_validate = array(
        array('name', 'require', '名称不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('parent_id', 'require', '父分类不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('lft', 'require', '左边界不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('rght', 'require', '右边界不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('level', 'require', '级别不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('sort', 'require', '排序不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('status', 'require', '状态不能够为空!', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
    );


    public function getList(){
        return $this->where("status>-1")->order('lft')->select();
    }

    /**
     * 重写基础模型中的add方法
     */
    public function add(){
        //是create收集到的数据
        //>>将该数据进行计算, 计算后生成lft,rght,level, 然后再添加到数据库中..
        //>>1.执行sql的对象
        $dbMysqlImpModel  = new DbMysqlImpModel();
        //>>2.完成业务运算的对象
        $nestedSetsService = new NestedSetsService($dbMysqlImpModel,'goods_category','lft','rght','parent_id','id','level');
        //>>3.生成sql,并且让执行sql的对象执行sql语句
        return $nestedSetsService->insert($this->data['parent_id'],$this->data,'bottom');
    }


}