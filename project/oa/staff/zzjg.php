<?php
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '组织架构列表';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_STAFF_ZZJG;
session_start();
require_once('../yz.php');

//权限判断
if (!in_array($pagePermissionId, $adminOptionPer)) {
    show_msg('您没有该页面的权限!', 2);
}

$clsUser = new \OA\ClsUser();
//获取组织结构
$clsEs = new \OA\ClsEnterpriseStructure();
$clsEs->esList = array();
$esTreeList = $clsEs->getEsListTree();
//$esList = $clsEs -> getEsList();
//$esList = $esList[ 'msg' ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div id="framework">
    <!--顶部部分-->
    <?php require_once('../header.php'); ?>
    <!--头部end-->
    <div id="frameworkMain">
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
            <legend><?php echo $pageTitle; ?></legend>
        </fieldset>
        <input type="hidden" name="aa" value="<?php echo $pageTitles; ?>">
        <div style="margin-left:10px;margin-bottom: 10px;height: 30px">
            <input type="button" class="layui-btn layui-btn-normal layui-btn-sm zzjg_log_btn" value="日志"
                   style="float: right;margin-right: 50px">
        </div>
        <div class="framework-table">
            <div class="table">
                <div class="table-header-group">
                    <ul class="table-row">
                        <li class="table-cell" style="width: 50%">部门层级</li>
                        <li class="table-cell" style="width: 10%">在职（直属/总共）</li>
                        <li class="table-cell" style="width: 10%">部门负责人</li>
                        <li class="table-cell" style="width: 30%">操作</li>
                    </ul>
                </div>
                <div>
                    <?php
                    foreach ($esTreeList as $key => $esInfo) {
                        showSonList($esInfo);
                    }
                    /**
                     * 显示组织列表
                     * @param $sonList 组织列表
                     */
                    function showSonList($sonList)
                    {
                        global $adminOptionPer;
                        //按钮名称
                        $btn_name_arr = array(
                            1 => '新建下属公司',
                            2 => '新建下属部门',
                        );
                        $clsUser = new \OA\ClsUser();
                        $clsEs = new \OA\ClsEnterpriseStructure();
                        foreach ($sonList as $sonInfo) {
                            $leader_user_name = $clsUser->getUserInfoById($sonInfo['es_leader_user_id']);
                            $num = $clsEs->getEsNum($sonInfo['es_id']);
                            if ($sonInfo['es_level'] != 1) {
                                echo '<div class="include heightActive">';
                            }
                            ?>
                            <ul class="same-ul">
                                <li class="rank-<?php echo $sonInfo['es_level']; ?>" style="width: 50%">
                                    <button class="layui-btn layui-btn-xs layui-btn layui-btn-primary include-btn">+
                                    </button><?php echo $sonInfo['es_name']; ?>
                                </li>
                                <li style="width: 10%"> <?php echo $num['msg'] ? $num['msg'] : 0; ?></li>
                                <li style="width: 10%"><?php echo $leader_user_name['msg'][0]['u_username']; ?></li>
                                <li style="width: 30%">
                                    <?php if (in_array(PRE_STAFF_ZZJG_ADD, $adminOptionPer)) { ?>
                                        <button class="layui-btn layui-btn-sm layui-btn add-btn"
                                                id="<?php echo 'esid_' . $sonInfo['es_id'] . '_level_' . $sonInfo['es_level']; ?>">
                                            <?php if (1 == $sonInfo['es_level']) {
                                                echo $btn_name_arr[1];
                                            } else {
                                                echo $btn_name_arr[2];
                                            } ?></button>
                                    <?php } ?>
                                    <?php
                                    if (1 != $sonInfo['es_level']) {
                                        if (in_array(PRE_STAFF_ZZJG_EDIT, $adminOptionPer)) {
                                            ?>
                                            <button class="layui-btn layui-btn-sm layui-btn layui-btn-normal edi-btn"
                                                    id="<?php echo 'es_' . $sonInfo['es_id']; ?>">编辑
                                            </button>
                                            <?php
                                        }
                                        if (in_array(PRE_STAFF_ZZJG_DELETE, $adminOptionPer)) {
                                            ?>
                                            <button class="layui-btn layui-btn-sm layui-btn layui-btn-danger del-btn"
                                                    id="delete_role" name="<?php echo $sonInfo['es_id']; ?>">删除
                                            </button>
                                            <?php
                                        } ?>
                                        <?php
                                    } ?>
                                </li>
                            </ul>
                            <?php
                            if ($sonInfo['children']) {
                                showSonList($sonInfo['children']);
                            }
                            echo '</div>';
                        }
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once('../footer.php');
?>
<script src="../theme/js/oa_staff.js"></script>
<script>
    $(function () {
        $(".same-ul>li").height($(".rank-1").height())
        //默认自动展开
        $('.include-btn').each(function () {
            var include = $(this).parents(".same-ul").next(".include");
            include.addClass("heightActive");
            $(this).text("-")
        })
    })
</script>
</body>
</html>