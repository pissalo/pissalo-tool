<?php
/**
 * 读取EXCEL
 * @param string $sheet_name
 * @return array
 * @throws PHPExcel_Exception
 * @throws PHPExcel_Reader_Exception
 */
function read( $sheet_name = '' )
{
    //if( filesize( $this->excel_name ) < 100 * 1024 )
    $obj_excel = PHPExcel_IOFactory::load( $this->excel_name );
    $sheet_count = $obj_excel->getSheetCount();
    $data = array();
    for ( $i = 0; $i < $sheet_count; $i++ )
    {
        $obj_sheet = $obj_excel->getSheet( $i );
        $sheet_title = $obj_sheet->getTitle();
        if ( $sheet_name && $sheet_name != $sheet_title )
        {
        } else
        {
            $data[ $i ][ 'name' ] = $obj_sheet->getTitle();
            $data[ $i ][ 'data' ] = $obj_sheet->toArray( null, true, true, true );
        }
    }
    
    return $data;
}


/**
 * data是要写的数据
 * $excel_type为写的格式 默认是csv，还有xlsx,xls
 */
function write_excel( $data, $col_name = array(), $excel_title, $excel_type = 'xls' )
{
    ob_end_clean();
    //record
    $record_info = array( 'uee_excel_title' => $excel_title, 'uee_type' => $excel_type, 'uee_col_num' => count( $col_name ), 'uee_data_count' => count( $data ), 'uee_add_time' => time(), 'uee_user_id' => $admin_id );
    $cls_data_excel_record = new cls_data( 'v2_user_excel_export' );
    $cls_data_excel_record->insert_ex( $record_info );
    
    $objPHPExcel = new PHPExcel();
    $obj = $objPHPExcel->setActiveSheetIndex( 0 );
    
    $objPHPExcel->getProperties()->setCreator( "HHJ" )
        ->setLastModifiedBy( "HHJ" )
        ->setTitle( "Excel export" )
        ->setSubject( "Excel export" )
        ->setDescription( "Test document for Office 2007 XLSX, generated using PHP classes." )
        ->setKeywords( "Data export" )
        ->setCategory( "Data export" );
    //第一行标题
    $i = 'A';
    foreach ( $col_name as $key => $value )
    {
        $col_title = $i . '1';
        $obj->setCellValue( $col_title, $value );
        $i++;
    }
    
    //二维数组的循环
    $num = '2';
    foreach ( $data as $key => $value )
    {
        $j = 'A';
        foreach ( $col_name as $k => $v )
        {
            $col_content = $j . $num;
            $obj->setCellValueExplicit( $col_content, $value[ $k ], PHPExcel_Cell_DataType::TYPE_STRING );
            $j++;
        }
        $num++;
    }
    
    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle( $excel_title );
    $objPHPExcel->setActiveSheetIndex( 0 );
    
    if ( $excel_type == 'xls' )
    {
        $file_name = $excel_title . date( 'Y-m-d' ) . ".xls";
        header( 'Content-Type: application/vnd.ms-excel' );
        header( "Content-Disposition: attachment;filename={$file_name}" );
        header( 'Cache-Control: max-age=0' );
        $objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel5' );
    } else if ( $excel_type == 'xlsx' )
    {
        $file_name = $excel_title . date( 'Y-m-d' ) . ".xlsx";
        header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
        //header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header( "Content-Disposition: attachment;filename={$file_name}" );
        header( 'Cache-Control: max-age=0' );
        $objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007' );
    } else if ( $excel_type == 'csv' )
    {
        $file_name = $excel_title . date( 'Y-m-d' ) . ".csv";
        header( "Content-type:text/csv;charset=utf-8" );
        header( "content-Disposition:filename={$file_name} " );
        $objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'CSV' );
        $objWriter->setUseBOM( true );
    }
    $objWriter->save( 'php://output' );
    exit;
}