<?php
$curDir = dirname(__FILE__);
$pageAuthor = '王银龙';
$pageComment = '审批流配置编辑';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_STAFF_ZZJG;
session_start();
require_once('../yz.php');

$supLevelArr = array(
    '直接上级' => 1,
    '第二上级' => 2,
    '第三上级' => 3,
    '第四上级' => 4,
    '第五上级' => 5,
    '第六上级' => 6,
    '第七上级' => 7,
    '第八上级' => 8,
    '第九上级' => 9,
    '第十上级' => 10,
);

if ($approvalLevelInfo) {
    $tmpArr = explode('-', $approvalLevelInfo);
    $checkType = $tmpArr[0];
    if (1 == $checkType) {
        $supLevel = $supLevelArr[$tmpArr[1]];
    } elseif (2 == $checkType) {
        $userArr = explode(',', $tmpArr[1]);
        foreach ($userArr as $key => $userId) {
            if (!strstr($userId, 'u_')) {
                if (strstr($tmpArr[1], 'u_')) {
                    unset($userArr[$key]);
                } else {
                    $userArr[$key] = 'u_' . $userId;
                }
            }
        }
        $checkUserStr = implode(',', array_remove_empty($userArr));
    }
    $methodArr = array_flip($splMethodList);
    $checkMethod = $methodArr[$tmpArr[2]];
}
//echo $checkUserStr;exit;
//设置默认值
$checkType = $checkType ? $checkType : 1;
$checkMethod = $checkMethod ? $checkMethod : 1;
//获取组织结构列表
$clsEs = new \OA\ClsEnterpriseStructure();
$esTree = $clsEs->getEsUserListTree();
$esTreeJson = json_encode($esTree[0][0]);
$esOneList = $clsEs->getOneLevelEsInfo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div id="approval-pop" style="border: none">
    <input type="hidden" id="approvalUserStr" value="<?php echo $checkUserStr; ?>">
    <input type="hidden" id="maxLevel" value="<?php echo $maxLevel; ?>">
    <input type="hidden" id="action" value="<?php echo $action; ?>">
    <div class="approval-main-pop">
        <h5>请选择</h5>
        <div class="approval-main-pop-nav">
            <span class="fl">审批人类别：</span>
            <ul class="layui-form approval-pop-nav-list">
                <?php foreach ($splCheckTypeList as $typeId => $typeName) { ?>
                    <li>
                        <input type="radio" name="approval" value="<?php echo $typeName; ?>"
                               title="<?php echo $typeName; ?>"
                            <?php if ($typeId == $checkType) {
                                echo ' checked ';
                            } ?>
                               class="<?php echo $typeId; ?>">
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="approval-pop-main-list">
            <div class="approvalType_1">
                <div class="approval-pop-select">
                    <div class="approval-pop-select-main">
                        <span>发起人</span>
                        <select name="userSupLevel" id="userSupLevel">
                            <option value="直接上级">直接上级</option>
                            <option value="第二上级">第二上级</option>
                            <option value="第三上级">第三上级</option>
                            <option value="第四上级">第四上级</option>
                            <option value="第五上级">第五上级</option>
                            <option value="第六上级">第六上级</option>
                            <option value="第七上级">第七上级</option>
                            <option value="第八上级">第八上级</option>
                            <option value="第九上级">第九上级</option>
                            <option value="第十上级">第十上级</option>
                        </select>
                        <?php select_value($tmpArr[1], 'userSupLevel'); ?>
                    </div>
                </div>
            </div>
            <div class="approvalType_2">
                <div>
                    <div class="layui-form">
                        <select xm-select="splCheckUser" xm-select-search="" xm-select-search-type="dl"
                                xm-select-skin="normal">
                            <option value="">请选择审批人</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="approvalMethod">
                <div>
                    <span>多人审批时采用的审批方式：</span>
                    <ul class="layui-form">
                        <?php foreach ($splMethodList as $methodId => $methodName) { ?>
                            <li>
                                <input type="radio" name="countersign" value="<?php echo $methodName; ?>"
                                       title="<?php echo $methodName; ?>"
                                    <?php if ($checkMethod == $methodId) {
                                        echo ' checked="" ';
                                    } ?>>
                            </li>
                        <?php } ?>

                    </ul>
                </div>
                <hr>
                <!--<div class="approval-pop-last">
                    <div class="layui-form">
                        <input type="radio" value="若该审批人空缺，由其在通讯录中的上级主管代审批" title="若该审批人空缺，由其在通讯录中的上级主管代审批" checked>
                    </div>
                </div>-->
            </div>
            <div class="approvalType_3">
                <span>发起人自己将作为审批人处理审批单</span>
            </div>
        </div>
        <div class="approval-pop-btn">
            <button class="layui-btn layui-btn-normal layui-btn-sm add">确定</button>
        </div>
    </div>
</div>
</body>
<script src="/theme/layui/layui.all.js"></script>
<script src="../theme/js/formSelects-v4.js"></script>
<link rel="stylesheet" href="../theme/css/formSelects-v4.css">
<script src="../theme/js/oa_approval.js"></script>
<script>
    $(function () {
        //初始化select
        layui.formSelects.data('splCheckUser', 'local', {
            arr: [<?php echo $esTreeJson;?>]
        });
        //判断类型
        var type = $("input[name='approval']:checked").val();
        showApprovalLevelInfo(type);
        //赋值
        var useArr = $('#approvalUserStr').val().split(',');
        layui.formSelects.value('splCheckUser', useArr)
    })
</script>
</html>
