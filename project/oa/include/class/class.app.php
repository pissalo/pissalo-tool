<?php
/**
 * 程序信息的类，比如获取一个编辑器 获取程序信息等全局工厂模式的类.
 */
namespace OA;

defined('IN_DCR') or exit('No permission.');

/**
 * Class ClsApp
 */
class ClsApp
{
    /**
     * 获取一个编辑器 并且会输出这个编辑器 目前支持ckeditor kindeditor
     * @param string $editor_name 编辑器名
     * @param string $default_value 编辑内默认值
     * @param string $editor_width 编辑器宽 以px为单位
     * @param string $editor_height 编辑器高 以px为单位
     * @param string $daohang 菜单样式 1为简单 2为全部
     * @return true 返回true
     */
    public static function getEditor($editor_name, $default_value = '', $editor_width = '930', $editor_height = '500', $daohang = 1)
    {
        global $web_url, $web_editor;
        if ($web_editor == 'ckeditor') {
            $editor_t = "<script src=\"" . $web_url . "/include/editor/$web_editor/ckeditor.js\" type=\"text/javascript\"></script>\r\n<script type=\"text/javascript\" src=\"" . $web_url . "/include/editor/$web_editor/ckfinder/ckfinder.js\"></script>\r\n<textarea id=\"" . $editor_name . "\" name=\"" . $editor_name . "\">" . $default_value . "</textarea>\r\n<script type=\"text/javascript\">var editor = CKEDITOR.replace('" . $editor_name . "',{height:'" . $editor_height . "',width:'" . $editor_width . "'});CKFinder.SetupCKEditor(editor, \"" . $web_url . "/include/editor/$web_editor/ckfinder/\");</script>";
        } else if ($web_editor == 'kindeditor') {
            //把宽度和高度换成cols和rows
            $cols = $editor_width / 7;
            $rows = $editor_height / 20;
            $editor_t = "<textarea cols='$cols' rows='$rows' id=\"" . $editor_name . "\" name=\"" . $editor_name . "\">" . $default_value . "</textarea><script charset='utf-8' src=\"" . $web_url . "/include/editor/$web_editor/kindeditor-min.js\"></script><script>KE.show({id : '$editor_name'});</script>";
        }
        echo $editor_t;
    }

    /**
     * 获取一个编辑器 这个方法用于一个页面有2个编辑器这个是第二调用的.get_editor必须调用在这GetEditor_2前
     * @since 1.0.7
     * @param string $editor_name 编辑器名
     * @param string $default_value 编辑内默认值
     * @param string $editor_width 编辑器宽 以px为单位
     * @param string $editor_height 编辑器高 以px为单位
     * @param string $daohang 菜单样式 1为简单 2为全部
     * @return true 返回true
     */
    public static function getEditor2($editor_name, $default_value = '', $editor_width = ' 930 ', $editor_height = '500', $daohang = 1)
    {
        global $web_url, $web_editor;
        if ($web_editor == 'ckeditor') {
            $editor_t = "<textarea id=\"" . $editor_name . "\" name=\"" . $editor_name . "\">" . $default_value . "</textarea>\r\n<script type=\"text/javascript\">var editor = CKEDITOR.replace('" . $editor_name . "',{height:'" . $editor_height . "',width:'" . $editor_width . "'});CKFinder.SetupCKEditor(editor, \"" . $web_url . "/include/editor/$web_editor/ckfinder/\");</script>";
        } else if ($web_editor == 'kindeditor') {
            //把宽度和高度换成cols和rows
            $cols = $editor_width / 7;
            $rows = $editor_height / 20;
            $editor_t = "<textarea cols='$cols' rows='$rows' id=\"" . $editor_name . "\" name=\"" . $editor_name . "\">" . $default_value . "</textarea><script> KindEditor.ready(function(K) {window.editor = K.create('#" . $editor_name . "');});</script>";
        }

        echo $editor_t;
    }

    /**
     * 检查数据
     * @param $table_name 表名
     * @param $info 要提交检测的数据
     * @return array
     */
    public static function checkData($table_name, $info)
    {
        $cls_data = new \OA\ClsData();
        $cls_data->setTable($table_name);
        $qz_info = $cls_data->getTableYz();
        $name_info = $cls_data->getTableName();
        $qz = $qz_info[ 'msg' ];
        $info["{$qz}_zt_id"] = $info['zt_id'];

        $table_check = array();
        $common_path = WEB_INCLUDE . '/table_check/common.php';
        $table_path = WEB_INCLUDE . '/table_check/ruler/' . $name_info['msg'] . '.php';

        require_once($common_path);
        if (file_exists($table_path)) {
            require_once($table_path);
        }

        $error_arr = array();
        foreach ($table_check as $key => $ruler) {
            $error = array();
            if (! isset($info[ "{$qz}_{$key}" ])) {
                continue;
            }
            $info_value = $info[ "{$qz}_{$key}" ];

            //max
            if ($ruler[ 'max' ] && $info_value > $ruler[ 'max' ]) {
                $error[] = "{$ruler['label']}:最大允许值[{$ruler['max']}]-传入值[{$info_value}]";
            }

            //min
            if ($ruler[ 'min' ] && $info_value < $ruler[ 'min' ]) {
                $error[] = "{$ruler['label']}:最大允许值[{$ruler['min']}]-传入值[{$info_value}]";
            }

            //in_value
            if ($ruler[ 'in_value' ]) {
                $in_value_arr = explode(',', $ruler[ 'in_value' ]);
                if (! in_array($info_value, $in_value_arr)) {
                    $error[] = "{$ruler['label']}:允许值[{$ruler['in_value']}]-传入值[{$info_value}]";
                }
            }

            //is_empty
            if (strlen($ruler[ 'is_empty' ]) > 0) {
                if (strlen($info_value) < 1 && $ruler[ 'is_empty' ]) {
                    $error[] = "{$ruler['label']}:不允许为空-传入值[{$info_value}]";
                }
            }

            if ($error) {
                $error_arr[ $key ] = implode('-', $error);
            }
        }

        $result = array();
        if ($error_arr) {
            $result['ack'] = 0;
            $result['error_id'] = 1000;
            $result['msg'] = implode('---@@@---', $error_arr);
        } else {
            $result['ack'] = 1;
        }

        return $result;
    }

