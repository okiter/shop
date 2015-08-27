<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:45
 */

namespace Admin\Controller;


use Think\Controller;

class ArticleController extends BaseController
{
    protected $meta_title = '文章';

    /**
     * 钩子方法:
     * 主要被子类覆盖.. 在编辑页面展示之前执行该方法..
     */
    protected function _before_edit_view()
    {
        $articleCategoryModel = D('ArticleCategory');
        $articleCategorys = $articleCategoryModel->getList();
        $this->assign('articleCategorys',$articleCategorys);
    }




    public function add()
    {
        if (IS_POST) {
            if ($this->model->create() !== false) { //1.接收数据  2.自动验证  3.自动完成
                $requestData = I('post.');
                $requestData['content'] = I('post.content','',false);
                if ($this->model->add($requestData) !== false) {
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



    public function edit($id)
    {
        if (IS_POST) {
            if ($this->model->create() !== false) { //1.接收数据  2.自动验证  3.自动完成
                $requestData = I('post.');
                $requestData['content'] = I('post.content','',false);
                if ($this->model->save($requestData) !== false) {
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

}