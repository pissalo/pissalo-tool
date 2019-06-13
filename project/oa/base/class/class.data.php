<?php
/**
 * Data数据类 数据库的处理类，一般数据库处理的类都以为这个父类
 * 这个类中有更新、插入、删除文档 还有获取数据等
 */
namespace OA;

defined('IN_DCR') or exit('No permission.');

/**
 * Class cls_data 核心处理数据类
 */

class ClsData
{
    /** @var string $table 操作表名 */
    private $table;
    /** @var  string $last_sql 最后操作的sql */
    private $lastSql;
    /** @var  resource $db 操作的db实例 */
    private $db;

    /**
     * 构造函数
     * @param string $table 要操作的表名
     * @return Data 返回Data实例
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * 设置当前db
     * @param resource $db mysql数据源
     */
    public function setDb($db)
    {
        $this->db = $db;
    }

    /**
     * 获取当前db
     * @return cls_db|resource
     */
    public function getDb()
    {
        if ($this->db) {
            return $this->db;
        } else {
            return new ClsDb();
        }
    }

    /**
     * 设置要操作的表
     * @param string $table 表名
     * @return true
     */
    public function setTable($table)
    {
        global $db_tablepre;
        $table = str_replace('{tablepre}', $db_tablepre, $table);//替换表名
        $table = str_replace('@#@', $db_tablepre, $table);//替换表名
        $this->table = $table;
    }

    /**
     * 获取当前data类的操作表名
     * @return array 表名结果
     */
    public function getTableName()
    {
        return array( 'ack' => 1, 'msg' => $this->table );
    }

    /**
     * 返回最后操作的sql
     * @return string
     */
    public function getLastSql()
    {
        $db = $this->getDb();
        return $db->option($this->lastSql);
    }

    /**
     * 获取数据库最后一条错误信息
     * @return string
     */
    public function getError()
    {
        $db = $this->getDb();
        return $db->getDbError();
    }

    /**
     * 设置最后操作的sql
     * @param string $sql 操作的sql
     * @return true
     */
    private function setLastSql($sql)
    {
        $this->lastSql = $sql;
    }

    /**
     * 执行sql 有返回值
     * @param string $sql 要执行的sql
     * @return array 返回执行结果
     */
    public function execute($sql)
    {
        $this->setLastSql($sql);
        $db = $this->getDb();
        $this->setDb($db);
        $db->execute($sql);
        $dataList = $db->getArray();

        return $dataList;
    }

    /**
     * 执行sql 无返回值
     * @param string $sql 要执行的sql
     * @param int $returnInsertId insert 是不是要返回最后的insert id
     * @return array 返回执行结果
     */
    public function executeNoneQuery($sql, $returnInsertId = 0)
    {
        echo $sql;
        $this->setLastSql($sql);
        $db = $this->getDb();
        $this->set_db($db);
        $val = $db->executeNoneQuery($sql, $returnInsertId);
        return $val;
    }

