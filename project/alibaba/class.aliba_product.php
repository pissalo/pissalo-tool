<?php

/**
 * abstract:阿里产品类
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年1月12日
 * Time:14:33:56
 */
class cls_aliba_product extends cls_data
{
    /**
     * cls_aliba_product constructor.
     */
    function __construct()
    {
        parent ::__construct( 'v2_aliba_product' );
    }
    
    /**
     * 添加阿里产品信息
     * @author 王银龙
     * @param array $product_info 主表信息数组
     * @param array $product_detail_list 明细表信息数组
     * @param array $sku_attribute_list 属性表信息数组
     * @return array        执行结果数组
     */
    function add_aliba_product_info( $product_info , $product_detail_list , $sku_attribute_list )
    {
        $return_msg = array();
        $this->transaction_begin();
        $p_flag = array('ack' => 1);
        $pd_flag = array('ack' => 1);
        $sku_attr_flag = array('ack' => 1);
        //添加主表
        $p_flag = $this->add_aliba_product($product_info);
        if ($p_flag['ack']) {
            //添加明细表
            if ($product_detail_list) {
                foreach ($product_detail_list as &$detail_info) {
                    $detail_info['alipd_alip_id'] = $p_flag['insert_id'];
                }
                $pd_flag = $this->add_aliba_product_detail($product_detail_list);
            }
            //添加属性表
            if ($sku_attribute_list) {
                $sku_attr_flag = $this->add_aliba_sku_attr($sku_attribute_list);
            }
        }
        if ($p_flag['ack'] && $pd_flag['ack'] && $sku_attr_flag['ack']) {
            $this->transaction_commit();
            $return_msg['ack'] = 1;
            $return_msg['msg'] = '添加成功!';
            //添加关注
            //$this -> follow_aliba_product( $product_info['alip_ali_pid'] );
        } else {
            $this->transaction_rollback();
            $return_msg['ack'] = 0;
            $return_msg['msg'] = '数据库操作失败，请重新尝试!';
        }
        return $return_msg;
    }
    
    /**
     * 添加阿里产品主表信息
     * @param $product_info
     * @return array
     */
    function add_aliba_product( $product_info )
    {
        $return_msg = array ();
        //检查该产品ID是否存在
        $alib_product_info = $this -> get_ali_product_by_id( $product_info['alip_ali_pid'] );
        if ( $alib_product_info )
        {
            //$p_flag = $this -> update_aliba_product( $product_info , $product_info['alip_ali_pid'] );
            //$return_msg['insert_id'] = $alib_product_info[0]['alip_id'];
            $p_flag = 0;
        } else
        {
            $p_flag = $this -> insert_ex( $product_info , true );
            $return_msg['insert_id'] = $p_flag;
        }
        if ( $p_flag )
        {
            $return_msg['ack'] = 1;
            $return_msg['msg'] = '添加成功！';
            
        } else
        {
            $return_msg['ack'] = 0;
            $return_msg['msg'] = '添加失败!';
        }
        return $return_msg;
    }
    
    /**
     * 添加阿里产品明细表信息
     * @param $product_detail_list
     * @return array
     */
    function add_aliba_product_detail( $product_detail_list )
    {
        $return_msg = array ();
        $cls_alipd = new cls_data( 'v2_aliba_product_detail' );
        $error_num = 0;
        foreach ( $product_detail_list as $product_detail_info )
        {
            $detail_info = $cls_alipd -> select_one_ex(
                array (
                    'where' => "alipd_sku_id = '{$product_detail_info['alipd_sku_id']}'"
                )
            );
            if ( $detail_info )
            {
                $flag = $this -> update_aliba_product_detail( $product_detail_info , $product_detail_info['alipd_sku_id'] );
            } else
            {
                $flag = $cls_alipd -> insert_ex( $product_detail_info );
            }
            $error_num += $flag ? 0 : 1;
        }
        if ( $error_num > 0 )
        {
            $return_msg['ack'] = 0;
            $return_msg['msg'] = '数据库操作失败!';
        } else
        {
            $return_msg['ack'] = 1;
            $return_msg['msg'] = '添加成功';
        }
        return $return_msg;
    }
    
