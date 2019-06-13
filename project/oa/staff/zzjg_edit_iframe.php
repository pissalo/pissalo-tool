<?php
/**
 * abstract:添加子组织结构
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2018年12月18日
 * Time:16:52:44
 */
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '添加子组织结构';
$pagePermissionId = 1;
include_once('../include/common.inc.php');
session_start();
require_once('../yz.php');

$readonly = '';
if ('edit' == $type) {
    $clsEs = new \OA\ClsEnterpriseStructure();
    $esInfo = $clsEs -> getEsInfo(array ( "es_id = {$es_id}" ));
    $esInfo = $esInfo['msg'][0];
    $readonly = ' readonly="readonly" ';
}
//获取用户列表
$clsUser = new \OA\ClsUser();
$userList = $clsUser -> getUserInfo();
$userList = $userList['msg'];
$es_type = $es_level > 4 ? 4 : $es_level ;   //新建组织类型
$es_level += 1;         //新建组织级别
$esInfo['es_type'] = $esInfo['es_type'] ? $esInfo['es_type'] : $es_type;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div>
    <form id="data-form" class="layui-form">
        <input type="hidden" name="es_sup_id" value="<?php echo $es_sup_id; ?>">
        <input type="hidden" name="es_id" value="<?php echo $es_id; ?>">
        <input type="hidden" name="es_level" value="<?php echo $es_level; ?>">
        <input type="hidden" name="type" value="<?php echo $type; ?>">
        <div class="layui-inline">
            <label class="layui-form-label">类别：</label>
            <div class="layui-input-block" style="width: 155px">
                <select id="es_type" name="es_type" lay-verify="required" required>
                    <option value="">请选择</option>
                    <?php
                    foreach ($es_type_list as $es_type_id => $es_type) {
                        $disable = '';
                        $select = ' selected="" ';
                        if ($esInfo['es_type'] != $es_type_id) {
                            $disable = ' disabled="" ';
                            $select = '';
                        }
                        ?>
                        
                        <option value="<?php echo $es_type_id; ?>"<?php echo $disable.$select;?> ><?php echo $es_type; ?></option>
                        <?php
                    } ?>
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">名称：</label>
            <div class="layui-input-block">
                <input type="text" class="layui-input" placeholder="输入名称"
                        name="es_name" lay-verify="required" id="es_name" lay-verify="required" required>
            </div>
        </div>
        <?php select_value($esInfo['es_name'], 'es_name'); ?>
        <?php if (1 == $esInfo['es_type']) { ?>
            <div class="layui-inline">
                <label class="layui-form-label">编号：</label>
                <div class="layui-input-block">
                    <input type="text" class="layui-input" placeholder="输入编号"
                            name="es_code" lay-verify="required" id="es_code"
                            lay-verify="required" required <?php echo $readonly;?>>
                </div>
            </div>
            <?php select_value($esInfo['es_code'], 'es_code'); ?>
        <?php } ?>
        <div class="layui-inline">
            <label class="layui-form-label">负责人：</label>
            <div class="layui-input-block" style="width: 155px">
                <select id="es_leader_user_id" name="es_leader_user_id">
                    <option value="0">请选择</option>
                    <?php foreach ($userList as $userInfo) { ?>
                        <option value="<?php echo $userInfo['u_id']; ?>"><?php echo $userInfo['u_username']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <?php select_value($esInfo['es_leader_user_id'], 'es_leader_user_id'); ?>
        <div class="pop-btn">
            <button class="layui-btn layui-btn-normal pop-sure-btn" id="handle_btn_id" lay-submit lay-filter="form_submit">确定</button>
        </div>
    </form>
</div>
<script>
    $( function () {
        layui.use( 'form', function () {  //此段代码必不可少
            var form = layui.form;
            form.render();
        } );

        //监听提交
        layui.use( 'form', function () {
            var form = layui.form;
            form.on( 'submit(form_submit)', function ( data ) {
                var url = "/c.php?m=Staff&f=addEnterpriseStructure";
                ajax_post( $( "#data-form" ).serialize(), url );
                return false;
            } );
        } )
    } )
    //添加
    //ajax_data( 'handle_btn_id', 'data-form', "/c.php?m=Staff&f=addEnterpriseStructure" );
</script>
</body>
</html>
