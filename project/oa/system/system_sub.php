<?php
$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_SYSTEM_ZXTPZ;
session_start();
require_once('../yz.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div id="framework">
    <!--顶部部分-->
    <?php require_once('../header.php'); ?>
    <!--头部end-->
    <div id="frameworkMain">
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
            <legend><?php echo $pageTitle; ?></legend>
        </fieldset>
        <div class="framework-table">
            <form class="table">
                <div class="table-header-group">
                    <ul class="table-row">
                        <li class="table-cell">ID</li>
                        <li class="table-cell">子系统名</li>
                        <li class="table-cell">内网地址</li>
                        <li class="table-cell">外网地址</li>
                    </ul>
                </div>
            </form>
            <form id="config_form">
                <?php
                $cls_config = new \OA\ClsConfig();
                $list_ss = $cls_config->getSystemSubList();
                $list_ss = $list_ss['msg'];
                array_push($list_ss, array(1));
                $index = 0;
                foreach ($list_ss as $info_ss) {
                    ?>
                    <ul class="same-ul">
                        <li><input class="layui-input" value="<?php echo $info_ss['css_id'] ?>" type="text"></li>
                        <li class="rank-1"><input class="layui-input" value="<?php echo $info_ss['css_name'] ?>"
                                                  type="text"
                                <?php if (strlen($info_ss['css_name']) > 0) {
                                    echo 'readonly';
                                }
                                ?> name="name[<?php echo $index; ?>]"></li>
                        <li><input class="layui-input" value="<?php echo $info_ss['css_url_inner'] ?>" type="text"
                                   name="url_inner[<?php echo $index; ?>]"></li>
                        <li><input class="layui-input" value="<?php echo $info_ss['css_url_remote'] ?>" type="text"
                                   name="url_remote[<?php echo $index; ?>]"></li>
                    </ul>
                    <?php
                    $index++;
                } ?>
            </form>
            <div class="m20">
                <?php echo \OA\ClsView::GetAddButton('新增', "", PRE_SYSTEM_ZXTPZ_ADD, 'save')['msg']; ?>
            </div>
            <div style="margin-left: 20px" class="layui-form-mid layui-word-aux">notice:只增不减，ID列请按顺序写，要删除请联系IT</div>
        </div>
    </div>
</div>
<?php
require_once('../footer.php');
?>
<script>
    ajax_data('save', 'config_form', "/c.php?m=System&f=systemSubConfig", 1);
</script>
</body>
</html>