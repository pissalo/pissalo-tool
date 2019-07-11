<?php

/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2019/7/7
 * Time: 16:12
 */
class cls_cal_stock
{
    private $jxc_obj;   //具体仓库实例
    private $start_month;
    private $end_month;
    
    public function __construct(jxc_inc $jxc_obj, $start_month, $end_month)
    {
        $this -> jxc_obj = $jxc_obj;
        $this -> start_month = $start_month;
        $this -> end_month = $end_month;
    }
    
    /**
     * 计算SKU列表
     * @param $start_month
     * @param $end_month
     */
    public function get_sku_list()
    {
        $this -> jxc_obj -> get_sku_list($this -> start_month, $this -> end_month);
    }
    
    /**
     * 计算出入库数据
     * @param $start_month
     * @param $end_month
     */
    public function get_out_in_stock()
    {
        $this -> jxc_obj -> get_out_in_stock($this -> start_month, $this -> end_month);
    }
    
    /**
     * 计算期初、期末
     * @param $start_month
     * @param $end_month
     */
    public function cal_first_end_stock()
    {
        $this -> jxc_obj -> cal_first_end_stock($this -> start_month, $this -> end_month);
    }
    
    /**
     * 计算进销存
     */
    public function cal_jxc()
    {
        $this -> get_sku_list();
        $this -> get_out_in_stock();
        $this -> cal_first_end_stock();
    }
}