    /**
     * 添加产品属性信息表信息
     * @param $sku_attribute_list
     * @return array
     */
    function add_aliba_sku_attr( $sku_attribute_list )
    {
        $return_msg = array ();
        $cls_sku_attr = new cls_data( 'v2_aliba_product_attributes' );
        $error_num = 0;
        foreach ( $sku_attribute_list as $sku_attr_info )
        {
            $attr_info = $cls_sku_attr -> select_one_ex(
                array (
                    'where' => "apa_aliba_skuid = '{$sku_attr_info['apa_aliba_skuid']}'" .
                        " and apa_attribute_id = {$sku_attr_info['apa_attribute_id']}"
                )
            );
            if ( $attr_info )
            {
                $flag = $this -> update_aliba_sku_attr( $sku_attr_info , $sku_attr_info['apa_aliba_skuid'] );
            } else
            {
                $flag = $cls_sku_attr -> insert_ex( $sku_attr_info );
            }
            $error_num += $flag ? 0 : 1;
        }
        if ( $error_num > 0 )
        {
            $return_msg['ack'] = 0;
            $return_msg['msg'] = '数据库执行失败!';
        } else
        {
            $return_msg['ack'] = 1;
            $return_msg['msg'] = '添加成功';
        }
        return $return_msg;
    }
    
    /**
     * 获取阿里产品信息
     * @param $param
     * @return array
     */
    function get_aliba_product_info( $param )
    {
        return $this -> select_ex( $param );
    }
    
    /**
     * 通过阿里产品ID获取产品信息
     * @param $ali_pid
     * @param string $col
     * @return array
     */
    function get_ali_product_by_id( $ali_pid , $col = '/*slave*/*' )
    {
        $param = array ( 'where' => "alip_ali_pid = '{$ali_pid}'" );
        return $this -> get_aliba_product_info( $param );
    }
    
    /**
     * 通过阿里SKU ID获取阿里产品信息
     * @param array $sku_id_arr
     * @param string $col
     * @return array 查询结果数组
     */
    function get_ali_product_by_skuid( array $sku_id_arr , $col )
    {
        $sku_id_str = implode( "','" , array_remove_empty( $sku_id_arr ) );
        return $this -> select_ex(
            array (
                'where' => "alipd_sku_id in ('{$sku_id_str}')" ,
                'col' => $col ,
                'join' => 'left join v2_aliba_product_detail on alip_id = alipd_alip_id'
            )
        );
    }
    
    /**
     * 通过牛蛙ID获取阿里产品信息
     */
    function get_product_by_nw_pid()
    {
    }
    
    /**
     * 更新阿里产品主表
     * @param $product_info
     * @param $ali_pid
     * @return bool
     */
    function update_aliba_product( $product_info , $ali_pid )
    {
        $this -> set_table( 'v2_aliba_product' );
        $flag = $this -> update_one( $product_info , "alip_ali_pid = '{$ali_pid}'" );
        return $flag;
    }
    
    /**
     * 更新产品明细表
     * @param $product_detail_info
     * @param $ali_skuid
     * @return bool
     */
    function update_aliba_product_detail( $product_detail_info , $ali_skuid )
    {
        $cls_alipd = new cls_data( 'v2_aliba_product_detail' );
        $flag = $cls_alipd -> update_one( $product_detail_info , "alipd_sku_id = {$ali_skuid}" );
        return $flag;
    }
    
    /**
     * 更新阿里产品属性表
     * @param $sku_attr_info
     * @param $ali_skuid
     * @return bool
     */
    function update_aliba_sku_attr( $sku_attr_info , $ali_skuid )
    {
        $cls_sku_attr = new cls_data( 'v2_aliba_product_attributes' );
        $flag = $cls_sku_attr -> update_one( $sku_attr_info , "apa_aliba_skuid = {$ali_skuid} and apa_attribute_id = {$sku_attr_info['apa_attribute_id']}" );
        return $flag;
    }
    
    /**
     * 通过阿里产品ID使用阿里API获取产品信息
     * @param $aliba_pid
     * @return string
     */
    function get_info_by_id_from_api( $aliba_pid )
    {
        //获取信息
        $list = $this -> get_aliba_product_info_from_api( $aliba_pid );
        //执行添加
        $return_msg = $this -> add_aliba_product_info( $list['product_info'] , $list['product_detail_list'] , $list['sku_attribute_list'] );
        return json_encode( $return_msg );
    }
    
