<?php
include_once 'interface/jxc_inc.php';
include_once '../class.data.php';
include_once '../class.jxc.php';

/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2019/7/7
 * Time: 16:14
 */
class cls_wyt_jxc implements jxc_inc
{
    /**
     * 仓库信息数组
     * @var array
     */
    private $warehouse_arr = [
        ['type' => 1, 'warehouse' => 303, 'wt_name' => '万邑通澳洲仓'],
        ['type' => 2, 'warehouse' => 324, 'wt_name' => '万邑通英国仓'],
    ];
    /**
     * @var cls_data
     */
    private $cls_sjw;
    
    /**
     * cls_wyt_jxc constructor.
     */
    public function __construct()
    {
        $this -> cls_sjw = new cls_data('v2_stock_jxc_wyt');
    }
    
    /**
     * @param $start_month
     * @param $end_month
     * @return mixed|void
     */
    public function get_sku_list($start_month, $end_month)
    {
        $this -> clear();
        $input_month = $start_month;
        
        foreach ($this -> warehouse_arr as $warehouse_info) {
            $all_sku_list = $this -> get_instock_sku_list($warehouse_info['warehouse']);
            $start_month = $input_month;
            $count = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
            while ($start_month != $end_month) {
                $start_month = date('Ym', strtotime("-{$count} month"));
                $error_num = 0;
                foreach ($all_sku_list as $sku_info) {
                    $flag = $this -> cls_sjw -> insert_ex(array (
                        'sjw_sku' => $sku_info['tod_sku'],
                        'sjw_type' => $warehouse_info['type'],
                        'sjw_month' => $start_month
                    ));
                    $error_num += $flag ? 0 : 1;
                }
                $count--;
                echo "{$warehouse_info['wt_name']}{$start_month}SKU生成结束，失败：{$error_num}<br>";
            }
        }
    }
    
    /**
     * 计算出、入库数据
     * @param $start_month
     * @param $end_month
     * @return mixed|void
     */
    public function get_out_in_stock($start_month, $end_month)
    {
        $cls_jxc = new cls_jxc();
        $input_month = $start_month;
        foreach ($this -> warehouse_arr as $warehouse_info) {
            $start_month = $input_month;
            $count = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
            while ($end_month <> $start_month) {
                $start_time = strtotime(date('Y-m-01', strtotime("-{$count} month")));
                $end_time = strtotime(date('Y-m-t', strtotime("-{$count} month")) . ' 23:59:59');
                $warehouse = 303;
                $str_end = '-154';
                $order_status = 184;
                $cangku = 'wyt_australia';
                if (2 == $warehouse_info['type']) {
                    $warehouse = 324;
                    $str_end = '-146';
                    $order_status = 202;
                    $cangku = 'wyt_england';
                }
                $sql = "SELECT /*finance*/sjw_sku,product_id,tod_num_ruku as ruku_num,sjw_first_stock_num
                FROM
                    v2_stock_jxc_wyt
                    LEFT JOIN ( SELECT /*finance*/sum(tod_num_ruku) as tod_num_ruku,tod_sku FROM v2_transfer_out LEFT JOIN v2_transfer_out_detail ON to_new_chuku_id = tod_new_chuku_id where to_check_time >= {$start_time} and to_check_time <= {$end_time} and to_dest_stock = {$warehouse}  GROUP BY tod_sku ) AS tran_tmp on tod_sku = sjw_sku
                    LEFT JOIN v2_products ON product_sku = sjw_sku where sjw_month = '{$input_month}' and sjw_type = {$warehouse_info['type']} group by sjw_sku";
                $wyt_data = $this -> cls_sjw -> execute($sql);
                $start_month = date('Ym', strtotime("-{$count} month"));
                //获取出库
                $chuku_list = $cls_jxc -> get_sku_order_chuku_num($cangku, $start_month,
                    "/*finance*/sum(osss_num) osss_num,REPLACE(REPLACE(osss_sku,'-154',''),'-146','')osss_sku",
                    array ('group' => "REPLACE(REPLACE(osss_sku,'-154',''),'-146','')"));
                $chuku_list = change_main_key($chuku_list, 'osss_sku');
                $count--;
                $error_num = 0;
                $sum_num = count($wyt_data);
                foreach ($wyt_data as $wyt_info) {
                    $wyt_info['chuku_num'] = $wyt_info['osss_num'];
                    //$first_stock = $wyt_info['sjw_first_stock_num'] - $wyt_info['ruku_num'] + $wyt_info['chuku_num'];
                    
                    $insert_info = array (
                        'sjw_sku' => $wyt_info['sjw_sku'],
                        'sjw_pid' => $wyt_info['product_id'],
                        'sjw_update_time' => time(),
                        'sjw_month' => $start_month,
                        'sjw_ruku_num' => $wyt_info['ruku_num'],
                        'sjw_chuku_num' => -$chuku_list[$wyt_info['sjw_sku']]['osss_num'],
                        'sjw_type' => $warehouse_info['type'],
                    );
                    $flag = $this -> cls_sjw -> update_one($insert_info,
                        "sjw_type={$warehouse_info['type']} and sjw_sku = '{$wyt_info['sjw_sku']}' and sjw_month='{$start_month}'");
                    //echo $flag . '-' . $cls_sjw -> get_last_sql() . '<br>';
                    $error_num += $flag ? 0 : 1;
                }
                echo "{$warehouse_info['wt_name']}{$start_month}出、入库处理完毕，失败:{$error_num}<br>";
            }
        }
    }
    
