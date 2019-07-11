<?php
require_once 'interface/jxc_inc.php';
include_once '../class.jxc.php';
include_once '../class.data.php';

/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2019/7/7
 * Time: 16:14
 */
class cls_zz_jxc implements jxc_inc
{
    /**
     * @var cls_data
     */
    private $cls_zz;
    
    /**
     * cls_zz_jxc constructor.
     */
    public function __construct()
    {
        $this -> cls_zz = new cls_data('v2_stock_jxc_zz');
    }
    
    /**
     * @param $start_month
     * @param $end_month
     * @return mixed|void
     */
    public function get_sku_list($start_month, $end_month)
    {
        // TODO: Implement get_sku_list() method.
        $this -> clear();
        //获取所有入过中转仓的SKU信息
        $has_ruku_zz_list = $this -> get_zz_instock_sku_list();
        //获取所有中转仓出库的SKU信息
        $has_chuku_zz_list = $this -> get_zz_outstock_sku_list();
        //所有调入中转仓
        $diaobo_list = $this -> get_allocation_sku_list();
        $all_sku_list = array_remove_empty(array_unique(array_column(array_merge($has_ruku_zz_list, $has_chuku_zz_list,
            $diaobo_list), 'sku')));
        $count = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
        $error_num = 0;
        #echo $now_month . '<br>';
        foreach ($all_sku_list as $has_info) {
            $insert_info = array (
                'sjz_sku' => $has_info,
                'sjz_month' => $start_month
            );
            $flag = $this -> cls_zz -> insert_ex($insert_info);
            $error_num += $flag ? 0 : 1;
        }
        $now_count = $count - 1;
        $now_month = date('Ym', strtotime("-{$now_count} month"));
        while ($end_month != $now_month) {
            $now_month = date('Ym', strtotime("-{$count} month"));
            $list = $this -> cls_zz -> select_ex(
                array (
                    'where' => "sjz_month = '{$start_month}'"
                )
            );
            foreach ($list as $l_info) {
                $insert_info = array (
                    'sjz_sku' => $l_info['sjz_sku'],
                    'sjz_month' => $now_month
                );
                $flag = $this -> cls_zz -> replace($insert_info);
                $error_num += $flag ? 0 : 1;
            }
            $count--;
            echo $now_month . '插入SKU列表处理结束' . '<br>';
        }
        echo '<hr>插入SKU结束' . $error_num;
    }
    
