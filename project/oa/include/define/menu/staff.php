<?php
$menu_config['staff'] =
array(
    'title'=> '员工权限',
    'permission_id'=> PRE_STAFF,
    'define'=> 'PRE_STAFF',
    'href'=>'javascript:;',
    'class'=> 'layui-icon-password',
    'sub'=> array(
        array(
            'title'=> '员工管理',
            'permission_id'=> PRE_STAFF_YGGL,
            'define'=> 'PRE_STAFF_YGGL',
            'href'=>'/staff/user_list.php',
        ),
        array(
            'title'=> '组织架构',
            'permission_id'=> PRE_STAFF_ZZJG,
            'define'=> 'PRE_STAFF_ZZJG',
            'href'=>'/staff/zzjg.php',
        ),
        array(
            'title'=> '角色管理',
            'permission_id'=> PRE_STAFF_ZWGL,
            'define'=> 'PRE_STAFF_ZWGL',
            'href'=>'/staff/role_list.php',
        ),
        array(
            'title' => '审批中心',
            'permission_id' => PRE_APPROVAL_LIST,
            'define' => 'PRE_APPROVAL_LIST',
            'href' => '/staff/approval.php',
        ),
    ),
);