    /**
     * 执行sql查询
     * @param string $col 需要查询的字段值[例`name`,`gender`,`birthday` 默认为*]
     * @param string $where 查询条件[例`name`='$name']
     * @param string $limit 返回结果范围[例：10或10,10 默认为空]
     * @param string $order 排序方式    [默认按数据库默认方式排序]
     * @param string $group 分组方式    [默认为空]
     * @param string $additionTable 附加表
     * @param string $additionCol 附加表字段
     * @param string $join join语句
     * @param string $having having子句
     * @param string $forceIndex 强制的index
     * @param array $joinEx 附加的join 格式 array('table'=>'order','join_type'=>'left join','where'=>'o_id=od_o_id');
     * @return array 查询结果集数组
     */
    public function select($col = '*', $where = '', $limit = '', $order = '', $group = '', $additionTable = '', $additionCol = '', $join = '', $having = '', $forceIndex = '', $joinEx = array())
    {
        global $adminZtId;
        if (is_array($where)) {
            if (count($where)) {
                $where = implode(' and ', $where);
            } else {
                $where = '';
            }
        }
        //加上帐套ID
        if ($where) {
            $where .= " and {$this->table}.zt_id in({$adminZtId},0)";
        } else {
            $where = "{$this->table}.zt_id in({$adminZtId},0)";
        }

        $col = $col == '' ? '*' : $col;
        $col = $col == '/*slave*/' ? '/*slave*/*' : $col;
        $where = $where == '' ? '' : ' where ' . $where;
        $group = $group == '' ? '' : ' group by ' . $group;
        $limit = $limit == '' ? '' : ' limit ' . $limit;
        $join = $join == '' ? '' : '' . ' ' . $join;
        if ($joinEx) {
            foreach ($joinEx as $join_info) {
                if ($join_info[ 'table' ]) {
                    $join .= " {$join_info['join_type']} {$join_info['table']} on {$join_info['where']}";
                }
            }
        }
        $having = $having == '' ? '' : '' . ' having ' . $having;
        $forceIndex = $forceIndex == '' ? '' : " force index ({$forceIndex}) ";

        $table = $this->table;
        if (!empty($additionTable)) {
            //这里col都要加上前标
            $colArr = explode(',', $col);
            foreach ($colArr as $colKey => $colV) {
                $colArr[ $colKey ] = $this->table . '.' . $colV;
            }
            $col = implode(',', $colArr);

            $colAdditionArr = explode(',', $additionCol);
            foreach ($colAdditionArr as $colAdditionKey => $colAdditionV) {
                $colAdditionArr[ $colAdditionKey ] = $additionTable . '.' . $colAdditionV;
            }
            $additionCol = implode(',', $colAdditionArr);

            $table .= ',' . $additionTable;
            $col .= ',' . $additionCol;
        }
        $order = $order == '' ? '' : ' order by ' . $order;

        $sql = 'select ' . $col . ' from ' . $table . $forceIndex . $join . $where . $group . $having . $order . $limit;
        $this->setLastSql($sql);
        $db = $this->getDb();
        $this->setDb($db);
        $db->execute($sql);
        $dataList = $db->getArray();

        return $dataList;
    }

    /**
     * 执行sql查询 和select不同的 这个解析字符串来查询
     * @param array $canshu 参数 cols 需要查询的字段值[例`name`,`gender`,`birthday` 默认为*] where 查询条件 limit 返回结果范围[例：10或10,10 默认为空] order 排序方式    [默认按数据库默认方式排序] group 分组方式    [默认为空]  addon_table附加表 addon_col 附加字段 例如:array('cols'=>'age', 'where'=>'age>18', limit=>'10', 'order'=>'id desc')
     * @return array 查询结果集数组
     */
    public function selectEx($canshu = array())
    {
        $dataList = $this->select($canshu[ 'col' ], $canshu[ 'where' ], $canshu[ 'limit' ], $canshu[ 'order' ], $canshu[ 'group' ], $canshu[ 'addon_table' ], $canshu[ 'addon_col' ], $canshu[ 'join' ], $canshu[ 'having' ], $canshu[ 'force_index' ], $canshu[ 'join_ex' ]);
        return $dataList;
    }

    /**
     * 返回一条记录
     * @param array $canshu 参数 同select_ex 的$canshu说明
     * @return array 查询结果集数组
     */
    public function selectOne($canshu = array())
    {
        $canshu[ 'limit' ] = 1;
        //p_r($canshu);
        $dataList = $this->selectEx($canshu);

        return $dataList;
    }

    /**
     * 返回一条记录 跟select_one区别是这个函数返回current($data_list) 而select_one返回$data_list
     * @param array $canshu 参数 同select_ex 的$canshu说明
     * @return array 查询结果集数组
     */
    public function selectOneEx($canshu = array())
    {
        $data_list = $this->selectOne($canshu);
        $dataList[ 'msg' ] = current($data_list[ 'msg' ]);

        return $dataList;
    }