    /**
     * 获取一个数据库连接
     * @return DB 一个db实例
     */
    public static function getDb()
    {
        global $db_config;

        return new ClsDB($db_config[ 'master' ][ 'db_type' ], $db_config[ 'master' ][ 'db_host' ], $db_config[ 'master' ][ 'db_name' ], $db_config[ 'master' ][ 'db_pass' ], $db_config[ 'master' ][ 'db_table' ], $db_config[ 'master' ][ 'db_ut' ]);
    }

    /**
     * 获取主库配置
     * @return mixed
     */
    public static function getDbMasterInfo()
    {
        global $db_config;

        return $db_config[ 'master' ];
    }

    /**
     * 获取一个随机的从库配置
     * @param int $rand_slave_id 是不是要随机ID
     * @param int $slave_id 指定的从库ID
     * @return mixed
     */
    public static function getDbSlaveInfo($rand_slave_id = 1, $slave_id = 0)
    {
        global $db_config;
        if ($rand_slave_id) {
            $slave_info = $db_config[ 'slave' ][ array_rand($db_config[ 'slave' ], 1) ];
        }
        if ($slave_id) {
            $slave_info = $db_config[ 'slave' ][ $slave_id ];
        }
        return $slave_info;
    }

    /**
     * 指定信息来获取一个db类
     * @param $db_type 类型  2是mysql
     * @param $db_host mysql ip地址
     * @param $db_name 数据库名
     * @param $db_pass 数据库密码
     * @param $db_table 数据库名
     * @param $db_ut 字符集
     * @param int $use_pconnect 是不是长连接
     * @param int $is_connect_fail_quit 连接失败直接退出
     * @return cls_db
     */
    public static function getDbByInfo($db_type, $db_host, $db_name, $db_pass, $db_table, $db_ut, $use_pconnect = 0, $is_connect_fail_quit = 1)
    {
        return new cls_db($db_type, $db_host, $db_name, $db_pass, $db_table, $db_ut, $use_pconnect, $is_connect_fail_quit);
    }

    /**
     * 获取一个redis连接
     * @return Redis 一个redis实例
     */
    public static function getRedis()
    {
        return cls_redis::getRedisInstance();
        //return false;
    }

    /**
     * 获取一个redis连接
     * @return Redis 一个redis实例
     */
    public static function getRedisSlave()
    {
        /*global $redis_slave_list;
        $redis_config_info = $redis_slave_list[array_rand( $redis_slave_list, 1 )];
        //p_r( $redis_config_info );

        return new cls_redis( $redis_config_info['host'], $redis_config_info['port'] );*/
    }

    /**
     * 获取一个ClsData实例
     * @param string $table_name 表名
     * @return ClsData 一个ClsData实例
     */
    public static function getData($table_name)
    {
        require_once(WEB_CLASS . '/class.data.php');
        $clsData = new \OA\ClsData($table_name);

        return $clsData;
    }

    /**
     * 用redis来缓存一个值
     * @param $key key名
     * @param $value 值
     * @param int $exp_time 有效时间 单位秒
     * @return bool
     */
    public static function setKeyCache($key, $value, $exp_time = 1900800)
    {
        if (class_exists('Redis')) {
            $redis = ClsApp:: getRedis();
            if ($redis) {
                $redis->setValue($key, $value, $exp_time);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取缓存好的key
     * @param $key key名
     * @return bool
     */
    public static function getKeyCache($key/*, $is_slave = 1*/)
    {
        if (class_exists('Redis')) {
            /*if( $is_slave )
            {
                //echo 'a';
                //echo '<hr>';
                $redis = ClsApp:: get_redis_slave();
            }else
            {
                $redis = ClsApp:: get_redis();
            }*/
            $redis = ClsApp:: getRedis();
            if ($redis) {
                $infoStr = $redis->getValue($key);
            }
            return $infoStr;
        } else {
            return false;
        }
    }

    /**
     * 删除redis缓存key
     * @param $key key名
     * @return bool
     */
    public static function delKeyCache($key)
    {
        if (class_exists('Redis')) {
            $redis = ClsApp:: getRedis();
            if ($redis) {
                $info_str = $redis->del($key);
            }
            return $info_str;
        } else {
            return false;
        }
    }

    /**
     * 获取ApiClassName
     * @param $moduleName Api模型名
     * @return array 结果
     */
    public static function getApiClass($moduleName)
    {
        $moduleNameNew = optionClassName($moduleName);
        $classFilePath = WEB_INCLUDE . '/api/api/class.' . $moduleNameNew . '.php';
        if (! file_exists($classFilePath)) {
            return array('ack'=>0, 'msg'=>'类文件不存在');
        }

        require_once($classFilePath);
        $className = "\API\Cls{$moduleName}";
        $cls = new $className();

        return array('ack'=>1, 'msg'=>$cls);
    }
}