    /**
     * 通过API获取产品信息
     * @author  王银龙
     * @param string $aliba_pid 阿里产品ID
     * @return array 阿里产品信息数组
     */
    function get_aliba_product_info_from_api( $aliba_pid )
    {
        global $admin_id;
        $return_msg = array ('ack'=>1);
        $cur_dir1 = WEB_CLASS . '/third_party_api/alibaba/ocean.client.php.basic-sources/';
        chdir( $cur_dir1 );
        include_once 'com/alibaba/openapi/client/APIId.class.php';
        include_once 'com/alibaba/openapi/client/APIRequest.class.php';
        include_once 'com/alibaba/openapi/client/APIResponse.class.php';
        include_once 'com/alibaba/openapi/client/SyncAPIClient.class.php';
        include_once 'com/alibaba/openapi/client/util/DateUtil.class.php';
        include_once 'com/alibaba/openapi/client/policy/ClientPolicy.class.php';
        include_once 'com/alibaba/openapi/client/policy/DataProtocol.class.php';
        include_once 'com/alibaba/openapi/client/policy/RequestPolicy.class.php';
        
        include_once WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaAgentProductSimpleGetParam.class.php';
        include_once WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaAgentProductSimpleGetResult.class.php';
        include_once WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaProductProductInfo.class.php';
        include_once WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaProductProductImageInfo.class.php';
        include_once WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaProductProductSKUInfo.class.php';
        include_once WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaProductSKUAttrInfo.class.php';
        include_once WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaProductProductBizGroupInfo.class.php';
        include_once WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaProductSKUAttrInfo.class.php';
        include_once WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaProductProductSaleInfo.class.php';
        include_once WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaProductProductPriceRange.class.php';
        
        $cls_ali = new cls_alibaba();
        $cls_safe = new cls_safe();
        $cls_aliba_account_info = new cls_data( '@#@aliba_account_info' );
        
        if ( $aliba_pid )
        {
            //接收参数
            $cls_product_param = new AlibabaAgentProductSimpleGetParam();
            $cls_product_param -> setProductID( $aliba_pid );
            $cls_product_param -> setWebSite( 1688 );
            //接收返回结果
            $cls_product_result = new AlibabaAgentProductSimpleGetResult();
            //授权
            //获取1688授权信息
            $account_info = $cls_aliba_account_info -> select_one_ex(
                array (
                    'where' => "aai_id = 29 "
                )
            );
            $token = $cls_safe -> decrypt( $account_info['aai_token'] );
            $cls_ali -> set_app_key( $cls_safe -> decrypt( $account_info['aai_app_key'] ) );
            $cls_ali -> set_sec_key( $cls_safe -> decrypt( $account_info['aai_app_sec'] ) );
            $ali_product_info = $cls_ali -> get_ali_product_info_by_id(
                $cls_product_param ,
                $cls_product_result ,
                $token
            );
            //插入主表
            $product_info = array ();
            $product_detail_list = array ();
            $sku_attribute_list = array ();
            if ( !$cls_product_result -> getErrMsg() )
            {
                $product_info['alip_ali_pid'] = $ali_product_info -> getProductID();
                $product_info['alip_category_id'] = $ali_product_info -> getCategoryID();
                $product_info['alip_subject'] = $ali_product_info -> getSubject();
                $product_info['alip_description'] = base64_encode( $ali_product_info -> getDescription() );
                $product_info['alip_add_time'] = time();
                $product_info['alip_add_user_id'] = 1;
                $product_info['alip_add_type'] = 1;
                $sku_list = $ali_product_info -> getSkuInfos();
                if ( $sku_list )
                {
                    foreach ( $sku_list as $sku_info )
                    {
                        $tmp_arr = array ();
                        $tmp_arr['alipd_cargo_number'] = $sku_info -> getCargoNumber();
                        $tmp_arr['alipd_amount_on_sale'] = $sku_info -> getAmountOnSale();
                        $tmp_arr['alipd_price'] = $sku_info -> getPrice();
                        $tmp_arr['alipd_sku_code'] = $sku_info -> getSkuCode();
                        $tmp_arr['alipd_sku_id'] = $sku_info -> getSkuId();
                        $tmp_arr['alipd_spec_id'] = $sku_info -> getSpecId();
                        $tmp_arr['alipd_add_time'] = time();
                        $tmp_arr['alipd_add_user_id'] = 1;
                        array_push( $product_detail_list , $tmp_arr );
                        $attribute_list = $sku_info -> getAttributes() -> getStdResult();
                        foreach ( $attribute_list as $attribute_info )
                        {
                            $tmp_attr_arr = array ();
                            $tmp_attr_arr['apa_aliba_skuid'] = $sku_info -> getSkuId();
                            $tmp_attr_arr['apa_attribute_id'] = $attribute_info['attributeID'];
                            $tmp_attr_arr['apa_attribute_value'] = $attribute_info['attributeValue'];
                            $tmp_attr_arr['apa_attribute_name'] = $attribute_info['attributeDisplayName'];
                            $tmp_attr_arr['apa_add_time'] = time();
                            $tmp_attr_arr['apa_add_user_id'] = $admin_id;
                            array_push( $sku_attribute_list , $tmp_attr_arr );
                        }
                    }
                } else
                {
                    $tmp_arr = array ();
                    $tmp_arr['alipd_cargo_number'] = '';
                    $tmp_arr['alipd_amount_on_sale'] = '';
                    $tmp_arr['alipd_price'] = '';
                    $tmp_arr['alipd_sku_code'] = '';
                    $tmp_arr['alipd_sku_id'] = 'aaaa' . $ali_product_info -> getProductID();;
                    $tmp_arr['alipd_spec_id'] = '';
                    $tmp_arr['alipd_add_time'] = time();
                    $tmp_arr['alipd_add_user_id'] = 1;
                }
                array_push( $product_detail_list , $tmp_arr );
            } else
            {
                $return_msg['ack'] = 0;
            }
        } else
        {
            $return_msg['ack'] = 0;
        }
        $return_msg['product_info'] = $product_info;
        $return_msg['product_detail_list'] = $product_detail_list;
        $return_msg['sku_attribute_list'] = $sku_attribute_list;
        return $return_msg;
    }
    
