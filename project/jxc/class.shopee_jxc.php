<?php
require_once 'interface/jxc_inc.php';
include_once '../class.data.php';
include_once '../class.jxc.php';

/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2019/7/7
 * Time: 16:14
 */
class cls_shopee_jxc implements jxc_inc
{
    /**
     * 仓库信息数组
     * @var array
     */
    private $warehouse_info = [
        array ('o_country' => 'Indonesia', 'to_dest_stock' => 190, 'type' => 1, 'cangku' => 'shopee_indonesia'),
        array ('o_country' => 'Malaysia', 'to_dest_stock' => '318,413', 'type' => 2, 'cangku' => 'shopee_malaysia'),
        array ('o_country' => 'Thailand', 'to_dest_stock' => 289, 'type' => 3, 'cangku' => 'shopee_thailand'),
        array ('o_country' => 'Philippines', 'to_dest_stock' => 351, 'type' => 4, 'cangku' => 'shopee_philippines')
    ];
    /**
     * @var cls_data
     */
    private $cls_sjs;
    
    /**
     * cls_shopee_jxc constructor.
     */
    public function __construct()
    {
        $this -> cls_sjs = new cls_data('v2_stock_jxc_shopee');
    }
    
    /**
     * @param $start_month
     * @param $end_month
     * @return mixed|void+
     */
    public function get_sku_list($start_month, $end_month)
    {
        $this -> clear();
        $input_month = $start_month;
        foreach ($this -> warehouse_info as $info) {
            //BD库
            $sku_arr_bd = $this -> get_outstock_sku_list($info['o_country']);
            //出库
            $sku_arr_chuku = $this -> get_instock_sku_list($info['to_dest_stock']);
            
            $sku_arr_chuku = $sku_arr_chuku ? $sku_arr_chuku : array ();
            $sku_arr_bd = $sku_arr_bd ? $sku_arr_bd : array ();
            $all_sku_list = array_unique(array_merge($sku_arr_bd, $sku_arr_chuku));
            $count = floor((time() - strtotime($input_month . '01')) / (28 * 24 * 60 * 60));
            $start_month = $input_month;
            $error_num = 0;
            while ($end_month != $start_month) {
                $start_month = date('Ym', strtotime("-{$count} month"));
                foreach ($all_sku_list as $all_sku_info) {
                    $insert_info = array (
                        'sjs_sku' => $all_sku_info,
                        'sjs_month' => $start_month,
                        'sjs_type' => $info['type'],
                    );
                    $flag = $this -> cls_sjs -> insert_ex($insert_info);
                    $error_num += $flag ? 0 : 1;
                }
                $count--;
                echo "{$info['cangku']}{$start_month}SKU生成结束,失败:{$error_num}<br>";
            }
        }
    }
    
    /**
     * @param $start_month
     * @param $end_month
     * @return mixed|void
     */
    public function get_out_in_stock($start_month, $end_month)
    {
        $cls_jxc = new cls_jxc();
        $year_month = $start_month;
        foreach ($this -> warehouse_info as $a_info) {
            $start_month = $year_month;
            $count = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
            while ($end_month != $start_month) {
                $start_time = strtotime(date('Y-m-01', strtotime("-{$count} month ")));
                $end_time = strtotime(date('Y-m-t', strtotime("-{$count} month ")));
                $sql_2 = "SELECT
                        /*finance*/
                        sum(tod_num) as ruku_num,
                        product_id,
                        sjs_sku,
                        sjs_first_stock_num
                    FROM
                        v2_stock_jxc_shopee
                        LEFT JOIN (
                            SELECT
                            /*finance*/
                                sum(tod_num) tod_num,
                                tod_sku
                            FROM
                                v2_transfer_out
                                inner JOIN v2_transfer_out_detail ON tod_new_chuku_id = to_new_chuku_id
                            WHERE
                                to_dest_stock IN ( {$a_info['to_dest_stock']} )
                                AND to_logistics_check = 1
                                and to_check_time >= {$start_time}
                                and to_check_time <= {$end_time}
                            group by tod_sku
                        ) as tmp on sjs_sku = tod_sku
                        LEFT JOIN v2_products on product_sku = sjs_sku
                        where sjs_month = '{$year_month}' and sjs_type = {$a_info['type']}
                        GROUP BY sjs_sku";
                $start_month = date('Ym', strtotime("-{$count} month "));
                $datas = $this -> cls_sjs -> execute($sql_2);
                //获取出库数据
                $chuku_list = $cls_jxc -> get_sku_order_chuku_num('shopee', $start_month,
                    '/*finance*/osss_num,osss_sku',
                    array ('where' => array ("osss_country = '{$a_info['o_country']}'")));
                $chuku_list = change_main_key($chuku_list, 'osss_sku');
                $error_num = 0;
                foreach ($datas as $jxc_info) {
                    $insert_info = array (
                        'sjs_sku' => $jxc_info['sjs_sku'],
                        'sjs_pid' => $jxc_info['product_id'],
                        'sjs_update_time' => time(),
                        'sjs_first_stock_num' => 0,
                        'sjs_end_stock_num' => 0,
                        'sjs_ruku_num' => $jxc_info['ruku_num'],
                        'sjs_chuku_num' => -$chuku_list[$jxc_info['sjs_sku']]['osss_num'],
                        'sjs_month' => $start_month,
                        'sjs_type' => $a_info['type'],
                    );
                    $flag = $this -> cls_sjs -> update_one($insert_info,
                        "sjs_sku='{$jxc_info['sjs_sku']}' and sjs_type = {$a_info['type']} and sjs_month = '{$start_month}'");
                    $error_num += $flag ? 0 : 1;
                }
                echo "{$a_info['cangku']}月份:{$start_month}计算出、入库数据结束,失败:{$error_num}<br>";
                $count--;
            }
        }
    }
    
