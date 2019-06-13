<?php
/**
 * 数据库处理
 * ===========================================================
 * 版权所有 (C) 2006-2020 我不是稻草人，并保留所有权利。
 * 网站地址: http://www.dcrcms.com
 * ----------------------------------------------------------
 * 这是免费开源的软件；您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * 不允许对程序修改后再进行发布。
 * ==========================================================
 * @author:     我不是稻草人 <junqing124@126.com>
 * @version:    v1.0.3
 * @package class
 * @since 1.0.8
 */
namespace OA;

defined('IN_DCR') or exit('No permission.');

/**
 * Class ClsDB
 * @package OA
 */
class ClsDB
{
    /** @var  $conn mysqli_connect结果 */
    private $conn;
    /** @var  $slaveId 从库id */
    private $slaveId;
    /** @var  数据库错误 */
    private $strError;
    /** @var $result 结果 */
    private $result;
    /** @var  $isConnectFailQuit 连接失败是不是退出 */
    private $isConnectFailQuit;
    /** @var  $isCheckSlaveConnect 是不是检测从库连接 */
    private $isCheckSlaveConnect;

    /**
     * ClsDB constructor.
     */
    public function __construct()
    {
    }


    /**
     * 返回数据连接的resource
     * @since 1.0.9
     * @return resource
     */
    public function getConn()
    {
        return $this->conn;
    }

    /**
     * 设置数据库错误信息 这个方法不用外面调用 程序自己调用 version>=1.0.6
     * @param string $slaveId 错误的信息
     * @return true
     */
    public function setSlaveId($slaveId)
    {
        $this->slaveId = $slaveId;
    }


    /**
     * 设置数据库错误信息 这个方法不用外面调用 程序自己调用 version>=1.0.6
     * @param string $error 错误信息
     * @return true
     */
    private function setDbError($error)
    {
        $this->strError = $error;
    }

    /**
     * @param $isConnectFailQuit 是不是连接失败退出
     */
    public function setConnectFailQuit($isConnectFailQuit)
    {
        $this->isConnectFailQuit = $isConnectFailQuit;
    }

    /**
     * @param $isCheckSlaveConnect 是不是检测从库连接
     */
    public function setCheckSlaveConnect($isCheckSlaveConnect)
    {
        $this->isCheckSlaveConnect = $isCheckSlaveConnect;
    }

    /**
     * 获取数据库错误信息 version>=1.0.6
     * @return string 错误信息
     */
    public function getDbError()
    {

        return $this->strError;
    }

    /**
     * 全局处理sql语句,DB类的每个sql语句执行前要通过这个来处理下
     * @param string $sql 要处理的sql语句
     * @return string 返回处理后的sql语句
     */
    public function option($sql)
    {
        global $db_tablepre;
        $sql = str_replace('{tablepre}', $db_tablepre, $sql);//替换表名
        $sql = str_replace('@#@', $db_tablepre, $sql);//替换表名
        $sqlInfo = $this->safeSql($sql);//安全处理sql
        $sql = $sqlInfo[ 'msg' ];

        $isError = 0;
        $error = '';
        //判断有没有帐套ID
        if (!strstr($sql, 'zt_id')) {
            $isError = 1;
            $error = '没有帐套id';
        }

        global $sqlRecord, $sqlArr;
        if ($sqlRecord) {
            array_push($sqlArr, $sql);
        }
        $result = array();
        if ($isError) {
            $result[ 'ack' ] = 0;
            $result[ 'msg' ] = $error;
            $result[ 'error_id' ] = 1000;
            $result[ 'sql' ] = $sql;
        } else {
            $result[ 'ack' ] = 1;
            $result[ 'msg' ] = $sql;
        }

        return $result;
    }

