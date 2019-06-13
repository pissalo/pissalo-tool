<?php
require_once('include/common.inc.php');
session_start();
require_once(WEB_CLASS . '/class.user.php');
$pageAuthor = '黄焕军';
$pageComment = '退出';
$pagePermissionId = 1;
require_once('yz.php');
$clsUser = new \OA\ClsUser();
$clsUser->logout();
show_next('', 'login.php');
