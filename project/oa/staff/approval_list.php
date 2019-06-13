<?php
/**
 * abstract:审批列表
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年2月13日
 * Time:2019年2月13日16:44:37
 */
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '审批列表';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_APPROVAL_LIST;
session_start();
require_once('../yz.php');

if (!in_array(PRE_APPROVAL_LIST, $adminOptionPer)) {
    show_msg('你没有该页面权限!', 2);
}

//获取用户信息
$clsUser = new \OA\ClsUser();
$userInfo = $clsUser->getUserInfoById($adminId);

//获取配置列表
$clsApprovalConfig = new \OA\ClsApprovalConfig();
$approvalConfigList = $clsApprovalConfig->getApprovalConfigList(
    array(
        'where' =>
            ' ac_approval_status > 0' .
            " and find_in_set({$userInfo['msg'][0]['u_es_id']},ac_use_range)"
    )
);
$approvalConfigList = $approvalConfigList['msg'];
?>
<!doctype html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div id="situation">
    <!--顶部部分-->
    <?php require_once('../header.php'); ?>
    <div class="layui-form" style="margin-top: 20px">
        <div class="layui-inline">
            <label class="layui-form-label">搜索：</label>
            <div class="layui-input-block">
                <select name="searchKey" id="searchKey">
                    <option value="">请选择</option>
                    <option value="1">申请人</option>
                    <option value="2">审批编号</option>
                </select>
            </div>
            <?php select_value($searchKey, 'searchKey'); ?>
            <div class="layui-input-block">
                <input type="text" id="searchValue" name="searchValue" class="layui-input">
            </div>
            <?php select_value($searchValue, 'searchValue'); ?>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">申请时间：</label>
            <div class="layui-input-block">
                <input type="text" id="dateStart" name="dateStart" class="layui-input">
            </div>
            <?php select_value($dateStart, 'dateStart'); ?>
            ~
            <div class="layui-input-block">
                <input type="text" id="dateEnd" name="dateEnd" class="layui-input">
            </div>
            <?php select_value($dateEnd, 'dateEnd'); ?>
        </div>
        <div class="layui-inline">
            <div class="layui-input-block">
                <input type="button" class="layui-btn" value="搜索" id="approvalSearchBtn">
            </div>
        </div>
        <hr>
    </div>

    <div class="layui-form" style="margin-top: 20px">
        <label class="layui-form-label">操作：</label>
        <div class="layui-inline">
            <label class="layui-form-label">审批类型：</label>
            <div class="layui-inline">
                <select name="approvalConfigId" id="approvalConfigId" xm-select-radio
                        xm-select-search="">
                    <option value="">请选择</option>
                    <?php foreach ($approvalConfigList as $configInfo) { ?>
                        <option value="<?php echo $configInfo['ac_id']; ?>"><?php echo $configInfo['ac_spl_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="layui-inline">
                <input type="button" class="layui-btn-normal layui-btn" value="申请审批" onclick="applyApproval()">
            </div>
        </div>
    </div>
    <div>
        <table class="layueTable layui-table" id="approvalList" lay-filter="approvalList" name="approvalList">
        </table>
    </div>
</div>
<?php
require_once('../footer.php');
?>
<script src="../theme/js/formSelects-v4.js"></script>
<link rel="stylesheet" href="../theme/css/formSelects-v4.css">
<script src="../theme/js/oa_approval.js"></script>
<script>
    $(function () {
        //初始化表格
        tableInitApprovalList();
    })
</script>
</body>
</html>