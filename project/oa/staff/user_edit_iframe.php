<?php
$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
@include_once('../include/common.inc.php');
$pagePermissionId = PRE_STAFF_YGGL_EDIT;
session_start();
require_once('../yz.php');

//获取所有用户列表
$clsUser = new \OA\ClsUser();
$user_list = $clsUser->getUserInfo(
    array(
        'where' =>
            array("u_status != 0")
    )
);
$user_list = $user_list['msg'];

//获取公司列表
$cls_es = new \OA\ClsEnterpriseStructure();
$es_company_list = $cls_es->getEsInfo(
    array(
        "es_type = " . ES_TYPE_COMPANY,
        "es_level != 1"
    )
);
$es_company_list = $es_company_list['msg'];

//获取部门列表
$es_department_list = $cls_es->getEsInfo(
    array(
        "es_type = " . ES_TYPE_DEPARTMENT
    )
);
$es_department_list = $es_department_list['msg'];

$read_only = '';
$can_not_save = false;
if ('edit' == $type || 'look' == $type) {
    $user_info = $clsUser->getUserInfo(
        array(
            'where' => array("u_id = {$u_id}"),
            'join' => 'left join oa_user_addition on ua_u_id = u_id'
        )
    );
    $user_info = $user_info['msg'][0];
    $read_only = ' readonly ';
    $es_where[] = "es_sup_id = {$user_info['u_department_id']}";
    if ('look' == $type) {
        $can_not_save = true;
    }
}
$es_where[] = "es_type = " . ES_TYPE_GROUP;
//获取组列表
$es_group_list = $cls_es->getEsInfo($es_where);
$es_group_list = $es_group_list['msg'];

//获取所属公司列表
$clsConfig = new \OA\ClsConfig();
$companyNameList = $clsConfig->getCompanyList(array('order' => 'cc_id desc', 'col' => 'cc_id,cc_name'));
$companyNameList = change_main_key($companyNameList['msg'], 'cc_id');

