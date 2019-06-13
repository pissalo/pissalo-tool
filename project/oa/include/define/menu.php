<?php
//本配置非常的核心 导航条及权限管理都通过这个生成
//user_permission会根据这个配置来生成
//配置菜单  菜单的php文件名 命名是 模块名.php
//每个子菜单的模板是：
/*
 array(
        'title'=> '系统首页',//标题
        'permission_id'=> PRE_TOOLS,//权限ID 对应user_permission表里的up_define_name 请用PRE开头 下级请以上级的 define值开头,比如员工工具的id是PRE_TOOLS 那下级的生成配置则为PRE_TOOLS_SCPZ 再下级的则是PRE_TOOLS_SCPZ_XX  后面的以拼音首字母开头 比如组织架构则为PRE_STAFF_ZZJG
        'define'=> 'PRE_TOOLS',//define值 字符和permission_id一样 与permission_id不一样的是，这个要用''包起来
        'href'=> '',//链接地址
        'top_url'=> '',//菜单url 只针对顶级菜单 和href的区别是href只针对文字生效,但top_url是针对div生效
        'class'=> 'layui-icon-home',//菜单class 只针对顶级菜单
        ’has_data_report'=> 1,//是不是有数据报表 默认为0
        ’target'=> '',//打开方式是新页面还是当前页面，默认为空(即当前页面打开)
        'sub'=> array( 子菜单array )
    )
 */
$menu_config = array();
foreach ($module_config_list as $module_key => $module_name) {
    $module_key = strtolower($module_key);
    require_once('menu/' . $module_key . '.php');
}