    /**
     * 执行$sql
     * <code>
     * <?php
     * cls_db->execute("select * from {tablepre}news");
     * ?>
     * </code>
     * @param string $sql 要执行的sql语句
     * @param int $resultType 返回记录集的类型 默认为MYSQL_ASSOC
     * @return array 返回执行结果的数组
     */
    public function execute($sql, $resultType = MYSQLI_ASSOC)
    {
        //先得出sql类型
        $dataType = 'slave';
        if ($sql && strpos($sql, '/*master*/')) {
            $dataType = 'master';
        }
        if (!$this->conn) {
            $dbInstance = ClsDbInstance::getInstance($dataType);
            $this->conn = $dbInstance->getConn();
        }

        $this->ping($dataType);
        $sqlInfo = $this->option($sql);
        if (!$sqlInfo[ 'ack' ]) {
            return $sqlInfo;
        }
        $sql = $sqlInfo[ 'msg' ];
        if (empty($sqlInfo[ 'ack' ])) {
            return array( 'ack' => 0, 'errorId' => 1001, 'msg' => '没有sql' );
        }
        unset($this->result);

        $isMysqlError = 0;
        if ($arrTmp = mysqli_query($this->conn, $sql)) {
        } else {
            $isMysqlError = 1;
            $this->setDbError(mysqli_error($this->conn));
            //ClsDbInstance::show_error( mysqli_error( $this->conn ), $sql, mysqli_errno( $this->conn ) );
        }
        $arr = array();
        if ($arrTmp) {
            while ($row = mysqli_fetch_array($arrTmp, $resultType)) {
                $arr[] = $row;
            }
        }
        $this->result = $arr;
        unset($arr_t);
        unset($arr);
        $result = array();
        if ($isMysqlError) {
            $result[ 'ack' ] = 0;
            $result[ 'error_id' ] = 1000;
            $result[ 'msg' ] = $this->getDbError();
            $result[ 'sql' ] = $sql;
        } else {
            $result[ 'ack' ] = 1;
            $result[ 'msg' ] = $this->result;
        }

        return $result;
    }

    /**
     * 函数execute_none_query,执行一个不要返回结果的$sql 如update insert
     * @param string $sql 要执行的sql语句
     * @param int $isReturnInsertId 是不是要返回insert id
     * @param int $backAffectedRows 是不是要返回affected rows
     * @return boolean 成功返回true 失败返回false;
     */
    public function executeNoneQuery($sql, $isReturnInsertId = 0, $backAffectedRows = 0)
    {
        $dataType = 'master';
        if (!$this->conn) {
            $db_instance = ClsDbInstance::getInstance($dataType);
            $this->conn = $db_instance->getConn();
        }
        $this->ping($dataType);
        $sqlInfo = $this->option($sql);
        //exit;
        if (!$sqlInfo[ 'ack' ]) {
            return $sqlInfo;
        }
        $sql = $sqlInfo[ 'msg' ];

        if (empty($sql)) {
            return array( 'ack' => 0, 'error_id' => 1001, 'msg' => '没有sql' );
        }
        $isMysqlError = 0;
        if (mysqli_query($this->conn, $sql)) {
        } else {
            $isMysqlError = 1;
            $this->setDbError(mysqli_error($this->conn));
            $error = mysqli_error($this->conn);
            //ClsDbInstance::show_error( mysqli_error( $this->conn ), $sql, mysqli_errno( $this->conn ) );
        }

        $result = array();
        if ($isMysqlError) {
            $result[ 'ack' ] = 0;
            $result[ 'errorId' ] = 1000;
            $result[ 'msg' ] = $error;
            $result[ 'sql' ] = $sql;
        } else {
            $result[ 'ack' ] = 1;
            if ($isReturnInsertId) {
                $result[ 'insert_id' ] = @mysqli_insert_id($this->conn);
            }
            if ($backAffectedRows) {
                $result[ 'affected_rows' ] = @mysqli_affected_rows($this->conn);
            }
        }

        return $result;
    }

    /**
     * 用来返回记录集的数组形式
     * @return array 成功数组 失败返回false;
     */
    public function getArray()
    {
        return array( 'ack' => 1, 'msg' => $this->result );
    }

    /**
     * ping数据库，如果已经断开则自动重连
     * @param $dataType 数据类型 从库还是主库
     * @return bool
     */
    public function ping($dataType)
    {
        if (!mysqli_ping($this->conn)) {
            $this->close_db();
            $db_instance = ClsDbInstance::getInstance($dataType);
            $this->conn = $db_instance->get_conn();
        }
        return true;
    }

