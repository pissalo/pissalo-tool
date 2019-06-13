<?php
/**
 * abstract:入口文件
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2018年12月18日
 * Time:11:50:45
 */
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '入口文件';
$pagePermissionId = 1;
include_once('include/common.inc.php');
session_start();
require_once('yz.php');
if ($_SESSION['backUrl']) {
    show_next('', $_SESSION['backUrl'], 1);
} else {
    show_next('', "/index/index.php", 1);
}
