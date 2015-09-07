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

class AttributeModel extends BaseModel
{
    // 自动验证定义   $fields 表中的每个字段信息
    protected $_validate        =   array(
        array('name','require','名称不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('goods_type_id','require','商品类型不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('attribute_type','require','类型不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('input_type','require','录入方式不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('status','require','状态不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('sort','require','排序不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
    );

    /**
     * 对$rows 进一步处理
     * @param $rows
     */
    public function _handleRows(&$rows){
         foreach($rows as &$row){
             switch($row['attribute_type']){
                 case 1:
                     $row['attribute_type'] = '单值'; break;
                 case 2:
                     $row['attribute_type'] = '多值'; break;
             }
             switch($row['input_type']){
                 case 1:
                     $row['input_type'] = '手工录入'; break;
                 case 2:
                     $row['input_type'] = '从下面列表中选择'; break;
                 case 3:
                     $row['input_type'] = '多行文本'; break;
             }
         }
        unset($row);
    }


    /**
     * 根据类型查询出属性.
     * @param $goods_type_id
     * @return mixed
     */
    public function getByGoodsTypeId($goods_type_id){
        $rows =  $this->field('id,name,attribute_type,input_type,option_value')->where(array('goods_type_id'=>$goods_type_id,'status'=>1))->select();
        foreach($rows as &$row){
            //将可选值  变成  一个数组
            if(!empty($row['option_value'])){
                $row['option_value']   = str2arr($row['option_value'],"\r\n");
            }
        }
        unset($row);

        return $rows;
    }


}