    /**
     * @param $start_month
     * @param $end_month
     * @return mixed|void
     */
    public function cal_first_end_stock($start_month, $end_month)
    {
        $cls_jxc = new cls_jxc();
        $input_month = $start_month;
        $input_count = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
        $before_num = $input_count + 1;
        $before_month = date('Ym', strtotime("-{$before_num} month"));
        foreach ($this -> warehouse_info as $warehouse_info) {
            $first_stock_arr_in = $cls_jxc -> get_jxc_qimo_stock($before_month, $warehouse_info['cangku']);
            $error_num = 0;
            foreach ($first_stock_arr_in as $stock_info_in) {
                $flag = $this -> cls_sjs -> update_one(
                    array (
                        'sjs_first_stock_num' => $stock_info_in['stock'],
                    ),
                    "sjs_type = {$warehouse_info['type']} and sjs_sku = '{$stock_info_in['sku']}'  and sjs_month = '{$start_month}'"
                );
                $error_num += $flag ? 0 : 1;
            }
            echo "{$warehouse_info['cangku']}期初库存处理完毕,失败:{$error_num}<br>";
        }
        //
        foreach ($this -> warehouse_info as $infos) {
            $start_month = $input_month;
            $count = $input_count;
            while ($start_month != $end_month) {
                $start_month = date('Ym', strtotime("-{$count} month "));
                $next_count = $count - 1;
                $next_month = date('Ym', strtotime("-{$next_count} month "));
                $count--;
                //获取本月数据
                $now_month_list = $this -> cls_sjs -> select_ex(
                    array (
                        'col' => '/*slave*/*',
                        'where' =>
                            " sjs_month = '{$start_month}'" .
                            " and sjs_type = {$infos['type']}"
                    )
                );
                $error_num = 0;
                foreach ($now_month_list as $now_month_info) {
                    $end_stock_num = $now_month_info['sjs_first_stock_num'] + $now_month_info['sjs_ruku_num'] + $now_month_info['sjs_chuku_num'];
                    $other_ruku = 0;
                    if ($end_stock_num < 0) {
                        $other_ruku = abs($end_stock_num);
                        $end_stock_num = 0;
                    }
                    $flag = $this -> cls_sjs -> update_one(
                        array (
                            'sjs_end_stock_num' => $end_stock_num,
                            'sjs_other_ruku_num' => $other_ruku
                        ),
                        "sjs_id = {$now_month_info['sjs_id']}"
                    );
                    $error_num += $flag ? 0 : 1;
                    #echo $cls_sjs -> get_last_sql() . '<br>';
                    $flag_next = $this -> cls_sjs -> update_one(
                        array (
                            'sjs_first_stock_num' => $end_stock_num
                        ),
                        "sjs_month = '{$next_month}' " .
                        " and sjs_sku = '{$now_month_info['sjs_sku']}' " .
                        " and sjs_type = {$infos['type']}"
                    );
                    $error_num += $flag_next ? 0 : 1;
                    #echo $cls_sjs -> get_last_sql() . '<br>';
                }
                echo "{$infos['cangku']}-{$start_month}期初、期末库存处理完毕,失败:{$error_num}<br>";
            }
        }
    }
    
    /**
     * 获取出库SKU列表
     * @param $country
     * @return array
     */
    public function get_outstock_sku_list($country)
    {
        $cls_osss = new cls_data('bullfrog_data.bd_order_status_sku_sale');
        //BD库
        $bd_sql = "SELECT
                /*finance*/
                    osss_sku AS product_sku
                FROM
                    bullfrog_data.bd_order_status_sku_sale
                WHERE
                    osss_status = 177
                    and osss_country = '{$country}'
                GROUP BY
                    osss_sku";
        $bd_list = $cls_osss -> execute($bd_sql);
        return array_column($bd_list, 'product_sku');
    }
    
    /**
     * 获取入库SKU列表
     * @param $warehouse
     * @return array
     */
    public function get_instock_sku_list($warehouse)
    {
        $chuku_sql = "select /*finance*/tod_sku  as product_sku from v2_transfer_out left join v2_transfer_out_detail on tod_new_chuku_id = to_new_chuku_id where to_dest_stock in ({$warehouse})  group by tod_sku";
        $sku_list_chuku = $this -> cls_sjs -> execute($chuku_sql);
        return array_column($sku_list_chuku, 'product_sku');
    }
    
    /**
     * 清除数据
     */
    public function clear()
    {
        $this -> cls_sjs -> execute_none_query('truncate v2_stock_jxc_shopee');
    }
}