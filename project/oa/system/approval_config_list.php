<?php

$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '审批流配置列表';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_APPROVAL_CONFIG;
session_start();
require_once('../yz.php');
if (!in_array($pagePermissionId, $adminOptionPer)) {
    show_msg('你没有该页面的权限!', 2);
}
$clsApprovalConfig = new \OA\ClsApprovalConfig();
$approvalConfigList = $clsApprovalConfig->getApprovalConfigList();
$approvalConfigList = $approvalConfigList['msg'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>审批流</title>
    <?php require_once('../header_common.php'); ?>
    <style>
        .approval-main ul {
            margin: 50px;
        }

        .approval-main li {
            overflow: hidden;
            width: 80%;
            padding: 20px;
            border-bottom: 1px solid #ddd;
            line-height: 35px;
        }

        .approval-main li:nth-child(2n-1) {
            background: #f2f2f2;
        }

        .approval-main .layui-form-switch {
            margin: 0;
        }

        .approval-main span {
            color: #333;
            font-size: 14px;
            font-weight: bold;
            margin: 0 5px;
        }

        .approval-main a {
            font-size: 14px;
            color: #444;
            margin-left: 15px;
        }

        .approval-main i {
            font-size: 30px;
            color: #1296db;
            font-weight: bold;
            vertical-align: sub;
        }
    </style>
</head>
<body>
<!--顶部部分-->
<?php require_once('../header.php'); ?>
<!--头部end-->
<div id="approval">
    <div class="approval-main">
        <h2 class="ao-title">审批流程设置</h2>
        <form action="">
            <ul>
                <?php if (in_array(PRE_APPROVAL_CONFIG_ADD, $adminOptionPer)) { ?>
                    <input type="button" value="添加审批类型" class="layui-btn approval-add-btn">
                <?php } ?>
            </ul>
            <ul>
                <?php foreach ($approvalConfigList as $approvalConfigInfo) { ?>
                    <li>
                        <div class="fl">
                            <i class="layui-icon layui-icon-form"></i>
                            <span><?php echo $approvalConfigInfo['ac_spl_name']; ?></span>
                        </div>
                        <div class="layui-form fr">
                            <span>开启审批</span>
                            <input type="checkbox" lay-skin="switch"
                                   lay-filter="approvalIsValid"
                                   value="<?php echo $approvalConfigInfo['ac_id']; ?>"
                                <?php if ($approvalConfigInfo['ac_approval_status']) {
                                    echo '  checked ';
                                } ?>>
                            <a href="approval_config_edit.php?action=edit&ac_id=<?php echo $approvalConfigInfo['ac_id']; ?>"
                               target="_self">设置</a>
                            <a href="javascript:show_log(<?php echo $approvalConfigInfo['ac_id'];?>,'log_approval_config')">日志</a>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </form>

    </div>
</div>
<?php require_once('../footer.php'); ?>
<script src="../theme/js/formSelects-v4.js"></script>
<link rel="stylesheet" href="../theme/css/formSelects-v4.css">
<script src="../theme/js/oa_approval.js"></script>
</body>
</html>