<extend name="Common:index"/>
<block name="list">
    <div class="list-div" id="listDiv">
        <input  type="button" value=" 删除 " url="{:U('changeStatus')}"   class="button ajax-post">
        <table cellpadding="3" cellspacing="1">
            <tr>
                <th width="10px">全选<input type="checkbox" class="check_all"></th>
                <?php foreach($fields as $field):
                     if($field['Key']=='PRI'){
                        continue;
                     }
                ?>
                <th><?php echo $field['Comment'] ?> </th>
                <?php endForeach;?>
                <th>操作</th>
            </tr>
            <volist name="rows" id="row">
                <tr>
                    <td align="center"><input type="checkbox" name="id[]" class="id" value="{$row.id}"></td>
                    <td class="first-cell">
                        <span style="float:right">{$row.name}</span>
                    </td>
                    <?php foreach($fields as $field){
                        //过滤掉主键和名字
                         if($field['Key']=='PRI' || $field['Field']=='name'){
                             continue;
                         }
                         if($field['Field']=='status'){
                            echo "<td align=\"center\"><a class=\"ajax-get\" href=\"{:U('changeStatus',array('id'=>\$row['id'],'status'=>1-\$row['status']))}\"><img src=\"__IMG__/{\$row.status}.gif\"/></a></td>";
                         }else{
                             echo "<td align=\"center\">{\$row.{$field['Field']}}</td>";
                         }
                        }
                    ?>
                    <td align="center">
                        <a href="{:U('edit',array('id'=>$row['id']))}" title="编辑">编辑</a> |
                        <a  class="ajax-get" href="{:U('changeStatus',array('id'=>$row['id']))}" title="移除">移除</a>
                    </td>
                </tr>
            </volist>
        </table>
        <div id="turn-page" class="page">{$pageHtml}</div>
    </div>
</block>