    /**
     * @param $start_month
     * @param $end_month
     * @return mixed|void
     */
    public function get_out_in_stock($start_month, $end_month)
    {
        $error_num = 0;
        $count = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
        while ($start_month != $end_month) {
            $start_month = date('Ym', strtotime("-{$count} month"));
            $start_time = strtotime(date('Y-m-01 00:00:00', strtotime("-{$count} month")));
            $end_time = strtotime(date('Y-m-t 23:59:59', strtotime("-{$count} month")));
            $fba_table = 'v2_fba_zhongzhuan';
            if (in_array($start_month, array ('201701', '201702', '201703', '201704', '201705', '201706', '201707'))) {
                $fba_table = 'v2_fba_zhongzhuan_tmp';
            }
            echo "开始计算{$start_month}正常入库数据<br>";
            //正常入库
            $sql = "SELECT
            /*finance*/sjz_sku,/*!99999 nokill*/
            sjz_pid,
            chuku_num,
            ruku_num,
            diao_ru,
            diao_chu ,
            total_price,
            pandian_num
        FROM
            v2_stock_jxc_zz
            LEFT JOIN (
            SELECT
                /*finance*//*!99999 nokill*/tod_sku,
                sum( tod_num ) AS chuku_num
            FROM
                v2_transfer_out
                LEFT JOIN v2_transfer_out_detail ON to_new_chuku_id = tod_new_chuku_id
            WHERE
                to_check_time >= {$start_time}
                AND to_check_time <= {$end_time}
                AND to_logistics_check = 1
                and to_new_chuku_id not in ('TRC1499768387G1','TRC1499768399CF','TRC1499854504XL','TRC1500372800dl','TRC1500372822ch','TRC1500372834kE','TRC1500372993BZ','TRC1500373012HM','TRC1500373024K6','TRC1500608845zk','TRC1500878850uE','TRC1500878913vJ','TRC1500878925X7','TRC1500878941vW','TRC1500878966iQ','TRC1501319157Vh','TRC1501319171IU','TRC1501319187kS','TRC1501838962QX','TRC1501838988Ll','TRC15018399196m','TRC1501840013h2','TRC1501840034Io','TRC1501840048sT','TRC1501840062I4','TRC1501840333bj','TRC1503057002XC','TRC1503115603Wr','TRC15031156266g','TRC1503115635cZ','TRC1503491443VF','TRC1503491462oK','TRC1503714256iJ','TRC1504072406TS','TRC1504264978G5','TRC1504265012cU','TRC1504265029Bu','TRC1504930617Qo','TRC1504930631lj','TRC1504930640sG','TRC1505193042xV','TRC1505457213Ak','TRC15057882689m','TRC1506310867DE','TRC1506317072AR','TRC1506393883nf','TRC1506393898Qx','TRC1506393912ha','TRC1506395092o4','TRC1506395105ft','TRC1506395113L2','TRC150650337152','TRC15065815208e','TRC1506751047hp','TRC15067510579l','TRC1506751071ej','TRC1507347924ms','TRC15073479432w','TRC1507347953Sr','TRC1507347962Es','TRC15073479738I','TRC1507605540k0','TRC1507711466d5','TRC1507774309mQ','TRC1507780116FC','TRC1507780130NW','TRC15077801414H','TRC1507947971TM','TRC1508212190D6','TRC1508309044fO','TRC1508309072oH','TRC1508309084NT','TRC1508377969XE','TRC1508377979Ri','TRC1508377991Q2','TRC1508751992hi','TRC1508837223Ac','TRC1508985379sW','TRC1509527130lB','TRC1509527141u0','TRC15095271506e','TRC1509612739xT','TRC15096146681g','TRC1509614694hj','TRC1509614705Uy','TRC1510044528sv','TRC1510132577w7','TRC1510557286Ng','TRC1510816994QH','TRC1510818189yt','TRC1510818199nZ','TRC1510818222G2','TRC1510819594xB','TRC1510819613gv','TRC1510819623kz','TRC15108252575Z','TRC1511766369hC','TRC1512028675e5','TRC1512028698TA','TRC1512028707cm','TRC1512352852VQ','TRC1512352878wF','TRC1512352887cw','TRC1512352896TH','TRC1513128470lQ','TRC1513128480hP','TRC1513389154jS','TRC15135610910T','TRC1513653972VM','TRC1513759168K5','TRC1513843227bq','TRC1513993663lO','TRC1514344050Fw','TRC15145106574T','TRC1514882607Dc','TRC15150683343Y','TRC1515068344sg','TRC1515385841Qd','TRC1515503708gA','TRC1515579794Sd','TRC1515807760FR','TRC1516074623gm','TRC1516074638XK','TRC15160746493C','TRC1516074659UI','TRC1516163847DX','TRC1516861020uX','TRC1517395887hI','TRC1518076211Ut','TRC15194441584o','TRC1520214742uF','TRC1520395943Yi','TRC1520419659L2','TRC1520841886dA','TRC1520844319CM','TRC1521428221BL','TRC1521603188fx','TRC1521603315i9','TRC15222085439H','TRC1522762756Ow','TRC1524046952CR','TRC1524049066n8','TRC1524049079xh','TRC1524049105fo','TRC1524049133gE','TRC152404915092','TRC1524049175jw','TRC1524715160EP','TRC1524795202Ok','TRC15247952228J','TRC15247952411M','TRC1524795272gY','TRC1524880777Fy','TRC1524880799Qa','TRC1525763332Ow','TRC1526028191sN','TRC1526354038Qi','TRC1526527043w2','TRC1526528470NQ','TRC1526960543AI','TRC1527754725wc','TRC1527754741ND','TRC1527761111U0','TRC1528179509wW','TRC1528179533FD','TRC1528268247BK','TRC1528268262rp','TRC1528268279i1','TRC1528364120gS','TRC15283641343g','TRC1528364344ES','TRC1529051363Z0','TRC1529054557ZU','TRC1529130989Py','TRC1529131002sI','TRC1529131115Gl','TRC1529131133HO','TRC1529638581wI','TRC1529907854FT','TRC1530779008gs','TRC1530779252Vt','TRC1531540670tA','TRC1531556382qA','TRC1531556411kM','TRC1531556425Hr','TRC1532338087zK','TRC1532338118m5','TRC1532338133Hs','TRC1532338149OV','TRC1532338170qa','TRC1532758450dc','TRC1532762020iw','TRC1532762042bp','TRC1532762056D0','TRC153354183811','TRC15335438846c','TRC1533795859C7','TRC153379590610','TRC1533795934j9','TRC1534410335Ma','TRC1535162496De','TRC1536574442su','TRC15366300573W','TRC1536654316kx','TRC1537358071ZG','TRC15373580915x','TRC1537358123td','TRC15380400506F','TRC1538040069m6','TRC1538043335rq','TRC1538043516AE','TRC1538043520c0','TRC15380442089i','TRC1538044282UD','TRC1538044422iw','TRR1536574574WU')
            GROUP BY
                tod_sku
            ) AS chuku ON tod_sku = sjz_sku
            LEFT JOIN (
            SELECT
                /*finance*//*!99999 nokill*/fba_zz_sku,
                sum( fba_zz_num ) AS ruku_num ,
                sum(fba_zz_num * pod_product_cost ) as total_price
            FROM
                {$fba_table}
                INNER JOIN v2_purchase_order_detail on pod_po_id = fba_zz_caigou_id and pod_sku = fba_zz_sku
                inner join v2_purchase_order on po_id = pod_po_id
            WHERE
                fba_zz_type = 'ruku'
                AND fba_zz_add_time >= {$start_time}
                AND fba_zz_add_time <= {$end_time}
                and po_cgtype not in (12)
            GROUP BY
                fba_zz_sku
            ) AS ruku ON fba_zz_sku = sjz_sku
            LEFT JOIN (
            SELECT
                /*finance*//*!99999 nokill*/pod_sku,
                sum( IF ( po_cgtype =- 2, pod_num_ruku, 0 ) ) AS diao_chu,
                sum( IF ( po_cgtype = 12, pod_num_ruku, 0 ) ) AS diao_ru
            FROM
                v2_purchase_order
                LEFT JOIN v2_purchase_order_detail ON po_id = pod_po_id
            WHERE
                po_cgtype IN ( - 2, 12 )
                AND po_add_time > {$start_time}
                AND po_add_time < {$end_time}
            GROUP BY
                pod_sku
            ) AS diaobo_chu ON pod_sku = sjz_sku
            LEFT JOIN (
            SELECT
                /*finance*//*!99999 nokill*/
                    tpd_sku,
                    sum( tpd_after_number - tpd_before_number ) pandian_num
                FROM
                    v2_transfer_pandian AS a
                    LEFT JOIN v2_transfer_pandian_detail AS b ON a.tp_order_id = b.tpd_order_id
                WHERE
                    tpd_before_number != tpd_after_number
                    AND tp_check_status = 2
                    and tp_fushen_time >= {$start_time}
                    and tp_fushen_time <= {$end_time}
                GROUP BY
                    tpd_sku
            ) pandian on tpd_sku = tod_sku
        WHERE
            sjz_month = '{$start_month}'";
            $stock_data = $this -> cls_zz -> execute($sql);
            #echo $cls_sjz -> get_last_sql() . '<br>';//exit;
            echo "获取{$start_month}数据结束，开始循环<br>";
            foreach ($stock_data as $key => $stock_info) {
                //调拨入库=新调拨+旧调拨
                $diao_ru = $stock_info['diao_ru'];
                //出库=正常发货+调拨
                //2019年3月25日 出库=正常发货（中转仓与本地仓之间的调拨算其它出、入库）。
                //$chuku = $stock_info['chuku_num'] + $stock_info['diao_chu'];
                $chuku = $stock_info['chuku_num'];
                
                //获取其它入库
                if ($stock_info['pandian_num'] > 0) {
                    $diao_ru = $diao_ru + $stock_info['pandian_num'];
                } elseif ($stock_info['pandian_num'] < 0) {
                    $stock_info['diao_chu'] = $stock_info['diao_chu'] + $stock_info['pandian_num'];
                }
                $now_update = array (
                    'sjz_chuku_num' => -$chuku,
                    'sjz_ruku_num' => +$stock_info['ruku_num'],
                    'sjz_current_price' => +$stock_info['total_price'],
                    'sjz_other_ruku_num' => $diao_ru,
                    'sjz_other_chuku_num' => -$stock_info['diao_chu'],
                );
                $now_flag = $this -> cls_zz -> update_one($now_update,
                    "sjz_month = '{$start_month}' and sjz_sku = '{$stock_info['sjz_sku']}'");
                $error_num += $now_flag ? 0 : 1;
                $stock_data[$key] = null;
            }
            $count--;
            echo "<br>---{$start_month}数据计算结束---{$error_num}<hr>";
            #echo $count.'-'.$start_month.'-'.$end_month;exit;
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
        $count = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
        $before_count = $count + 1;
        $before_month = date('Ym', strtotime("-{$before_count} month"));
        $first_stock_arr_in = $cls_jxc -> get_jxc_qimo_stock($before_month, 'zhongzhuancang');
        $error_num = 0;
        foreach ($first_stock_arr_in as $stock_info_in) {
            $flag = $this -> cls_zz -> update_one(
                array (
                    'sjz_first_stock_num' => $stock_info_in['stock'],
                ),
                "sjz_sku = '{$stock_info_in['sku']}'  and sjz_month = '{$start_month}'"
            );
            $error_num += $flag ? 0 : 1;
        }
        echo "获取期初库存结束,失败:{$error_num}<br>";
        $this -> get_refund($start_month, $end_month);
        while ($end_month != $start_month) {
            #$start_month = date( 'Ym' , strtotime( "-{$count} month " ) );
            $start_month = date('Ym', strtotime("-{$count} month "));
            $next_count = $count - 1;
            $next_month = date('Ym', strtotime("-{$next_count} month "));
            $count--;
            //获取本月数据
            $now_month_list = $this -> cls_zz -> select_ex(
                array (
                    'col' =>
                        '/*slave*/*',
                    'where' =>
                        " sjz_month = '{$start_month}'",
                )
            );
            $error_num = 0;
            foreach ($now_month_list as $key => $now_month_info) {
                $end_stock_num = $now_month_info['sjz_first_stock_num'] + $now_month_info['sjz_chuku_num'] + $now_month_info['sjz_diaobo_ruku_num'] + $now_month_info['sjz_other_ruku_num'] + $now_month_info['sjz_ruku_num'] - $now_month_info['sjz_return_num'] + $now_month_info['sjz_other_chuku_num'];
                //$first_stock_num = $now_month_info['sjz_end_stock_num'] - $now_month_info['sjz_chuku_num'] - $now_month_info['sjz_diaobo_ruku_num'] - $now_month_info['sjz_other_ruku_num'] - $now_month_info['sjz_ruku_num'] + $now_month_info['sjz_return_num'] - $now_month_info['sjz_other_chuku_num'];
                $flag = $this -> cls_zz -> update_one(
                    array (
                        'sjz_end_stock_num' => $end_stock_num,
                    ),
                    "sjz_id = {$now_month_info['sjz_id']}"
                );
                $error_num += $flag ? 0 : 1;
                /*$flag_next = $cls_sjz -> update_one(
                    array (
                        'sjz_first_stock_num' => $end_stock_num
                    ) ,
                    "sjz_month = '{$next_month}' " .
                    " and sjz_sku = '{$now_month_info['sjz_sku']}' "
                );*/
                //echo $cls_sjz -> get_last_sql() . '<br>';
                $now_month_list[$key] = null;
            }
            echo "{$start_month}处理完毕,失败:{$error_num}<br>";
        }
    }
    
    /**
     * 清空数据
     */
    private function clear()
    {
        $this -> cls_zz -> execute_none_query('TRUNCATE v2_stock_jxc_zz');
    }
    
    /**
     * 调拨SKU列表
     * @return array
     */
    private function get_allocation_sku_list()
    {
        $cls_purchase = new cls_data('v2_purchase_order');
        return $cls_purchase -> select_ex(
            array (
                'where' => "po_cgtype = 12",
                'group' => 'pod_sku',
                'join' => 'inner join v2_purchase_order_detail on po_id = pod_po_id',
                'col' => '/*slave*/pod_sku as sku',
                #'limit' => 10,
            )
        );
    }
    
    /**
     * 出库SKU列表
     * @return array
     */
    private function get_zz_outstock_sku_list()
    {
        //获取所有中转仓出库的SKU信息
        $cls_tod = new cls_data('v2_transfer_out_detail');
        return $cls_tod -> select_ex(
            array (
                'col' => 'tod_sku as sku',
                'group' => 'tod_sku',
                #'limit' => 10,
            )
        );
    }
    
    /**
     * 入库SKU列表
     * @return array
     */
    private function get_zz_instock_sku_list()
    {
        $cls_fba = new cls_data('v2_fba_zhongzhuan');
        $cls_fbat = new cls_data('v2_fba_zhongzhuan_tmp');
        $has_ruku_zz_list = $cls_fba -> select_ex(array (
            'col' => 'fba_zz_sku as sku',
            'group' => 'fba_zz_sku',
            #'limit' => 10,
        ));
        //获取所有入过中转仓的SKU信息
        $has_ruku_zz_list_tmp = $cls_fbat -> select_ex(array (
            'col' => 'fba_zz_sku as sku',
            'group' => 'fba_zz_sku',
            #'limit' => 10,
        ));
        return array_merge($has_ruku_zz_list_tmp, $has_ruku_zz_list);
    }
    
    /**
     * 退款金额
     * @param $start_month
     * @param $end_month
     */
    private function get_refund($start_month, $end_month)
    {
        $cls_re = new cls_data('v2_transfer_return_goods_all');
        $count = floor((time() - strtotime($start_month . '01')) / (28 * 24 * 60 * 60));
        while ($start_month != $end_month) {
            $start_month = date('Ym', strtotime("-{$count} month"));
            $start_time = strtotime(date('Y-m-01 00:00:00', strtotime("-{$count} month ")));
            $end_time = strtotime(date('Y-m-t 23:59:59', strtotime("-{$count} month ")));
            $count--;
            $data = $cls_re -> select_ex(
                array (
                    'col' => 'trgd_sku,sum(trgd_number) as num,trga_apply_time,product_id,sum(trgd_sku_price) price',
                    'join' => 'LEFT JOIN v2_transfer_return_goods_detail ON trga_apply_id = trgd_apply_id left join v2_products on product_sku = trgd_sku',
                    'where' => "trga_status = 2 and trga_apply_time >= {$start_time} and trga_apply_time <= {$end_time}",
                    'group' => 'trgd_sku'
                )
            );
            echo "{$start_month}退款开始<br>";
            $error_num = 0;
            foreach ($data as $info) {
                $flag = $this -> cls_zz -> update_one(
                    array (
                        'sjz_return_num' => $info['num'],
                        'sjz_return_amounts' => $info['price'] * $info['num'],
                    ),
                    "sjz_sku = '{$info['trgd_sku']}' and sjz_month = '{$start_month}'"
                );
                $error_num += $flag ? 0 : 1;
            }
            echo "{$start_month}退款结束{$error_num}，失败:{$error_num}<hr>";
        }
    }
}