    /**
     * 关闭当前数据库连接
     * @return boolean 返回true
     */
    public function closeDb()
    {
        //mysqli_close( $this->conn );
        unset($this->conn);
    }

    /**
     * 关闭当前数据库连接
     * @return boolean 返回true
     */
    public function __destruct()
    {
        $this->closeDb();
    }

    /**
     * 语句过滤程序
     * @param string $dbString 要处理的sql语句
     * @return string 返回一个sql语句安全处理后的sql
     */
    public function safeSql($dbString)
    {
        //var_dump($db_string);
        //完整的SQL检查
        if (empty($dbString)) {
            return false;
        }
        $clean = '';
        $pos = 0;
        $old_pos = 0;
        while (true) {
            $pos = strpos($dbString, '\'', $pos + 1);
            if ($pos === false) {
                break;
            }
            $clean .= substr($dbString, $old_pos, $pos - $old_pos);
            while (true) {
                $pos1 = strpos($dbString, '\'', $pos + 1);
                $pos2 = strpos($dbString, '\\', $pos + 1);
                if ($pos1 === false) {
                    break;
                } elseif ($pos2 == false || $pos2 > $pos1) {
                    $pos = $pos1;
                    break;
                }
                $pos = $pos2 + 1;
            }
            $clean .= '$s$';
            $old_pos = $pos + 1;
        }
        $clean .= substr($dbString, $old_pos);
        $clean = trim(strtolower(preg_replace(array( '~\s+~s' ), array( ' ' ), $clean)));

        //老版本的Mysql并不支持union，常用的程序里也不使用union，但是一些黑客使用它，所以检查它
        /*if ( strpos( $clean, 'union' ) !== false && preg_match( '~(^|[^a-z])union($|[^[a-z])~s', $clean) != 0 )
        {
            $fail = true;
            $error = "union detect";
        }

        //发布版本的程序可能比较少包括--,#这样的注释，但是黑客经常使用它们
        else*/
        if (( strpos($clean, '/*') > 2 || strpos($clean, '--') !== false || strpos($clean, '#') !== false ) && !( strpos($clean, '/*master*/') > 2 ) && !( strpos($clean, '/*slave*/') > 2 ) && !( strpos($clean, '/*zt_id*/') > 2 ) && !( strpos($clean, '/*finance*/') > 2 ) && !( strpos($clean, '/*nokill*/') > 2 ) && !( strpos($clean, '/*!99999 nokill */') > 2 )) {
            $fail = true;
            $error = "comment detect";
        } elseif (strpos($clean, 'sleep') !== false && preg_match('~(^|[^a-z])sleep($|[^[a-z])~s', $clean) != 0) {
            $fail = true;
            $error = "slown down detect";
        } elseif (strpos($clean, 'benchmark') !== false && preg_match('~(^|[^a-z])benchmark($|[^[a-z])~s', $clean) != 0) {
            $fail = true;
            $error = "slown down detect";
        } elseif (strpos($clean, 'load_file') !== false && preg_match('~(^|[^a-z])load_file($|[^[a-z])~s', $clean) != 0) {
            $fail = true;
            $error = "file fun detect";
        } elseif (strpos($clean, 'into outfile') !== false && preg_match('~(^|[^a-z])into\s+outfile($|[^[a-z])~s', $clean) != 0) {
            $fail = true;
            $error = "file fun detect";
        }
        $result = array();
        if (!empty($fail)) {
            $result[ 'ack' ] = 0;
            $result[ 'error_id' ] = 1000;
            $result[ 'msg' ] = $error;
            //echo $db_string;
            //fputs(fopen($log_file,'a+'),"$userIP||$getUrl||$db_string||$error\r\n");
            //echo $db_string;
            //exit("<font size='5' color='red'>Safe Alert: Request Error step 2!</font>");
            //ClsApp::log( $db_string );
        } else {
            $result[ 'ack' ] = 1;
            $result[ 'msg' ] = $dbString;
        }
        return $result;
    }
}
