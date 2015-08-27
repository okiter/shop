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

class ArticleModel extends BaseModel
{
    // 自动验证定义   $fields 表中的每个字段信息
    protected $_validate        =   array(
        array('name','require','名称不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('article_category_id','require','文章分类不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('status','require','状态不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('sort','require','排序不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('inputtime','require','录入时间不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
    );


    /**
     * 该方法被父类的getPageResult方法调用执行..
     */
    protected function _setModel()
    {
        $this->join("__ARTICLE_CATEGORY__ as ac on obj.article_category_id = ac.id");
        $this->field("obj.*,ac.name as article_category_name");
    }


    /**
     * 将请求中的内容添加到article表和article_content中
     * @param mixed|string $requestData   请求中的所有数据
     */
    public function add($requestData){
        //$this->data 是  create收集到的当前article表中的数据
        $id = parent::add();

        //保存新闻内容
        $articleContent = array('article_id'=>$id,'content'=>$requestData['content']);
        M('ArticleContent')->add($articleContent);
        return $id;
    }


    public function find($id){
        $row = parent::find($id);
        if($row){
            $content = M('ArticleContent')->getFieldByArticle_id($id,'content');
            $row['content'] =$content;
        }
        return $row;
    }



    public function save($requestData){
        $result = parent::save();

        $articleContent = array('article_id'=>$requestData['id'],'content'=>$requestData['content']);
        $articleContentModel = M('ArticleContent');
        //1.先删除
        $articleContentModel->delete($requestData['id']);
        //2.再添加
        $articleContentModel->add($articleContent);
        return $result;
    }
}