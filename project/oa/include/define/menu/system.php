<?php
$menu_config['system'] =
    array(
        'title'=> '系统配置',
        'permission_id'=> PRE_SYSTEM,
        'define'=> 'PRE_SYSTEM',
        'href'=>'javascript:;',
        'class'=> 'layui-icon-set',
        'sub'=> array(
            array(
                'title'=> '子系统配置',
                'permission_id'=> PRE_SYSTEM_ZXTPZ,
                'define'=> 'PRE_SYSTEM_ZXTPZ',
                'href'=>'/system/system_sub.php',
            ),
            array(
                'title'=> '公司列表配置',
                'permission_id'=> PRE_SYSTEM_GCLBPZ,
                'define'=> 'PRE_SYSTEM_GCLBPZ',
                'href'=>'/system/company_list.php',
            ),
            array(
                'title' => '审批流配置',
                'permission_id' => PRE_APPROVAL_CONFIG,
                'define' => 'PRE_APPROVAL_CONFIG',
                'href' => '/system/approval_config_list.php',
            ),
        ),
    );
