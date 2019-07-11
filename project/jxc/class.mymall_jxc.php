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
class cls_mymall_jxc implements jxc_inc
{
    private $warehouse_id = 412;    //仓库ID
    private $order_status = 219;    //订单状态ID
    private $cls_sjo;
    
    /**
     * cls_mymall_jxc constructor.
     */
    public function __construct()
    {
        $this -> cls_sjo = new cls_data('v2_stock_jxc_other');
    }
    
    /**
     * 生成SKU
     * @param $start_month
     * @param $end_month
     * @return mixed|void
     */
    public function get_sku_list($start_month, $end_month)
    {
        $this -> clear();
        $to_list = $this -> get_instock_sku_list();
        $sku_list_now = $this -> get_outstock_sku_list();
        
        $sku_list = array_unique(array_merge($sku_list_now, $to_list));
        $count = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
        while ($start_month != $end_month) {
            $start_month = date('Ym', strtotime("-{$count} month"));
            $error_num = 0;
            foreach ($sku_list as $sku_info) {
                $insert_info = array (
                    'sjo_sku' => $sku_info,
                    'sjo_update_time' => time(),
                    'sjo_month' => $start_month,
                    'sjo_cangku' => 'mymall',
                );
                $flag = $this -> cls_sjo -> insert_ex($insert_info);
                $error_num += $flag ? 0 : 1;
            }
            echo "mymall{$start_month}SKU生成结束,失败:{$error_num}<br>";
            $count--;
        }
    }
    
    /**
     * 计算出、入库
     * @param $start_month
     * @param $end_month
     * @return mixed|void
     */
    public function get_out_in_stock($start_month, $end_month)
    {
        $cls_jxc = new cls_jxc();
        $count = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
        while ($start_month != $end_month) {
            $start_month = date('Ym', strtotime("-{$count} month"));
            $start_time = strtotime(date('Y-m-01 00:00:00', strtotime("-{$count} month")));
            $end_time = strtotime(date('Y-m-t 23:59:59', strtotime("-{$count} month")));
            $sku_list_now = $cls_jxc -> get_sku_order_chuku_num('mymall', $start_month, '/*finance*/osss_num,osss_sku');
            foreach ($sku_list_now as $sku_info_old) {
                $update_info = array (
                    'sjo_chuku_num' => $sku_info_old['osss_num']
                );
                $update_where = "sjo_sku = '{$sku_info_old['osss_sku']}' and sjo_month = '{$start_month}' and sjo_cangku = 'mymall'";
                $flag = $this -> cls_sjo -> update_one($update_info, $update_where);
                $error_num = $flag ? 0 : 1;
            }
            //获取中转仓出库SKU列表
            $to_list = $this -> get_instock_data($start_time, $end_time);
            $error_num = 0;
            foreach ($to_list as $to_info) {
                $update_info = array (
                    'sjo_ruku_num' => $to_info['num']
                );
                $update_where = "sjo_sku = '{$to_info['tod_sku']}' and sjo_month = '{$start_month}' and sjo_cangku = 'mymall'";
                $flag = $this -> cls_sjo -> update_one($update_info, $update_where);
                $error_num = $flag ? 0 : 1;
            }
            echo "mymall{$start_month}出、入库计算结束，失败：{$error_num}<br>";
            $count--;
        }
    }
    
    /**
     * 计算期初、期末
     * @param $start_month
     * @param $end_month
     * @return mixed|void
     */
    public function cal_first_end_stock($start_month, $end_month)
    {
        //获取上个月的期末库存作为期初库存
        $cls_jxc = new cls_jxc();
        $count = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
        $before_num = $count + 1;
        $before_month = date('Ym', strtotime("-{$before_num} month"));
        $stock_info = $cls_jxc -> get_jxc_qimo_stock($before_month, 'mymall');
        $error_num = 0;
        foreach ($stock_info as $stock) {
            $update_info = array ('sjo_first_stock_num' => $stock['stock']);
            $update_where = "sjo_cangku = 'mymall' and sjo_sku = '{$stock['sku']}' and sjo_month = '{$start_month}'";
            $flag = $this -> cls_sjo -> update_one($update_info, $update_where);
            #echo $flag.'-'.$this->cls_sjo->get_last_sql().'<br>';
            $error_num += $flag ? 0 : 1;
        }
        echo "mymall{$start_month}期初库存处理结束,失败:{$error_num}<br>";
        
        while ($start_month != $end_month) {
            $start_month = date('Ym', strtotime("-{$count} month"));
            //获取本月数据
            $now_month_data = $this -> cls_sjo -> select_ex(
                array (
                    'where' => "sjo_month = '{$start_month}' and sjo_cangku = 'mymall'",
                    'col' => '/*finance*/sjo_sku,sjo_first_stock_num,sjo_end_stock_num,sjo_ruku_num,sjo_chuku_num,sjo_cangku'
                )
            );
            //$now_month_data = change_main_key( $now_month_data , 'sjo_sku' );
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
            echo "mymall{$start_month}期末期末库存处理结束,失败:{$error_num}<br>";
            #echo $cls_sjo -> get_last_sql() . '<br>';
            #echo $info . '-' . $start_month . '<br>';
            $count--;
        }
    }
    
    /**
     * 获取入库SKU列表
     * @return array
     */
    public function get_instock_sku_list()
    {
        $to_sql = "SELECT
                        /*finance*/ /*!99999 nokill */tod_sku as sku
                    FROM
                        v2_transfer_out
                        INNER JOIN v2_transfer_out_detail ON tod_new_chuku_id = to_new_chuku_id
                    WHERE
                        to_logistics_check = 1
                        AND to_dest_stock = {$this->warehouse_id}
                    GROUP BY
                        tod_sku";
        $to_list = $this -> cls_sjo -> execute($to_sql);
        return array_column($to_list, 'sku');
    }
    
    /**
     * 获取出库SKU列表
     * @return array
     */
    public function get_outstock_sku_list()
    {
        $cls_osss = new cls_data('bullfrog_data.bd_order_status_sku_sale');
        $sku_list_now = $cls_osss -> select_ex(
            array (
                'where' => "osss_status = {$this->order_status}",
                'group' => 'osss_sku',
                'col' => '/*finance*/osss_sku as sku'
            )
        );
        return array_column($sku_list_now, 'sku');
    }
    
    /**
     * 获取入库数据
     * @param $start_time
     * @param $end_time
     * @return array
     */
    public function get_instock_data($start_time, $end_time)
    {
        $to_sql = "SELECT
                /*finance*/ /*!99999 nokill */tod_sku,sum(tod_num) num
            FROM
                v2_transfer_out
                INNER JOIN v2_transfer_out_detail ON tod_new_chuku_id = to_new_chuku_id
            WHERE
                to_logistics_check = 1
                AND to_dest_stock = {$this->warehouse_id}
                and to_check_time >= {$start_time}
                and to_check_time <= {$end_time}
            GROUP BY
                tod_sku";
        return $this -> cls_sjo -> execute($to_sql);
    }
    
    /**
     * 清除数据
     */
    public function clear()
    {
        $this -> cls_sjo -> delete_ex("sjo_cangku='mymall'");
    }
}