<?php
$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_STAFF_ZWGL_BJQX;
session_start();
require_once('../yz.php');
//通过审批页面进入不需要权限。
$beforeUrl = $_SERVER['HTTP_REFERER'];
$tmpArr = explode('/', $beforeUrl);
$beforeFile = array_pop($tmpArr);
//权限判断
if (!in_array($pagePermissionId, $adminOptionPer) && $beforeFile != 'approval.php') {
    show_msg('您没有该页面的权限!', 2);
}
//p_r(getReqData());
$cls_config = new \OA\ClsConfig();
$oa_id_info = $cls_config->getSystemSubIdByName('OA');
$system_id = isset($system_id) ? $system_id : $oa_id_info['msg'];
$cls_role = new \OA\ClsRole();
$new_role_list = array();
if (!$approvalId) {
    //获取角色列表
    $role_list = $cls_role->getRoleInfo(
        array(
            'col' => 'oa_role.*,es_name',
            'order' => 'r_belong_department asc',
            'join' => 'left join oa_enterprise_structure on es_id = r_belong_department'
        )
    );
    foreach ($role_list['msg'] as $role_infos) {
        $new_role_list[$role_infos['es_name']][] = $role_infos;
    }
    //获取角色权限。
    $cls_rp = new \OA\ClsRolePermission();
    $role_permission_list = $cls_rp->getRolePermissionList($role_id, $system_id);
    //操作权限
    $role_per_id_arr = array_column($role_permission_list['msg']['option_per'], 'rp_option_per_id');
    $role_per_id_str = implode(',', $role_per_id_arr);   //操作权限字符串
    //读权限
    $role_read_per_list = $role_permission_list['msg']['read_per'];
    if ($role_read_per_list) {
        $read_per_id_arr = $cls_rp->analysisReadPer($role_read_per_list);
        $read_per_id_str = implode(',', $read_per_id_arr['msg']);
    }
} else {
    require_once(WEB_CLASS . '/approval/class.apply_permission.php');
    $clsApplyPermission = new \OA\ClsApplyPermission();
    $clsUserExtendPermission = new \OA\ClsUserExtendPermission();
    $perList = $clsApplyPermission->getPermissionByApprovalId($approvalId);
    //操作权限
    $role_per_id_str = implode(',', $perList['option_per']);   //操作权限字符串
    //读权限
    $cls_rp = new \OA\ClsRolePermission();
    $role_read_per_list = $perList['read_per'];
    if ($role_read_per_list) {
        $read_per_id_arr = $cls_rp->analysisReadPer($role_read_per_list);
        $read_per_id_str = implode(',', $read_per_id_arr['msg']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div>
    <?php require_once('../header.php'); ?>
    <!--添加统一头部-->

    <div id="permission">
        <input type="hidden" value="<?php echo $role_id; ?>" id="role_id_input">
        <input type="hidden" value="<?php echo $role_per_id_str; ?>" id="role_per_id_get">
        <input type="hidden" value="<?php echo $read_per_id_str; ?>" id="role_per_read_id">
        <!--角色选择-->
        <?php if (!$ac_id && !$approvalId) { ?>
            <div class="layui-form" style="width: 250px;margin-bottom: 10px">
                <select name="role_id" xm-select="select_role" lay-verify="required" required xm-select-search=""
                        xm-select-height="50px" xm-select-radio="" xm-select-skin="normal" disabled="disabled">
                    <?php
                    foreach ($new_role_list as $department_name => $new_role_info) { ?>
                    <option value="">请选择角色</option>
                    <optgroup label="<?php echo $department_name; ?>">
                        <?php foreach ($new_role_info as $role_info) { ?>
                            <option value="<?php echo $role_info['r_id']; ?>"><?php echo $role_info['r_name']; ?></option>
                            <?php
                        } ?>
                        <?php
                    } ?>
                </select>
            </div>
        <?php } ?>
        <div class="select-system">
            <span>选择系统：</span>
            <ul>
                <?php
                $cls_config = new \OA\ClsConfig();
                $list_ss = $cls_config->getSystemSubList(array('col' => 'css_name,css_id'));
                $list_ss = $list_ss['msg'];
                foreach ($list_ss as $info_ss) {
                    $class = '';
                    if ($system_id == $info_ss['css_id']) {
                        $class = 'active';
                    }
                    echo "<li onclick='window.location=\"permission.php?system_id={$info_ss['css_id']}&role_id={$role_id}\"' class='{$class}'>{$info_ss['css_name']}</li>";
                }
                ?>
            </ul>
        </div>
        <form action="" id="permission_form" onkeydown="if(event.keyCode==13) return false;">
            <input type="hidden" value="<?php echo $ac_id; ?>" id="ac_id" name="ac_id">
            <input type="hidden" name="role_id" value="<?php echo $role_id; ?>">
            <input type="hidden" name="system_id" value="<?php echo $system_id; ?>">
            <input type="hidden" value="<?php echo $type;?>" id="approvalType" name="approvalType">
            <input type="hidden" value="<?php echo $approvalId;?>" id="approvalId" name="approvalId">
            <div class="layui-form">
                <!--OA系统表格-->
                <div class="system-table active">
                    <table class="layui-table" lay-even="" lay-skin="row">
                        <thead>
                        <tr>
                            <th width="20%">权限名</th>
                            <th width="20%">查看权限</th>
                            <th width="20%">操作权限</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $cls_per = new \OA\ClsPermissions();
                        $list = $cls_per->getList($system_id, 0, 1, '', "up_type=2");
                        $list = $list['msg'];
                        if ($list) {
                            function show_per_list($list, $sup_id = 0)
                            {
                                global $clsPermissions, $cls_config, $system_id;
                                foreach ($list as $info) {
                                    $name_info = explode('_', $info['up_name']);
                                    ?>
                                    <tr>

                                        <td>
                                            <?php echo str_repeat("&nbsp;", ($info['class_level'] - 1) * 5); ?>
                                            <input type="checkbox" name="permission[]"
                                                   title="<?php echo $name_info[count($name_info) - 1]; ?>(<?php echo $info['up_third_system_id'] ?>)"
                                                   lay-skin="primary"
                                                   value='<?php echo $info['up_id']; ?>'
                                                <?php if ($sup_id) {
                                                    echo "check_child='{$sup_id}'";
                                                } ?>
                                                   lay-filter='permission_allot'
                                                   id="<?php echo 'page_per_' . $info['up_id']; ?>">
                                        </td>
                                        <td>
                                            <?php
                                            //牛蛙没有查看权限
                                            $bl_id_info = $cls_config->getSystemSubIdByName('牛蛙');
                                            if ($system_id != $bl_id_info['msg']['css_id']) {
                                                ?>
                                                <input type="checkbox" name="read_all[]"
                                                       title="查看全部" lay-skin="primary"
                                                       value="<?php echo $info['up_id']; ?>"
                                                       lay-filter='permission_allot'
                                                       check_child="<?php echo $info['up_id']; ?>"
                                                       read-per="<?php echo $info['up_id']; ?>"
                                                       id="read_all_<?php echo $info['up_id']; ?>"
                                                >
                                                <input type="checkbox" name="read_sub[]"
                                                       title="查看下级(自己)" lay-skin="primary"
                                                       value="<?php echo $info['up_id']; ?>"
                                                       lay-filter='permission_allot'
                                                       check_child="<?php echo $info['up_id']; ?>"
                                                       read-per="<?php echo $info['up_id']; ?>"
                                                       id="read_some_<?php echo $info['up_id']; ?>"
                                                >
                                                <?php
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            //得出下级的操作按钮
                                            $per_sub = $clsPermissions->selectEx(array('where' => "up_parent_id={$info['up_third_system_id']} and up_type=1"));
                                            $per_sub = $per_sub['msg'];
                                            if ($per_sub) {
                                                foreach ($per_sub as $info_sub) {
                                                    if (1 == $info_sub['up_type']) {
                                                        ?>
                                                        <input type='checkbox' name='permission[]'
                                                               title='<?php echo $info_sub['up_name']; ?>'
                                                               lay-skin='primary'
                                                               value='<?php echo $info_sub['up_id']; ?>'
                                                               lay-filter='permission_allot'
                                                               check_child="<?php echo $info['up_id']; ?>"
                                                               option-per="<?php echo $info['up_id']; ?>"
                                                               id="option_per_<?php echo $info_sub['up_id']; ?>"
                                                        >
                                                    <?php }
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    if ($info['sub_class']['ack'] && count($info['sub_class']['msg'])) {
                                        show_per_list($info['sub_class']['msg'], $info['up_id']);
                                    }
                                }
                            }

                            show_per_list($list);
                            ?>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if ($type) { ?>
                <div class="layui-inline">
                    <label class="layui-form-label">有效期：</label>
                    <div class="layui-input-block">
                        <input type="text" id="uep_valid_time" name="uep_valid_time"
                               class="layui-input" placeholder="权限截止时间" readonly="readonly"
                               lay-filler="uep_valid_time"
                               value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>">
                    </div>
                </div>
            <?php } ?>
        </form>

        <?php
        //查看审批禁止修改
        if ($type != 1) {
            ?>
            <div style="text-align: center">
                <button class="layui-btn permission-btn" lay-submit lay-filter="form_submit">保存</button>
            </div>
        <?php } ?>
    </div>
</div>
<?php
require_once('../footer.php');
?>
<script src="../theme/js/formSelects-v4.js" type="text/javascript" charset="utf-8"></script>
<script src="../theme/js/oa_tools.js"></script>
<link rel="stylesheet" href="../theme/css/formSelects-v4.css">
<script>
    $(function () {
        var form = layui.form;
        var role_id_input = $('#role_id_input').val();
        layui.formSelects.value('select_role', [role_id_input]);
        //初始化操作复选框。
        var per_id_str = $('#role_per_id_get').val();
        if (per_id_str) {
            var per_id_arr = per_id_str.split(',');
            $("input[name='permission[]']").each(function () {
                if ($.inArray(this.value, per_id_arr) != -1) {
                    this.checked = true;
                }
            })
        }
        //初始化读权限
        var per_read_id_str = $('#role_per_read_id').val();
        if (per_read_id_str) {
            var per_read_arr = per_read_id_str.split(',');
            for (var i = 0; i < per_read_arr.length; i++) {
                check_one_checkbox(per_read_arr[i], true);
            }
        }
        form.render('checkbox');

        layui.use('laydate', function () {
            var laydate = layui.laydate;

            //执行一个laydate实例
            laydate.render({
                elem: '#uep_valid_time' //指定元素
            });
        });
    })
</script>
</body>
</html>