//获取组织结构列表
$clsEs = new \OA\ClsEnterpriseStructure();
$esTree = $clsEs->getEsListTree();
$esTreeJson = json_encode($esTree[0][0]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div id="addStaff">
    <!--顶部部分-->
    <?php /*require_once( '../header.php' ); */ ?>
    <!--头部end-->
    <div class="add-staff-main">
        <input type="hidden" id="edit_user_department" value="<?php echo $user_info['u_department_id']; ?>">
        <input type="hidden" id="userEsId" value="<?php echo $user_info['u_es_id'];?>">
        <form id="addStaffForm" class="layui-form">
            <input type="hidden" name="type" id="type" value="<?php echo $type; ?>">
            <input type="hidden" name="u_id" id="u_id" value="<?php echo $u_id; ?>">
            <div class="">
                <div class="layui-inline">
                    <label class="layui-form-label"><span style="color: red">*  </span>姓名：</label>
                    <div class="layui-input-block">
                        <input type="text" id="u_name" name="u_name"
                               class="layui-input" placeholder="输入姓名"
                               lay-verify="required" required <?php echo $read_only; ?>>
                    </div>
                </div>
                <?php select_value($user_info['u_name'], 'u_name'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label">用户名：</label>
                    <div class="layui-input-block">
                        <input type="text" id="u_username" name="u_username"
                               class="layui-input" placeholder="不需要填写" readonly="readonly">
                    </div>
                </div>
                <?php select_value($user_info['u_username'], 'u_username'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label">籍贯：</label>
                    <div class="layui-input-block">
                        <input type="text" id="ua_native_place" name="ua_native_place"
                               class="layui-input" placeholder="输入籍贯">
                    </div>
                </div>
                <?php select_value($user_info['ua_native_place'], 'ua_native_place'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label"><span style="color: red">*  </span>组织：</label>
                    <div class="layui-input-block" style="width: 515px;">
                        <select name="organizationEdit" id="organizationEdit" xm-select="companyOrganizationEdit" xm-select-radio xm-select-search lay-verify="required" required>
                            <option value="">组织架构</option>
                        </select>
                    </div>
                </div>
                <?php select_value($user_info[''], ''); ?>
                <div class="layui-inline">
                    <label class="layui-form-label">工号：</label>
                    <div class="layui-input-block">
                        <input type="text" id="u_work_numb" name="u_work_numb"
                               class="layui-input" placeholder="不需要填写" readonly="readonly">
                    </div>
                </div>
                <?php select_value($user_info['u_work_numb'], 'u_work_numb'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label"><span style="color: red">*  </span>性别：</label>
                    <div class="layui-input-block">
                        <select id="ua_sex" name="ua_sex" lay-verify="required" required lay-search="">
                            <option value="">请选择</option>
                            <option value="男">男</option>
                            <option value="女">女</option>
                        </select>
                    </div>
                </div>
                <?php select_value($user_info['ua_sex'], 'ua_sex'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label">毕业学校：</label>
                    <div class="layui-input-block">
                        <input type="text" id="ua_college" name="ua_college"
                               class="layui-input" placeholder="输入毕业学校">
                    </div>
                </div>
                <?php select_value($user_info['ua_college'], 'ua_college'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label"><span style="color: red">*  </span>职位：</label>
                    <div class="layui-input-block">
                        <select id="u_position_id" name="u_position_id" lay-verify="required" required lay-search="">
                            <option value="">请选择</option>
                            <?php foreach ($position_list as $position_id => $position_name) { ?>
                                <option value="<?php echo $position_id; ?>"><?php echo $position_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php select_value($user_info['u_position_id'], 'u_position_id'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label">政治面貌：</label>
                    <div class="layui-input-block">
                        <select id="ua_politics_status" name="ua_politics_status" lay-search="">
                            <option value="0">请选择</option>
                            <?php foreach ($politics_status_list as $ps_id => $ps_name) { ?>
                                <option value="<?php echo $ps_id; ?>"><?php echo $ps_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php select_value($user_info['ua_politics_status'], 'ua_politics_status'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label"><span style="color: red"></span>所属公司：</label>
                    <div class="layui-input-block">
                        <select id="ua_belong_company" name="ua_belong_company" lay-search="">
                            <option value="0">请选择</option>
                            <?php foreach ($companyNameList as $companyInfo) { ?>
                                <option
                                    value="<?php echo $companyInfo['cc_id']; ?>"><?php echo $companyInfo['cc_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php select_value($user_info['ua_belong_company'], 'ua_belong_company'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label">入职时间：</label>
                    <div class="layui-input-block">
                        <input type="text" id="ua_entry_time" name="ua_entry_time"
                               class="layui-input" placeholder="输入入职时间">
                    </div>
                </div>
                <?php select_value($user_info['ua_entry_time'] ? date('Y-m-d', $user_info['ua_entry_time']) : date('Y-m-d', time()), 'ua_entry_time'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label"><span style="color: red">*  </span>电话号码：</label>
                    <div class="layui-input-block">
                        <input type="text" id="ua_phone_num" name="ua_phone_num"
                               class="layui-input" placeholder="输入电话号码"
                               lay-verify="required" required <?php echo $read_only; ?>>
                    </div>
                </div>
                <?php select_value($user_info['ua_phone_num'], 'ua_phone_num'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label"><span style="color: red">*  </span>状态：</label>
                    <div class="layui-input-block">
                        <select id="u_status" name="u_status" lay-verify="required" required lay-search="">
                            <option value="">请选择</option>
                            <?php foreach ($user_status_list as $user_status_id => $user_status_name) { ?>
                                <option value="<?php echo $user_status_id; ?>"><?php echo $user_status_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php select_value($user_info['u_status'], 'u_status'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label"><span style="color: red">*  </span>直属领导：</label>
                    <div class="layui-input-block">
                        <select id="u_sup_id" name="u_sup_id" lay-verify="required" required lay-search="">
                            <option value="">请选择</option>
                            <?php foreach ($user_list as $user_infos) { ?>
                                <option
                                    value="<?php echo $user_infos['u_id']; ?>"><?php echo $user_infos['u_username']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php select_value($user_info['u_sup_id'], 'u_sup_id'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label"><span style="color: red"></span>员工性质：</label>
                    <div class="layui-input-block">
                        <select id="ua_user_character" name="ua_user_character" lay-search="">
                            <option value="0">请选择</option>
                            <?php foreach ($userCharacterList as $characterId => $userCharacter) { ?>
                                <option
                                        value="<?php echo $characterId; ?>"><?php echo $userCharacter; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php select_value($user_info['ua_user_character'], 'ua_user_character'); ?>
                <?php if ('add' == $type) { ?>
                    <div class="layui-inline">
                        <label class="layui-form-label"><span style="color: red">*  </span>密码：</label>
                        <div class="layui-input-block">
                            <input type="text" class="layui-input" id="u_password" name="u_password"
                                   lay-verify="required" required>
                        </div>
                    </div>
                <?php } ?>
                <?php select_value($user_info['role'], 'role'); ?>
            </div>
            <?php if (!$can_not_save) { ?>
                <div class="add_staff_btn">
                    <button class="layui-btn layui-btn-normal" id="addStaffBtn" lay-submit lay-filter="form_submit"
                            type="button">确定
                    </button>
                </div>
            <?php } ?>
        </form>
    </div>
</div>
<?php
/*require_once( '../footer.php' );
*/ ?>
<script src="/theme/js/jquery.min.js"></script>
<script src="/theme/js/common.js"></script>
<script src="/theme/js/fSelect.js"></script>
<link rel="stylesheet" href="/theme/css/fSelect.css">
<script>
    $(function () {
        layui.use('form', function () {  //此段代码必不可少
            var form = layui.form;
            form.render();
        });
        $('.demo').fSelect();
        $("#noneLayui .layui-unselect").remove();
        layui.use('form', function () {
            var form = layui.form;
            //监听提交
            form.on('submit(form_submit)', function (data) {
                //console.log(JSON.stringify(data.field));
                var type = $('#type').val();
                var action = 'addUser'
                if ('edit' == type) {
                    action = 'editUser'
                }
                var url = "/c.php?m=Staff&f=" + action;
                ajax_post($("#addStaffForm").serialize(), url);
                return false;
            });
        });

        layui.use('laydate', function () {
            var laydate = layui.laydate;

            //执行一个laydate实例
            laydate.render({
                elem: '#ua_entry_time' //指定元素
            });
        });

        layui.use('form', function () {  //此段代码必不可少
            var form = layui.form;
            form.on('select(depart)', function (data) {
                console.log(1);
                $.post('/c.php?m=Staff&f=showUserGroupInfo', {department_id: data.value}, function (msg) {
                    var return_msg = JSON.parse(msg);
                    $('.group_option').remove();
                    form.render('select'); //刷新select选择框渲染
                    $('#u_group_id').append(return_msg.msg);
                    form.render('select'); //刷新select选择框渲染
                })
                form.render('select'); //刷新select选择框渲染
            });
        });
        layui.formSelects.data('companyOrganizationEdit', 'local', {
            arr: [<?php echo $esTreeJson;?>]
        });
        var userEsId = $('#userEsId').val();
        layui.formSelects.value('companyOrganizationEdit', [userEsId]);
    })
</script>
</body>
</html>
