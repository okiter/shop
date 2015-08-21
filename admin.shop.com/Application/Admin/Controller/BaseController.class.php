<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/21
 * Time: 11:53
 */

namespace Admin\Controller;


use Think\Controller;

abstract class BaseController extends Controller
{
    /**
     * @var 需要子类使用..
     */
    protected $model;

    /**
     * 定义_initialize方法, 让父类的构造函数调用执行.
     */
    public function _initialize(){
        $this->model = D(CONTROLLER_NAME);
    }


    public function index()
    {

        //>>3.存放查询条件
        $wheres = array();
        //>>2.接收查询参数
        $keyword = I('get.keyword', '');
        if (!empty($keyword)) {
            $wheres['name'] = array('like', "%{$keyword}%");
        }

        //>>1.需要$model提供分页中需要使用的数据
        /**
         * 分页数据:
         * array(
         * rows=>分页列表中的数据
         *  pageHtml=>'分页工具条'
         * )
         */
        $pageResult = $this->model->getPageResult($wheres);
        $this->assign($pageResult);

        //保存当前请求的url地址到cookie中,为了做其他操作再通过该url回去...
        cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->display('index');
    }

    public function add()
    {
        if (IS_POST) {
            if ($this->model->create() !== false) { //1.接收数据  2.自动验证  3.自动完成
                if ($this->model->add() !== false) {
                    $this->success('保存成功!', U('index'));
                    return;
                }
            }
            $this->error('保存失败!' . showModelError($this->model));
        } else {
            $this->display('edit');
        }
    }

    public function edit($id)
    {
        if (IS_POST) {
            if ($this->model->create() !== false) { //1.接收数据  2.自动验证  3.自动完成
                if ($this->model->save() !== false) {
                    $this->success('更新成功!', cookie('__forward__'));
                    return;
                }
            }
            $this->error('更新失败!' . showModelError($this->model));
        } else {
            $row = $this->model->find($id);
            $this->assign($row);
            $this->display('edit');
        }
    }/**
     * 改变一行数据的状态
     * @param $id
     * @param $status  -1 表示删除
 */
    public function changeStatus($id, $status = -1)
    {
        if ($this->model->changeStatus($id, $status) !== false) {
            $this->success('操作成功!', cookie('__forward__'));
        } else {
            $this->error('操作失败!');
        }
    }}