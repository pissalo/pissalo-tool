<?php

/**
 * abstract:报文生成类
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年5月9日
 * Time:19:36:21
 */
Class cls_data_gram extends cls_data
{
    public $gram_code = 'E002';                         //接收人标识
    public $company_code = 'EDI4403161FMD';             //发送人标识（企业数据交换编号）
    #public $create_file_path = '/home/www/uploads/drawback_xml/';              //生成报文文件的目录
    public $create_file_path = '';              //生成报文文件的目录
    public $commerce_plat_code = '4403161FMD';          //电商平台代码/电商企业代码
    public $company_name = '深圳市艾密特供应链管理有限公司';  //电商平台名称
    public $dxp_model = 'DXP';
    public $dxp_id = 'DXPENT0000022561';
    public $currency_id = 142;                       //币种
    public $logistics_code = '4403180001';           //物流公司编号
    public $logistics_name = '深圳市邮政速递有限公司';   //物流公司名称
    public $logistics_dxp_id = 'DXPENT0000020472';  //物流公司DXP
    public $declare_addre_code = 5314;              //申报地海关编码
    public $supe_place_code = 531498;               //监管场所代码
    public $order_pre = 'ZH';                       //订单前缀
    #public $commerce_plat_code = '4403161FMD';              //申报公司编号
    #public $declare_name = '深圳市艾密特供应链管理有限公司';//申报公司名称
    //国家列表
    public $drawback_country_list = array (
        '阿富汗' => 101 ,
        '阿尔巴尼亚' => 313 ,
        '阿尔及利亚' => 201 ,
        '美属萨摩亚' => 502 ,
        '安道尔' => 314 ,
        '安哥拉' => 202 ,
        '安圭拉' => 303 ,
        '南极洲' => 701 ,
        '安提瓜和巴布达' => 401 ,
        '阿根廷' => 402 ,
        '亚美尼亚' => 338 ,
        '阿鲁巴' => 403 ,
        '澳大利亚' => 601 ,
        '奥地利' => 315 ,
        '阿塞拜疆' => 339 ,
        '巴哈马' => 404 ,
        '巴林' => 102 ,
        '巴巴多斯' => 405 ,
        '孟加拉国' => 103 ,
        '白俄罗斯' => 340 ,
        '比利时' => 301 ,
        '伯利兹' => 406 ,
        '贝宁' => 203 ,
        '百慕大' => 504 ,
        '不丹' => 104 ,
        '博茨瓦纳' => 204 ,
        '玻利维亚' => 408 ,
        '波斯尼亚和黑塞哥维那' => 355 ,
        '布维岛' => 326 ,
        '巴西' => 410 ,
        '英属印度洋领地' => 303 ,
        '文莱达鲁萨兰国' => 105 ,
        '保加利亚' => 316 ,
        '布基纳法索' => 251 ,
        '布隆迪' => 205 ,
        '喀麦隆' => 206 ,
        '加拿大' => 501 ,
        '佛得角' => 208 ,
        '开曼群岛' => 411 ,
        '中非共和国' => 209 ,
        '乍得' => 211 ,
        '智利' => 412 ,
        '中国' => 142 ,
        '圣诞岛' => 601 ,
        '科科斯群岛' => 601 ,
        '哥伦比亚' => 413 ,
        '科摩罗' => 212 ,
        '刚果' => 213 ,
        '刚果民主共和国' => 252 ,
        '库克群岛' => 602 ,
        '哥斯达黎加' => 415 ,
        '科特迪瓦（象牙海岸）' => 223 ,
        '克罗地亚共和国' => 351 ,
        '古巴' => 416 ,
        '塞浦路斯' => 108 ,
        '捷克共和国' => 352 ,
        '斯洛伐克' => 353 ,
        '丹麦' => 302 ,
        '吉布提' => 214 ,
        '多米尼加共和国' => 418 ,
        '东帝汶' => 144 ,
        '厄瓜多尔' => 419 ,
        '埃及' => 215 ,
        '萨尔瓦多' => 440 ,
        '赤道几内亚' => 216 ,
        '厄立特里亚' => 258 ,
        '爱沙尼亚' => 334 ,
        '埃塞俄比亚' => 217 ,
        '福克兰群岛（马尔维纳斯）' => 402 ,
        '法罗群岛' => 357 ,
        '斐济' => 603 ,
        '芬兰' => 318 ,
        '法国' => 305 ,
        '法国本土' => 305 ,
        '法属圭亚那' => 420 ,
        '法属波利尼西亚' => 623 ,
        '法国南部领土' => 305 ,
        'F.Y.R.O.M.(马其顿)' => 354 ,
        '加蓬' => 218 ,
        '冈比亚' => 219 ,
        '格鲁吉亚' => 337 ,
        '加纳' => 220 ,
        '直布罗陀' => 320 ,
        '英国' => 303 ,
        '希腊' => 310 ,
        '格陵兰' => 503 ,
        '格林纳达' => 421 ,
        '瓜德罗普岛' => 305 ,
        '关岛' => 502 ,
        '危地马拉' => 423 ,
        '几内亚' => 221 ,
        '几内亚比绍' => 222 ,
        '圭亚那' => 424 ,
        '海地' => 425 ,
        '赫德岛' => 601 ,
        '洪都拉斯' => 426 ,
        '香港' => 110 ,
        '匈牙利' => 321 ,
        '冰岛' => 322 ,
        '印度' => 111 ,
        '印尼' => 112 ,
        '伊朗' => 113 ,
        '伊拉克' => 114 ,
        '爱尔兰' => 306 ,
        '以色列' => 115 ,
        '意大利' => 307 ,
        '牙买加' => 427 ,
        '日本' => 116 ,
        '约旦' => 117 ,
        '哈萨克斯坦' => 145 ,
        '肯尼亚' => 224 ,
        '基里巴斯' => 618 ,
        '韩国（朝鲜）' => 109 ,
        '韩国（南）' => 133 ,
        '科威特' => 118 ,
        '吉尔吉斯斯坦' => 146 ,
        '老挝' => 119 ,
        '拉脱维亚' => 335 ,
        '黎巴嫩' => 120 ,
        '列支敦士登' => 323 ,
        '利比里亚' => 225 ,
        '利比亚' => 226 ,
        '莱索托' => 255 ,
        '立陶宛' => 336 ,
        '卢森堡' => 308 ,
        '澳门' => 121 ,
        '马达加斯加' => 227 ,
        '马拉维' => 228 ,
        '马来西亚' => 122 ,
        '马尔代夫' => 123 ,
        '马里' => 229 ,
        '马耳他' => 324 ,
        '马绍尔群岛' => 621 ,
        '马提尼克' => 428 ,
        '毛里塔尼亚' => 230 ,
        '毛里求斯' => 231 ,
        '墨西哥' => 429 ,
        '密克罗尼西亚' => 620 ,
        '摩纳哥' => 325 ,
        '摩尔多瓦' => 343 ,
        '摩洛哥' => 232 ,
        '蒙古' => 124 ,
        '蒙特塞拉特岛' => 430 ,
        '莫桑比克' => 233 ,
        '缅甸' => 106 ,
        '纳米比亚' => 234 ,
        '瑙鲁' => 606 ,
        '尼泊尔' => 125 ,
        '荷兰' => 309 ,
        '荷属安的列斯群岛' => 449 ,
        '中性区' => 999 ,
        '新喀里多尼亚' => 607 ,
        '新西兰' => 609 ,
        '尼加拉瓜' => 431 ,
        '尼日尔' => 235 ,
        '尼日利亚' => 236 ,
        '纽埃' => 602 ,
        '诺福克岛' => 610 ,
        '北马里亚纳群岛' => 502 ,
        '挪威' => 326 ,
        '阿曼' => 126 ,
        '巴基斯坦' => 127 ,
        '帕劳' => 622 ,
        '巴拿马' => 432 ,
        '巴布亚新几内亚' => 611 ,
        '巴拉圭' => 433 ,
        '秘鲁' => 434 ,
        '菲律宾' => 129 ,
        '皮特凯恩' => 303 ,
        '波兰' => 327 ,
        '葡萄牙' => 311 ,
        '波多黎各' => 435 ,
        '卡塔尔' => 130 ,
        '留尼汪岛' => 237 ,
        '罗马尼亚' => 328 ,
        '俄罗斯联邦' => 344 ,
        '卢旺达' => 238 ,
        '美国佐治亚州和南三明治群岛。' => 502 ,
        '圣基茨和尼维斯' => 447 ,
        '圣卢西亚' => 437 ,
        '圣文森特和格林纳丁斯' => 439 ,
        '萨摩亚' => 617 ,
        '圣马力诺' => 329 ,
        '圣多美和普林西比' => 239 ,
        '沙特阿拉伯' => 131 ,
        '塞内加尔' => 240 ,
        '塞舌尔' => 241 ,
        '塞拉利昂' => 242 ,
        '新加坡' => 132 ,
        '斯洛文尼亚' => 350 ,
        '斯洛伐克(前)' => 353 ,
        '所罗门群岛' => 613 ,
        '索马里' => 243 ,
        '南非' => 244 ,
        '西班牙' => 312 ,
        '斯里兰卡' => 134 ,
        '圣海伦娜' => 303 ,
        '圣彼埃尔和密克隆岛' => 448 ,
        '苏丹' => 246 ,
        '苏里南' => 441 ,
        '斯瓦尔巴特群岛' => 326 ,
        '斯威士兰' => 257 ,
        '瑞典' => 330 ,
        '瑞士' => 331 ,
        '叙利亚' => 135 ,
        '台湾' => 142 ,
        '塔吉克斯坦' => 147 ,
        '坦桑尼亚' => 247 ,
        '泰国' => 136 ,
        '多哥' => 248 ,
        '托克劳群岛' => 623 ,
        '汤加' => 614 ,
        '特立尼达和多巴哥' => 442 ,
        '突尼斯' => 249 ,
        '土耳其' => 137 ,
        '土库曼斯坦' => 148 ,
        '特克斯和凯科斯群岛' => 443 ,
        '图瓦卢' => 619 ,
        '乌干达' => 250 ,
        '乌克兰' => 347 ,
        '阿拉伯联合酋长国' => 138 ,
        '美国' => 502 ,
        '我们的小岛屿' => 701 ,
        '乌拉圭' => 444 ,
        '苏联（前）' => 344 ,
        '乌兹别克斯坦' => 149 ,
        '瓦努阿图' => 608 ,
        '梵蒂冈城国（教廷）' => 356 ,
        '委内瑞拉' => 445 ,
        '越南' => 141 ,
        '维尔京群岛（英国）' => 446 ,
        '维尔京群岛（美国）' => 701 ,
        '沃利斯及富图纳群岛' => 625 ,
        '西撒哈拉' => 245 ,
        '也门' => 139 ,
        '南斯拉夫' => 701 ,
        '赞比亚' => 253 ,
        '津巴布韦' => 254 ,
        '扎伊尔' => 252 ,
        '塞尔维亚' => 358 ,
        '马其顿' => 354 ,
        '美国外围岛屿' => 502 ,
        '德国' => 304 ,
        '柬埔寨' => 107 ,
        '黑山' => 359 ,
        '马约特岛' => 259 ,
        '科索沃' => 358 ,
        '斯洛伐克共和国' => 353 ,
        '巴勒斯坦' => 128 ,
        '加那利群岛' => 207 ,
    );
    //单位列表
    public $drawback_unit_list = array ();
    
    /**
     * 报文类型常量
     */
    const DATAGRAM_TYPE_ALL = 0b111111;     //所有报文
    const DATAGRAM_TYPE_ORDER = 0b000001;   //订单
    const DATAGRAM_TYPE_PAID = 0b000010;    //付款单
    const DATAGRAM_TYPE_BILL = 0b000100;    //清单
    const DATAGRAM_TYPE_CLEAR = 0b001000;   //撤销
    const DATAGRAM_TYPE_ZF = 0b010000;      //总分
    const DATAGRAM_TYPE_SUM = 0b100000;     //汇总
    
    /**
     * cls_data_gram constructor.
     */
    public function __construct()
    {
        parent ::__construct( 'v2_order_drawback' );
        $this -> create_file_path = WEB_DR . '/uploads/drawback_xml/';
        if ( !is_dir( $this -> create_file_path ) )
        {
            mkdir( $this -> create_file_path , 777 , true );
        }
    }
    
    /**
     * 插入
     * @param $o_id_str //订单字符串
     * @return string
     */
    public function add_order( $o_id_str )
    {
        $return_msg = array ( 'ack' => 0 , 'msg' => '' );
        //报文批次
        $draw_code = 'ZH' . date( 'YmdHisu' );
        $o_id_arr = array_remove_empty( explode( ',' , $o_id_str ) );
        $o_id_arr = $this -> check_order_is_exist( $o_id_arr );
        if ( !empty( $o_id_arr ) )
        {
            $insert_arr = array ();
            foreach ( $o_id_arr as $o_id )
            {
                $insert_info = array (
                    'odb_o_id' => $o_id ,
                    'odb_add_time' => time() ,
                    'odb_update_time' => time() ,
                    'odb_code' => $draw_code
                );
                $insert_arr[] = $insert_info;
            }
            $flag = $this -> insert_bulk( $insert_arr );
            if ( $flag )
            {
                $return_msg['ack'] = 1;
                $return_msg['msg'] = '插入数据成功!';
            } else
            {
                $return_msg['msg'] = '插入数据失败';
            }
        } else
        {
            $return_msg['msg'] = '没有可以生成的订单!';
        }
        return json_encode( $return_msg , JSON_UNESCAPED_UNICODE );
    }
    
    /**
     * 更新
     * @param $o_id //订单字符串
     * @param array $update_info //更新信息数组
     * @return bool
     */
    public function update_drawback_order( $o_id , array $update_info )
    {
        $o_id_str = $o_id;
        if ( strstr( $o_id , ',' ) )
        {
            $o_id_str = implode( ',' , array_remove_empty( explode( ',' , $o_id ) ) );
        }
        if ( !isset( $update_info['odb_update_time'] ) )
        {
            $update_info['odb_update_time'] = time();
        }
        return $this -> update( $update_info , "odb_o_id in ({$o_id_str})" );
    }
    
    /**
     * 根据订单ID获取信息
     * @param $o_id
     * @param string $col
     * @return array
     */
    public function get_odb_list_by_oid( $o_id , $col = '/*slave*/*' )
    {
        $o_id_str = implode( ',' , array_remove_empty( explode( ',' , $o_id ) ) );
        return $this -> select_ex(
            array (
                'where' => "odb_o_id in ({$o_id_str})" ,
                'col' => $col
            )
        );
    }
    
    /**
     * 生成订单报文(SZCPORTCEB303Message)
     * @author 王银龙
     * @param string $o_id 订单编号
     * @param int $app_type 报文编辑类型：1、新增，2、修改，3、删除。
     * @return string 执行结果数组
     */
    public function create_order_data_gram( $o_id , $app_type = 1 )
    {
        $o_id_arr = array_remove_empty( explode( ',' , $o_id ) );
        $bs_type = 'SZCPORTCEB303Message';
        $guid = $this -> get_guid();
        $new_oid_arr = array_chunk( $o_id_arr , 100 );
        $this -> get_unit_code_list();
        //order信息数组
        foreach ( $new_oid_arr as $new_oid_info )
        {
            $order_arr = array ();
            //XML头
            $xml_head = '<?xml version="1.0" encoding="UTF-8"?>
        <ceb:CEB303Message guid="' . $guid . '" version="1.0"  xmlns:ceb="http://www.chinaport.gov.cn/ceb" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
            foreach ( $new_oid_info as $o_all_key => $oid )
            {
                //获取订单信息
                $order_list = $this -> get_order_list_by_id( $oid );
                //设置报文内容
                $date = date( 'YmdHis' , time() );
                $tmp_str = '';
                $product_count = 0;
                $order_all_price = 0;
                foreach ( $order_list as $oi_info )
                {
                    $order_all_price += $this -> get_rmb_amounts( $oi_info['o_platform_id'] , $oi_info['currency_name'] , $oi_info['total_amounts'] );
                }
                foreach ( $order_list as $oi_key => $order_info )
                {
                    //获取ID
                    $tmp_oid = $this -> get_special_o_id( $oid );
                    //获取guid
                    $o_guid = $this -> get_guid( $tmp_oid );
                    $product_count ++;//订单数量+1
                    //获取金额
                    $product_price = $this -> get_rmb_amounts( $order_info['o_platform_id'] , $order_info['currency_name'] , $order_info['od_price'] );
                    $all_price = $this -> get_rmb_amounts( $order_info['o_platform_id'] , $order_info['currency_name'] , $order_info['total_amounts'] );
                    //获取申报单位编号
                    $product_application_unit = $this -> drawback_unit_list[$order_info['product_application_unit']]['phu_code'];
                    if ( $oi_key == 0 )
                    {
                        $tmp_str .= '
                <ceb:Order>';
                        $tmp_str .= '
                <ceb:OrderHead>
                    <ceb:guid>' . $o_guid . '</ceb:guid>
                    <ceb:appType>' . $app_type . '</ceb:appType>
                    <ceb:appTime>' . $date . '</ceb:appTime>
                    <ceb:appStatus>2</ceb:appStatus>
                    <ceb:orderType>E</ceb:orderType>
                    <ceb:orderNo>' . $tmp_oid . '</ceb:orderNo>
                    <ceb:ebpCode>' . $this -> commerce_plat_code . '</ceb:ebpCode>
                    <ceb:ebpName>' . $this -> company_name . '</ceb:ebpName>
                    <ceb:ebcCode>' . $this -> commerce_plat_code . '</ceb:ebcCode>
                    <ceb:ebcName>' . $this -> company_name . '</ceb:ebcName>
                    <ceb:goodsValue>' . $order_all_price . '</ceb:goodsValue>
                    <ceb:freight>0</ceb:freight>
                    <ceb:currency>' . $this -> currency_id . '</ceb:currency>
                    <ceb:note></ceb:note>
                </ceb:OrderHead>';
                    }
                    $tmp_str .= '
                <ceb:OrderList>
                    <ceb:gnum>' . $product_count . '</ceb:gnum>
                    <ceb:itemNo>' . $order_info['product_sku'] . '</ceb:itemNo>
                    <ceb:itemName>' . $order_info['product_zwsbmc'] . '</ceb:itemName>
                    <ceb:itemDescribe></ceb:itemDescribe>
                    <ceb:barCode></ceb:barCode>
                    <ceb:unit>' . $product_application_unit . '</ceb:unit>
                    <ceb:currency>' . $this -> currency_id . '</ceb:currency>
                    <ceb:qty>' . $order_info['od_num'] . '</ceb:qty>
                    <ceb:price>' . $product_price . '</ceb:price>
                    <ceb:totalPrice>' . $all_price . '</ceb:totalPrice>
                    <ceb:note></ceb:note>
                </ceb:OrderList>';
                }
                $tmp_str .= '
                </ceb:Order>';
                $order_arr[] = $tmp_str;
            }
            $xml_content = implode( "" , $order_arr );
            $xml_end = '
            <ceb:BaseTransfer>
                <ceb:copCode>' . $this -> commerce_plat_code . '</ceb:copCode>
                <ceb:copName>' . $this -> company_name . '</ceb:copName>
                <ceb:dxpMode>' . $this -> dxp_model . '</ceb:dxpMode>
                <ceb:dxpId>' . $this -> dxp_id . '</ceb:dxpId>
                <ceb:note></ceb:note>
            </ceb:BaseTransfer>
        </ceb:CEB303Message>';
            $all_xml = $xml_head . $xml_content . $xml_end;
            //文件名
            $file_name = $this -> set_xml_file_name( $bs_type );
            $flag = $this -> write_info_to_xml( $all_xml , 1 , $file_name );
        }
        $this -> set_has_send_gram( $o_id , cls_data_gram::DATAGRAM_TYPE_ORDER );
        $this -> get_zip_file( 1 );
    }
    
    /**
     * 生成收款单报文(SZCPORTCEB403Message)
     * @author 王银龙
     * @param string $o_id 订单编号
     * @return string
     */
    public function create_income_data_gram( $o_id )
    {
        $o_id_arr = array_remove_empty( explode( ',' , $o_id ) );
        $bs_type = 'SZCPORTCEB403Message';//报文类型名称
        $guid = $this -> get_guid();
        $new_oid_arr = array_chunk( $o_id_arr , 100 );
        foreach ( $new_oid_arr as $new_oid_info )
        {
            //XML头
            $xml_head = '<?xml version="1.0" encoding="UTF-8"?>
<ceb:CEB403Message guid="' . $guid . '" version="1.0"  xmlns:ceb="http://www.chinaport.gov.cn/ceb" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
            //order信息数组
            $order_arr = array ();
            foreach ( $new_oid_info as $oid )
            {
                //获取订单信息
                $order_list = $this -> get_order_list_by_id( $oid );
                $all_price = 0.00;
                $date = date( 'YmdHis' , time() );
                $paid_date = date( 'YmdHis' , $order_list[0]['o_paid_time'] );
                //获取ID
                $tmp_oid = $this -> get_special_o_id( $oid );
                //获取guid
                $o_guid = $this -> get_guid();
                foreach ( $order_list as $o_key => $order_info )
                {
                    //获取金额
                    $all_price += $this -> get_rmb_amounts( $order_info['o_platform_id'] , $order_info['currency_name'] , $order_info['total_amounts'] );
                }
                $tmp_str = '
            <ceb:Receipts>
                <ceb:guid>' . $o_guid . '</ceb:guid>
                <ceb:appType>1</ceb:appType>
                <ceb:appTime>' . $date . '</ceb:appTime>
                <ceb:appStatus>2</ceb:appStatus>
                <ceb:ebpCode>' . $this -> commerce_plat_code . '</ceb:ebpCode>
                <ceb:ebpName>' . $this -> company_name . '</ceb:ebpName>
                <ceb:ebcCode>' . $this -> commerce_plat_code . '</ceb:ebcCode>
                <ceb:ebcName>' . $this -> company_name . '</ceb:ebcName>
                <ceb:orderNo>' . $tmp_oid . '</ceb:orderNo>
                <ceb:payCode></ceb:payCode>
                <ceb:payName>EDAL TECHNOLOGY CO., LIMITED</ceb:payName>
                <ceb:payNo></ceb:payNo>
                <ceb:charge>' . $all_price . '</ceb:charge>
                <ceb:currency>' . $this -> currency_id . '</ceb:currency>
                <ceb:accountingDate>' . $paid_date . '</ceb:accountingDate>
                <ceb:note></ceb:note>
            </ceb:Receipts>';
                $order_arr[] = $tmp_str;
            }
            $xml_content = implode( "" , $order_arr );
            $xml_end = '
            <ceb:BaseTransfer>
                <ceb:copCode>' . $this -> commerce_plat_code . '</ceb:copCode>
                <ceb:copName>' . $this -> company_name . '</ceb:copName>
                <ceb:dxpMode>' . $this -> dxp_model . '</ceb:dxpMode>
                <ceb:dxpId>' . $this -> dxp_id . '</ceb:dxpId>
                <ceb:note></ceb:note>
            </ceb:BaseTransfer>
        </ceb:CEB403Message>';
            $all_xml = $xml_head . $xml_content . $xml_end;
            //文件名
            $file_name = $this -> set_xml_file_name( $bs_type );
            $this -> write_info_to_xml( $all_xml , 2 , $file_name );
        }
        $this -> set_has_send_gram( $o_id , cls_data_gram::DATAGRAM_TYPE_PAID );
        $this -> get_zip_file( 2 );
    }
    
    /**
     * 生成出口清单(SZCPORTCEB603Message)
     * @author 王银龙
     * @param string $o_id 订单编号
     * @param int $app_type 类型：1、暂存，2、申报，3、删除
     * @return string
     */
    public function create_qdzfd_data_gram( $o_id , $app_type = 1 )
    {
        $o_id_arr = array_remove_empty( explode( ',' , $o_id ) );
        $bs_type = 'SZCPORTCEB603Message';//报文类型名称
        $guid = $this -> get_guid();
        $new_oid_arr = array_chunk( $o_id_arr , 100 );
        $this -> get_unit_code_list();
        foreach ( $new_oid_arr as $new_oid_info )
        {
            //order信息数组
            $order_arr = array ();
            //XML头
            $xml_head = '<?xml version="1.0" encoding="UTF-8"?>
<ceb:CEB603Message guid="' . $guid . '" version="1.0"  xmlns:ceb="http://www.chinaport.gov.cn/ceb" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
            foreach ( $new_oid_info as $oid )
            {
                //获取订单信息
                $order_list = $this -> get_order_list_by_id( $oid );
                //国家
                $country = $this -> drawback_country_list[$order_list[0]['country_name_cn']];
                $tmp_oid = $this -> get_special_o_id( $oid );
                //获取guid
                $o_guid = $this -> get_guid( $tmp_oid );
                //出口日期
                $out_date = date( 'Ymd' , strtotime( '+1 day' ) );
                //发送时间
                $date = date( 'YmdHis' , time() );
                //获取净重
                $weigth_only = 0;
                foreach ( $order_list as $o_info )
                {
                    $tmp_weight = min( $o_info['product_netweight'] , $o_info['product_grossweight'] , $o_info['o_weight'] );
                    $tmp_weight = $tmp_weight > 0.0001 ? $tmp_weight : $o_info['product_grossweight'];
                    $tmp_weight = $tmp_weight > 0.0001 ? $tmp_weight : 0.01;
                    $weigth_only += $tmp_weight;
                }
                $weigth_only = $weigth_only > $order_list[0]['o_weight'] ? $order_list[0]['o_weight'] : $weigth_only;
                $tmp_str = '
            <ceb:Inventory>
                <ceb:InventoryHead>
                    <ceb:guid>' . $o_guid . '</ceb:guid>
                    <ceb:appType>' . $app_type . '</ceb:appType>
                    <ceb:appTime>' . $date . '</ceb:appTime>
                    <ceb:appStatus>2</ceb:appStatus>
                    <ceb:customsCode>' . $this -> declare_addre_code . '</ceb:customsCode>
                    <ceb:ebpCode>' . $this -> commerce_plat_code . '</ceb:ebpCode>
                    <ceb:ebpName>' . $this -> company_name . '</ceb:ebpName>
                    <ceb:orderNo>' . $tmp_oid . '</ceb:orderNo>
                    <ceb:logisticsCode>' . $this -> logistics_code . '</ceb:logisticsCode>
                    <ceb:logisticsName>' . $this -> logistics_name . '</ceb:logisticsName>
                    <ceb:logisticsNo>' . $order_list[0]['o_tracknumber'] . '</ceb:logisticsNo>
                    <ceb:copNo>0</ceb:copNo>
                    <ceb:ieFlag>E</ceb:ieFlag>
                    <ceb:portCode>' . $this -> declare_addre_code . '</ceb:portCode>
                    <ceb:ieDate>' . $out_date . '</ceb:ieDate>
                    <ceb:statisticsFlag>B</ceb:statisticsFlag>
                    <ceb:agentCode>' . $this -> commerce_plat_code . '</ceb:agentCode>
                    <ceb:agentName>' . $this -> company_name . '</ceb:agentName>
                    <ceb:ebcCode>' . $this -> commerce_plat_code . '</ceb:ebcCode>
                    <ceb:ebcName>' . $this -> company_name . '</ceb:ebcName>
                    <ceb:ownerCode>' . $this -> commerce_plat_code . '</ceb:ownerCode>
                    <ceb:ownerName>' . $this -> company_name . '</ceb:ownerName>
                    <ceb:iacCode></ceb:iacCode>
                    <ceb:iacName></ceb:iacName>
                    <ceb:emsNo></ceb:emsNo>
                    <ceb:tradeMode>9610</ceb:tradeMode>
                    <ceb:trafMode>4</ceb:trafMode>
                    <ceb:trafName></ceb:trafName>
                    <ceb:voyageNo></ceb:voyageNo>
                    <ceb:billNo></ceb:billNo>
                    <ceb:totalPackageNo></ceb:totalPackageNo>
                    <ceb:loctNo>' . $this -> supe_place_code . '</ceb:loctNo>
                    <ceb:licenseNo></ceb:licenseNo>
                    <ceb:country>312</ceb:country>
                    <ceb:POD>110</ceb:POD>
                    <ceb:freight>0</ceb:freight>
                    <ceb:fCurrency>' . $this -> currency_id . '</ceb:fCurrency>
                    <ceb:fFlag>3</ceb:fFlag>
                    <ceb:insuredFee>0</ceb:insuredFee>
                    <ceb:iCurrency>' . $this -> currency_id . '</ceb:iCurrency>
                    <ceb:iFlag>3</ceb:iFlag>
                    <ceb:wrapType>2</ceb:wrapType>
                    <ceb:packNo>1</ceb:packNo>
                    <ceb:grossWeight>' . $order_list[0]['o_weight'] . '</ceb:grossWeight>
                    <ceb:netWeight>' . $weigth_only . '</ceb:netWeight>
                    <ceb:note></ceb:note>
                </ceb:InventoryHead>';
                $product_count = 0;
                foreach ( $order_list as $o_key => $order_info )
                {
                    $product_count ++;
                    //获取金额
                    $product_price = $this -> get_rmb_amounts( $order_info['o_platform_id'] , $order_info['currency_name'] , $order_info['od_price'] );
                    $all_price = $this -> get_rmb_amounts( $order_info['o_platform_id'] , $order_info['currency_name'] , $order_info['total_amounts'] );
                    //获取法定单位编号
                    $unit_list = $this -> get_hscode_unit_code( $order_info['product_hs_code'] );
                    $product_legal_unit = $unit_list['unit_1'];
                    //第二法定单位
                    $product_legal_unit_2 = $unit_list['unit_2'];
                    //获取申报单位编号
                    $product_application_unit = $this -> drawback_unit_list[$order_info['product_application_unit']]['phu_code'];
                    //法定数量
                    $legal_num = $order_info['od_num'];
                    $legal_num_2 = $order_info['od_num'];
                    if ( $product_legal_unit == '035' )
                    {
                        $legal_num = min( $order_info['product_netweight'] , $order_info['product_grossweight'] , $order_info['o_weight'] );
                        $legal_num = $legal_num > 0.0001 ? $legal_num : $order_info['product_grossweight'];
                        $legal_num = $legal_num > 0.0001 ? $legal_num : 0.01;
                        $legal_num = $legal_num < $order_info['o_weight'] ? $legal_num : $order_info['o_weight'];
                    }
                    if ( $product_legal_unit_2 == '035' )
                    {
                        $legal_num_2 = min( $order_info['product_netweight'] , $order_info['product_grossweight'] , $order_info['o_weight'] );
                        $legal_num_2 = $legal_num_2 > 0.0001 ? $legal_num_2 : $order_info['product_grossweight'];
                        $legal_num_2 = $legal_num_2 > 0.0001 ? $legal_num_2 : 0.01;
                        $legal_num_2 = $legal_num_2 < $order_info['o_weight'] ? $legal_num_2 : $order_info['o_weight'];
                    }
                    $tmp_str .= '
                <ceb:InventoryList>
                    <ceb:gnum>' . $product_count . '</ceb:gnum>
                    <ceb:itemNo>' . $order_info['product_sku'] . '</ceb:itemNo>
                    <ceb:itemRecordNo></ceb:itemRecordNo>
                    <ceb:itemName>' . $order_info['product_zwsbmc'] . '</ceb:itemName>
                    <ceb:gcode>' . $order_info['product_hs_code'] . '</ceb:gcode>
                    <ceb:gname>' . $order_info['product_zwsbmc'] . '</ceb:gname>
                    <ceb:gmodel>无</ceb:gmodel>
                    <ceb:barCode>无</ceb:barCode>
                    <ceb:country>' . $country . '</ceb:country>
                    <ceb:currency>' . $this -> currency_id . '</ceb:currency>
                    <ceb:qty>' . $order_info['od_num'] . '</ceb:qty>
                    <ceb:qty1>' . $legal_num . '</ceb:qty1>';
                    //只有有第二法定单位的时候，才需要以下两个节点。
                    if ( $product_legal_unit_2 )
                    {
                        $tmp_str .= "
                    <ceb:qty2>{$legal_num_2}</ceb:qty2>";
                    }
                    $tmp_str .= '
                    <ceb:unit>' . $product_application_unit . '</ceb:unit>
                    <ceb:unit1>' . $product_legal_unit . '</ceb:unit1>';
                    //只有有第二法定单位的时候，才需要以下两个节点。
                    if ( $product_legal_unit_2 )
                    {
                        $tmp_str .= "
                    <ceb:unit2>{$product_legal_unit_2}</ceb:unit2>";
                    }
                    $tmp_str .= "
                    <ceb:price>{$product_price}</ceb:price>
                    <ceb:totalPrice>{$all_price}</ceb:totalPrice>
                    <ceb:note></ceb:note>
                </ceb:InventoryList>";
                }
                $tmp_str .= '
            </ceb:Inventory>';
                $order_arr[] = $tmp_str;
            }
            $xml_content = implode( "" , $order_arr );
            $xml_end = '
            <ceb:BaseTransfer>
                <ceb:copCode>' . $this -> commerce_plat_code . '</ceb:copCode>
                <ceb:copName>' . $this -> company_name . '</ceb:copName>
                <ceb:dxpMode>' . $this -> dxp_model . '</ceb:dxpMode>
                <ceb:dxpId>' . $this -> dxp_id . '</ceb:dxpId>
                <ceb:note></ceb:note>
            </ceb:BaseTransfer>
        </ceb:CEB603Message>';
            $all_xml = $xml_head . $xml_content . $xml_end;
            //文件名
            $file_name = $this -> set_xml_file_name( $bs_type );
            $this -> write_info_to_xml( $all_xml , 3 , $file_name );
        }
        $this -> set_has_send_gram( $o_id , cls_data_gram::DATAGRAM_TYPE_PAID );
        $this -> get_zip_file( 3 );
    }
    
    /**
     * 清单总分单
     * @param $o_id
     * @param int $app_type 1-新增 2-变更 3- 删除。
     * @return string
     */
    public function create_all_bill( $o_id , $app_type = 1 , $ty_code )
    {
        $o_id_arr = array_remove_empty( explode( ',' , $o_id ) );
        $bs_type = 'SZCPORTCEB607Message';//报文类型名称
        $new_oid_arr = array_chunk( $o_id_arr , 1000 );
        $chunk_num = count( $new_oid_arr );
        foreach ( $new_oid_arr as $now_key => $new_oid_info )
        {
            //当前报文数
            $now_num = $now_key + 1;
            $guid = $this -> get_guid();
            //获取guid
            $o_guid = $this -> get_guid();
            //唯一编码
            $cop_no = $this -> get_cop_no();
            //发送日期
            $date = date( 'YmdHis' , time() );
            //获取订单总重量
            $all_weight = 0;
            //获取订单信息
            $order_list = $this -> get_order_list_by_id( $new_oid_info , array ( 'group' => 'o_id' ) );
            foreach ( $order_list as $ord_info )
            {
                $all_weight += $ord_info['o_weight'];
            }
            //XML头
            $xml_head = '<?xml version="1.0" encoding="UTF-8"?>
<ceb:CEB607Message guid="' . $guid . '" version="1.0"  xmlns:ceb="http://www.chinaport.gov.cn/ceb" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
        <ceb:WayBill>
                    <ceb:WayBillHead>
                        <ceb:guid>' . $o_guid . '</ceb:guid>
                        <ceb:appType>' . $app_type . '</ceb:appType>
                        <ceb:appTime>' . $date . '</ceb:appTime>
                        <ceb:appStatus>2</ceb:appStatus>
                        <ceb:customsCode>' . $this -> declare_addre_code . '</ceb:customsCode>
                        <ceb:copNo>' . $cop_no . '</ceb:copNo>
                        <ceb:agentCode>' . $this -> commerce_plat_code . '</ceb:agentCode>
                        <ceb:agentName>' . $this -> company_name . '</ceb:agentName>
                        <ceb:loctNo>' . $this -> supe_place_code . '</ceb:loctNo>
                        <ceb:trafMode>4</ceb:trafMode>
                        <ceb:trafName>VN1315</ceb:trafName>
                        <ceb:voyageNo>HD94</ceb:voyageNo>
                        <ceb:billNo>' . $ty_code . '</ceb:billNo>
                        <ceb:domesticTrafNo></ceb:domesticTrafNo>
                        <ceb:grossWeight>' . $all_weight . '</ceb:grossWeight>
                        <ceb:logisticsCode>' . $this -> logistics_code . '</ceb:logisticsCode>
                        <ceb:logisticsName>' . $this -> logistics_name . '</ceb:logisticsName>
                        <ceb:msgCount>' . $chunk_num . '</ceb:msgCount>
                        <ceb:msgSeqNo>' . $now_num . '</ceb:msgSeqNo>
                        <ceb:note></ceb:note>
                    </ceb:WayBillHead>';
            //order信息数组
            $order_arr = array ();
            $product_num = 0;
            $o_id_str = implode( ',' , $o_id_arr );
            foreach ( $order_list as $order_info )
            {
                $product_num ++;
                $tmp_str = '
                    <ceb:WayBillList>
                        <ceb:gnum>' . $product_num . '</ceb:gnum>
                        <ceb:totalPackageNo></ceb:totalPackageNo>
                        <ceb:logisticsNo>' . $order_info['o_tracknumber'] . '</ceb:logisticsNo>
                        <ceb:note></ceb:note>
                    </ceb:WayBillList>';
                $order_arr[] = $tmp_str;
            }
            $xml_content = implode( "" , $order_arr );
            $xml_content .= '
            </ceb:WayBill>';
            $xml_end = '
            <ceb:BaseTransfer>
                <ceb:copCode>' . $this -> commerce_plat_code . '</ceb:copCode>
                <ceb:copName>' . $this -> company_name . '</ceb:copName>
                <ceb:dxpMode>' . $this -> dxp_model . '</ceb:dxpMode>
                <ceb:dxpId>' . $this -> dxp_id . '</ceb:dxpId>
                <ceb:note></ceb:note>
            </ceb:BaseTransfer>
        </ceb:CEB607Message>';
            $all_xml = $xml_head . $xml_content . $xml_end;
            //文件名
            $file_name = $this -> set_xml_file_name( $bs_type );
            $this -> write_info_to_xml( $all_xml , 4 , $file_name );
        }
        //$all_file_path = $this -> create_file_path . $file_name;
        //$flag = file_put_contents( $all_file_path , $all_xml );
        //设置提运编号
        $update_info = array (
            'odb_logistics_num' => $ty_code ,
        );
        $this -> update_drawback_order( $o_id_str , $update_info );
        $this -> set_has_send_gram( $o_id , cls_data_gram::DATAGRAM_TYPE_ZF );
        $this -> get_zip_file( 4 );
    }
    
    /**
     * 生成汇总申请单(SZCPORTCEB701Message)
     * @author 王银龙
     * @param string $o_id 订单编号
     * @param int $app_type
     * @return string 执行结果数组
     */
    public function create_sum_apply_data_gram( $o_id , $app_type = 1 )
    {
        $o_id_arr = array_unique( array_remove_empty( explode( ',' , $o_id ) ) );
        $bs_type = 'SZCPORTCEB701Message';//报文类型名称
        $guid = $this -> get_guid();
        $new_oid_arr = array_chunk( $o_id_arr , 100 );
        $this -> get_unit_code_list();
        $chunk_num = count( $new_oid_arr );
        foreach ( $new_oid_arr as $now_key => $new_oid_info )
        {
            $now_chunk = $now_key + 1;
            //获取guid
            $o_guid = $this -> get_guid();
            //获取日期
            $date = date( 'YmdHis' , time() );
            //唯一编码
            $cop_no = $this -> get_cop_no();
            //XML头
            $xml_head = '<?xml version="1.0" encoding="UTF-8"?>
<ceb:CEB701Message guid="' . $guid . '" version="1.0"  xmlns:ceb="http://www.chinaport.gov.cn/ceb" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <ceb:SummaryApply>
                <ceb:SummaryApplyHead>
                    <ceb:guid>' . $o_guid . '</ceb:guid>
                    <ceb:appType>' . $app_type . '</ceb:appType>
                    <ceb:appTime>' . $date . '</ceb:appTime>
                    <ceb:appStatus>2</ceb:appStatus>
                    <ceb:customsCode>' . $this -> declare_addre_code . '</ceb:customsCode>
                    <ceb:copNo>' . $cop_no . '</ceb:copNo>
                    <ceb:agentCode>' . $this -> commerce_plat_code . '</ceb:agentCode>
                    <ceb:agentName>' . $this -> company_name . '</ceb:agentName>
                    <ceb:ebcCode>' . $this -> commerce_plat_code . '</ceb:ebcCode>
                    <ceb:ebcName>' . $this -> company_name . '</ceb:ebcName>
                    <ceb:declAgentCode>' . $this -> commerce_plat_code . '</ceb:declAgentCode>
                    <ceb:declAgentName>' . $this -> company_name . '</ceb:declAgentName>
                    <ceb:summaryFlag>' . $chunk_num . '</ceb:summaryFlag>
                    <ceb:itemNameFlag>' . $now_chunk . '</ceb:itemNameFlag>
                    <ceb:msgCount>' . $chunk_num . '</ceb:msgCount>
                    <ceb:msgSeqNo>' . $now_chunk . '</ceb:msgSeqNo>
                </ceb:SummaryApplyHead>';
            $order_arr = array ();
            foreach ( $new_oid_info as $oid )
            {
                //获取订单信息
                $order_list = $this -> get_order_list_by_id( $oid );
                $tmp_str = '
                <ceb:SummaryApplyList>
                    <ceb:invtNo>' . $order_list[0]['odb_bill_code'] . '</ceb:invtNo>
                </ceb:SummaryApplyList>';
                $order_arr[] = $tmp_str;
            }
            $xml_content = implode( "" , $order_arr );
            $xml_end = '
            </ceb:SummaryApply>
            <ceb:BaseTransfer>
                <ceb:copCode>' . $this -> commerce_plat_code . '</ceb:copCode>
                <ceb:copName>' . $this -> company_name . '</ceb:copName>
                <ceb:dxpMode>' . $this -> dxp_model . '</ceb:dxpMode>
                <ceb:dxpId>' . $this -> dxp_id . '</ceb:dxpId>
                <ceb:note></ceb:note>
            </ceb:BaseTransfer>
        </ceb:CEB701Message>';
            $all_xml = $xml_head . $xml_content . $xml_end;
            //文件名
            $file_name = $this -> set_xml_file_name( $bs_type );
            $this -> write_info_to_xml( $all_xml , 5 , $file_name );
        }
        $this -> set_has_send_gram( $o_id , cls_data_gram::DATAGRAM_TYPE_SUM );
        $this -> get_zip_file( 5 );
        //header( 'Content-type: text/xml; charset=UTF-8' );
        //提示下载
        //header( "Content-Disposition:attachement;filename={$file_name}" );
        //echo $all_xml;
    }
    
    /**
     * 生成guid编号
     * @param string $input_msg 明细表ID
     * @return string guid
     */
    public function get_guid( $input_msg = null )
    {
        $input_msg = $input_msg ? $input_msg : microtime( true );
        $msg_32 = md5( uniqid( $input_msg , true ) );
        $msg = substr_replace( $msg_32 , '-' , 8 , 0 );
        $msg = substr_replace( $msg , '-' , 13 , 0 );
        $msg = substr_replace( $msg , '-' , 18 , 0 );
        $msg = substr_replace( $msg , '-' , 23 , 0 );
        return strtoupper( $msg );
    }
    
    /**
     * 获取唯一编码
     */
    public function get_cop_no()
    {
        return uniqid();
    }
    
    /**
     * 将其它币种金额转换为人民币金额
     * @param int $platform_id 平台ID
     * @param string $currency_name 币种名称
     * @param double $amounts 金额
     * @return float
     */
    public function get_rmb_amounts( $platform_id , $currency_name , $amounts )
    {
        $cls_currency = new cls_currency();
        $to_RMB_rate = $cls_currency -> get_rate_by_name( 'RMB' , $platform_id );
        //获取当前结算货币对美元的汇率
        $currency_rate = $cls_currency -> get_rate_by_name( $currency_name , $platform_id );
        //计算每个订单的总价   转换为美元 保留四位小数
        $total_price_usd = round( $amounts * $currency_rate , 4 );
        //计算每个订单的总价   转换为人民币  保留2位小数
        $total_price_rmb = round( $total_price_usd / $to_RMB_rate , 2 );
        return $total_price_rmb ? $total_price_rmb : $amounts;
    }
    
    /**
     * 根据订单ID获取订单信息
     * @param int $o_id 订单ID
     * @param array $ex_param 额外条件
     * @return array
     */
    public function get_order_list_by_id( $o_id , $ex_param = array () )
    {
        if ( is_array( $o_id ) )
        {
            $o_id = implode( ',' , array_remove_empty( $o_id ) );
        }
        $cls_order = new cls_order();
        $param = array ();
        $param['col'] = '/*slave*/o_id,
                        product_sku,
                        product_name,
                        od_price,
                        o_currency_id,
                        od_num*od_price total_amounts,
                        currency_name,
                        o_user_name,
                        product_legal_unit,
						product_zwsbmc,
						product_hs_code,
						o_weight,
						od_num,
						product_grossweight,
						product_application_unit,
						od_id,
						o_paid_time,
						o_load_time,
						country_name_cn,
						product_zwsbmc,
						o_platform_id,
						odb_logistics_num,
						product_legal_unit_2,
						product_netweight,
						odb_bill_code,
						o_tracknumber';
        $param['where'][] = "o_id in ({$o_id})";
        $param['join'] = '  inner join v2_order_detail on o_id = od_o_id';
        $param['join'] .= ' inner join v2_products on product_sku = od_sku';
        $param['join'] .= ' left join v2_currency on currency_id = o_currency_id';
        $param['join'] .= ' left join v2_country on country_name = o_country';
        $param['join'] .= ' left join v2_order_drawback on odb_o_id = o_id';
        if ( $ex_param['group'] )
        {
            $param['group'] = $ex_param['group'];
        }
        $order_list = $cls_order -> select_ex( $param );
        return $order_list;
    }
    
    /**
     * 检查数据库中是否已经存在
     * @param array $o_id_arr 订单明细ID数组
     * @return array
     */
    public function check_order_is_exist( array $o_id_arr )
    {
        $o_id_str = implode( ',' , array_remove_empty( $o_id_arr ) );
        $order_list = $this -> select_ex(
            array (
                'where' =>
                    "odb_o_id in ({$o_id_str})" ,
                'col' =>
                    '/*slave*/odb_o_id' ,
            )
        );
        $has_o_id_arr = array_column( $order_list , 'odb_o_id' );
        $unexist_o_arr = array_diff( $o_id_arr , $has_o_id_arr );
        return $unexist_o_arr;
    }
    
    /**
     * 生成新的订单编号
     * @param int $o_id 订单编号
     * @return string
     */
    public function get_special_o_id( $o_id )
    {
        $tmp_oid = $this -> order_pre . $o_id;
        //测试阶段所有单号添加“test”前缀
        //$tmp_oid = 'test' . $tmp_oid;
        return $tmp_oid;
    }
    
    /**
     * 设置订单已发报文信息
     * @param $o_id
     * @param int $num
     */
    public function set_has_send_gram( $o_id , $num = 0 )
    {
        $order_list = $this -> get_odb_list_by_oid( $o_id , '/*slave*/odb_o_id,odb_has_send_gram' );
        foreach ( $order_list as $order_info )
        {
            $tmp_num = $order_info['odb_has_send_gram'] | $num;
            $update_info = array (
                'odb_has_send_gram' => $tmp_num
            );
            $this -> update_drawback_order( $order_info['odb_o_id'] , $update_info );
        }
    }
    
    /**
     * 获取订单已发报文信息
     * @param string $o_id 订单ID
     * @return array
     */
    public function get_has_send_gram( $o_id )
    {
        $order_list = $this -> get_odb_list_by_oid( $o_id , '/*slave*/odb_o_id,odb_has_send_gram' );
        $return_msg = array ();
        foreach ( $order_list as $order_info )
        {
            $return_msg[$order_info['odb_o_id']] = $this -> parse_hase_send_dram( $order_info['odb_has_send_gram'] );
        }
        return $return_msg;
    }
    
    /**
     * 获取已发送报文记录
     * @param int/binary $num 类常量
     * @return string
     */
    public function parse_hase_send_dram( $num )
    {
        $return_msg = '';
        if ( cls_data_gram::DATAGRAM_TYPE_ORDER & $num )
        {
            $return_msg .= '已发送订单报文<br>';
        }
        if ( cls_data_gram::DATAGRAM_TYPE_PAID & $num )
        {
            $return_msg .= '已发送付款单报文<br>';
        }
        if ( cls_data_gram::DATAGRAM_TYPE_CLEAR & $num )
        {
            $return_msg .= '已发送撤销单报文<br>';
        }
        if ( cls_data_gram::DATAGRAM_TYPE_BILL & $num )
        {
            $return_msg .= '已发送出口清单报文<br>';
        }
        if ( cls_data_gram::DATAGRAM_TYPE_ZF & $num )
        {
            $return_msg .= '已发送总分单报文<br>';
        }
        if ( cls_data_gram::DATAGRAM_TYPE_SUM & $num )
        {
            $return_msg .= '已发送汇总单报文<br>';
        }
        return $return_msg;
    }
    
    /**
     * 获取复合订单的子订单重量
     * @param float $can_use_weight 剩余重量
     * @param int $arr_max 复合订单SKU种类个数
     * @param int $key 当前循环数
     * @param float $sku_weight 当前SKU重量
     * @return array
     */
    public function get_order_wetght( $can_use_weight , $arr_max , $key , $sku_weight )
    {
        $return_msg = array ();
        if ( $arr_max - $key > 1 )
        {
            $return_msg['o_weight'] = $sku_weight;
            $can_use_weight -= $sku_weight;
            $return_msg['can_use_weight'] = $can_use_weight;
        } else
        {
            $return_msg['can_use_weight'] = 0;
            $return_msg['o_weight'] = $can_use_weight;
        }
        return $return_msg;
    }
    
    
    /**
     * 将XML对象转换为数组
     * @param SimpleXMLElement $xmls xml对象
     * @return array
     */
    public function parse_xml_to_arr( SimpleXMLElement $xmls )
    {
        $array = [];
        foreach ( $xmls as $key => $xml )
        {
            $count = $xml -> count();
            if ( $count == 0 )
            {
                $res = (string)$xml;
            } else
            {
                $res = $this -> parse_xml_to_arr( $xml );
            }
            $array[$key] = $res;
        }
        return $array;
    }
    
    /**
     * 将数据写入到文件
     * @param string $xml_content 报文内容
     * @param int $type 报文类型
     * @return array 执行结果数组
     */
    public function write_info_to_xml( $xml_content , $type = 1 , $file_name = '1.xml' )
    {
        $return_msg = array ( 'ack' => 1 , 'msg' => '' );//返回结果数组
        $path = $this -> create_file_path . $type . '/';//文件写入目录
        //创建目录
        if ( !is_dir( $path ) )
        {
            mkdir( $path , 0777 , true );
        }
        //写入文件
        $flag = file_put_contents( $path . $file_name , $xml_content );
        if ( $flag > 0 )
        {
            $return_msg['ack'] = 1;
            $return_msg['msg'] = '文件写入成功!';
        } else
        {
            $return_msg['ack'] = 0;
            $return_msg['msg'] = '文件写入失败!';
        }
        return $return_msg;
    }
    
    /**
     * 将目录文件进行压缩
     * @param $type
     */
    public function get_zip_file( $type )
    {
        $path = $this -> create_file_path . $type . '/';    //文件目录
        $zip_filename = date( 'YmdHis' , time() ) . '.zip';
        $zip_filepath = $path . $zip_filename;//压缩后文件名
        
        $zip = new ZipArchive();
        $zip -> open( $zip_filepath , ZipArchive::CREATE );   //打开压缩包
        $xml_list = scandir( $path );   //扫描压缩目录
        foreach ( $xml_list as $xml_info )
        {
            if ( !strstr( $xml_info , '.xml' ) )
            {
                continue;
            }
            $file_path = $path . $xml_info;
            $zip -> addFile( $file_path , basename( $file_path ) );   //向压缩包中添加文件
        }
        $zip -> close();  //关闭压缩包
        //删除文件
        foreach ( $xml_list as $xml_info )
        {
            if ( $xml_info == '.' || $xml_info == '..' )
            {
                continue;
            }
            $file_path = $path . $xml_info;
            unlink( $file_path );
        }
        //下载ZIP文件
        header( 'Content-Type:text/html;charset=utf-8' );
        header( "Content-disposition:attachment;filename={$zip_filename}" );
        $filesize = filesize( $zip_filepath );
        readfile( $zip_filepath );
        header( 'Content-length:' . $filesize );
        //删除压缩包
        unlink( $zip_filepath );
    }
    
    /**
     * 设置报文文件名
     * @param string $btype 报文类型
     * @return string
     */
    public function set_xml_file_name( $btype )
    {
        //文件名
        return $btype . '_' . $this -> company_code . '_' . $this -> gram_code . '_' . uniqid() . '.xml';
    }
    
    /**
     * 返回单位列表
     * @return array
     */
    public function get_unit_code_list()
    {
        $cls_phu = new cls_data( 'v2_product_hs_unit' );
        $phu_list = $cls_phu -> select_ex(
            array (
                'col' => '/*slave*/phu_code,phu_unit' ,
            )
        );
        $this -> drawback_unit_list = change_main_key( $phu_list , 'phu_unit' );
        return $this -> drawback_unit_list;
    }
    
    /**
     * 获取海关编码对应法定单位
     * @param string $hs_code 海关编码
     * @return array
     */
    public function get_hscode_unit_code( $hs_code )
    {
        $return_msg = array ( 'ack' => 1 , 'msg' => '' );
        
        $cls_phc = new cls_data( 'v2_product_hs_code' );
        $phc_info = $cls_phc -> select_one_ex(
            array (
                'where' =>
                    "phc_hs_code = '{$hs_code}'" ,
                'join' =>
                    ' inner join v2_product_hs_unit p_1 on phc_unit_1 = p_1.phu_id' .
                    ' inner join v2_product_hs_unit p_2 on phc_unit_2 = p_2.phu_id' ,
                'col' =>
                    '/*slave*/p_1.phu_code unit_1,p_2.phu_code unit_2,phc_unit_2' ,
            )
        );
        $return_msg['unit_1'] = $phc_info['unit_1'];
        $return_msg['unit_2'] = $phc_info['unit_2'];
        if ( $phc_info['unit_2'] == '000' )
        {
            $return_msg['unit_2'] = '';
        }
        return $return_msg;
    }
}