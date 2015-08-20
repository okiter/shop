<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:45
 */

namespace Admin\Controller;


use Think\Controller;

class SupplierController extends Controller
{

    public function index()
    {
        $model = D('Supplier');
        $rows = $rows = $model->getList();
        $this->assign('rows', $rows);
        $this->display('index');
    }


    public function add()
    {
        if (IS_POST) {
            $model = D('Supplier');
            if ($model->create() !== false) { //1.接收数据  2.自动验证  3.自动完成
                if ($model->add() !== false) {
                    $this->success('保存成功!', U('index'));
                    return;
                }
            }
            $this->error('保存失败!' . showModelError($model));
        } else {
            $this->display('edit');
        }
    }


    public function edit($id)
    {
        $model = D('Supplier');
        if (IS_POST) {
            if ($model->create() !== false) { //1.接收数据  2.自动验证  3.自动完成
                if ($model->save() !== false) {
                    $this->success('更新成功!', U('index'));
                    return;
                }
            }
            $this->error('更新失败!' . showModelError($model));
        } else {
            $row = $model->find($id);
            $this->assign($row);
            $this->display('edit');
        }
    }

    /**
     * 改变一行数据的状态
     * @param $id
     * @param $status  -1 表示删除
     */
    public function changeStatus($id, $status=-1)
    {
        $model = D('Supplier');
        if ($model->changeStatus($id, $status) !== false) {
            $this->success('操作成功!', U('index'));
        } else {
            $this->error('操作失败!');
        }
    }

}