    /**
     * 获取表前缀
     * @return array
     */
    public function getTableYz()
    {
        $this->setTable($this->table);
        $tableNameArr = explode('_', $this->table);
        unset($tableNameArr[ 0 ]);
        $qz = '';
        //p_r( $table_name_arr );
        //p_r( $this->table );
        foreach ($tableNameArr as $tableTmp) {
            $qz .= substr($tableTmp, 0, 1);
        }
        return array( 'ack' => 1, 'msg' => $qz );
    }

    /**
     * 执行添加记录操作
     * @param array $info 插入的数据 用数组表示,用$key=>$value来表示列名=>值 如array('title'=>'标题') 表示插入title的值为 标题
     * @param int $returnSqlOnly 只返回sql 不执行
     * @param int $returnInsertId 返回insert id
     * @param int $isIgnore 是不是要加ignore
     * @return int 返回值为文档的ID,失败返回0
     */
    public function insert($info, $returnSqlOnly = 0, $returnInsertId = 1, $isIgnore = 0)
    {
        global $adminZtId;
        if (!$info[ 'zt_id' ]) {
            $info[ 'zt_id' ] = intval($adminZtId);
        }
        //判断是不是合规范数据
        $checkResult = ClsApp::checkData($this->table, $info);
        if (!$checkResult[ 'ack' ]) {
            return array( 'ack' => 0, 'error_id' => 1001, 'msg' => $checkResult[ 'msg' ] );
        }

        $keyList = implode('`,`', array_keys($info));
        $keyList = '`' . $keyList . '`';
        $valueList = implode("','", array_values($info));
        $valueList = "'" . $valueList . "'";
        $ignoreStr = $isIgnore ? 'ignore' : '';
        $sql = 'insert ' . $ignoreStr . ' into ' . $this->table . "($keyList) values($valueList)";

        $val = array();
        if ($returnSqlOnly) {
            $val = array( 'ack' => 1, 'msg' => $sql );
        } else {
            $db = $this->getDb();
            $this->setDb($db);
            $val = $db->executeNoneQuery($sql, $returnInsertId);
            $this->setLastSql($sql);
        }
        return $val;
    }

    /**
     * 执行添加记录操作 修正一个insert的function会造成的BUG,该bug在多次select中不会返回last_insert_id
     * @param array $info 插入的数据 用数组表示,用$key=>$value来表示列名=>值 如array('title'=>'标题') 表示插入title的值为 标题
     * @param int $returnInsertId 是否返回insert id
     * @param int $returnSqlOnly 只返回sql 不执行
     * @param int $isIgnore 是否添加ignore
     * @return array 结果
     */
    public function insertEx($info, $returnInsertId = 0, $returnSqlOnly = 0, $isIgnore = 0)
    {
        return $this->insert($info, $returnSqlOnly, $returnInsertId, $isIgnore);
    }

    /**
     * 批量执行添加记录操作
     * @param array $info 插入的数据 格式为:array( array('a'=>1, 'b'=>2),array('a'=>3, 'b'=>4) )
     * @param int $returnSqlOnly 只返回sql 不执行
     * @param int $isIgnore 是否添加ignore
     * @return array 结果
     */
    public function insertBulk($info, $returnSqlOnly = false, $isIgnore = 0)
    {
        global $adminZtId;
        $keyList = implode('`,`', array_keys(current($info)));
        $keyList = '`' . $keyList . '`';
        $valueArr = array();
        foreach ($info as $data) {
            if (!$data[ 'zt_id' ]) {
                $data[ 'zt_id' ] = $adminZtId;
            }

            //判断是不是合规范数据
            $checkResult = ClsApp::checkData($this->table, $data);
            if (!$checkResult[ 'ack' ]) {
                return array( 'ack' => 0, 'error_id' => 1001, 'msg' => $checkResult[ 'msg' ] );
            }
            $valueStr = "('" . implode("','", $data) . "')";
            array_push($valueArr, $valueStr);
        }
        $ignoreStr = $isIgnore ? 'ignore' : '';
        $valueList = implode(',', $valueArr);
        $sql = 'insert ' . $ignoreStr . ' into ' . $this->table . "($keyList) values {$valueList}";

        $returnVal = '';
        if ($returnSqlOnly) {
            $returnVal = $sql;
        } else {
            $db = $this->getDb();
            $returnVal = $db->executeNoneQuery($sql);
            //ClsApp::log($sql);
            $this->setLastSql($sql);
        }

        return $returnVal;
    }

