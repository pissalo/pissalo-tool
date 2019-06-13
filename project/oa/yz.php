<?php
namespace OA;

$adminU = trim($_SESSION[ 'adminU' ]);
$adminId = $_SESSION[ 'adminId' ];
$adminP = $_SESSION[ 'adminP' ];
$adminZtId = $_SESSION[ 'adminZtId' ];
$adminOptionPer = $_SESSION['adminOptionPer'];
$adminReadPer = $_SESSION['adminReadPer'];

if (empty($pageAuthor) || empty($pageComment)) {
    show_msg('请设置本页的开发者和说明', 2);
}
if (empty($pagePermissionId)) {
    show_msg('请设置本页的权限ID', 2);
}

require_once(WEB_CLASS . '/class.user.php');
$clsUser = new ClsUser($adminU, $adminP, $adminZtId);
$clsPermissions = new ClsPermissions($pagePermissionId);
$permission_info = $clsPermissions->getTitle();
$pageTitle = $permission_info[ 'msg' ];
$is_admin = $clsUser->yz();
/*p_r( $is_admin );
p_r( $_SESSION );
exit;*/
if ($is_admin[ 'ack' ] == 1) {
} else {
    if (file_exists('login.php')) {
        show_next('', "login.php", 1);
    } else {
        show_next('', "/login.php", 1);
    }
}
