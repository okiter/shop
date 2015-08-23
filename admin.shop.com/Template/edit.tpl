<extend name="Common:edit"/>
<block name="form">
    <form method="post" action="{:U()}">
        <table cellspacing="1" cellpadding="3" width="100%">
            <?php foreach($fields as $field):
                  if($field['Key']=='PRI'){
                        continue;
                  }
             ?>
            <tr>
                <td class="label"><?php echo  $field['Comment'] ?></td>
                <td>
                   <?php
                      if(!empty($field['field_type'])){
                          switch($field['field_type']){
                            case 'textarea':
                                echo "<textarea  name=\"{$field['Field']}\" cols=\"60\" rows=\"4\">{\${$field['Field']}}</textarea>";
                                break;
                            case 'file':
                                 echo  "<input type=\"file\" name=\"{$field['Field']}\" maxlength=\"60\"/>";
                                break;
                            case 'radio':
                                   //如果是status字段, 需要特殊处理
                                    $status_class ='';
                                    if($field['Field']=='status'){
                                             $status_class="class='status'";
                                    }
                                    foreach($field['field_values'] as $k=>$v){
                                         echo  "<input type=\"radio\"  {$status_class}   name=\"{$field['Field']}\" maxlength=\"60\" value=\"{$k}\"/>{$v}";
                                    }
                                break;
                          }
                      }else{
                        //如果是sort字段, 需要特殊处理
                        if($field['Field']=='sort'){
                          echo  "<input type=\"text\" name=\"{$field['Field']}\" maxlength=\"60\" value=\"{\${$field['Field']}|default=20}\" />";
                        }else{
                            echo  "<input type=\"text\" name=\"{$field['Field']}\" maxlength=\"60\" value=\"{\${$field['Field']}}\" />";
                        }
                      }
                   ?>
                    <span class="require-field">*</span>
                </td>
            </tr>
            <?php endForeach?>
            <tr>
                <td colspan="2" align="center"><br />
                    <input type="hidden" name="id" value="{$id}" />
                    <input type="submit" class="button ajax-post" value=" 确定 " />
                    <input type="reset" class="button" value=" 重置 " />
                </td>
            </tr>
        </table>
    </form>
</block>