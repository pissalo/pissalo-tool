<?php
/**
 * abstract:角色分配
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2018年12月20日
 * Time:15:13:13
 */
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '角色分配';
$pagePermissionId = 1;
include_once('../include/common.inc.php');
session_start();
require_once('../yz.php');
$cls_ur = new \OA\ClsUserRole();
$param = array ();
$param['col'] = 'oa_user_role.*,u_username';
$param['join'] .= ' left join oa_user on u_id = ur_u_id';
$param['where'][] = " ur_role_id = {$role_id}";
$role_user_list = $cls_ur -> getUrInfo($param);
$role_user_list = $role_user_list['msg'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        .layui-table-cell {
            height: inherit;
        }
    </style>
</head>
<body>
<div class="allocation-main">
    <h2>已分配用户</h2>
    <div class="allocation-use">
        <form action="" id="edit_role_form" class="layui-form">
            <input type="hidden" name="allot_role_id" id="allot_role_id" value="<?php echo $role_id; ?>">
            <input type="hidden" name="allot_role_use_range" id="allot_role_use_range" value="<?php echo $role_use_range;?>">
            <input type="hidden" name="user_name_str" id="user_name_str">
            <ul id="role_user_list">
                <?php foreach ($role_user_list as $role_user_info) { ?>
                    <li>
                        <span class="role_name"><?php echo $role_user_info['u_username']; ?></span><i class="delete_role">×</i>
                    </li>
                <?php } ?>
            </ul>
        </form>
    </div>
    <div>
        <div class="layueTable" id="allocation_table" lay-filter="role_allot_table_filter">
        </div>
    </div>
    <div class="pop-btn">
        <button class="layui-btn layui-btn-normal pop-sure-btn">确定</button>
    </div>
</div>
</body>
<script src="../theme/js/oa_staff.js"></script>
<script>
    var table = layui.table;
    var role_id = $( '#allot_role_id' ).val() ? $( '#allot_role_id' ).val() : 0;
    var role_use_range = $( '#allot_role_use_range' ).val() ? $( '#allot_role_use_range' ).val() : 0;
    $( function () {
        role_allot_table_init({r_id:role_id,r_use_range:role_use_range})
        layui.use( 'form', function () {  //此段代码必不可少
            var form = layui.form;
            form.render();
        } );
    } )

    $( ".allocation-main" ).on( "click", ".allocation-search-btn", function () {
        $( this ).siblings( ".allocation-search-box" ).show();
        $( ".shadow" ).show();
    } )
    $( ".shadow" ).on( "click", function () {
        $( ".allocation-search-box" ).hide();
        $( this ).hide();
    } )
    //取消按钮点击事件
    $( '.pop-sure-btn' ).on( 'click', function () {
        var user_name_str = '';
        $( '.role_name' ).each( function () {
            user_name_str += $( this ).html() + ',';
        } )
        $( '#user_name_str' ).val( user_name_str );
        ajax_post( $( '#edit_role_form' ).serialize(), '/c.php?m=Staff&f=roleAllot' )
    } )

</script>
</html>
