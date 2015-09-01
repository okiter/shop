<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/1
 * Time: 14:37
 */

namespace Home\Model;


use Think\Model;

class ArticleModel extends Model
{

    /**
     * 查询出所有的帮助类的文章
     * @return mixed
     */
    public function getHelpArticles(){
        $sql = "SELECT obj.id,obj.name,obj.article_category_id FROM `article`  as obj join article_category as ac on obj.article_category_id = ac.id
where ac.is_help = 1";
        return $this->query($sql);
    }
}