<?php
/**
 * abstract:返回审批配置信息
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年2月16日
 */
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '返回审批配置信息';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_STAFF_ZZJG;
session_start();
require_once('../yz.php');
$clsApprovalConfig = new \OA\ClsApprovalConfig();

if ($approvalConfigId) {
    //获取配置信息
    $approvalConfigInfo = $clsApprovalConfig->getApprovalConfigInfoById($approvalConfigId);
    $approvalConfigInfo = $approvalConfigInfo['msg'][0];

    //获取配置信息
    $configInfo = $clsApprovalConfig->getApprovalConfigInfoById($approvalConfigId);
    $configInfo = $configInfo['msg'][0];

    require_once(WEB_CLASS . "/approval/class.{$configInfo['ac_class']}.php");
    $className = '\OA\Cls' . str_replace(' ', '', ucwords(str_replace('_', ' ', $configInfo['ac_class'])));
    $clsApprovalSpecific = new $className();
    $urlInfo = $clsApprovalSpecific->getApprovalDetailUrl(array(), array(), 2);
    $url = $configInfo['ac_path'] . $urlInfo['msg'];
    echo $url;
} elseif ($approvalId) {
    //获取审批信息
    $clsApproval = new \OA\ClsApproval();
    $approvalList = $clsApproval->getApprovalInfoById($approvalId, 1);
    $approvalList = $approvalList['msg'];
    //获取配置信息
    $configInfo = $clsApprovalConfig->getApprovalConfigInfoById($approvalList[0]['approval_type']);
    $configInfo = $configInfo['msg'][0];

    require_once(WEB_CLASS . "/approval/class.{$configInfo['ac_class']}.php");
    $className = '\OA\Cls' . str_replace(' ', '', ucwords(str_replace('_', ' ', $configInfo['ac_class'])));
    $clsApprovalSpecific = new $className();
    $url = $clsApprovalSpecific->getApprovalDetailUrl($approvalList[0], $configInfo);
    echo $url['msg'];
}