    /**
     * 执行replace记录操作
     * @param array $info 插入的数据 用数组表示,用$key=>$value来表示列名=>值 如array('title'=>'标题') 表示插入title的值为 标题
     * @param int $returnSqlOnly 只返回sql 不执行
     * @return int 返回值为文档的ID,失败返回0
     */
    public function replace($info, $returnSqlOnly = false)
    {
        if (!$info[ 'zt_id' ]) {
            global $adminZtId;
            $info[ 'zt_id' ] = $adminZtId;
        }

        //判断是不是合规范数据
        $check_result = ClsApp::checkData($this->table, $info);
        if (!$check_result[ 'ack' ]) {
            return array( 'ack' => 0, 'error_id' => 1001, 'msg' => $check_result[ 'msg' ] );
        }

        $keyList = implode('`,`', array_keys($info));
        $keyList = '`' . $keyList . '`';
        $valueList = implode("','", array_values($info));
        $valueList = "'" . $valueList . "'";
        $sql = 'replace into ' . $this->table . "($keyList) values($valueList)";

        $returnVal = '';
        if ($returnSqlOnly) {
            $returnVal = $sql;
        } else {
            $db = $this->getDb();
            $this->setDb($db);
            $returnVal = $db->executeNoneQuery($sql);
            //ClsApp::log($sql);
            $this->setLastSql($sql);
        }

        return $returnVal;
    }

    /**
     * 更新文档
     * @param array $info 更新的数据 用数组表示,用$key=>$value来表示列名=>值 如array('title'=>'标题') 表示插入title的值为 标题
     * @param string $where 更新条件
     * @param int $returnSqlOnly 只返回sql 不执行
     * @param int $backAffectedRows 是否返回受影响行
     * @return boolean 更新成功返回true 失败返回false
     */
    public function update($info = array(), $where = '', $returnSqlOnly = false, $backAffectedRows = 0)
    {
        $updateStr = '';
        if (!strstr($where, 'zt_id')) {
            global $adminZtId;
            if ($where) {
                $where .= " and {$this->table}.zt_id in({$adminZtId},0)";
            } else {
                $where = "{$this->table}.zt_id in({$adminZtId},0)";
            }
        }

        //判断是不是合规范数据
        $checkResult = ClsApp::checkData($this->table, $info);
        if (!$checkResult[ 'ack' ]) {
            return array( 'ack' => 0, 'error_id' => 1001, 'msg' => $checkResult[ 'msg' ] );
        }

        foreach ($info as $key => $value) {
            //如果是+则不要''包围 如:click=click+1
            if (strpos($value, $key . '+') === false && strpos($value, $key . '-') === false) {
                $value = (string)$value;
                if ('null' == $value) {
                    $updateStr .= "`$key`=null,";
                } else {
                    $updateStr .= "`$key`='$value',";
                }
            } else {
                $updateStr .= "`$key`=$value,";
            }
        }
        $updateStr = substr($updateStr, 0, strlen($updateStr) - 1);
        $sql = "update " . $this->table . " set $updateStr where $where";

        if ($returnSqlOnly) {
            $returnVal = $sql;
        } else {
            $db = $this->getDb();
            $this->setDb($db);
            $returnVal = $db->executeNoneQuery($sql, 0, $backAffectedRows);
            //ClsApp::log($sql);
            $this->setLastSql($sql);
        }

        return $returnVal;
    }

