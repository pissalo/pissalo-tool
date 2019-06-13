<?php
/**
 * abstract:ajax获取组织信息
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年2月27日
 * Time:14:51:24
 */
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = 'ajax获取组织信息';
include_once('../include/common.inc.php');
$pagePermissionId = 1;
session_start();
require_once('../yz.php');

$clsEs = new \OA\ClsEnterpriseStructure();
$returnMsg = array('ack' => 1);
if ('getEsSonIdArr' == $action) {
    $sonEsList = $clsEs->getSonEsIdArr($esId);
    $sonEsIdStr = implode(',', array_remove_empty($sonEsList['msg']));
    $returnMsg['msg'] = $sonEsIdStr;
}
echo json_encode($returnMsg);
