<?php
/**
 * abstract:审批主页
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年2月23日
 * Time:10:16:40
 */
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '审批列表';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_APPROVAL_LIST;
session_start();
require_once('../yz.php');
if (!in_array($pagePermissionId, $adminOptionPer)) {
    show_msg('你没有该页面的权限!', 2);
}

//获取用户信息
$clsUser = new \OA\ClsUser();
$userInfo = $clsUser -> getUserInfoById($adminId);

//获取审批类型。
$clsApprovalConfig = new \OA\ClsApprovalConfig();
$configList = $clsApprovalConfig -> getApprovalConfigList(
    array (
        'where' =>
            " find_in_set({$userInfo['msg'][0]['u_es_id']},ac_use_range) " .
            " and ac_approval_status = 1"
    )
);
$configList = $configList['msg'];
?>
<!doctype html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
    <style>
        .apply_btn:hover {
            border: blue;
        }
    </style>
</head>
<body>
<?php require_once('../header.php'); ?>
<div>
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
        <legend><?php echo $pageTitle; ?></legend>
    </fieldset>
    <input type="hidden" value="<?php echo $adminId; ?>" id="myUserId">
    <input type="hidden" id="tabId">
    <div id="searchDiv" hidden>
        <div class="layui-form-item">
            <div class="layui-form-label">搜索:</div>
            <div class="layui-input-inline">
                <select name="searchKey" id="searchKey" class="layui-input">
                    <option value="">请选择</option>
                    <option value="1">申请人</option>
                    <option value="2">审批编号</option>
                </select>
            </div>
            <?php select_value($searchKey, 'searchKey'); ?>
            <div class="layui-input-inline" style="">
                <input type="text" class="layui-input" style="margin-left: 10px" id="searchValue" name="searchValue">
            </div>
            <?php select_value($searchValue, 'searchValue'); ?>
            <div class="layui-form-label">审批类型:</div>
            <div class="layui-input-inline">
                <select name="acId" id="acId" class="layui-input-inline layui-input">
                    <option value="">请选择</option>
                    <?php foreach ($configList as $configInfo) { ?>
                        <option value="<?php echo $configInfo['ac_id']; ?>"><?php echo $configInfo['ac_spl_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="layui-input-inline">
                <input type="button" onclick="searchApprovalInfo(null,2)" value="搜索" class="layui-btn ">
            </div>
        </div>
    </div>
    <hr>
    <div class="layui-tab" style="text-align: center" lay-filter="approvalIndex">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="1">发起审批</li>
            <li lay-id="2">待我审批</li>
            <li lay-id="3">我已审批</li>
            <li lay-id="4">我发起的</li>
            <li lay-id="5">抄送我的</li>
        </ul>
        <div class="layui-tab-content">
            <!--申请审批-->
            <div class="layui-tab-item layui-show">
                <div style="width: 100%;height: 100%;">
                    <?php
                    $count = 0;
                    foreach ($configList as $configInfo) {
                        $count++;
                        $approval_name = $configInfo['ac_spl_name'];
                        if (strlen($approval_name) > 20) {
                            $approval_name = my_substr($approval_name, 0, 20) . '...';
                        }
                        ?>
                        <input type="button" class="layui-btn layui-btn-primary "
                                value="<?php echo $approval_name; ?>"
                                title="<?php echo $configInfo['ac_spl_name']; ?>"
                                onclick="applyApproval(<?php echo $configInfo['ac_id']; ?>)"
                                style="word-break: break-all;height: 100px;width: 150px;margin-top: 5px">
                        <?php
                        if (5 == $count) {
                            $count = 0;
                            echo '<hr>';
                        }
                    } ?>
                </div>
            </div>
            <!--待我审批-->
            <div class="layui-tab-item">
                <table class="layueTable layui-table"
                        id="waitMeCheckList" lay-filter="waitMeCheckList"
                        name="waitMeCheckList">
                </table>
            </div>
            <!--我已审批-->
            <div class="layui-tab-item">
                <table class="layueTable layui-table"
                        id="myCheckList" lay-filter="myCheckList"
                        name="myCheckList">
                </table>
            </div>
            <!--我发起的-->
            <div class="layui-tab-item">
                <table class="layueTable layui-table"
                        id="myApplyList" lay-filter="myApplyList"
                        name="myApplyList">
                </table>
            </div>
            <!--抄送我的-->
            <div class="layui-tab-item">
                <table class="layueTable layui-table"
                        id="copyToMeList" lay-filter="copyToMeList"
                        name="copyToMeList">
                </table>
            </div>
        </div>
    </div>
</div>
<!--<fieldset class="layui-elem-field layui-field-title" style="margin-top: 50px;">-->
<!--    <legend>动态操作Tab</legend>-->
<!--</fieldset>-->
<!---->
<!--<div class="layui-tab" lay-filter="demo" lay-allowclose="true">-->
<!--    <ul class="layui-tab-title">-->
<!--        <li class="layui-this" lay-id="11">网站设置</li>-->
<!--        <li lay-id="22">用户管理</li>-->
<!--        <li lay-id="33">权限分配</li>-->
<!--        <li lay-id="44">商品管理</li>-->
<!--        <li lay-id="55">订单管理</li>-->
<!--    </ul>-->
<!--    <div class="layui-tab-content">-->
<!--        <div class="layui-tab-item layui-show">内容1</div>-->
<!--        <div class="layui-tab-item">内容2</div>-->
<!--        <div class="layui-tab-item">内容3</div>-->
<!--        <div class="layui-tab-item">内容4</div>-->
<!--        <div class="layui-tab-item">内容5</div>-->
<!--    </div>-->
<!--</div>-->
<!--<div class="site-demo-button" style="margin-bottom: 0;">-->
<!--    <button class="layui-btn site-demo-active" data-type="tabAdd">新增Tab项</button>-->
<!--    <button class="layui-btn site-demo-active" data-type="tabDelete">删除：商品管理</button>-->
<!--    <button class="layui-btn site-demo-active" data-type="tabChange">切换到：用户管理</button>-->
<!--</div>-->
<?php
require_once('../footer.php');
?>
<script src="/theme/js/formSelects-v4.js"></script>
<script src="/theme/js/oa_approval.js"></script>
<script>
    layui.use( 'element', function () {
        var $ = layui.jquery
            , element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

        //触发事件
        var active = {
            tabChange: function () {
                //切换到指定Tab项
                var tabId = $( '#tabId' ).val();
                tabId = parseInt( tabId ) + 1;
                element.tabChange( 'approvalIndex', tabId ); //切换到：用户管理
            }
        };
        $( '.site-demo-active' ).on( 'click', function () {
            var othis = $( this ), type = othis.data( 'type' );
            active[ type ] ? active[ type ].call( this, othis ) : '';
        } );
    } );
</script>
</body>
</html>
