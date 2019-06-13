<?php
$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_SYSTEM_GCLBPZ;
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
                        <li class="table-cell">公司名</li>
                        <li class="table-cell">备注</li>
                        <li class="table-cell">添加时间</li>
                    </ul>
                </div>
            </form>
            <form id="config_form">
                <?php
                $cls_config = new \OA\ClsConfig();
                $list_ss = $cls_config->getCompanyList(array(
                    'order' => 'cc_id desc',
                    'col' => 'cc_id,cc_name,cc_note,cc_add_time'
                ));
                $list_ss = $list_ss['msg'];
                array_push($list_ss, array(1));
                $index = 0;
                foreach ($list_ss as $info_ss) {
                    ?>
                    <ul class="same-ul">
                        <li><input class="layui-input" value="<?php echo $info_ss['cc_id'] ?>" type="text"
                                   name="id[<?php echo $index; ?>]"></li>
                        <li><input class="layui-input" value="<?php echo $info_ss['cc_name'] ?>" type="text"
                                   name="name[<?php echo $index; ?>]"></li>
                        <li><input class="layui-input" value="<?php echo $info_ss['cc_note'] ?>" type="text"
                                   name="note[<?php echo $index; ?>]"></li>
                        <li><?php echo date('Y-m-d', $info_ss['cc_add_time']); ?></li>
                    </ul>
                    <?php
                    $index++;
                } ?>
            </form>
            <div class="m20">
                <?php echo \OA\ClsView::GetAddButton('新增', "", PRE_SYSTEM_GCLBPZ_ADD, 'save')['msg']; ?>
            </div>
            <div style="margin-left: 20px" class="layui-form-mid layui-word-aux">notice:只增不减，ID列请按顺序写，要删除请联系IT</div>
        </div>
    </div>
</div>
<?php
require_once('../footer.php');
?>
<script>
    ajax_data('save', 'config_form', "/c.php?m=System&f=companyConfig", 1);
</script>
</body>
</html>