    /**
     * 关注阿里产品
     * @author 王银龙
     * @param string $aliba_pid 阿里产品ID
     * @return array 执行结果数组
     */
    function follow_aliba_product( $aliba_pid )
    {
        $return_msg = array ();
        $cls_aliba = new cls_alibaba();
        $cur_dir1 = WEB_CLASS . '/third_party_api/alibaba/ocean.client.php.basic-sources/';
        chdir( $cur_dir1 );
        include_once 'com/alibaba/openapi/client/APIId.class.php';
        include_once 'com/alibaba/openapi/client/APIRequest.class.php';
        include_once 'com/alibaba/openapi/client/APIResponse.class.php';
        include_once 'com/alibaba/openapi/client/SyncAPIClient.class.php';
        include_once 'com/alibaba/openapi/client/util/DateUtil.class.php';
        include_once 'com/alibaba/openapi/client/policy/ClientPolicy.class.php';
        include_once 'com/alibaba/openapi/client/policy/DataProtocol.class.php';
        include_once 'com/alibaba/openapi/client/policy/RequestPolicy.class.php';
        include_once 'com/alibaba/openapi/client/entity/AuthorizationToken.class.php';
        
        include_once WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaProductFollowCrossborderParam.class.php';
        include_once WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaProductFollowCrossborderResult.class.php';
        $cls_param = new AlibabaProductFollowCrossborderParam();
        $cls_result = new AlibabaProductFollowCrossborderResult();
        //设置阿里产品ID
        $cls_param -> setProductId( $aliba_pid );
        //授权
        $cls_safe = new cls_safe();
        $cls_aliba_account_info = new cls_data( '@#@aliba_account_info' );
        $account_info = $cls_aliba_account_info -> select_one_ex(
            array (
                'where' => "aai_id = 29 "
            )
        );
        //$token = '4ad92c6f-d03a-4b12-955e-7100da3496d5';
        //$cls_aliba -> set_app_key( 9966606 );
        //$cls_aliba -> set_sec_key( 'ZTCMrNJhSjMh' );
        $token = $cls_safe -> decrypt( $account_info['aai_token'] );
        $cls_aliba -> set_app_key( $cls_safe -> decrypt( $account_info['aai_app_key'] ) );
        $cls_aliba -> set_sec_key( $cls_safe -> decrypt( $account_info['aai_app_sec'] ) );
        $cls_aliba -> follow_aliba_product( $cls_param , $cls_result , $token );
        if ( 0 === $cls_result -> getCode() )
        {
            $this -> update_aliba_product(
                array ( 'alip_is_follow' => 1 ) ,
                $aliba_pid
            );
            $return_msg['ack'] = 1;
            $return_msg['msg'] = '关注成功!';
        }
        return $return_msg;
    }
    
