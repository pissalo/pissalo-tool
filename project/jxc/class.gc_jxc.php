<?php
require 'interface/jxc_inc.php';
include '../class.data.php';
include '../class.jxc.php';

/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2019/7/7
 * Time: 16:14
 */
class cls_gc_jxc implements jxc_inc
{
    /**
     * 订单状态ID
     * @var int
     */
    private $order_status = 132;
    /**
     * 仓库信息数组
     * @var array
     */
    private $warehouse_info = [
        417 => ['wt_name' => 'gc_en', 'country' => 'United Kingdom',],
        487 => ['wt_name' => 'gc_jk', 'country' => 'Germany',],
    ];
    private $cls_osss;  //OSSS实例
    private $cls_to;    //中转仓出库实例
    private $cls_sjo;   //进销存其它小仓实例
    private $cls_jxc;   //进销存实例
    
    /**
     * cls_gc_jxc constructor.
     */
    public function __construct()
    {
        $this -> cls_osss = new cls_data('bullfrog_data.bd_order_status_sku_sale');
        $this -> cls_to = new cls_data('v2_transfer_out');
        $this -> cls_sjo = new cls_data('v2_stock_jxc_others');
        $this -> cls_jxc = new cls_jxc();
    }
    
    /**
     * 生成SKU列表
     * @param $start_month
     * @param $end_month
     * @return int|mixed
     */
    public function get_sku_list($start_month, $end_month)
    {
        $this -> clear();
        $input_month = $start_month;
        $before_num = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
        foreach ($this -> warehouse_info as $wt_key => $wt_name) {
            //获取OSSS SKU列表
            $sku_list_now = $this -> get_out_sku_list($wt_name['country']);
            //获取中转仓出库SKU列表
            $to_list = $this -> get_in_sku_list($wt_key);
            //合并
            $sku_list_to = array_column($to_list, 'tod_sku');
            $sku_list_now = array_column($sku_list_now, 'osss_sku');
            $sku_list_now = $sku_list_now ? $sku_list_now : array ();
            $sku_list_to = $sku_list_to ? $sku_list_to : array ();
            $sku_list = array_unique(array_merge($sku_list_now, $sku_list_to));
            
            unset($sku_list_now);
            unset($sku_list_to);
            $insert_arr = array ();
            $error_num = 0;
            $start_month = $input_month;
            $count = $before_num;
            while ($start_month != $end_month) {
                $start_month = date('Ym', strtotime("-{$count} month"));
                $insert_num = 0;
                foreach ($sku_list as $sku_info) {
                    $insert_info = array (
                        'sjo_sku' => $sku_info,
                        'sjo_update_time' => time(),
                        'sjo_month' => $start_month,
                        'sjo_cangku' => $wt_name['wt_name'],
                    );
                    array_push($insert_arr, $insert_info);
                    if (10000 > $insert_num) {
                        $flag = $this -> cls_sjo -> insert_bulk($insert_arr);
                        $error_num += $flag ? 0 : 1;
                        $insert_num = 0;
                        $insert_arr = array ();
                    }
                    $insert_num++;
                }
                $count--;
                echo "{$wt_name['wt_name']}{$start_month}SKU生成结束<br>";
            }
            if ($insert_arr) {
                $flag = $this -> cls_sjo -> insert_bulk($insert_arr);
            }
            $error_num += $flag ? 0 : 1;
            $sku_list = array ();
        }
        return $error_num;
    }
    
    /**
     * 计算出库、入库数据
     * @param $start_month
     * @param $end_month
     * @return int|mixed
     */
    public function get_out_in_stock($start_month, $end_month)
    {
        $before_num = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
        $input_month = $start_month;
        $error_num = 0;
        foreach ($this -> warehouse_info as $wt_id => $wt_info) {
            $count = $before_num;
            $start_month = $input_month;
            while ($start_month != $end_month) {
                $start_month = date('Ym', strtotime("-{$count} month"));
                $start_time = strtotime(date('Y-m-01 00:00:00', strtotime("-{$count} month")));
                $end_time = strtotime(date('Y-m-t 23:59:59', strtotime("-{$count} month")));
                $insert_num = 0;
                $sku_list_now = $this -> cls_jxc -> get_sku_order_chuku_num('gc', $start_month,
                    '/*finance*/osss_num,osss_sku',
                    array ('where' => array ("osss_country = '{$wt_info['country']}'")));
                foreach ($sku_list_now as $sku_info_old) {
                    $update_info = array (
                        'sjo_chuku_num' => $sku_info_old['osss_num']
                    );
                    $update_where = "sjo_sku = '{$sku_info_old['osss_sku']}' and sjo_month = '{$start_month}' and sjo_cangku = '{$wt_info['wt_name']}'";
                    $flag = $this -> cls_sjo -> update_one($update_info, $update_where);
                    $error_num = $flag ? 0 : 1;
                }
                //获取中转仓出库SKU列表
                $to_list = $this -> get_instock_data($wt_id, $start_time, $end_time);
                foreach ($to_list as $to_info) {
                    $update_info = array (
                        'sjo_ruku_num' => $to_info['num']
                    );
                    $update_where = "sjo_sku = '{$to_info['tod_sku']}' and sjo_month = '{$start_month}' and sjo_cangku = '{$wt_info['wt_name']}'";
                    $flag = $this -> cls_sjo -> update_one($update_info, $update_where);
                    $error_num = $flag ? 0 : 1;
                }
                echo "{$wt_info['wt_name']}{$start_month}计算出、入库结束<br>";
                $count--;
            }
            $sku_list = array ();
        }
        return $error_num;
    }
    
