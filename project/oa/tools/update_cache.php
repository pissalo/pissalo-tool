<?php
$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_TOOLS_KFZGJ_GXYTHC;
session_start();
require_once('../yz.php');
$system_id = isset($system_id) ? $system_id : 1;
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
        <div class="layui-form add-permission-select">
            <form id="frm">
                <div class="layui-inline">
                    <label class="layui-form-label">选择系统：</label>
                    <div class="layui-input-block">
                        <?php
                        //要更新的缓存
                        $list = array(
                            'Permissions#updateDefineList'=>'权限列表',
                        );
                        foreach ($list as $key => $name) {
                            echo "<input checked type='checkbox' name='cache_name[]' value='{$key}'>&nbsp;{$name}&nbsp;&nbsp;";
                        }
                        ?>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-form add-permission-select" style="margin: 10px 0 0 0 ">
            <button id="btn" class="layui-btn layui-btn-warm" style="margin-left: 20px">更新缓存</button>
        </div>

    </div>
</div>
<?php
require_once('../footer.php');
?>
<script>
    ajax_data( 'btn', 'frm', "/c.php?m=Tools&f=updateCache" );
</script>
</body>
</html>
