<?php
/**
 * 操作mongodb类
 */
namespace OA;

/**
 * Class cls_mongo 操作mongo的类
 */

class ClsMongo
{
    /** @var \MongoDB\Driver\Manager 连接好的mongodb资源 */
    private $mongodb;
    /** @var  $_instance 存好的连接 */
    private static $_instance;
    /** @var $db 当前库 */
    private $db;
    /** @var $collection 当前集合 */
    private $collection;
    /** @var string $collection_name 当前使用中的集合名 */
    private $collection_name;
    /** @var string $db_name 当前的库名 */
    private $db_name;

    /**
     * cls_mongo constructor.
     * @param string $host 地址
     * @param int $port 端口
     */
    private function __construct($host, $port = 27017)
    {
        try {
            $this->mongodb = new \MongoDB\Driver\Manager("mongodb://{$host}:{$port}");
        } catch (Exception $e) {
            $max_try = 5;
            for ($counts = 1; $counts <= $max_try; $counts++) {
                try {
                    $this->mongodb = new \MongoDB\Driver\Manager("mongodb://{$host}:{$port}");
                } catch (Exception $e) {
                    continue;
                }
                return;
            }
        }
    }

    /**
     * 获取mongodb连接实例
     * @param string $host 地址
     * @param int $port 端口
     * @return cls_mongo|存好的连接
     */
    public static function getInstance($host, $port = 27017)
    {
        if (!( self:: $_instance instanceof self )) {
            self::$_instance = new self($host, $port);
        }
        return self::$_instance;
    }

    /**
     * 禁止clone
     * @return true
     */
    private function __clone()
    {
    }

    /**
     * 设置当前要操作的库名
     * @param string $db_name 库名
     * @return 当前库
     */
    public function setCurrentDb($db_name = '')
    {
        $this->db_name = $db_name;
        $this->db = $this->mongodb->$db_name;
        return $this->db;
    }

    /**
     * 设置要操作的集合名
     * @param string $collection_name 设置要操作的当前集合名
     * @return 当前集合
     */
    public function setCurrentCollection($collection_name = '')
    {
        $this->collection_name = $collection_name;
        $this->collection = $this->db->$collection_name;
        return $this->collection;
    }

    /**
     * 判断文档里有没有帐套id
     * @param array $doc 要提交的info
     * @return array
     */
    private function checkDoc($doc)
    {
        //判断有没有帐套
        $key_all = array_keys($doc);
        $key_strs = implode(',', $key_all);
        return array( 'ack'=> strstr($key_strs, 'zt_id') );
    }

    /**
     * 添加文档
     * @param array $doc 要提交的info
     * @return array
     */
    public function insert($doc)
    {
        $bulk = new \MongoDB\Driver\BulkWrite;
        $check_result = $this->checkDoc($doc);
        if (! $check_result['ack']) {
            return array( 'ack'=> 0, 'error_id'=>1000, 'msg'=>'没有帐套ID' );
        }
        $bulk->insert($doc);
        //var_dump(  );
        return array( 'ack'=>1, 'msg'=> $this->mongodb->executeBulkWrite("{$this->db_name}.{$this->collection_name}", $bulk) );
        //p_r( $doc );
        //return $this->collection->insert( $doc );
    }

    /**
     * 获取条数
     * @param array $where 条件
     * @return array 结果
     */
    public function getNum($where = array())
    {
        $check_result = $this->checkDoc($where);
        if (! $check_result['ack']) {
            return array( 'ack'=> 0, 'error_id'=>1000, 'msg'=>'没有帐套ID' );
        }
        $command = new \MongoDB\Driver\Command([ "count" => $this -> collection_name, "query" => $where ]);
        try {
            $result = $this->mongodb->executeCommand($this->db_name, $command);
            $res = current($result->toArray());
            $count = $res->n;
            #echo $count;
            return array( 'ack'=> 1, 'msg'=>$count );
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            #echo $e->getMessage(), "\n";
        }
        #return $this->collection->count( $where );
    }

    /**
     * 获取列表
     * @param $start 开始
     * @param $page_list_num 条数
     * @param array $where 条件
     * @param array $order 排序
     * @param array $field 字段
     * @return array 结果
     */
    public function getList($start, $page_list_num, $where = array(), $order = array( '_id' => -1 ), $field = array())
    {
        $check_result = $this->checkDoc($where);
        if (! $check_result['ack']) {
            return array( 'ack'=> 0, 'error_id'=>1000, 'msg'=>'没有帐套ID' );
        }
        if (!$where) {
            $where = array();
        }
        $filter = $where;

        $options = array( 'limit' => $page_list_num, 'skip' => $start, 'sort' => $order );


        $query = new \MongoDB\Driver\Query($filter, $options);
        $list = $this->mongodb->executeQuery("{$this->db_name}.{$this->collection_name}", $query);
        //$list = $this->collection->find( $where )->sort( array( $order ) )->limit( $page_list_num )->skip( $start )->fields( $field );

        $arr = array();
        foreach ($list as $document) {
            array_push($arr, (array)$document);
        }
        return array( 'ack'=> 1, 'msg'=>$arr );
    }
}
