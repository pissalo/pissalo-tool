<?php

defined('IN_DCR') or exit('No permission.');

include_once(WEB_DR . '/share/cache/permissions.php');
//本页的配置值都要用''包括起来
require_once(WEB_DR . '/share/config/config.db.php');
require_once(WEB_INCLUDE . '/define/common.php');
require_once(WEB_INCLUDE . '/define/menu.php');


$web_url = 'http://127.0.0.1'; //网址
$web_dir = '';//网站目录 以/开头 如:/dcr

$top_url = $_SERVER[ 'HTTP_HOST' ];
if ('http://' . $top_url != $web_url) {
    $web_url = 'http://' . $top_url;
}

$web_name = 'OA办公，在工作中享受生活！';//网站标题
$web_keywords = '网站关键字';//网站关键字
$web_description = '网站简介';//网站简介

//初始化weburl
if (!empty($web_dir)) {
    $web_url = $web_url . $web_dir;
}
if (!empty($_GET[ 'my_tpl_dir' ])) {
    $tpl_dir = $_GET[ 'my_tpl_dir' ];
}
