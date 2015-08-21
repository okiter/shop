<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:46
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class SupplierModel extends Model
{
    // 是否批处理验证
    protected $patchValidate    =   true;

    // 自动验证定义
    protected $_validate        =   array(
        array('name','require','名称不能够为空!'),
        array('name','','名称已经存在,请更改!',self::EXISTS_VALIDATE,'unique')
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
        $data  = array('id'=>$id,'status'=>$status);
        if($status==-1){
            $data['name'] = array('exp','concat(name,"_del")');
        }
        return parent::save($data);
    }

    /**
     * 返回分页所需要的数据:
     *  * 分页数据:
     * array(
        rows=>分页列表中的数据
     *  pageHtml=>'分页工具条'
     * )
     *
     * @param $wheres array  查询条件
     */
    public function getPageResult($wheres=array()){

        //过滤没有被删除的数据
        $wheres['status'] = array('gt',-1);

        //>>1.提供$pageHtml的值
        $count = $this->where($wheres)->count();
        $pageSize = 2;
        $page = new Page($count,$pageSize);
        //当总条数大于每页多少条时再显示总条数
        if($page->totalRows>$page->listRows){
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $pageHtml = $page->show();
        //>>2.提供$rows的值,当前页的数据
        $rows = $this->limit($page->firstRow,$page->listRows)->where($wheres)->select();
        return array('rows'=>$rows,'pageHtml'=>$pageHtml);
    }
}