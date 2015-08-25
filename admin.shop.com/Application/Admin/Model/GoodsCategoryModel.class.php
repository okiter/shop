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


    public function getList($field="*"){
        return $this->field($field)->where("status>-1")->order('lft')->select();
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

    public function save()
    {

        //>>1.执行sql的对象
        $dbMysqlImpModel  = new DbMysqlImpModel();
        //>>2.完成业务运算的对象
        $nestedSetsService = new NestedSetsService($dbMysqlImpModel,'goods_category','lft','rght','parent_id','id','level');
        //>>3.进行移动
          // 第一参数: 移动谁     第二个参数为: 移动到谁下面
        $result = $nestedSetsService->moveUnder($this->data['id'], $this->data['parent_id'],'bottom');
        if($result===false){
            $this->error = '移动失败!不能够移动到自己的子节点下!';
            return false;
        }
        //>>4.更新其他表单字段中的数据
        return parent::save();
    }


    /**
     * 根据id改变一行数据为status的状态值
     * @param $id
     * @param $status
     * @return bool
     */
    public function changeStatus($id, $status)
    {
        //>>1. id找到 自己以及子分类的id
        $sql = "SELECT node.id FROM `goods_category` as node,`goods_category` as parent
where node.lft>=parent.lft and node.rght <= parent.rght and parent.id = $id";
        $rows = $this->query($sql);
        //从二维数组中取出id的值
        $id = array_column($rows,'id');
        //>>2.再作为id参数传入
        $data = array('id' => array('in',$id), 'status' => $status);
        if ($status == -1) {
            $data['name'] = array('exp', 'concat(name,"_del")');
        }
        return parent::save($data);
    }


}