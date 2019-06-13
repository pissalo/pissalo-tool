<?php
/**
 * abstract:角色列表
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2018年12月20日
 * Time:11:03:53
 */
$curDir = dirname(__FILE__);
$pageAuthor = '角色列表';
$pageComment = '王银龙';
$pageTitles = '角色列表';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_STAFF_ZWGL;
session_start();
require_once('../yz.php');

//权限判断
if (!in_array($pagePermissionId, $adminOptionPer)) {
    show_msg('您没有该页面的权限!', 2);
}

$cls_role = new \OA\ClsRole();
$role_list = $cls_role -> getRoleInfo();
$role_list = $role_list[ 'msg' ];

//获取组织列表
$cls_es = new \OA\ClsEnterpriseStructure();
$cls_es->esList = array();
$es_list = $cls_es -> getEsList();
$es_list = $es_list[ 'msg' ];
$es_list = change_main_key($es_list, 'es_id');
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
<!--头部end-->
<div class="ao-manages-main">
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
        <legend><?php echo $pageTitle; ?></legend>
    </fieldset>
    <form action="" id="search_form" class="layui-form">
        <div class="layui-form">
            <label class="layui-form-label">搜索：</label>
            <div class="layui-inline">
                <label class="layui-form-label">角色名：</label>
                <div class="layui-input-block">
                    <input type="text" id="role_name" name="r_name" class="layui-input" placeholder="角色名">
                </div>
            </div>
            <?php select_value($role_name, 'role_name'); ?>
            <div class="layui-inline">
                <label class="layui-form-label">部门：</label>
                <div class="layui-input-inline">
                    <select name="department_id" id="department_id" lay-search="">
                        <option value="">请选择</option>
                        <?php
                        foreach ($es_list as $es_keys => $es_info) {
                            $disable = '';
                            if ($es_info[ 'es_type' ] == ES_TYPE_COMPANY) {
                                echo "<optgroup label='{$es_info['es_name']}'>";
                            } elseif ($es_info[ 'es_type' ] != ES_TYPE_DEPARTMENT) {
                                continue;
                            } else {
                                ?>
                                <option value="<?php echo $es_info[ 'es_id' ]; ?>"
                                        style="margin-left: 20px"><?php echo $es_info[ 'es_name' ]; ?></option>
                                <?php
                            }

                            if ($es_info[ 'es_type' ] == ES_TYPE_COMPANY && $es_keys != 0) {
                                echo "</optgroup>";
                            }
                        } ?>
                    </select>
                </div>
            </div>
            <?php select_value($department_id, 'department_id'); ?>

            <div class="layui-inline">
                <label class="layui-form-label"></label>
                <div class="layui-input-block">
                    <input type="checkbox" name="qx_wfp" id="qx_wfp" checked value="1" title="未分配权限">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label"></label>
                <div class="layui-input-block">
                    <input type="checkbox" name="qx_yfp" id="qx_yfp" checked value="1" title="已分配权限">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label"></label>
                <div class="layui-input-block" style="width: 250px">
                    <?php echo \OA\ClsView::getSearchButton('搜索', 'RoleListSearch()')['msg']; ?>
                    <?php echo \OA\ClsView::GetAddButton('新增角色', "show_add_role_page('add')", PRE_STAFF_ZWGL_ADD)['msg']; ?>
                    <?php echo \OA\ClsView::getSearchButton('删除日志', 'show_del_role_log()')['msg']; ?>
                </div>
            </div>
        </div>
    </form>
    <input type="hidden" name="role_id" id="role_id" value="<?php echo $role_id; ?>">
    <div>
        <table class="layueTable layui-table" id="role_list" lay-filter="role_list">
        </table>
    </div>
</div>
    </div>
<?php
require_once('../footer.php');
?>
<script src="../theme/js/oa_staff.js"></script>
<script>
    var table = layui.table;
    $( function () {
        tableInitRoleList()

        layui.use( 'laydate', function () {
            var laydate = layui.laydate;
            //执行一个laydate实例
            laydate.render( {
                elem: '#entry_time' //指定元素
            } );
        } );
    } )
</script>
<script type="text/html" id="barDemo">
    <?php
    if (in_array(PRE_STAFF_ZWGL_BJQX, $adminOptionPer)) { ?>
        <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
        <?php
    }
    if (in_array(PRE_STAFF_ZWGL_ALLOT, $adminOptionPer)) {
        ?>
        <a class="layui-btn layui-btn-warm layui-btn-sm" lay-event="allot">分配用户</a>
        <?php
    }
    if (in_array(PRE_STAFF_ZWGL_PER, $adminOptionPer)) {
        ?>
        <a class="layui-btn layui-btn-normal layui-btn-sm" lay-event="allot_permission">分配权限</a>
        <?php
    }
    if (in_array(PRE_STAFF_ZWGL_DELETE, $adminOptionPer)) {
        ?>
        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="delete_role">删除</a>
        <?php
    } ?>
    <a class="layui-btn layui-btn-sm" lay-event="role_log">日志</a>
</script>
</body>
</html>
