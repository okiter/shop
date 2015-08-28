<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/21
 * Time: 11:53
 */

namespace Admin\Controller;


use Think\Controller;

/**
 * 设置为abstract主要是被子类继承而不直接创建对象
 * Class BaseController
 * @package Admin\Controller
 */
abstract class BaseController extends Controller
{
    /**
     * 是否是有post中的所有参数传递给model
     * @var bool
     */
    protected $usePostParam = false;
    /**
     * @var 需要子类使用..
     */
    protected $model;

    /**
     * 主要是被子类覆盖,提供自己的标题
     * @var string
     */
    protected $meta_title = '';
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
            $wheres['obj.name'] = array('like', "%{$keyword}%");
        }
        $this->_setWheres($wheres);

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

        //显示出当前列表的标题
        $this->assign('meta_title',$this->meta_title);

        $this->_before_index_view();

        $this->display('index');
    }

    /**
     * 该方法主要是被子类覆盖,用来忘wheres中添加条件
     * @param $wheres
     */
    protected function _setWheres(&$wheres){
    }

    /**
     * 被子类覆盖, 在index页面展示之前执行..
     */
    protected function _before_index_view(){

    }

    public function add()
    {
        if (IS_POST) {
            if ($this->model->create() !== false) { //1.接收数据  2.自动验证  3.自动完成
                if ($this->model->add($this->usePostParam?I('post.'):'') !== false) {
                    $this->success('保存成功!', U('index'));
                    return;
                }
            }
            $this->error('保存失败!' . showModelError($this->model));
        } else {
            $this->assign('meta_title','添加'.$this->meta_title);

            $this->_before_edit_view();
            $this->display('edit');
        }
    }

    /**
     * 钩子方法:
     * 主要被子类覆盖.. 在编辑页面展示之前执行该方法..
     */
    protected function _before_edit_view(){
    }

    public function edit($id)
    {
        if (IS_POST) {
            if ($this->model->create() !== false) { //1.接收数据  2.自动验证  3.自动完成
                if ($this->model->save($this->usePostParam?I('post.'):'') !== false) {
                    $this->success('更新成功!', cookie('__forward__'));
                    return;
                }
            }
            $this->error('更新失败!' . showModelError($this->model));
        } else {
            //根据id只找到当前表中id对应的记录
            $row = $this->model->find($id);
            $this->assign($row);
            $this->assign('meta_title','编辑'.$this->meta_title);
            $this->_before_edit_view();
            $this->display('edit');
        }
    }
    /**
     * 改变一行数据的状态
     * @param $id
     * @param $status  -1 表示删除
     *
     *
     *  success和error的特点:
     *  当浏览器通过jquery的ajax访问时,这两个方法返回的是一个 json对象
     *  Object {info: "操作成功!", status: 1, url: "/index.php/Supplier/index/p/1.html"}
     *   info: 表示提示信息
     *   status: 表示状态,  如果使用success方法, status就是1
     *   status: 表示状态,  如果使用error方法, status就是0
     *   url:   表示状态,   需要跳转的url
     */
    public function changeStatus($id, $status = -1)
    {
      if ($this->model->changeStatus($id, $status) !== false) {
            $this->success('操作成功!', cookie('__forward__'));
        } else {
            $this->error('操作失败!');
        }

    }}