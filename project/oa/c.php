<?php
namespace Controller;

$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
$pagePermissionId = 1;
include_once('include/common.inc.php');
session_start();
require_once('yz.php');

//这是用的是控制器
if (!isset($m) || !isset($f)) {
    echo json_encode(array( 'ack' => 0, 'error_id' => 1000, 'msg' => '没有指定的module和controller' ));
    exit;
}
$module_name_arr = array_keys($module_config_list);
if (! in_array($m, $module_name_arr)) {
    echo json_encode(array( 'ack' => 0, 'error_id' => 1001, 'msg' => 'module不存在' ));
    exit;
}

$m_file = strtolower($m);
$controllers_path = WEB_DR . "/include/controllers/{$m_file}.php";
require_once($controllers_path);
$class_name = 'Controller\\' . $m;
$function_name = $c;
$cls = new $class_name();
$cls->setData(getReqData());
echo json_encode($cls->$f());
