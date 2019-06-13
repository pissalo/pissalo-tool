<?php
$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_TOOLS_KFZGJ_APIZX;
session_start();
require_once('../yz.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div id="qx_layer" style="padding: 10px">
    <div class="layui-form-mid layui-word-aux m20">
        API调用方式:<br>
        &lt;?php<br>
        $url = '127.0.0.1:85/api/v1/';<br>
        $data = array();<br>
        //必填参数开始<br>
        $data['m'] = 'PutPermission';//处理器<br>
        $data['token'] = 'vHZ2NWC366Q7uUhFJdfiC2KdYjFsFcTvYBj9yxBg';<br>
        //必填参数结束<br>
        //处理器自定参数<br>
        $data['parent_id'] = 1; //上级ID<br>
        $data['up_name'] = 1; //上级ID<br>

        $curl = curl_init();<br>
        curl_setopt($curl, CURLOPT_URL, $url);<br>
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);<br>
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);<br>
        curl_setopt($curl, CURLOPT_POST, 1);<br>
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);<br>
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);<br>
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);<br>
        $result = curl_exec($curl);<br>
        curl_close($curl);<br>

        var_dump( $result );<br>
        ?&gt;<br>
    </div>

</div>
</body>
</html>
