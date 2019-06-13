<?php
/**
 * 日志通用页面
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年1月2日
 * Time:15:25:33
 */
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '日志通用页面';
@include_once('../include/common.inc.php');
$pagePermissionId = 1;
session_start();
require_once('../yz.php');

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
<div>
    <input type="hidden" id="id" value="<?php echo $id; ?>">
    <input type="hidden" id="log_table" value="<?php echo $log_table; ?>">
    <table class="layueTable layui-table" id="log_table" lay-filter="log_table">
    </table>
</div>
<script>
    $( function () {
        var id = $( '#id' ).val();
        var table = $( '#log_table' ).val();
        var where = {
            id: id,
            table: table
        };
        log_table( where, 'log_table' );
    } )
</script>
</body>
</html>
