<?php
/**
 * abstract:修改用户密码
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年1月21日
 * Time:17:17:19
 */
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '修改用户密码';
$pagePermissionId = 1;
include_once('../include/common.inc.php');
session_start();
require_once('../yz.php');

$clsUser = new \OA\ClsUser();
$userInfo = $clsUser->getUserNameById($userId);
$userName = $userInfo['msg'][0]['name'];
?>
<!doctype html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div class="changeUserMain">
    <form class="layui-form">
        <input type="hidden" name="changeUserId" id="changeUserId" value="<?php echo $userId; ?>">
        <div>
            <div class="layui-inline" style="margin-top: 20px">
                <label class="layui-form-label"><span>用户名:</label>
                <div class="layui-input-block">
                    <input type="text" id="userName" class="layui-input" style="border: none" readonly>
                    <?php select_value($userName, 'userName'); ?>
                </div>
            </div>
        </div>
        <div>
            <div class="layui-inline" style="margin-top: 20px">
                <label class="layui-form-label"><span>新密码:</label>
                <div class="layui-input-block">
                    <input type="text" id="userPassword" name="userPassword"
                           class="layui-input" placeholder="输入密码"
                           lay-verify="required" required>
                </div>
            </div>
        </div>
        <div style="margin-top: 20px;margin-left: 50px" class="layui-block">
            <button class="layui-btn layui-btn-normal changePass"  lay-submit lay-filter=""
                    type="button">确定
            </button>
        </div>
    </form>
</div>
<script src="../theme/js/oa_staff.js"></script>
<script>
    $( function () {
        layui.use( 'form', function () {  //此段代码必不可少
            var form = layui.form;
            form.render();
        } );
    } )
</script>
</body>
</html>
