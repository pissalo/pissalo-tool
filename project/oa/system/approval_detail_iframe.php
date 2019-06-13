<?php
/**
 * abstract:审批明细
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年2月25日
 * Time:11:10:08
 */
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '审批明细';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_APPROVAL_LIST;
session_start();
require_once('../yz.php');

$clsApproval = new \OA\ClsApproval();
$param = array();
//col
$param['col'] = '   approval_id,
                    addUser.u_username as add_username,
                    checkUser.u_username as check_username,
                    addUser.u_es_id,
                    approval_add_time,
                    ac_spl_name,
                    ad_check_time,
                    ad_check_user_id,
                    ac_class,
                    ad_check_level,
                    approval_status,
                    approval_check_level,
                    acd_check_method,
                    approval_copy_to_user';
//join
$param['join'] = 'left join oa_approval_detail on ad_approval_id = approval_id';
$param['join'] .= ' left join oa_user addUser on addUser.u_id = approval_add_user_id';
$param['join'] .= ' left join oa_user checkUser on checkUser.u_id = ad_check_user_id';
$param['join'] .= ' left join oa_approval_config on ac_id = approval_type';
$param['join'] .= ' left join oa_approval_config_detail on acd_ac_id = ac_id and acd_check_level = ad_config_level';
//where
$param['where'][] = "approval_id = {$approvalId}";
//order
$param['order'] = 'ad_check_level asc';
$approvalInfo = $clsApproval->getApprovalInfo($param);
$approvalInfo = $approvalInfo['msg'];
//获取审批内容
require_once(WEB_CLASS . "/approval/class.{$approvalInfo[0]['ac_class']}.php");
$className = '\OA\Cls' . str_replace(' ', '', ucwords(str_replace('_', ' ', $approvalInfo[0]['ac_class'])));
$clsApprovalSpecific = new $className();
$applyPerList = $clsApprovalSpecific->getApprovalContent($approvalId);

//获取审批过程
$approvalCheckProcess = $clsApproval->getApprovalProcess($approvalInfo);
$approvalCheckProcess = $approvalCheckProcess['msg'];

//获取组织上级列表
$clsEs = new \OA\ClsEnterpriseStructure();
$allEsStr = '';
$clsEs->esList = array();
$supList = $clsEs->getSupEsList($approvalInfo[0]['u_es_id']);
foreach ($supList['msg'] as $supInfo) {
    if ($allEsStr) {
        $allEsStr .= '->';
    }
    $allEsStr .= $supInfo['es_name'];
}
$organization = $allEsStr;

//获取抄送人
$clsUser = new \OA\ClsUser();
$userList = $clsUser->getUserNameById($approvalInfo[0]['approval_copy_to_user']);
$copyToUserArr = array_remove_empty(explode(',', $userList['msg'][0]['name']));

?>
<!doctype html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
    <script src="/theme/layui/layui.all.js"></script>
    <style>
        .approvalDetailInfo {
            width: 90%;
            height: 90%;
            padding: 10px;
        }

        label {
            font-size: 16px;
            color: #9F9F9F;
        }
    </style>
</head>
<body>
<div class="approvalDetailMain">
    <div class="approvalDetailInfo">
        <div class="approvalApprovalInfo">
            <div class="layui-input-block">
                <span style="font-size: 18px"><?php echo $approvalInfo[0]['add_username']; ?></span>
                <br>
                <span style="color: #9F9F9F;"><?php echo $approvalStatusList[$approvalInfo[0]['approval_status']] ?></span>
            </div>
            <hr>
            <div class="layui-input-block">
                <label>审批编号:</label>
                <?php echo $approvalInfo[0]['approval_id']; ?>
            </div>
            <div class="layui-input-block">
                <label>所在部门:</label>
                <?php echo $organization; ?>
            </div>
            <div class="layui-input-block">
                <label>审批类型:</label>
                <?php echo $approvalInfo[0]['ac_spl_name']; ?>
            </div>
            <div class="layui-input-block">
                <label>申请时间:</label>
                <?php echo date('Y-m-d H:i:s', $approvalInfo[0]['approval_add_time']); ?>
            </div>
            <div class="layui-input-block">
                <label>审批内容:</label><br>
                申请了以下权限：<br>
                <p style="margin-left: 10%">
                    <?php echo $applyPerList['msg']; ?>
                </p>
            </div>
            <hr>
            <div class="approvalProcess">
                <div class="layui-input-block">
                    <span style="margin-bottom: 10px;"><?php echo $approvalInfo[0]['add_username'] ?> </span>
                    <span style="color: #5FB878;margin-left: 10px">发起审批</span>
                    <span style="float: right;color: #9F9F9F;"><?php echo date('Y-m-d H:i:s', $approvalInfo[0]['approval_add_time']); ?></span>
                    <br>
                    <i class="layui-icon layui-icon-down" style="font-size: 30px; color: #9F9F9F;margin-top: 10px"></i>
                </div>
                <?php
                $count = count($approvalCheckProcess);
                foreach ($approvalCheckProcess as $key => $checkProcess) { ?>
                    <div class="layui-input-block">
                        <span><?php echo $checkProcess['checkUser']; ?></span>
                        <?php if ($checkProcess['checkTime']) { ?>
                            <span style="color: #5FB878;margin-left: 10px">已通过</span>
                        <?php } ?>
                        <span style="float: right;color: #9F9F9F;"><?php echo $checkProcess['checkTime'] ? $checkProcess['checkTime'] : '未审核'; ?></span>
                        <br>
                        <?php if ($count > $key + 1) { ?>
                            <i class="layui-icon layui-icon-down"
                               style="font-size: 30px; color: #9F9F9F;margin-top: 10px"></i>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <hr>
            <div class="copyToUser layui-input-block">
                <label style="font-size: 18px;color: black">抄送 </label>
                <span style="margin-left: 10px;color: #9F9F9F;">审批通过后，通知抄送人</span>
                <br>
                <?php foreach ($copyToUserArr as $copyToUser) { ?>
                    <div class="layui-input-inline" style="margin-left: 10px">
                            <?php echo $copyToUser; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