    /**
     * 更新一条文档
     * @param array $info 更新的数据 用数组表示,用$key=>$value来表示列名=>值 如array('title'=>'标题') 表示插入title的值为 标题
     * @param string $where 更新条件
     * @param int $returnSqlOnly 只返回sql 不执行
     * @return boolean 更新成功返回true 失败返回false
     */
    public function updateOne($info = array(), $where = '', $returnSqlOnly = false)
    {
        //判断是不是合规范数据
        $check_result = ClsApp::checkData($this->table, $info);
        if (!$check_result[ 'ack' ]) {
            return array( 'ack' => 0, 'error_id' => 1001, 'msg' => $check_result[ 'msg' ] );
        }

        if (!strstr($where, 'zt_id')) {
            global $adminZtId;
            if ($where) {
                $where .= " and {$this->table}.zt_id in({$adminZtId},0)";
            } else {
                $where = "{$this->table}.zt_id in({$adminZtId},0)";
            }
        }
        $sql = $this->update($info, $where, true);
        $sql .= ' limit 1';

        if ($returnSqlOnly) {
            $returnVal = $sql;
        } else {
            $db = $this->getDb();
            $this->setDb($db);
            $returnVal = $db->executeNoneQuery($sql);
            //ClsApp::log($sql);
            $this->setLastSql($sql);
        }

        return $returnVal;
    }

    /**
     * 执行删除操作
     * @param string $where 删除条件
     * @param string $limit 删除条数
     * @return array 成功返回true 失败返回false
     */
    public function deleteEx($where, $limit = '')
    {
        if (!strstr($where, 'zt_id')) {
            global $adminZtId;
            if ($where) {
                $where .= " and {$this->table}.zt_id in({$adminZtId},0)";
            } else {
                $where = "{$this->table}.zt_id in({$adminZtId},0)";
            }
        }
        if (!empty($where)) {
            $where = ' where ' . $where;
        }
        if (!empty($limit)) {
            $limit = ' limit ' . $limit;
        }

        $db = $this->getDb();
        $this->setDb($db);
        $sql = "delete from " . $this->table . $where . $limit;
        $this->setLastSql($sql);
        $rVal = $db->executeNoneQuery($sql);

        return $rVal;
    }

    /**
     * 事务开始
     * @return boolean 成功返回true 失败返回false
     */
    public function transactionBegin()
    {
        $db = $this->getDb();
        $this->setDb($db);
        $sql = 'begin/*zt_id*/';
        $this->setLastSql($sql);
        $rVal = $db->executeNoneQuery($sql);
    }

    /**
     * 事务提交
     * @return boolean 成功返回true 失败返回false
     */
    public function transactionRollback()
    {
        $db = $this->getDb();
        $this->setDb($db);
        $sql = 'rollback/*zt_id*/';
        $this->setLastSql($sql);
        $rVal = $db->executeNoneQuery($sql);
    }

    /**
     * 事务回滚
     * @return boolean 成功返回true 失败返回false
     */
    public function transactionCommit()
    {
        $db = $this->getDb();
        $this->setDb($db);
        $sql = 'commit/*zt_id*/';
        $this->setLastSql($sql);
        $rVal = $db->executeNoneQuery($sql);
    }


    /**
     * 获取update后影响的值
     * @return int 影响的值
     */
    public function getFoundRows()
    {
        $db = $this->getDb();
        $this->setDb($db);
        $sql = 'select found_rows() as found_rows';
        $this->setLastSql($sql);
        $r_val = $db->execute($sql);
        $value = $r_val[ 0 ][ 'found_rows' ];
        return $value;
    }
}