    /**
     * 计算期初、期末库存
     * @param $start_month
     * @param $end_month
     * @return mixed|void
     */
    public function cal_first_end_stock($start_month, $end_month)
    {
        $cls_jxc = new cls_jxc();
        $input_month = $start_month;
        $input_count = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
        foreach ($this -> warehouse_arr as $warehouse_info) {
            $count = $input_count;
            $before_num = $count + 1;
            $before_month = date('Ym', strtotime("-{$before_num} month"));
            $first_stock_arr_in = $cls_jxc -> get_jxc_qimo_stock($before_month, 'wyt_australia');
            $error_num = 0;
            foreach ($first_stock_arr_in as $stock_info_in) {
                $flag = $this -> cls_sjw -> update_one(
                    array (
                        'sjw_first_stock_num' => $stock_info_in['stock'],
                    ),
                    "sjw_type = 1 and sjw_sku = '{$stock_info_in['sku']}'  and sjw_month = '{$start_month}'"
                );
                $error_num += $flag ? 0 : 1;
            }
            echo "{$warehouse_info['wt_name']}期初库存处理完毕<br>";
            
            
            $start_month = $input_month;
            $count = $input_count;
            while ($start_month != $end_month) {
                $start_month = date('Ym', strtotime("-{$count} month "));
                $next_count = $count - 1;
                //$next_count = $count + 1;
                $next_month = date('Ym', strtotime("-{$next_count} month "));
                $count--;  //顺算减
                //$count ++;//倒算加
                //获取本月数据
                $now_month_list = $this -> cls_sjw -> select_ex(
                    array (
                        'col' => '/*slave*/*',
                        'where' =>
                            " sjw_month = '{$start_month}'" .
                            " and sjw_type = {$warehouse_info['type']}"
                    )
                );
                foreach ($now_month_list as $now_month_info) {
                    //$first_stock_num = $now_month_info['sjw_end_stock_num'] - $now_month_info['sjw_ruku_num'] - $now_month_info['sjw_chuku_num'] - $now_month_info['sjw_other_chuku_num'] - $now_month_info['sjw_other_ruku_num'];
                    $end_stock_num = $now_month_info['sjw_first_stock_num'] + $now_month_info['sjw_ruku_num'] + $now_month_info['sjw_chuku_num'] + $now_month_info['sjw_other_chuku_num'] + $now_month_info['sjw_other_ruku_num'];
                    $flag = $this -> cls_sjw -> update_one(
                        array (
                            'sjw_end_stock_num' => $end_stock_num,
                        ),
                        "sjw_month = '{$start_month}' " .
                        " and sjw_sku = '{$now_month_info['sjw_sku']}' " .
                        " and sjw_type = {$warehouse_info['type']}"
                    );
                    $error_num += $flag ? 0 : 1;
                    $flag_next = $this -> cls_sjw -> update_one(
                        array (
                            'sjw_first_stock_num' => $end_stock_num
                        ),
                        "sjw_month = '{$next_month}' " .
                        " and sjw_sku = '{$now_month_info['sjw_sku']}' " .
                        " and sjw_type = {$warehouse_info['type']}"
                    );
                    $error_num += $flag_next ? 0 : 1;
                }
                echo "{$warehouse_info['wt_name']}{$start_month}期初、期末处理完毕，失败{$error_num}<br>";
            }
        }
    }
    
    /**
     * 获取入库SKU列表
     * @param $warehouse
     * @return array
     */
    public function get_instock_sku_list($warehouse)
    {
        $cls_to = new cls_data('v2_transfer_out');
        $sql = "SELECT
                  /*finance*/tod_sku
                FROM
                  v2_transfer_out
                LEFT JOIN v2_transfer_out_detail ON to_new_chuku_id = tod_new_chuku_id
                where to_dest_stock = {$warehouse} and to_check_time > 0
                GROUP BY tod_sku";
        return $cls_to -> execute($sql);
    }
    
    /**
     * 清除数据
     */
    public function clear()
    {
        $this -> cls_sjw -> execute_none_query('truncate v2_stock_jxc_wyt');
    }
}