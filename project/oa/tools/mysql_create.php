<?php
$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_TOOLS_KFZGJ_TSCQ;
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
        <h2 class="ao-title"><?php echo $pageTitle; ?></h2>
        <div class="framework-table">
                <form id="form">
                    <div class="layui-form">
                    <div class="layui-block">
                        <label class="layui-form-label">系统：</label>
                        <div class="layui-input-block">
                            <select id="system_name">
                                <option value="">请选择</option>
                                <?php
                                $cls_config = new OA\ClsConfig();
                                $list_ss = $cls_config->getSystemSubList(array( 'col'=> 'css_name' ));
                                $list_ss = $list_ss['msg'];
                                foreach ($list_ss as $info_ss) {
                                    echo "<option value=\"{$info_ss['css_name']}\">{$info_ss['css_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="layui-block">
                        <label class="layui-form-label">表名：</label>
                        <div class="layui-input-block">
                            <input required type="text" id="table_name" class="layui-input" placeholder="输入要添加的表名">
                        </div>
                    </div>
                    <div class="layui-block">
                        <label class="layui-form-label">结果：</label>
                        <div class="layui-input-block">
                            <textarea class="layui-textarea" id="result"></textarea>
                        </div>
                    </div>
                    </div>
                </form>
                <div>
                    <button class="layui-btn layui-btn-normal" id="general">生成</button>
                </div>
        </div>
    </div>
</div>
<?php
require_once('../footer.php');
?>
<script>
    $("#general").on("click", function () {
        var system_name = $('#system_name').val();
        var table_name = $('#table_name').val();
        if( 'OA' == system_name )
        {
            if( table_name.length > 0 )
            {
                var str_arr = table_name.split('_');
                var qz = '';
                for (i = 0; i < str_arr.length; i++) {
                    if( i != 0 )
                    {
                        qz += str_arr[i].substr(0,1);
                    }
                }

                //生成语句
                var create_sql = 'CREATE TABLE `' + table_name + '` (`' + qz + '_id` int(11) NOT NULL AUTO_INCREMENT,`' + qz + '_add_time` int(11) NOT NULL,`' + qz + '_update_time` int(11) NOT NULL,`' + qz + '_approval_status` tinyint(4) NOT NULL,`' + qz + '_add_user_id` smallint(6) NOT NULL,`zt_id` smallint(6) NOT NULL, PRIMARY KEY (`' + qz + '_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

                $('#result').val(create_sql);
            }
        }else
        {
            $('#result').val('不支持');
        }
    });
</script>
</body>
</html>
