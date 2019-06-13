<?php
$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_TOOLS_KFZGJ_APIZX;
session_start();
require_once('../yz.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div id="qx_layer" style="padding: 10px">
    <form id="frm" class="layui-form">
        <input type="hidden" id="a_id" value="" name="a_id" value="<?php echo $a_id; ?>">
        <div class="layui-form">
            <div class="layui-block">
                <label class="layui-form-label">名称：</label>
                <div class="layui-input-block">
                    <input type="text" id="a_name" required name="a_name" class="layui-input" placeholder="输入名称">
                </div>
            </div>
            <div class="layui-block">
                <label class="layui-form-label">处理器：</label>
                <div class="layui-input-block">
                    <input type="text" id="a_module_name" required name="a_module_name" class="layui-input" placeholder="输入模块名">
                </div>
            </div>
            <div class="layui-block">
                <label class="layui-form-label">Token：</label>
                <div class="layui-input-block">
                    <input type="text" required id="a_token" name="a_token" class="layui-input" value="<?php $cls_api = new \OA\ClsApi();
                    $token_info = $cls_api->getRandToken();
                    echo $token_info['msg']; ?>">
                </div>
            </div>
        </div>

    </form>
    <div class="m20"><button id="save" class="layui-btn">添 加</button></div>
</div>
</body>
<script src="/theme/js/jquery.min.js"></script>
<script src="/theme/layui/layui.all.js"></script>
<script src="/theme/js/common.js"></script>
<script>
    ajax_data( 'save', 'frm', "/c.php?m=Tools&f=editApi", 1 );
</script>
</html>
