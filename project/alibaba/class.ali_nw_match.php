<?php

/**
 * abstract:阿里SKU与牛蛙SKU关系类
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年1月15日
 * Time:18:27:17
 */
class cls_ali_nw_match extends cls_data
{
    /**
     * cls_ali_nw_match constructor.
     */
    public function __construct()
    {
        parent ::__construct( 'v2_aliba_nw_product_match' );
    }
    
    /**
     * 获取匹配关系
     * @param $param
     * @return array
     */
    public function get_match_info( $param )
    {
        return $this -> select_ex( $param );
    }
    
    /**
     * 通过阿里SKU ID 获取匹配关系
     * @param $sku_id
     * @return array
     */
    public function get_match_info_by_skuid( $sku_id )
    {
        $anpm_info = $this -> get_match_info( array ( 'where' => "anpm_sku_id = '{$sku_id}'" ) );
        return $anpm_info[0];
    }
    
    /**
     * 通过牛蛙SKU、供应商获取匹配关系
     * @author 王银龙
     * @param string/array $proudct_sku  牛蛙SKU
     * @param int $product_supplier 供应商ID
     * @param string $col 字段
     * @return array 查询结果数组
     */
    public function get_match_info_by_nw_sku( $product_sku , $product_supplier , $col = '/*slave*/*' )
    {
        if ( is_array( $product_sku ) )
        {
            $product_sku_str = implode( "','" , array_remove_empty( $product_sku ) );
        } else
        {
            $product_sku_str = $product_sku;
        }
        return $this -> get_match_info(
            array (
                'where' =>
                    "product_sku in ('{$product_sku_str}')" .
                    " and product_supplier = {$product_supplier}" ,
                'col' => $col ,
                'join' => 'inner join v2_products on product_id = anpm_nw_pid'
            )
        );
    }
    
    /**
     * 添加匹配关系
     * @param $insert_info
     * @return int
     */
    public function add_match_info( $insert_info )
    {
        $return_msg = array ();
        //检查信息
        $has_info = $this -> select_one_ex(
            array (
                'where' =>
                    "anpm_nw_pid = '{$insert_info['anpm_nw_pid']}'" .
                    " and anpm_su_id = {$insert_info['anpm_su_id']}"
            )
        );
        if ( $has_info )
        {
            $return_msg['ack'] = 0;
            $return_msg['msg'] = "添加失败，该SKU、供应商已经绑定了阿里SKU ID：'{$has_info['anpm_sku_id']}',无法重复绑定，如果确定要绑定此阿里SKU ID，请先取消之前的绑定。";
            return $return_msg;
        }
        $flag = $this -> insert_ex( $insert_info , true );
        if ( $flag )
        {
            $return_msg['ack'] = 1;
            $return_msg['msg'] = '添加成功';
        } else
        {
            $return_msg['ack'] = 0;
            $return_msg['msg'] = '添加失败!数据库操作失败，请重新尝试!';
        }
        return $return_msg;
    }
    
    /**
     * 修改匹配关系
     * @param $update_info
     * @param $ali_skuid
     * @return bool
     */
    public function update_match_info( $update_info , $ali_skuid )
    {
        $return_msg = array ();
        $old_info = $this -> get_match_info_by_skuid( $ali_skuid );
        $flag = $this -> update_one( $update_info , "anpm_sku_id = '{$ali_skuid}'" );
        if ( $flag )
        {
            $return_msg['ack'] = 1;
            $return_msg['msg'] = '更新成功!';
            //写日志
            $this -> set_log( $old_info , $update_info );
        } else
        {
            $return_msg['ack'] = 0;
            $return_msg['msg'] = '更新失败!';
        }
        return $return_msg;
    }
    
