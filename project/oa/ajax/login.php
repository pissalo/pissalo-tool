<?php
namespace OA;

error_reporting(0);

require_once('../include/common.inc.php');
session_start();
require_once(WEB_CLASS . '/class.user.php');
$adminZtId = $zt_id;

//页面错误信息列表
$error_list = array( 1002 => '验证码输入不正确' );

if ('no' != $encrypt) {
    $password = encrypt($password);
}

if ($_SESSION[ 'admin_yz' ] != md5(strtoupper($captcha))) {
    echo json_encode(array( 'ack' => 0, 'error_id' => 1002, 'msg' => $error_list[ 1002 ] ));
    exit;
}

$clsUser = new ClsUser($username, $password, $adminZtId);

$result = $clsUser->yz();
if ($result[ 'ack' ] == 1) {
    $clsUser->login();
    echo json_encode(array( 'ack' => 1 ));
} else {
    echo json_encode(array( 'ack' => 0, 'msg' => $result[ 'msg' ] ));
}
