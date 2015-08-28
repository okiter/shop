<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/24
 * Time: 16:34
 */

namespace Admin\Model;


class DbMysqlImpModel implements  DbMysqlModel
{
    /**
     * DB connect
     *
     * @access public
     *
     * @return resource connection link
     */
    public function connect()
    {
        // TODO: Implement connect() method.
        echo 'connect....<br/>';
    }

    /**
     * Disconnect from DB
     *
     * @access public
     *
     * @return viod
     */
    public function disconnect()
    {
        // TODO: Implement disconnect() method.
        echo 'disconnect....<br/>';
    }

    /**
     * Free result
     *
     * @access public
     * @param resource $result query resourse
     *
     * @return viod
     */
    public function free($result)
    {
        // TODO: Implement free() method.
        echo 'free....<br/>';
    }

    /**
     * Execute simple query
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return resource|bool query result
     */
    public function query($sql, array $args = array())
    {
       $tempSql = $this->buildSQL(func_get_args());
       return M()->execute($tempSql);
    }

    /**
     * Insert query method
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return int|false last insert id
     *
     *
     * array(3) {
            [0] => string(21) "INSERT INTO ?T SET ?%"
            [1] => string(14) "goods_category"
            [2] => array(9) {
                ["name"] => string(12) "鍘ㄦ埧鐢靛櫒"
                ["parent_id"] => int(10)
                ["intro"] => string(12) "鍘ㄦ埧鐢靛櫒"
                ["sort"] => string(2) "20"
                ["status"] => string(1) "1"
                ["id"] => string(0) ""
                ["lft"] => int(22)
                ["rght"] => int(23)
                ["level"] => int(2)
    }
    }
     */
    public function insert($sql, array $args = array())
    {
        //>>1.接收实际参数
        $params = func_get_args();
        //>>2.sql模板
        $sql = $params[0];
        //>>3.表名
        $table_name = $params[1];
        //>>3, 字段对应的值
        $fieldValues = $params[2];
        //>>3.?T变成一个表名
        $sql=str_replace('?T',$table_name,$sql);
        //>>4. 将字段对应的值拼接到sql中
        $sets = '';
        foreach($fieldValues as $field=>$v){
            $sets.="$field='$v',";
        }
        $sets=rtrim($sets,',');
        //INSERT INTO goods_category SET ?%INSERT INTO goods_category SET name='鍘ㄦ埧鐢靛櫒',parent_id='10',intro='鍘ㄦ埧鐢靛櫒',sort='20',status='1',id='',lft='36',rght='37',level='2'
        $sql = str_replace('?%',$sets,$sql);
        $model = M();
        $model->execute($sql);
        return $model->getLastInsID();  //返回最后插入的id
    }

    /**
     * Update query method
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return int|false affected rows
     */
    public function update($sql, array $args = array())
    {
        // TODO: Implement update() method.
        echo 'update....<br/>';
    }

    /**
     * Get all query result rows as associated array
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array associated data array (two level array)
     */
    public function getAll($sql, array $args = array())
    {
        // TODO: Implement getAll() method.
        echo 'getAll....<br/>';
    }

    /**
     * Get all query result rows as associated array with first field as row key
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array associated data array (two level array)
     */
    public function getAssoc($sql, array $args = array())
    {
        // TODO: Implement getAssoc() method.
        echo 'getAssoc....<br/>';
    }

    /**
     * Get only first row from query
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array associated data array
     */
    public function getRow($sql, array $args = array())
    {
        //>>1.得到该方法执行时传递过来的实际参数
        $tempSql  =$this->buildSQL(func_get_args());
        $rows = M()->query($tempSql);

        return $rows[0];
    }

    /**
     * 根据用户传递的实际参数构建sql语句
     * @param $params
     * @return string
     */
    private function buildSQL($params){
        $sql = array_shift($params);
        //>>3.将sql分割
        $sqls = preg_split("/\?[NFT]/",$sql);
        $tempSql = '';
        //>>4.将参数合并进行
        foreach($sqls as $k=>$v){
            $tempSql.=($v.$params[$k]);
        }
        return $tempSql;
    }


    /**
     * Get first column of query result
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array one level data array
     */
    public function getCol($sql, array $args = array())
    {
        // TODO: Implement getCol() method.
        echo 'getCol....<br/>';
    }

    /**
     * Get one first field value from query result
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return string field value
     */
    public function getOne($sql, array $args = array())
    {
        $sql = $this->buildSQL(func_get_args());
        $rows = M()->query($sql);
        //得到第一行第一列中的值
        return array_shift(array_values($rows[0]));
    }

}