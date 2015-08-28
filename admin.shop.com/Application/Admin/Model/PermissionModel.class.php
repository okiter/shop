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

class PermissionModel extends BaseModel
{
    // 自动验证定义   $fields 表中的每个字段信息
    protected $_validate        =   array(
        array('name','require','名称不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('parent_id','require','父分类不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('lft','require','左边界不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('rght','require','右边界不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('level','require','级别不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('status','require','状态不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('sort','require','排序不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
    );



    public function add(){
        $db = new DbMysqlImpModel();
        $nestedSetsService =  new NestedSetsService($db,'permission','lft','rght','parent_id','id','level');
       return  $nestedSetsService->insert($this->data['parent_id'],$this->data,'bottom');
    }


    /**
     * 查询出需要在列表中显示的数据
     * @return mixed
     */
    public function getList($fields='*')
    {
        //准备排序
        $orders = array();
        $orders[] = 'lft';

        //过滤
        $wheres['status'] = array('gt', -1);
        return $this->field($fields)->order($orders)->where($wheres)->select();
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
        $sql = "SELECT node.id FROM `permission` as node,`permission` as parent
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