    /**
     * 删除匹配关系
     * @param $ali_skuid
     */
    public function delete_match_info( $ali_skuid )
    {
        global $admin_u;
        $return_msg = array ();
        
        //获取产品信息
        $product_info = $this -> select_one_ex(
            array (
                'where' =>
                    "anpm_sku_id = '{$ali_skuid}'" ,
                'join' =>
                    ' left join v2_products on product_id = anpm_nw_pid' .
                    ' left join v2_supplier on su_id = anpm_su_id' ,
                'col' =>
                    '/*slave*/product_sku,su_name'
            )
        );
        $flag = $this -> delete_ex( "anpm_sku_id = '{$ali_skuid}'" , 1 );
        if ( $flag )
        {
            $return_msg['ack'] = 1;
            $return_msg['msg'] = '取消匹配关系成功';
            //日志
            $cls_log = new cls_log();
            $cls_log -> set_collection( 'log_aliba_nw_match' );
            
            $option_msg = "删除匹配关系：阿里SKU ID：{$ali_skuid}，牛蛙SKU：{$product_info['product_sku']}，供应商：{$product_info['su_name']}";
            $log_info = array (
                'lanm_user' => $admin_u ,
                'lanm_add_time' => time() ,
                'lanm_option' => $option_msg ,
                'lanm_ali_sku_id' => $ali_skuid
            );
            $cls_log -> add_log( $log_info );
        } else
        {
            $return_msg['ack'] = 0;
            $return_msg['msg'] = '取消匹配关系失败';
        }
        return $return_msg;
    }
    
    /**
     * 设置日志
     * @param $update_old_arr
     * @param $update_new_arr
     * @return array 执行结果数组
     */
    public function set_log( $update_old_arr , $update_new_arr )
    {
        $cls_log = new cls_log();
        $cls_log -> set_collection( 'log_aliba_nw_match' );
        global $admin_u;
        $option_msg = '';
        //修改SKU
        if ( $update_new_arr['anpm_nw_pid'] != $update_old_arr['anpm_nw_pid'] && isset( $update_new_arr['anpm_nw_pid'] ) )
        {
            $cls_product = new cls_product();
            $old_product_info = $cls_product -> get_info_by_id( $update_old_arr['anpm_nw_pid'] );
            $new_product_info = $cls_product -> get_info_by_id( $update_new_arr['anpm_nw_pid'] );
            $option_msg .= "绑定牛蛙SKU由：{$old_product_info['product_sku']}，改为：{$new_product_info['product_sku']};";
        }
        //修改供应商
        if ( $update_new_arr['anpm_su_id'] != $update_old_arr['anpm_su_id'] && isset( $update_new_arr['anpm_su_id'] ) )
        {
            $cls_su = new cls_supplier();
            $new_su_info = $cls_su -> get_supplier_name( $update_new_arr['anpm_su_id'] );
            $old_su_info = $cls_su -> get_supplier_name( $update_old_arr['anpm_su_id'] );
            $option_msg .= "供应商由：{$old_su_info['su_name']}，改为：{$new_su_info['su_name']};";
        }
        //确认
        if ( $update_new_arr['anpm_is_valid'] != $update_old_arr['anpm_is_valid'] && isset( $update_new_arr['anpm_is_valid'] ) )
        {
            $tmp_arr = array (
                1 => '已确认' ,
                0 => '待确认' ,
            );
            $option_msg .= "状态由：{$tmp_arr[$update_old_arr['anpm_is_valid']]}，改为：{$tmp_arr[$update_new_arr['anpm_is_valid']]}";
        }
        if ( $option_msg )
        {
            $log_info = array (
                'lanm_user' => $admin_u ,
                'lanm_add_time' => time() ,
                'lanm_option' => $option_msg ,
                'lanm_ali_sku_id' => $update_old_arr['anpm_sku_id']
            );
            $flag = $cls_log -> add_log( $log_info );
        }
        return $flag;
    }
    
    /**
     * 获取日志
     * @param array $where 查询条件
     * @return array 查询数组
     */
    public function get_log( $where )
    {
        $cls_log = new cls_log();
        $cls_log -> set_collection( 'log_aliba_nw_match' );
        return $cls_log -> get_list( 1 , 100 , $where );
    }
}
