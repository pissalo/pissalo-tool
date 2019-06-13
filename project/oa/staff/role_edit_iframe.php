<?php
/**
 * abstract:角色编辑
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2018年12月20日
 * Time:11:09:01
 */
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '角色编辑';
$pagePermissionId = 1;
@include_once('../include/common.inc.php');
session_start();
require_once('../yz.php');

$cls_role = new \OA\ClsRole();
if ('edit' == $type) {
    $role_info = $cls_role -> getRoleInfo(
        array (
            'where' => "r_id = {$role_id}"
        )
    );
    $role_info = $role_info['msg'];
}
$cls_es = new \OA\ClsEnterpriseStructure();
//获取部门
$department_list = $cls_es -> getEsInfo(array ( "es_type = 2" ));
$department_list = $department_list['msg'];

//获取组织架构
$cls_es->esList = array();
$es_list = $cls_es -> getEsList();
$es_list = $es_list['msg'];
foreach ($es_list as $es_key => $es_infos) {
    //去掉分组
    if ($es_infos['es_type'] == ES_TYPE_GROUP) {
        unset($es_list[$es_key]);
    }
    //去掉集团
    if ($es_infos['es_level'] == 1) {
        unset($es_list[$es_key]);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
</head>
<body>
<div>
    <input type="hidden" name="range_str" id="range_str" value="<?php echo $role_info[0]['r_use_range'];?>">
    <from id="role_from" class="layui-form">
        <input type="hidden" name="type" id="type" value="<?php echo $type; ?>">
        <input type="hidden" name="eidt_role_id" id="eidt_role_id" value="<?php echo $role_id; ?>">
        <div class="layui-block" style="width: 70%">
            <label class="layui-form-label"><span style="color: red">*  </span>角色名称：</label>
            <div class="layui-input-block">
                <input type="text" id="r_name" name="r_name"
                        class="layui-input" placeholder="输入角色名"
                        lay-verify="required" required>
            </div>
        </div>
        <?php select_value($role_info[0]['r_name'], 'r_name'); ?>
        <div class="layui-block" style="margin-top: 10px;width: 70%" id="noneLayui">
            <label class="layui-form-label"><span style="color: red">*  </span>所属部门：</label>
            <div class="layui-input-block">
                <select name="r_belong_department" id="r_belong_department" lay-verify="required" required lay-search="">
                    <option value="">请选择</option>
                    <?php
                    foreach ($es_list as $es_keys => $es_info) {
                        $ad_str = '';
                        $disable = '';
                        if ($es_info['es_type'] != ES_TYPE_DEPARTMENT) {
                            $disable = ' disabled="" ';
                        }
                        if ($es_info['es_type'] == ES_TYPE_COMPANY) {
                            echo "<optgroup label='{$es_info['es_name']}'>";
                        } elseif ($es_info['es_type'] == ES_TYPE_DEPARTMENT) {
                            ?>
                            <option value="<?php echo $es_info['es_id']; ?>"
                                    style="margin-left: 20px" <?php echo $disable; ?>><?php echo $ad_str . $es_info['es_name']; ?></option>
                            <?php
                        }
                        if ($es_info['es_type'] == ES_TYPE_COMPANY && $es_keys != 0) {
                            echo "</optgroup>";
                        }
                    } ?>
                </select>
            </div>
        </div>
        <?php select_value($role_info[0]['r_belong_department'], 'r_belong_department'); ?>
        <div class="layui-block" style="margin-top: 10px;width: 70%" id="noneLayui">
            <label class="layui-form-label"><span style="color: red">*  </span>应用范围：</label>
            <div class="layui-input-block">
                <select name="r_use_range" xm-select="select1" lay-verify="required" required  xm-select-search="" xm-select-height="50px" xm-select-show-count="2">
                    <?php
                    foreach ($es_list as $es_keys => $es_info) {
                        $ad_str = '';
                        $disable = '';
                        if ($es_info['es_type'] != ES_TYPE_DEPARTMENT) {
                            $disable = ' disabled="" ';
                        }
                        if ($es_info['es_type'] == ES_TYPE_COMPANY) {
                            echo "<optgroup label='{$es_info['es_name']}'>";
                        } elseif ($es_info['es_type'] == ES_TYPE_DEPARTMENT) {
                            ?>
                            <option value="<?php echo $es_info['es_id']; ?>"
                                    style="margin-left: 20px" <?php echo $disable; ?>><?php echo $ad_str . $es_info['es_name']; ?></option>

                            <?php
                        }
                        if ($es_info['es_type'] == ES_TYPE_COMPANY && $es_keys != 0) {
                            echo "</optgroup>";
                        }
                    } ?>
                </select>
            </div>
        </div>
        <?php select_value($role_info[0]['r_use_range'], 'r_use_range'); ?>
        <div class="layui-block" style="margin-top: 10px">
            <label class="layui-form-label">职责描述:</label>
            <div class="layui-input-block">
                <textarea name="r_note" id="r_note" cols="25" rows="6"
                        style="resize: none;width: 50%" required="required" required
                ><?php echo $role_info[0]['r_note']; ?></textarea>
            </div>
        </div>
        <div class="layui-block" style="margin-top: 10px">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" id="add_role_btn" lay-submit lay-filter="role_add_submit" type="button" >确定</button>
                <!--<input type="button" class="layui-btn layui-btn-normal" id="add_role_btn" lay-submit lay-filter="role_add_submit" value="确定">-->
            </div>
        </div>
    </from>
</div>
<?php
?>
<script src="../theme/js/formSelects-v4.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="../theme/css/formSelects-v4.css">
<script>
    $( function () {
        layui.use( 'form', function () {  //此段代码必不可少
            var form = layui.form;
            form.render();//更新

            //监听提交
            form.on( 'submit(role_add_submit)', function ( data ) {
                var type = $( '#type' ).val();
                var action = 'addRole';
                var role_id = $( '#eidt_role_id' ).val() ? $( '#eidt_role_id' ).val() : 0;
                if ( 'edit' == type )
                {
                    action = 'editRole';
                }
                var data = {
                    role_name: $( '#r_name' ).val(),
                    role_note: $( '#r_note' ).val(),
                    role_use_range: $( "input[name='r_use_range']" ).val(),
                    role_belong_department: $( '#r_belong_department' ).val(),
                    role_id: role_id,
                }
                var url = "/c.php?m=Staff&f=" + action;
                ajax_post( data, url );
                return false;
            } );
            
            //应用范围选项
            var range_str = $('#range_str').val();
            if( range_str )
            {
                var tmp = range_str.split(',');
                layui.formSelects.value('select1',tmp );
            }
        } );
        
    } )
</script>
</body>
</html>
