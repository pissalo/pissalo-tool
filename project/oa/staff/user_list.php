<?php
$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_STAFF_YGGL;
session_start();
require_once('../yz.php');
//权限判断
if (!in_array($pagePermissionId, $adminOptionPer)) {
    show_msg('您没有该页面的权限!', 2);
}

//解析读权限
$clsRolePermission = new \OA\ClsRolePermission();
$read_per = $clsRolePermission -> analysisReadPerForUid($adminReadPer[ $pagePermissionId ]);

$clsUser = new \OA\ClsUser();
//获取组织结构列表
$clsEs = new \OA\ClsEnterpriseStructure();
$esTree = $clsEs->getEsListTree();
$data = json_encode($esTree[0][0]);

/*p_r($clsEs->getSupEsList(111));
exit;*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div id="situation">
    <input type="hidden" id="read_per" value="<?php echo $read_per[ 'msg' ]; ?>">
    <!--顶部部分-->
    <?php require_once('../header.php'); ?>
    <!--头部end-->
    <div class="ao-manages-main">
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
            <legend><?php echo $pageTitle; ?></legend>
        </fieldset>
        <form action="" id="search_form" class="layui-form">
            <div class="layui-form">
                <label class="layui-form-label">搜索：</label>
                <div class="layui-inline">
                    <label class="layui-form-label">用户名：</label>
                    <div class="layui-input-block">
                        <input type="text" id="username" name="username" class="layui-input" placeholder="输入用户名">
                    </div>
                </div>
                <?php select_value($username, 'username'); ?>
                <div class="layui-inline" style="width: 30%">
                    <select name="organization" id="organization" xm-select="companyOrganization" xm-select-radio xm-select-search="">
                        <option value="">组织架构</option>
                    </select>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">状态：</label>
                    <div class="layui-input-block">
                        <select id="status" name="status" lay-search="">
                            <option value="">请选择</option>
                            <?php foreach ($user_status_list as $user_status_id => $user_status_name) { ?>
                                <option value="<?php echo $user_status_id; ?>"><?php echo $user_status_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php select_value($status, 'status'); ?>
                <div class="layui-inline">
                    <label class="layui-form-label">入职日期：</label>
                    <div class="layui-input-block">
                        <input type="text" placeholder="请选择日期" lay-key="1" class="layui-input" id="entry_time" name="entry_time">
                    </div>
                </div>
                <div class="layui-inline" style="margin-left: 35px">
                    <?php echo \OA\ClsView::GetSearchButton('搜索', 'UserListSearch()', 0, 'search_btn')['msg']; ?>
                    <!--<button class="layui-btn layui-btn-sm layui-btn-normal">导出</button>-->
                    <?php echo \OA\ClsView::GetAddButton('新增', "add_or_edit_user( 0, 'add' )", PRE_STAFF_YGGL_ADD)['msg']; ?>
                </div>
            </div>
        </form>
    </div>
    <?php if (in_array(PRE_STAFF_YGGL_IMPORT, $adminOptionPer)) { ?>
        <div style="margin-left: 40px">
            <button type="button" class="layui-btn layui-btn-sm" id="btnImportUser"><i class="layui-icon"></i>导入用户信息
            </button>
            <a href="../tpl/excel_user_import.xlsx">模板下载</a>
        </div>
    <?php } ?>
    <div>
        <table class="layueTable layui-table" id="allocation_table" lay-filter="allocation_table">
        </table>
    </div>
</div>
<script type="text/html" id="user_edit_option">
    <?php
    if (in_array(PRE_STAFF_YGGL_EDIT, $adminOptionPer)) { ?>
        <a class="layui-btn layui-btn-sm" lay-event="user_edit">编辑</a>
        <?php
    } else {
        ?>
        <a class="layui-btn layui-btn-sm" lay-event="user_look">查看</a>
        <?php
    } ?>
    <?php if (in_array(PRE_STAFF_YGGL_ALLOTROLE, $adminOptionPer)) { ?>
        <a class="layui-btn layui-btn-sm" lay-event="user_allot_role">分配角色</a>
    <?php } ?>
    <?php if (in_array(PRE_STAFF_CHANGE_PASSWORD, $adminOptionPer)) { ?>
    <a  class="layui-btn layui-btn-sm" lay-event="user_change_password">修改密码</a>
    <?php } ?>
    <a class="layui-btn layui-btn-sm" lay-event="user_edit_log">日志</a>
</script>
<?php
require_once('../footer.php');
?>
<script src="../theme/js/formSelects-v4.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="../theme/css/formSelects-v4.css">
<script src="../theme/js/oa_staff.js"></script>
<script>
    layui.formSelects.data('companyOrganization', 'local', {
        arr: [<?php echo $data; ?>],
        tree: {
            //在点击节点的时候, 如果没有子级数据, 会触发此事件
            /*nextClick: function(id, item, callback){
                //需要在callback中给定一个数组结构, 用来展示子级数据
                callback([
                    {name: 'test1', value: Math.random()},
                    {name: 'test2', value: Math.random()}
                ])
            },*/
        }
    });
</script>
<script>
    var table = layui.table;
    $(function () {
        layui.use( 'form', function () {  //此段代码必不可少
            var form = layui.form;
            form.render();//更新
        })
        tableInitUserList()
        //导入
        layui.use('upload', function () {
            var $ = layui.jquery
                , upload = layui.upload;
            upload.render({
                elem: '#btnImportUser'
                , url: '/c.php?m=Staff&f=importUserInfo'
                , accept: 'file' //普通文件
                , field: 'userFile'
                , done: function (res) {
                    if (1 == res.ack) {
                        layer.alert(res.msg, 5, function () {
                            window.location.reload();
                        })
                    } else {
                        layer.alert(res.msg, 3)
                    }
                }
            });
        })
    })
</script>
</body>
</html>
