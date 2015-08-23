/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/20
 * Time: 15:46
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class <?php echo $name ?>Model extends BaseModel
{
    // 自动验证定义   $fields 表中的每个字段信息
    protected $_validate        =   array(
        <?php foreach($fields as $field):
              //过滤掉主键和可以为空的内容
              if($field['Key']=='PRI' || $field['Null']=='YES'){
                    continue;
              }
             echo "array('{$field['Field']}','require','{$field['Comment']}不能够为空!',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),\r\n";
            endForeach;
        ?>
    );
}