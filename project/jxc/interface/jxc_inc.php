<?php

interface jxc_inc
{
    /**
     * 获取SKU列表
     * @param $start_month
     * @param $end_month
     * @return mixed
     */
    public function get_sku_list($start_month, $end_month);

    /**
     * 获取出、入库数据
     * @param $start_month
     * @param $end_month
     * @return mixed
     */
    public function get_out_in_stock($start_month, $end_month);

    /**
     * 计算期初、期末库存数
     * @param $start_month
     * @param $end_month
     * @return mixed
     */
    public function cal_first_end_stock($start_month, $end_month);
}

?>