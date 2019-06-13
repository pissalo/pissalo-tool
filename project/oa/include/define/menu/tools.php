<?php
$menu_config['tools'] =
    array(
        'title'=> '员工工具',
        'permission_id'=> PRE_TOOLS,
        'define'=> 'PRE_TOOLS',
        'href'=>'javascript:;',
        'class'=> 'layui-icon-util',
        'sub'=> array(
            array(
                'title'=> '开发者工具',
                'permission_id'=> PRE_TOOLS_KFZGJ,
                'define'=> 'PRE_TOOLS_KFZGJ',
                'href'=>'javascript:;',
                'sub'=> array(
                    array(
                        'title'=> 'Table生成器',
                        'permission_id'=> PRE_TOOLS_KFZGJ_TSCQ,
                        'define'=> 'PRE_TOOLS_KFZGJ_TSCQ',
                        'href'=>'/tools/mysql_create.php',
                    ),
                    array(
                        'title'=> '权限管理',
                        'permission_id'=> PRE_TOOLS_KFZGJ_QXGL,
                        'define'=> 'PRE_TOOLS_KFZGJ_QXGL',
                        'href'=>'/tools/permission_edit.php',
                    ),
                    array(
                        'title'=> '更新系统缓存',
                        'permission_id'=> PRE_TOOLS_KFZGJ_GXYTHC,
                        'define'=> 'PRE_TOOLS_KFZGJ_GXYTHC',
                        'href'=>'/tools/update_cache.php',
                    ),
                    array(
                        'title'=> 'API中心',
                        'permission_id'=> PRE_TOOLS_KFZGJ_APIZX,
                        'define'=> 'PRE_TOOLS_KFZGJ_APIZX',
                        'href'=>'/tools/api.php',
                    ),
                ),
            ),
        ),
    );
