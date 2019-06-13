<?php

$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '审批流配置编辑';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_APPROVAL_CONFIG;
session_start();
require_once('../yz.php');
if (!in_array($pagePermissionId, $adminOptionPer)) {
    show_msg('你没有该页面的权限!', 2);
}

if ('edit' == $action) {
    $clsApprovalConfig = new \OA\ClsApprovalConfig();
    $approvalConfigInfo = $clsApprovalConfig->getApprovalConfigInfoById($ac_id);
    $approvalConfigInfo = $approvalConfigInfo ['msg'][0];
    $approvalConfigDetailList = $clsApprovalConfig->getApprovalConfigDetailById($ac_id);
    $approvalConfigDetailList = $approvalConfigDetailList['msg'];
    //处理抄送人
    $tmpArr = array_remove_empty(explode(',', $approvalConfigInfo['ac_copy_to_user']));
    foreach ($tmpArr as &$tmpInfo) {
        $tmpInfo = 'u_' . $tmpInfo;
    }
    $copyToUserStr = implode(',', $tmpArr);
}
//获取组织结构列表
$clsEs = new \OA\ClsEnterpriseStructure();
$esTree = $clsEs->getEsListTree();
$esTreeJson = json_encode($esTree[0][0]);
//获取组织结构列表(含用户)
$clsEs = new \OA\ClsEnterpriseStructure();
$esUserTree = $clsEs->getEsUserListTree();
$esUserTreeJson = json_encode($esUserTree[0][0]);
//
$supLevelArr = array(
    1 => '直接上级',
    2 => '第二上级',
    3 => '第三上级',
    4 => '第四上级',
    5 => '第五上级',
    6 => '第六上级',
    7 => '第七上级',
    8 => '第八上级',
    9 => '第九上级',
    10 => '第十上级',
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div id="approvalSet">
    <!--顶部部分-->
    <?php require_once('../header.php'); ?>
    <!--头部end-->
    <div class="approval-set-main">
        <h2 class="ao-title">审批流程设置：权限申请</h2>
        <input type="hidden" id="edit_use_range" value="<?php echo $approvalConfigInfo['ac_use_range']; ?>">
        <input type="hidden" id="copy_to_user" value="<?php echo $copyToUserStr; ?>">
        <input type="hidden" id="action" value="<?php echo $action; ?>">
        <input type="hidden" id="acId" value="<?php echo $ac_id; ?>">
        <form class="layui-form approval-set-form">
            <div class="layui-form-item">
                <label class="layui-form-label"><i style="color: red">*</i> 审批名称：</label>
                <div class="layui-input-block">
                    <input type="text" name="spl_name" id="spl_name" required lay-verify="required" placeholder="审核名称"
                           autocomplete="off" class="layui-input">
                </div>
            </div>
            <?php select_value($approvalConfigInfo['ac_spl_name'], 'spl_name'); ?>
            <?php if (in_array(PRE_APPROVAL_CONFIG_KF, $adminOptionPer)) { ?>
                <!--开发者配置信息-->
                <div class="layui-form-item">
                    <label class="layui-form-label"><i style="color: red">*</i> 配置路径：</label>
                    <div class="layui-input-block">
                        <input type="text" name="ac_path" id="ac_path" required lay-verify="required" placeholder="相对路径"
                               autocomplete="off" class="layui-input">
                    </div>
                </div>
                <?php select_value($approvalConfigInfo['ac_path'], 'ac_path'); ?>
                <div class="layui-form-item">
                    <label class="layui-form-label"><i style="color: red">*</i>类文件名：</label>
                    <div class="layui-input-block">
                        <input type="text" name="ac_class" id="ac_class" required lay-verify="required"
                               placeholder="类文件名"
                               autocomplete="off" class="layui-input">
                    </div>
                </div>
                <?php select_value($approvalConfigInfo['ac_class'], 'ac_class'); ?>
            <?php } ?>
            <div class="layui-form-item">
                <label class="layui-form-label"><i style="color: red">*</i>应用范围：</label>
                <div class="layui-input-block">
                    <select xm-select="splUseRange" xm-select-search="" xm-select-search-type="dl"
                            xm-select-skin="normal" name="spl_use_range" id="spl_use_range" xm-select-show-count="4"
                            required lay-verify="required">
                        <option value="">请选择适用范围</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">审批说明：</label>
                <div class="layui-input-block">
                    <textarea name="spl_note" id="spl_note" cols="88" rows="10"
                              style="resize: none"><?php echo $approvalConfigInfo['ac_note']; ?></textarea>
                </div>
            </div>
            <div class="layui-form-item" style="width: 100%">
                <label class="layui-form-label"><i style="color: red">*</i>审批人：</label>
                <div class="approval-set-same-div">
                    <ul id="spl_check_list">
                        <?php
                        foreach ($approvalConfigDetailList as $approvalConfigDetailInfo) {
                            $checkInfo = '';    //级别审核信息
                            $checkMenthod = ''; //审核方式
                            $checkType = '';    //审核类型
                            //审核信息串
                            $checkInfo = $approvalConfigDetailInfo['acd_check_type'] . '-';
                            switch ($approvalConfigDetailInfo['acd_check_type']) {
                                case 1:
                                    $checkInfo .= $supLevelArr[$approvalConfigDetailInfo['acd_check_sup_level']] . '-';
                                    $checkMenthod = $splMethodList[$approvalConfigDetailInfo['acd_check_method']];
                                    $checkType = $splCheckTypeList[$approvalConfigDetailInfo['acd_check_type']] . '(' . $supLevelArr[$approvalConfigDetailInfo['acd_check_sup_level']] . ')';
                                    break;
                                case 2:
                                    $checkInfo .= 'u_' . str_replace(',', ',u_', $approvalConfigDetailInfo['acd_check_user_id']) . '-';
                                    $checkMenthod = $splMethodList[$approvalConfigDetailInfo['acd_check_method']];
                                    $userNum = count(array_unique(array_remove_empty(explode(',', $approvalConfigDetailInfo['acd_check_user_id']))));
                                    $checkType = $splCheckTypeList[$approvalConfigDetailInfo['acd_check_type']] . '(' . $userNum . '人)';
                                    break;
                                case 3:
                                    $checkMenthod = '申请人自己审核';
                                    $checkType = $splCheckTypeList[$approvalConfigDetailInfo['acd_check_type']];
                                    break;
                                default:
                                    break;
                            }
                            $checkInfo .= $splMethodList[$approvalConfigDetailInfo['acd_check_method']];
                            ?>
                            <li class="splLevel <?php echo $count; ?>" style="width: 140px"
                                id="check<?php echo $approvalConfigDetailInfo['acd_check_level']; ?>">
                                <div class="border-div">
                                    <?php echo $checkType; ?>
                                    <i class="layui-icon layui-icon-close splLevelClose" style="float: right"></i>
                                </div>
                                <p class="showApprovalEdit"
                                   id="<?php echo 'checkLevel_' . $approvalConfigDetailInfo['acd_check_level']; ?>">
                                    <?php echo $checkMenthod; ?>
                                </p>
                                <input type="hidden" value="<?php echo $checkInfo; ?>" class="approvalCheckInfo">
                            </li>
                        <?php } ?>
                    </ul>
                    <i class="layui-icon layui-icon-add-circle approval-people-add"></i>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><i style="color: red">*</i>抄送人：</label>
                <div class="layui-input-block">
                    <select xm-select="approvalCopyTo" xm-select-search="" xm-select-search-type="dl"
                            xm-select-skin="normal" name="ac_copy_to_user" id="ac_copy_to_user" xm-select-show-count="4"
                            required lay-verify="required">
                        <option value="">请选择抄送人</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item" style="margin-top: 100px">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal editApproval" lay-submit lay-filter="approvalConfigEdit" type="button">立即提交
                    </button>
                    <!--<button class="layui-btn layui-btn-primary" type="button">取消</button>-->
                </div>
            </div>
        </form>
    </div>
</div>
<?php require_once('../footer.php'); ?>
</body>
<script src="../theme/js/formSelects-v4.js"></script>
<link rel="stylesheet" href="../theme/css/formSelects-v4.css">
<script src="../theme/js/oa_approval.js"></script>
<script>
    $(function () {
        //初始化Select选项
        layui.formSelects.data('splUseRange', 'local', {
            arr: [<?php echo $esTreeJson;?>]
        });
        layui.formSelects.data('approvalCopyTo', 'local', {
            arr: [<?php echo $esUserTreeJson;?>]
        });
        //初始化Select选中值
        var useRangeArr = $('#edit_use_range').val().split(',');
        layui.formSelects.value('splUseRange', useRangeArr)
        var copyToUserArr = $('#copy_to_user').val().split(',');
        layui.formSelects.value('approvalCopyTo', copyToUserArr)
    })
</script>
</html>