    /**
     * 通过阿里产品ID获取SKU信息列表
     * @author 王银龙
     * @param string $aliba_pid 阿里产品ID
     * @param string $col 需要字段字符串
     * @return array        查询结果数组
     */
    function get_aliba_sku_info_by_pid( $aliba_pid , $col = '/*slave*/alipd_sku_id' )
    {
        $cls_alipd = new cls_data( 'v2_aliba_product_detail' );
        return $cls_alipd -> select_ex(
            array (
                'where' => "alip_ali_pid = '{$aliba_pid}'" ,
                'join' => 'inner join v2_aliba_product on alip_id = alipd_alip_id' ,
                'col' => $col
            )
        );
    }
    
    
    function delete_aliba_product_info_by_pid( $aliba_pid )
    {
        
        //获取阿里产品信息
        $aliba_product_info = $this -> get_ali_product_by_id( $aliba_pid );
        
    }
    
    /**
     * 重新获取阿里产品信息
     * @author 王银龙
     * @param array $aliba_pid_arr 阿里产品ID数组
     * @return array        执行结果数组
     */
    function remake_aliba_data( $aliba_pid_arr )
    {
        $return_msg = array ();
        $anpm_delete_flag = 1;
        $need_rematch_sku_array = array ();
        $cls_anpm = new cls_data( 'v2_aliba_nw_product_match' );
        $cls_alipd = new cls_data( 'v2_aliba_product_detail' );
        $cls_attr = new cls_data( 'v2_aliba_product_attributes' );
        foreach ( $aliba_pid_arr as $aliba_pid )
        {
            //从阿里获取最新信息
            $aliba_product_info = $this -> get_aliba_product_info_from_api( $aliba_pid );
            if ( !$aliba_product_info['ack'] )
            {
                break;
            }
            //获取当前系统阿里SKU ID
            $old_sku_id_list = $this -> get_aliba_sku_info_by_pid( $aliba_pid , '/*slave*/alipd_sku_id,alipd_alip_id,alipd_spec_id' );
            $old_sku_id_arr = array_column( $old_sku_id_list , 'alipd_sku_id' );
            //获取重新获取的阿里产品信息的SKU列表
            $new_sku_id_arr = array_column( $aliba_product_info['product_detail_list'] , 'alipd_sku_id' );
            //是否有删除,有删除要删除掉匹配列表数据
            $delete_arr = array_diff( $old_sku_id_arr , $new_sku_id_arr );
            $this -> transaction_begin();
            if ( $delete_arr )
            {
                $delete_sku_str = implode( "','" , $delete_arr );
                $anpm_delete_flag = $cls_anpm -> delete_ex( "anpm_sku_id in ('{$delete_sku_str}')" );
            }
            //清除现有系统中SKU数据
            $alip_delete_flag = $this -> delete_ex( "alip_ali_pid = '{$aliba_pid}'" , 1 );
            $alipd_delete_flag = $cls_alipd -> delete_ex( "alipd_alip_id = {$old_sku_id_list[0]['alipd_alip_id']}" );
            $old_sku_id_str = implode( "','" , $old_sku_id_arr );
            $attr_delete_flag = $cls_attr -> delete_ex( "apa_aliba_skuid in ('{$old_sku_id_str}')" );
            //清除完后，再将新数据插入
            $insert_flag = $this -> add_aliba_product_info( $aliba_product_info['product_info'] , $aliba_product_info['product_detail_list'] , $aliba_product_info['sku_attribute_list'] );
        }
        if ( $alip_delete_flag && $alipd_delete_flag && $attr_delete_flag && $insert_flag['ack'] && $anpm_delete_flag )
        {
            $this -> transaction_commit();
            $return_msg['ack'] = 1;
            $return_msg['msg'] = '重新获取成功!';
            //日志
        } else
        {
            $this -> transaction_rollback();
            $return_msg['ack'] = 0;
            $return_msg['msg'] = '重新获取失败，请重新尝试';
        }
        return $return_msg;
    }
    
    /**
     * 获取日志
     */
    function get_log()
    {
    
    }
    
    /**
     * 设置日志
     */
    function set_log( $info_new , $info_old )
    {
    }
}