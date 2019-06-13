<?php
session_start();
$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
$pagePermissionId = 1;
include_once('../../include/common.inc.php');
$adminZtId = 1;
require_once(WEB_INCLUDE . '/api/abstract.api_tpl.php');

//这是用的是控制器
if (!isset($m)) {
    echo json_encode(array('ack' => 0, 'error_id' => 1000, 'msg' => '没有指定的处理器'));
    exit;
}

$cls = \OA\ClsApp::getApiClass($m);
if (!$cls['ack']) {
    echo json_encode(array('ack' => 0, 'error_id' => 1001, 'msg' => $cls['msg']));
    exit;
}

//判断token
if (!isset($token)) {
    echo json_encode(array('ack' => 0, 'error_id' => 1002, 'msg' => 'token为空'));
    exit;
}
$cls_api = new \OA\ClsApi();
$api_info = $cls_api->selectOneEx(array('col' => 'a_id', 'where' => "a_module_name='{$m}' and a_token='{$token}'"));

if (!$api_info['msg']['a_id']) {
    echo json_encode(array('ack' => 0, 'error_id' => 1003, 'msg' => 'token错误'));
    exit;
}

$cls = $cls['msg'];
$cls->baseSetData(getReqData());
echo json_encode($cls->baseOption());
