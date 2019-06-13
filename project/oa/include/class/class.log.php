<?php
/**
 * 日志类
 * @author:     我不是稻草人 <junqing124@126.com>
 * @version:    v1.0.3
 * @package class
 * @since 1.0.8
 */
namespace OA;

require_once(WEB_DR . '/base/class/class.mongo.php');

/**
 * Class ClsLog 日志类
 * @package OA
 */
class ClsLog extends ClsMongo
{
    /** @var cls_mongo|存好的连接  */
    private $mongo;
    /** @var log_mongodb|存好的连接  */
    private $log_mongodb;
    /** @var collection_name 当前的collection name  */
    private $collection_name;

    /**
     * ClsLog constructor.
     * @param string $db_name 库名
     */
    public function __construct($db_name = '')
    {
        if ($db_name) {
            global $mongo_config;
            $config_info = $mongo_config[ $db_name ];
            $mongo_host = $config_info[ 'host' ];
            $mongo_port = $config_info[ 'port' ];
        } else {
            global $mongo_host, $mongo_port;
            $db_name = 'log';
        }
        //echo $mongo_host;
        $this->mongo = ClsMongo::getInstance($mongo_host, $mongo_port, $db_name);
        $this->setDb($db_name);
    }

    /**
     * 设置库名
     * @param $dbName 库名
     */
    public function setDb($dbName)
    {
        $this->mongo->setCurrentDb($dbName);
    }

    /**
     * @param $collectionName 集合名
     */
    public function setCollection($collectionName)
    {
        $this->collection_name = $collectionName;
        $this->log_mongodb = $this->mongo->setCurrentCollection($collectionName);
    }

    /**
     * 添加日志
     * @param $log 日志
     * @return array 添加结果
     */
    public function addLog($log)
    {
        if ($this->collection_name) {
            $val = $this->mongo->insert($log);
            return $val;
        } else {
            return array( 'ack' => 0 );
        }
    }

    /**
     * 获取日志列表
     * @param 开始 $page 第几页
     * @param 条数 $list_num 一页多少内容
     * @param array $where 条件
     * @param array $order 排序
     * @param array $field 要获取的字段
     * @return mixed 日志列表
     */
    public function getList($page, $list_num, $where = array(), $order = array( '_id' => -1 ), $field = array())
    {
        if ($page < 1) {
            $page = 1;
        }
        $start = ( $page - 1 ) * $list_num;

        //p_r( $this->mongo );
        return $this->mongo->getList($start, $list_num, $where, $order, $field);
    }

    /**
     * 获取某条where的集合条目数
     * @param string $where 条件
     * @return mixed
     */
    public function getNum($where = '')
    {
        return $this->mongo->getNum($where) ;
    }

    /**
     * 移除集合内容
     * @param $where 条件
     * @return array 结果
     */
    public function remove($where)
    {
        return array( 'ack' => 1, 'msg' => $this->mongo->remove($where) );
    }
}
