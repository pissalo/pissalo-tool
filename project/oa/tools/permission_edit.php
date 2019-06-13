<?php
$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_TOOLS_KFZGJ_QXGL;
session_start();
require_once('../yz.php');
$cls_config = new \OA\ClsConfig();
$oa_id_info = $cls_config->getSystemSubIdByName('OA');
$system_id = isset($system_id) ? $system_id : $oa_id_info['msg'];
$is_show_edit = true;//是不是允许操作编辑，只有OA才行
if ($oa_id_info['msg'] != $system_id) {
    $is_show_edit = false;
}
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
            <div class="layui-inline">
                <label class="layui-form-label">选择系统：</label>
                <div class="layui-input-block">

                    <?php
                    $list_ss = $cls_config->getSystemSubList(array( 'col'=> 'css_id,css_name,css_url_inner,css_url_remote' ));
                    $list_ss = $list_ss['msg'];
                    foreach ($list_ss as $info_ss) {
                        $class_name = 'layui-btn';
                        if ($info_ss['css_id'] == $system_id) {
                            $class_name = 'layui-btn layui-btn-normal';
                        }
                        echo "<button onclick='window.location=\"permission_edit.php?system_id={$info_ss['css_id']}\"' class=\"{$class_name}\">{$info_ss['css_name']}</button>";
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="layui-form add-permission-select" style="margin: 10px 0 0 0 ">
            <button id="menu_sc_qx" class="layui-btn layui-btn-warm" style="margin-left: 20px">根据Menu配置生成权限列表</button>
            <div style="margin-left: 20px;float:none;" class="layui-form-mid layui-word-aux">请点击自动生成权限列表，然后配置。notice:只增不减，如果menu里删除了请在这里手动删除。查看类型的，才能添加下级权限。其它系统无法在OA里编辑权限条目，请在各自系统里用API(put_permission)推送过来</div>
        </div>
        <div class="layui-form" style="margin: 20px">
            <table class="layui-table">
                <thead>
                <tr>
                    <th>名称</th>
                    <th>define</th>
                    <th>判断</th>
                    <th>获取标题</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $cls_per = new \OA\ClsPermissions();
                $list = $cls_per->getList($system_id);
                $list = $list['msg'];
                if ($list) {
                    function showPerList($list)
                    {
                        global $is_show_edit;
                        foreach ($list as $info) {
                            $name_info = explode('_', $info['up_name']);
                            ?>
                <tr>
                    <td><?php echo str_repeat("&nbsp;", ($info['class_level'] - 1) * 5); ?><?php echo $name_info[count($name_info)-1]; ?></td>
                    <td>
                            <?php
                            echo $info['up_define_name'];
                            ?>
                    </td>

                    <td>
                            <?php
                            echo " if( in_array( {$info['up_define_name']}, \$admin_a ) ){  }";
                            ?>
                    </td>
                    <td>
                            <?php
                            echo " get_permission_title( {$info['up_define_name']} )";
                            ?>
                    </td>
                    <td>
                            <?php
                            if (2 == $info['up_type'] && $is_show_edit) {
                                //只有查看才有下级权限
                                echo "<button onclick=\"permission_edit({$info['up_id']})\" class=\"layui-btn\">添加下级权限</button>";
                            }
                            ?>
                    </td>
                </tr>
                            <?php
                            if ($info[ 'sub_class' ]['ack'] && count($info[ 'sub_class' ]['msg'])) {
                                showPerList($info[ 'sub_class' ]['msg']);
                            }
                        }
                    }
                    showPerList($list);
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
    $(function () {
        $(".select-system li").click(function () {
            $(this).addClass("active").siblings().removeClass("active");
            var index = $(this).index();
            $(".system-table").eq(index).addClass("active").siblings().removeClass("active")
        })
    })

    function permission_edit( up_id )
    {
        layer.open({
            type : 2,
            shade : [0.5 , '#000' , true],
            shadeClose : true,
            border : 1,
            title : '添加编辑权限',
            offset : ['25px',''],
            area : ['480px', '350px'],
            content: 'permission_edit_iframe.php?up_id=' + up_id + '&system_id=<?php echo $system_id ?>'
        });
        return false;
    }

    ajax_data( 'menu_sc_qx', 'menu_sc_qx', "/c.php?m=Tools&f=permissionScByMenu" );
</script>
</body>
</html>
