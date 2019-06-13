<?php
$table_check['name'] = array( 'is_empty'=> 0 );
$table_check['define_name'] = array( 'is_empty'=> 0 );
$table_check['system_id'] = array( 'max'=> 10000, 'min'=> 0 );
$table_check['third_system_id'] = array( 'max'=> 10000, 'min'=> 0 );
$table_check['source'] = array( 'in_value'=> 'system,push' );
$table_check['type'] = array( 'in_value'=> '1,2' );
