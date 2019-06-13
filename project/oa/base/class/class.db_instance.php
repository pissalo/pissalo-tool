<?php
/**
 * 数据库核心连接类
 */
namespace OA;

defined('IN_DCR') or exit('No permission.');

/**
 * Class ClsDbInstance 本类用的是单例模式，用来连接数据库
 */
class ClsDbInstance
{
    /** @var mixed $_instance 实例好的mysql */
    private static $_instance;
    /** @var int $failConnectCount 重连最多次数 */
    private $failConnectCount = 5;
    /** @var int $isConnectFailQuit 连接失败就退出不返回值 */
    private $isConnectFailQuit = 1;
    /** @var string $type 类型 目前为master和slave */
    private $type;
    /** @var resource $conn mysqli连接结果 */
    private $conn;

    /**
     * ClsDbInstance constructor.
     * @author 黄焕军
     * @param $type 连接类型
     * @return boolean true
     */
    private function __construct($type)
    {
        $this->type = $type;
        $this->conn = $this->connect($type);
    }

    /**
     * ClsDbInstance connect 连接数据库
     * @author 黄焕军
     * @param $type 连接类型
     * @return resource mysqli连接结果
     */
    private function connect($type)
    {
        $dbInfo = array();
        if ('master' == $type) {
            $dbInfo = ClsApp:: getDbMasterInfo();
        }
        if ('slave' == $type) {
            $dbInfo = ClsApp:: getDbSlaveInfo();
        }
        $func_name = 'mysqli_connect';
        $i = 0;
        do {
            $i++;
            $mysql_connect = $func_name($dbInfo[ 'db_host' ], $dbInfo[ 'db_name' ], $dbInfo[ 'db_pass' ], $dbInfo[ 'db_table' ]);
        } while ($i < $this->failConnectCount && ! $mysql_connect);
        if (! $mysql_connect) {
            if ($this->isConnectFailQuit) {
                die('连接数据库[' . $dbInfo[ 'db_host' ] . ']失败，请检查您的数据库配置');
            } else {
                return false;
            }
        }

        mysqli_select_db($mysql_connect, $dbInfo[ 'db_table']) or self::show_error($dbInfo[ 'db_host'] . '选择数据库失败,请检查数据库[' . $dbInfo[ 'db_table'] . ']是否创建');
        mysqli_query($mysql_connect, "SET NAMES '{$dbInfo[ 'db_ut']}'");
        mysqli_query($mysql_connect, "SET character_set_client=binary");

        return $mysql_connect;
    }

    /**
     * 函数show_error,显示数据库错误信息
     * @author 黄焕军
     * @param string $msg 内容
     * @param string $sql sql语句
     * @param int $errorId 错误ID
     * @return true 直接输出结果
     */
    public static function showError($msg, $sql = '', $errorId = 0)
    {
        //$ignoreErrorId 忽略不显示的错误
        global $ignoreErrorId;
        if ($ignoreErrorId && in_array($errorId, $ignoreErrorId)) {
            return false;
        }
        if ($errorId && !in_array($errorId, array( 1064, 1062 ))) {
            /*$clsLog = new ClsLog();
            $clsLog->setCollection( 'log_error_mysql' );
            $clsLog->addLog( array(
                'lem_add_time'=> time(),
                'lem_sql'=> $sql,
                'lem_msg'=> $msg,
                'lem_error_id'=> $errorId,
            ) );*/
        }

        $msg_str = "<div style='width:70%; margin:0 auto 10px auto;background:#f5e2e2;border:1px red solid; font-size:12px;'><div style='font-size:12px;padding:5px; font-weight:bold; color:#FFF;color:red'>DCRCMS DB Error</div>";
        $msg_str .= "<div style='border:1px #f79797 solid;background:#fcf2f2; width:95%; margin:0 auto; margin-bottom:10px;padding:5px;'><ul style='list-style:none;color:green;line-height:22px;'><li><span style='color:red;'>错误页面:</span></li>";
        if (!empty($sql)) {
            $msg_str .= "<li><span style='color:red;'>错误语句:</span>$sql</li>";
        }
        $msg_str .= "<li><span style='color:red;'>提示信息:</span>$msg</li>";
        $msg_str .= "</ul></div></div>";

        echo $msg_str;
    }

    /**
     * 禁止clone
     * @author 黄焕军
     * @return none
     */
    private function __clone()
    {
    }

    /**
     * 连接数据库获取实例
     * @author 黄焕军
     * @param $type 连接类型，master和slave或更多
     * @param int $forceReconnect 是不是强制重连
     * @return mixed
     */
    public static function getInstance($type, $forceReconnect = 0)
    {
        if (!( self::$_instance[ $type ] instanceof self ) || self::$_instance[ $type ]->type != $type || $forceReconnect) {
            self::$_instance[ $type ] = new self($type);
        }
        return self::$_instance[ $type ];
    }

    /**
     * 获取本类连接好的mysqli
     * @author 黄焕军
     * @return resource
     */
    public function getConn()
    {
        return $this->conn;
    }
}
