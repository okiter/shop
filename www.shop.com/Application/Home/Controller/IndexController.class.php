<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function _initialize(){
        //>>1.将所有的分类从数据库从查询出来
        $goodsCategorys = S('goodsCategorys');
        if(empty($goodsCategorys)){
            $goodsCategoryModel = D('GoodsCategory');
            $goodsCategorys = $goodsCategoryModel->getList();
            S('goodsCategorys',$goodsCategorys);
        }
        $this->assign('goodsCategorys',$goodsCategorys);

        //>>2.查询出所有的帮助类的分类
        $articleCategorys = S('articleGategorys');
        if(empty($articleCategorys)){
            $articleCategoryModel = D('ArticleCategory');
            $articleCategorys = $articleCategoryModel->getList('id,name',array('is_help'=>1));
            S('articleGategorys',$articleCategorys);
        }
        $this->assign('articleGategorys',$articleCategorys);

        //>>3.查询出所有的帮助类的文章
        $helpArticles = S('helpArticles');
        if(empty($helpArticles)){
            $articleModel = D('Article');
            $helpArticles = $articleModel->getHelpArticles();
            S('helpArticles',$helpArticles);
        }
        $this->assign('helpArticles',$helpArticles);


    }
    public function index()
    {

        //>>1.查询出不同状态下的手机
            $goodsModel  =  D('Goods');
            //>>1.1疯狂抢购
            $is_8s = $goodsModel->getGoodsByStatus(8,5);
            $this->assign('is_8s',$is_8s);
            //>>1.2热销
            $is_4s = $goodsModel->getGoodsByStatus(4,5);
            $this->assign('is_4s',$is_4s);
            //>>1.3推荐商品
            $is_1s = $goodsModel->getGoodsByStatus(1,5);
            $this->assign('is_1s',$is_1s);
            //>>1.4新品上架
            $is_2s = $goodsModel->getGoodsByStatus(2,5);
            $this->assign('is_2s',$is_2s);
            //>>1.5猜你喜欢
            $is_16s = $goodsModel->getGoodsByStatus(16,5);
            $this->assign('is_16s',$is_16s);




        $this->assign('is_show',"true");
        $this->assign('meta_title','源代码商城首页');
        $this->display('index');
    }
    public function lst(){

        $this->assign('meta_title','源代码商城--商品列表');
        $this->display('lst');
    }

    public function goods($id){
         //>>1.根据商品id查询出商品的详细
        $goodsModel = D('Goods');
        $goods= $goodsModel->find($id);
        $this->assign($goods);

        $this->assign('meta_title','源代码商城-'.$goods['name']);
        $this->display('goods');
    }
}