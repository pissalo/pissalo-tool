<?php
$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_TOOLS_KFZGJ_APIZX;
session_start();
require_once('../yz.php');
require_once(WEB_INCLUDE . '/api/abstract.api_tpl.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div id="qx_layer" style="padding: 10px">
    <div class="layui-form-mid layui-word-aux m20">
        <?php
        $clsApi = new \OA\ClsApi();
        $apiInfo = $clsApi->selectOneEx(array( 'col'=> 'a_module_name', 'where'=> "a_id={$a_id}" ));
        $moduleName = $apiInfo['msg']['a_module_name'];

        $cls = \OA\ClsApp::getApiClass($moduleName)['msg'];
        $info = $cls->baseGetInfo();
        echo $info['msg'];
        ?>
    </div>

</div>
</body>
</html>
