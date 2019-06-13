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
<div id="addPermission">
    <!--添加统一头部-->
    <?php require_once('../header.php'); ?>
    <div class="add-permission-main">
        <h2 class="ao-title"><?php echo $pageTitle; ?></h2>
        <div class="layui-form add-permission-select" style="margin: 10px 0 0 0 ">
            <button onclick="api_edit(0)" class="layui-btn" style="margin-left: 20px">添加API</button>
            <button onclick="help()" class="layui-btn layui-btn-warm" style="margin-left: 20px">使用说明</button>
        </div>
        <div class="layui-form" style="margin: 20px">
            <table class="layui-table">
                <thead>
                <tr>
                    <th>名称</th>
                    <th>Token</th>
                    <th>处理器</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $cls_api = new \OA\ClsApi();
                $list = $cls_api->selectEx();
                $list = $list['msg'];
                if ($list) {
                    foreach ($list as $info) {
                        ?>
                        <tr>
                            <td><?php echo $info['a_name']; ?></td>
                            <td><?php echo $info['a_token']; ?></td>
                            <td><?php echo $info['a_module_name']; ?></td>
                            <td><?php echo $info['a_is_valid']; ?></td>
                            <td>
                                <button id="update_<?php echo $info['a_id'] ?>" onclick="api_update_token(<?php echo $info['a_id'] ?>)" class="layui-btn">更新Token</button>
                                <?php if ($info['a_is_valid']) { ?>
                                    <button id="invalid_<?php echo $info['a_id'] ?>" onclick="api_invalid(<?php echo $info['a_id'] ?>)" class="layui-btn layui-btn-warm">作废</button>
                                <?php } ?>
                                <button onclick="api_document(<?php echo $info['a_id'] ?>)" class="layui-btn">查看文档</button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
require_once('../footer.php');
?>
<script>
    function api_edit( a_id )
    {
        layer.open({
            type : 2,
            shade : [0.5 , '#000' , true],
            shadeClose : true,
            border : 1,
            title : '查看API文档',
            offset : ['25px',''],
            area : ['480px', '350px'],
            content: 'api_edit_iframe.php?a_id=' + a_id
        });
        return false;
    }
    function api_document( a_id )
    {
        layer.open({
            type : 2,
            shade : [0.5 , '#000' , true],
            shadeClose : true,
            border : 1,
            title : '添加编辑API',
            offset : ['25px',''],
            area : ['480px', '350px'],
            content: 'api_document_iframe.php?a_id=' + a_id
        });
        return false;
    }
    function help()
    {
        layer.open({
            type : 2,
            shade : [0.5 , '#000' , true],
            shadeClose : true,
            border : 1,
            title : '添加编辑API',
            offset : ['25px',''],
            area : ['600px', '600px'],
            content: 'api_help_iframe.php'
        });
        return false;
    }
    function api_update_token( a_id )
    {
        ajax_post({a_id:a_id},'/c.php?m=Tools&f=apiUpdateToken', 1 );
    }
    function api_invalid( a_id )
    {
        ajax_post({a_id:a_id},'/c.php?m=Tools&f=apiInvalid', 1 );
    }
</script>
</body>
</html>
