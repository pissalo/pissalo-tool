<?php
/**
 * abstract:给用户分配角色
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2018年12月27日
 * Time:10:22:15
 */
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '给用户分配角色';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_USER_ALLOT_ROLE;
session_start();
require_once('../yz.php');

//获取用户角色列表
$cls_ur = new \OA\ClsUserRole();
$user_role_list = $cls_ur -> getUserRoleList($u_id);
$user_role_list = $user_role_list[ 'msg' ];
//获取用户信息
$clsUser = new \OA\ClsUser();
$userInfo = $clsUser->getUserInfoById($u_id);
//获取用户可用角色列表
$role_list = $cls_ur -> getUserDepartmentRoleList($u_id);
$can_use_role_list = $role_list[ 'msg' ];

//获取部门信息
$cls_es = new \OA\ClsEnterpriseStructure();
$department_id_str = implode(',', array_remove_empty(array_unique(array_column($can_use_role_list, 'r_belong_department'))));

$department_list = $cls_es -> getEsInfoById($department_id_str);
$department_list = $department_list[ 'msg' ];
$department_list = change_main_key($department_list, 'es_id');
foreach ($can_use_role_list as $can_user_role_info) {
    $department_list[ $can_user_role_info[ 'r_belong_department' ] ][ 'sub_role' ][] = $can_user_role_info;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        .staff-name-main, .staff-select-main {
            overflow: hidden;
            padding: 15px 20px;
            width: 100%;
            box-sizing: border-box;
        }

        .staff-name-main > span, .staff-select-main > span {
            width: 10%;
            text-align: right;
            float: left;
            margin-right: 15px;
        }

        .staff-name-box {
            float: left;
            width: 80%;
            border: 1px solid #ddd;
            border-radius: 5px;
            height: 150px;
        }

        .staff-select-main .layui-collapse {
            float: left;
            width: 80%;
        }

        .staff-pop-main .layui-colla-content {
            padding: 0;
        }

        .staff-pop-main .layui-table {
            margin: 0;
        }

        .staff-pop-main .layui-table td {
            cursor: pointer;
        }

        .staff-name-list {
            overflow: hidden;
            margin: 5px;
        }

        .staff-name-list li {
            float: left;
            padding: 2px 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
            margin-right: 10px;
            margin-bottom: 5px;
        }

        .staff-name-list li i {
            font-size: 20px;
            color: #666;
            cursor: pointer;
            font-weight: bold;
            vertical-align: sub;
        }

        td:hover {
            background: #dddddd;
        }

        tr:hover {
            background: #ffffff;
        }
    </style>
</head>
<body>
<div class="staff-pop-main">
    <input type="hidden" id="user_allot_u_id" value="<?php echo $u_id; ?>">
    <form action="">
        <div class="staff-name-main">
            <span>已选择：</span>
            <div class="staff-name-box">
                <ul class="staff-name-list" id="user_allot_role_ul">
                    <?php foreach ($user_role_list as $user_role_info) { ?>
                        <li>
                            <i class="layui-icon layui-icon-close delete_role"></i>
                            <span name="<?php echo $user_role_info['r_id'];?>"><?php echo $user_role_info[ 'r_name' ]; ?></span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </form>
    <div class="staff-select-main">
        <span>选择：</span>
        <div class="layui-collapse" lay-filter="user_allot_role_list">
            <?php foreach ($department_list as $department_info) { ?>
                <div class="layui-colla-item">
                    <h2 class="layui-colla-title"><?php echo $department_info[ 'es_name' ]; ?></h2>
                    <div class="layui-colla-content">
                        <table class="layui-table">
                            <tbody>
                            <?php
                            $count = 0;
                            foreach ($department_info[ 'sub_role' ] as $role_info) {
                                if (0 == $count) {
                                    echo '<tr>';
                                }
                                ?>
                                <td class="ur_class" onclick="show_role_to_user(<?php echo $role_info[ 'r_id' ]; ?>,'<?php echo $role_info[ 'r_name' ]; ?>')"><?php echo $role_info[ 'r_name' ]; ?></td>
                                <?php
                                $count ++;
                                if (3 == $count) {
                                    echo '</tr>';
                                    $count = 0;
                                }
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div style="width: 100%;text-align: center">
        <input type="button" value="确定" class="layui-btn user_add_role">
    </div>
</div>
<script src="../theme/js/oa_staff.js"></script>
<script>
    $( function () {
        layui.use( 'form', function () {  //此段代码必不可少
            var form = layui.form;
            form.render();
        } );

        layui.use( [ 'element', 'layer' ], function () {
            var element = layui.element;
            var layer = layui.layer;
            element.render();
        } );
    } )
</script>
</body>
</html>
