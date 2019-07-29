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
/**
     * 导出SKU图片
     * @param array $sku_arr    SKU数组
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     * @throws PHPExcel_Writer_Exception
     */
    public function export_sku_pic(array $sku_arr)
    {
        global $product_status_list;
        
        //表头配置
        $col_arr = array (
            'A' => array (
                'col' => 'SKU',
                'table_col' => 'product_sku'
            ),
            'B' => array (
                'col' => '产品名称',
                'table_col' => 'product_name'
            ),
            'C' => array (
                'col' => '图片',
                'table_col' => 'pic'
            ),
            'D' => array (
                'col' => 'SKU状态',
                'table_col' => 'product_status'
            ),
            'E' => array (
                'col' => '库存',
                'table_col' => 'pi_stock_num'
            ),
            'F' => array (
                'col' => '物流属性',
                'table_col' => 'gtd_name'
            ),
            'G' => array (
                'col' => '周销量',
                'table_col' => 'pa_seven_sold'
            ),
        );
        //处理SKU
        $sku_arr_new = array ();
        foreach ($sku_arr as $info) {
            $sku_arr_new[] = trim($info['A']);
        }
        $sku_str = implode("','", $sku_arr_new);
        $param = [];
        $param['col'] = '/*slave*/product_sku,
                        product_name,
                        product_status,
                        gtd_name,
                        pa_seven_sold,
                        pi_stock_num';
        $param['where'][] = "product_sku in ('{$sku_str}')";
        $param['join'] = ' left join v2_product_categories_delivery ON product_category_delivery = gtd_id';
        $param['join'] .= ' left join v2_products_addtion on pa_pid = product_id';
        $param['join'] .= ' left join v2_product_inventory on pi_pid = product_id';
        $cls_product = new cls_product();
        $sku_list = $cls_product -> select_ex($param);
        #echo $sku_str;
        $objPHPExcel = new PHPExcel();
        $obj = $objPHPExcel -> setActiveSheetIndex(0);
        $objPHPExcel -> getActiveSheet() -> getColumnDimension('A') -> setWidth(15);
        $objPHPExcel -> getActiveSheet() -> getColumnDimension('B') -> setWidth(50);
        $objPHPExcel -> getActiveSheet() -> getColumnDimension('C') -> setWidth(20);
        $objPHPExcel -> getActiveSheet() -> getColumnDimension('F') -> setWidth(20);
        //第一行
        $objPHPExcel -> getActiveSheet() -> getStyle('A1') -> getFont() -> setSize(10);
        $objPHPExcel -> getActiveSheet() -> getRowDimension('1') -> setRowHeight(20);
        //表头
        $num = 1;
        foreach ($col_arr as $col_name => $col_info) {
            $obj -> setCellValue("{$col_name}{$num}", $col_info['col']);
        }
        $num++;
        //表体
        foreach ($sku_list as $sku_info) {
            $objPHPExcel -> getActiveSheet() -> getRowDimension($num) -> setRowHeight(80);
            foreach ($col_arr as $col_name => $col_info) {
                if ('pic' != $col_info['table_col']) {
                    if( 'product_status' == $col_info['table_col'])
                    {
                        $sku_info[$col_info['table_col']] = $product_status_list[$sku_info[$col_info['table_col']]];
                    }
                    $obj -> setCellValue("{$col_name}{$num}", $sku_info[$col_info['table_col']]);
                } else {
                    //D图片
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $img_url = $cls_product -> get_image($sku_info['product_sku']);
                    if ($img_url['file']) {
                        $objDrawing -> setPath($img_url['file']);
                        $objDrawing -> setHeight(100);
                        $objDrawing -> setOffsetX(5);
                        $objDrawing -> setOffsetY(3);
                        $objDrawing -> setRotation(5);
                        $objDrawing -> setWorksheet($objPHPExcel -> getActiveSheet());
                        $objDrawing -> setCoordinates($col_name . $num);
                    }
                }
            }
            $num++;
        }
        $file_name = "SKU图片信息.xls";
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename={$file_name}");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory ::createWriter($objPHPExcel, 'Excel5');
        $objWriter -> save('php://output');
        exit;
    }