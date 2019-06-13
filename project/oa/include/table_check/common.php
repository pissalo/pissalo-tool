<?php
/**
 * 通用的规则
 */
$table_check = array();

$table_check['add_time'] = array( 'label'=> '添加时间', 'max'=> 4099737600, 'min'=> 1385913600 );
$table_check['update_time'] = array( 'label'=> '更新时间', 'max'=> 4099737600, 'min'=> 1543680000 );
$table_check['approval_status'] = array( 'label'=> '审核状态', 'in_value'=> '0,1,2' );
$table_check['add_user_id'] = array( 'label'=> '添加人', 'max'=> 100000, 'min'=> 0 );
$table_check['zt_id'] = array( 'label'=> '帐套ID', 'max'=> 100000, 'min'=> 0 );
