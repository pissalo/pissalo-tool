<?php
$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_TOOLS_KFZGJ_QXGL;
session_start();
require_once('../yz.php');
if (! $system_id) {
    show_msg('请指定系统', 2);
    exit;
}

$per_list = $clsPermissions->getList($system_id);
$per_list = $per_list['msg'];

//只有oa才能在这里添加。
$info = $clsPermissions->selectOneEx(array( 'where'=> "up_id={$up_id}" ));

$info = $info['msg'];

$cls_config = new \OA\ClsConfig();
$sub_name = $cls_config->getSystemSubNameById($system_id);
if ('OA' != $sub_name['msg']) {
    show_msg('只能处理OA添加子权限，其它权限请在各自系统里添加', 2);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div id="qx_layer" style="padding: 10px">
    <form id="permission_edit" class="layui-form">
        <input type="hidden" id="up_id" value="" name="up_id" value="<?php echo $up_id; ?>">
        <input type="hidden" id="up_system_id" value="<?php echo $system_id; ?>" name="up_system_id">
        <div class="layui-form">
            <div class="layui-block">
                <label class="layui-form-label">权限名称：</label>
                <div class="layui-input-block">
                    <input type="text" id="up_name" name="up_name" class="layui-input" placeholder="输入名称">
                </div>
            </div>
            <div class="layui-block">
                <label class="layui-form-label">define：</label>
                <div class="layui-input-block">
                    <input type="text" id="up_define_name" name="up_define_name" class="layui-input" value="<?php echo $info['up_define_name'] ?>_" placeholder="输入Define">
                </div>
            </div>
            <div class="layui-block">
                <label class="layui-form-label">上级：</label>
                <div class="layui-input-block">
                    <select name="up_parent_id" id="up_parent_id">
                        <option value="">一级权限</option>
                        <?php $clsPermissions-> getListSelect($per_list, $up_id); ?>
                    </select>
                </div>
            </div>
            <div class="layui-block">
                <label class="layui-form-label">类型：</label>
                <div class="layui-input-block">
                    <input type="radio" name="up_type" value="1" checked title="操作">
                    <input disabled type="radio" name="up_type" value="2" title="查看">
                </div>
            </div>
        </div>
    </form>
    <div class="m20"><button id="save" class="layui-btn">配 置</button></div>
</div>
</body>
<script src="/theme/js/jquery.min.js"></script>
<script src="/theme/layui/layui.all.js"></script>
<script src="/theme/js/common.js"></script>
<script>
    ajax_data( 'save', 'permission_edit', "/c.php?m=Tools&f=permissionEdit" );
</script>
</html>
