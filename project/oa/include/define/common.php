<?php
//用户在状态
define('USER_STATUS_ZZ', 1);
define('USER_STATUS_LZ', 2);
define('USER_STATUS_TXLZ', 3);

$user_status_list = array(USER_STATUS_ZZ => '在职', USER_STATUS_LZ => '离职', USER_STATUS_TXLZ => '停薪留职');

$module_config_list = array(
    'Index' => '首页',
    'Staff' => '员工权限',
    'System' => '系统配置',
    'Tools' => '员工工具',
);

//审核状态
define('SH_WSH', 2);
define('SH_YSH', 1);
define('SH_SHFAILED', 3);
$sh_list = array(SH_YSH => '已审核', SH_SHFAILED => '审核失败', SH_WSH => '未审核');

require_once('zt.php'); //帐套
require_once('menu.php'); //菜单

//组织结构类型
define('ES_TYPE_COMPANY', 1);
define('ES_TYPE_DEPARTMENT', 2);
define('ES_TYPE_GROUP', 3);
define('ES_TYPE_TEAM', 4);
$es_type_list = array(
    ES_TYPE_COMPANY => '公司',
    ES_TYPE_DEPARTMENT => '部门',
    ES_TYPE_GROUP => '科室',
    ES_TYPE_TEAM => '组（队）'
);

//职位 董事长 总裁 总经理 副总经理 总监  经理 副经理 主管  组长 普通员工
define('POSITION_NAME_DSZ', 1);
define('POSITION_NAME_ZC', 2);
define('POSITION_NAME_ZJL', 3);
define('POSITION_NAME_FZJL', 4);
define('POSITION_NAME_ZJ', 5);
define('POSITION_NAME_JL', 6);
define('POSITION_NAME_FJL', 7);
define('POSITION_NAME_ZG', 8);
define('POSITION_NAME_ZZ', 9);
define('POSITION_NAME_YG', 10);
$position_list = array(
    POSITION_NAME_DSZ => '董事长',
    POSITION_NAME_ZC => '总裁',
    POSITION_NAME_ZJL => '总经理',
    POSITION_NAME_FZJL => '副总经理',
    POSITION_NAME_ZJ => '总监',
    POSITION_NAME_JL => '经理',
    POSITION_NAME_FJL => '副经理',
    POSITION_NAME_ZG => '主管',
    POSITION_NAME_ZZ => '组长',
    POSITION_NAME_YG => '普通员工',
);

//政治面貌 politics
define('POLITICS_STATUS_DY', 1);
define('POLITICS_STATUS_TY', 2);
define('POLITICS_STATUS_QZ', 3);
$politics_status_list = array(
    POLITICS_STATUS_DY => '中共党员',
    POLITICS_STATUS_TY => '共青团员',
    POLITICS_STATUS_QZ => '群众',
);

//员工性质
define('USER_CHARACTER_HTG', 1);
define('USER_CHARACTER_LWG', 2);
define('USER_CHARACTER_LSG', 3);
$userCharacterList = array(
    USER_CHARACTER_HTG => '合同工',
    USER_CHARACTER_LWG => '劳务工',
    USER_CHARACTER_LSG => '临时工',
);
//审批方式
define('SPL_METHOD_SORT', 1);
define('SPL_METHOD_ALL', 2);
define('SPL_METHOD_SOME', 3);
$splMethodList = array(
    SPL_METHOD_SORT => '依次审核',
    SPL_METHOD_ALL => '会签',
    SPL_METHOD_SOME => '或签',
);

//审核类型
define('SPL_CHECK_TYPE_SUP', 1);
define('SPL_CHECK_TYPE_USER', 2);
define('SPL_CHECK_TYPE_SELF', 3);
$splCheckTypeList = array(
    SPL_CHECK_TYPE_SUP => '上级',
    SPL_CHECK_TYPE_USER => '指定成员',
    SPL_CHECK_TYPE_SELF => '发起人自己',
);

//审批流状态
define('APPROVAL_STATUS_UNCHECK', 1);
define('APPROVAL_STATUS_CHECKING', 2);
define('APPROVAL_STATUS_BACK', 3);
define('APPROVAL_STATUS_END', 4);
define('APPROVAL_STATUS_CANCEL', 5);
$approvalStatusList = array(
    APPROVAL_STATUS_UNCHECK => '未审核',
    APPROVAL_STATUS_CHECKING => '审核中',
    APPROVAL_STATUS_BACK => '打回',
    APPROVAL_STATUS_END => '完成',
    APPROVAL_STATUS_CANCEL => '已撤销',
);