    /**
     * 计算期初、期末
     * @param $start_month
     * @param $end_month
     * @return int|mixed
     */
    public function cal_first_end_stock($start_month, $end_month)
    {
        $input_month = $start_month;
        $error_num = 0;
        foreach ($this -> warehouse_info as $warehouse_id => $info) {
            $count = floor((time() - strtotime($input_month . '01')) / (28 * 24 * 60 * 60));
            $before_num = $count + 1;
            $before_month = date('Ym', strtotime("-{$before_num} month"));
            $stock_info = $this -> cls_jxc -> get_jxc_qimo_stock($before_month, $info['wt_name']);
            foreach ($stock_info as $stock) {
                $update_info = array ('sjo_first_stock_num' => $stock['stock']);
                $update_where = "sjo_cangku = '{$info['wt_name']}' and sjo_sku = '{$stock['sku']}' and sjo_month = '{$input_month}'";
                $flag = $this -> cls_sjo -> update_one($update_info, $update_where);
                #echo $flag.'-'.$cls_sjo->get_last_sql().'<br>';
                $error_num += $flag ? 0 : 1;
            }
            $start_month = $input_month;
            while ($start_month != $end_month) {
                $start_month = date('Ym', strtotime("-{$count} month"));
                //获取本月数据
                $now_month_data = $this -> cls_sjo -> select_ex(
                    array (
                        'where' => "sjo_month = '{$start_month}' and sjo_cangku = '{$info['wt_name']}'",
                        'col' => '/*finance*/sjo_sku,sjo_first_stock_num,sjo_end_stock_num,sjo_ruku_num,sjo_chuku_num,sjo_cangku'
                    )
                );
                //$now_month_data = change_main_key( $now_month_data , 'sjo_sku' );
                //echo $cls_sjo -> get_last_sql() . '<br>';
                //获取下个月
                $next_count = $count - 1;
                $next_month = date('Ym', strtotime("-{$next_count} month"));
                foreach ($now_month_data as $sku => $now_month_info) {
                    //$first_stock_num = $now_month_info['sjo_end_stock_num'] - $now_month_info['sjo_ruku_num'] + $now_month_info['sjo_chuku_num'];
                    $end_stock_num = $now_month_info['sjo_first_stock_num'] + $now_month_info['sjo_ruku_num'] - $now_month_info['sjo_chuku_num'];
                    $other_ruku_num = 0;
                    $flag = $this -> cls_sjo -> update_one(
                        array (
                            'sjo_end_stock_num' => $end_stock_num,
                        ),
                        "sjo_sku = '{$now_month_info['sjo_sku']}' and sjo_cangku = '{$now_month_info['sjo_cangku']}' and sjo_month = '{$start_month}'"
                    );
                    $error_num += $flag ? 0 : 1;
                    $flag = $this -> cls_sjo -> update_one(
                        array (
                            'sjo_first_stock_num' => $end_stock_num
                        ),
                        "sjo_sku = '{$now_month_info['sjo_sku']}' and sjo_cangku = '{$now_month_info['sjo_cangku']}' and sjo_month = '{$next_month}'"
                    );
                    $error_num += $flag ? 0 : 1;
                }
                #echo $this -> cls_sjo -> get_last_sql() . '<br>';
                echo $info['wt_name'] . '-' . $start_month . '获取计算期初、期末结束!<br>';
                $count--;
            }
        }
        return $error_num;
    }
    
    /**
     * 获取出库SKU列表
     * @param $country
     * @return array
     */
    public function get_out_sku_list($country)
    {
        return $this -> cls_osss -> select_ex(
            array (
                'where' => "osss_status = {$this->order_status} and osss_country = '{$country}'",
                'group' => "REPLACE(REPLACE(osss_sku,'-156',''),'-157','')",
                'col' => "/*finance*//*!99999 nokill */REPLACE(REPLACE(osss_sku,'-156',''),'-157','') osss_sku"
            )
        );
    }
    
    /**
     * 获取入库SKU列表
     * @param $warehouse
     * @return array
     */
    public function get_in_sku_list($warehouse)
    {
        $to_sql = "SELECT
                            /*finance*/ /*!99999 nokill */tod_sku
                        FROM
                            v2_transfer_out
                            INNER JOIN v2_transfer_out_detail ON tod_new_chuku_id = to_new_chuku_id
                        WHERE
                            to_logistics_check = 1
                            AND to_dest_stock in({$warehouse})
                        GROUP BY
                            tod_sku";
        return $this -> cls_to -> execute($to_sql);
    }
    
    /**
     * @param $wt_id
     * @param $start_time
     * @param $end_time
     * @return array
     */
    public function get_instock_data($wt_id, $start_time, $end_time)
    {
        $to_sql = "SELECT
                        /*finance*/ /*!99999 nokill */tod_sku,sum(tod_num) num
                    FROM
                        v2_transfer_out
                        INNER JOIN v2_transfer_out_detail ON tod_new_chuku_id = to_new_chuku_id
                    WHERE
                        to_logistics_check = 1
                        AND to_dest_stock = {$wt_id}
                        and to_check_time >= {$start_time}
                        and to_check_time <= {$end_time}
                    GROUP BY
                        tod_sku";
        return $this -> cls_sjo -> execute($to_sql);
    }
    
    /**
     * 清除数据
     * @return bool
     */
    public function clear()
    {
        return $this -> cls_sjo -> delete_ex("sjo_cangku in ('gc_en','gc_jk')");
    }
}