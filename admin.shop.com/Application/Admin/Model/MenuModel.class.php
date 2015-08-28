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

class MenuModel extends BaseModel
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


    public function add($requestData){

        //>>1.将菜单保存到指定一个菜单下面
        $db = new DbMysqlImpModel();
        $nestedSetsService =  new NestedSetsService($db,'menu','lft','rght','parent_id','id','level');
        $id = $nestedSetsService->insert($this->data['parent_id'],$this->data,'bottom');

        //>>2.将菜单对应的权限保存到menu_permission表中
        $this->handlePermission($id,$requestData['permission_ids']);

        return  $id;
    }


    public function save($requestData){

        //>>1.移动
        $db = new DbMysqlImpModel();
        $nestedSetsService =  new NestedSetsService($db,'menu','lft','rght','parent_id','id','level');
        $result  = $nestedSetsService->moveUnder($this->data['id'],$this->data['parent_id']);
        if($result===false){
            $this->error = '不能够选中自己和子节点作为父节点';
            return false;
        }

        //>>2.更新当前表中的数据
        $result = parent::save();
        if($result===false){
            return false;
        }

        //>>3.处理访问菜单的权限
        $result = $this->handlePermission($requestData['id'],$requestData['permission_ids']);
        if($result===false){
            $this->error = '更新菜单权限的时候出错!';
            return false;
        }

    }

    /**
     * 将menu_id和permission_id保存到menu_permission表中
     * @param $menu_id
     * @param $permission_ids
     */
    private function handlePermission($menu_id,$permission_ids){
        $menuPermissionModel = D('MenuPermission');
        $menuPermissionModel->where(array('menu_id'=>$menu_id))->delete();
        if(!empty($permission_ids)){
                $rows = array();
                foreach($permission_ids as $permission_id){
                    $rows[] = array('menu_id'=>$menu_id,'permission_id'=>$permission_id);
                }
                return $menuPermissionModel->addAll($rows);
            }
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
        $sql = "SELECT node.id FROM `menu` as node,`menu` as parent
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


    public function find($id){
        $menu = parent::find($id);
        if(!empty($menu)){
            $menuPermissionModel = M('MenuPermission');
            $permission_ids = $menuPermissionModel->field('permission_id')->where(array('menu_id'=>$id))->select();
            $permission_ids = array_column($permission_ids,'permission_id');
            $menu['permission_ids'] = json_encode($permission_ids);
        }
        return $menu;
    }
}