<?php

/**
 * 后台管理员类
 * ===========================================================
 * 版权所有 (C) 2006-2020 我不是稻草人，并保留所有权利。
 * 网站地址: http://www.dcrcms.com
 * ----------------------------------------------------------
 * 这是免费开源的软件；您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * 不允许对程序修改后再进行发布。
 * ==========================================================
 * @author:     我不是稻草人 <junqing124@126.com>
 * @version:    v1.0.3
 * @package class
 * @since 1.0.8
 */

class cls_alibaba
{
    private $server_host = "gw.open.1688.com";
    private $http_port = 80;
    private $https_port = 443;
    private $app_key;
    private $sec_key;
    private $sync_api_client;

    public function set_server_host( $server_host )
    {
        $this->server_host = $server_host;
    }

    public function set_http_port( $http_port )
    {
        $this->http_port = $http_port;
    }

    public function set_https_port( $https_port )
    {
        $this->https_port = $https_port;
    }

    public function set_app_key( $app_key )
    {
        $this->app_key = $app_key;
    }

    public function set_sec_key( $sec_key )
    {
        $this->sec_key = $sec_key;
    }

    public function init_client()
    {
        $client_policy = new ClientPolicy ();
        $client_policy->appKey = $this->app_key;
        $client_policy->secKey = $this->sec_key;
        $client_policy->httpPost = $this->http_post;
        $client_policy->httpsPort = $this->https_port;
        $client_policy->serverHost = $this->server_host;

        $this->sync_api_client = new SyncAPIClient ( $client_policy );
    }

    public function get_api_client()
    {
        if ( $this->sync_api_client == null )
        {
            $this->init_client();
        }
        return $this->sync_api_client;
    }

    /**
     * 鏍规嵁鎺堟潈鐮佹崲鍙栨巿鏉冧护鐗�
     *
     * @param
     *            code 鎺堟潈鐮�
     * @return 鎺堟潈浠ょ墝
     */
    public function get_token( $code )
    {
        $req_policy = new RequestPolicy ();
        $req_policy->httpMethod = "POST";
        $req_policy->needAuthorization = false;
        $req_policy->requestSendTimestamp = true;
        $req_policy->useHttps = true;
        $req_policy->requestProtocol = DataProtocol::param2;

        $request = new APIRequest ();
        $request->addtionalParams [ "code" ] = $code;
        $request->addtionalParams [ "grant_type" ] = "authorization_code";
        $request->addtionalParams [ "need_refresh_token" ] = true;
        $request->addtionalParams [ "client_id" ] = $this->app_key;
        $request->addtionalParams [ "client_secret" ] = $this->sec_key;
        $request->addtionalParams [ "redirect_uri" ] = "default";
        $api_id = new APIId ( "system.oauth2", "getToken", $req_policy->defaultApiVersion );
        $request->apiId = $api_id;

        $result_definition = new AuthorizationToken ();
        $this->get_api_client()->send( $request, $result_definition, $req_policy );
        return $result_definition;
    }

    /**
     * 鍒锋柊token
     *
     * @param
     *            refreshToken refresh 浠ょ墝
     * @return 鎺堟潈浠ょ墝
     */
    public function refresh_token( $refresh_token )
    {
        $req_policy = new RequestPolicy ();
        $req_policy->httpMethod = "POST";
        $req_policy->needAuthorization = false;
        $req_policy->requestSendTimestamp = true;
        $req_policy->useHttps = true;
        $req_policy->requestProtocol = DataProtocol::param2;

        $request = new APIRequest ();
        $request->addtionalParams [ "refreshToken" ] = $refresh_token;
        $request->addtionalParams [ "grant_type" ] = "refreshToken";
        $request->addtionalParams [ "client_id" ] = $this->app_key;
        $request->addtionalParams [ "client_secret" ] = $this->sec_key;
        $api_id = new APIId ( "system.oauth2", "getToken", $req_policy->defaultApiVersion );
        $request->apiId = $api_id;

        $result_definition = new AuthorizationToken ();
        $this->get_api_client()->send( $request, $result_definition, $req_policy );
        return $result_definition;
    }

    public function get_alipay_url( AlibabaAlipayUrlGetParam $param, $access_token, AlibabaAlipayUrlGetResult $result_definition )
    {
        $req_policy = new RequestPolicy ();
        $req_policy->httpMethod = "POST";
        $req_policy->needAuthorization = true;
        $req_policy->requestSendTimestamp = false;
        $req_policy->useHttps = false;
        $req_policy->useSignture = true;
        $req_policy->accessPrivateApi = false;

        $request = new APIRequest ();
        $api_id = new APIId ( "com.alibaba.trade", "alibaba.alipay.url.get", 1 );
        $request->apiId = $api_id;

        $request->requestEntity = $param;
        $request->accessToken = $access_token;
        $result = $this->get_api_client()->send( $request, $result_definition, $req_policy );

        return $result;
    }
    
    //获取买家物流信息
    public function get_buyer_logistics_num(  AlibabaTradeGetLogisticsInfosBuyerViewParam $param, $access_token, AlibabaTradeGetLogisticsInfosBuyerViewResult $result_definition )
    {
        $req_policy = new RequestPolicy ();
        $req_policy->httpMethod = "POST";
        $req_policy->needAuthorization = true;
        $req_policy->requestSendTimestamp = false;
        $req_policy->useHttps = false;
        $req_policy->useSignture = true;
        $req_policy->accessPrivateApi = false;

        $request = new APIRequest ();
        $api_id = new APIId ( "com.alibaba.logistics", "alibaba.trade.getLogisticsInfos.buyerView", 1 );
        $request->apiId = $api_id;

        $request->requestEntity = $param;
        $request->accessToken = $access_token;
        //p_r($request);
        $result = $this->get_api_client()->send( $request, $result_definition, $req_policy );

        return $result;
    }
    
    /*
     * 获取阿里产品信息请求信息
     * @param $cls_product_param 应用级参数
     * @param $cls_product_result 接收返回结果对象
     * @param $token 授权token
     * @param $result API返回对象
     * */
    function get_ali_product_info_result( $key )
    {
        $cur_dir1 = WEB_CLASS . '/third_party_api/alibaba/ocean.client.php.basic-sources/';
        chdir( $cur_dir1 );
        include_once( 'com/alibaba/openapi/client/APIId.class.php' );
        include_once( 'com/alibaba/openapi/client/APIRequest.class.php' );
        include_once( 'com/alibaba/openapi/client/APIResponse.class.php' );
        include_once( 'com/alibaba/openapi/client/SyncAPIClient.class.php' );
        include_once( 'com/alibaba/openapi/client/util/DateUtil.class.php' );
        include_once( 'com/alibaba/openapi/client/policy/ClientPolicy.class.php' );
        include_once( 'com/alibaba/openapi/client/policy/DataProtocol.class.php' );
        include_once( 'com/alibaba/openapi/client/policy/RequestPolicy.class.php' );
    
        include_once( WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaProductSuggestCrossBorderParam.class.php' );
        include_once( WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaProductSuggestCrossBorderResult.class.php' );
        include_once( WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaSearchProductSearchResultInfo.class.php' );
        include_once( WEB_CLASS . '/third_party_api/alibaba/com/alibaba/product/param/AlibabaSimpleSku.class.php' );
    
        //接收参数
        $cls_product_param = new AlibabaProductSuggestCrossBorderParam();
        //接收返回结果
        $cls_product_result = new AlibabaProductSuggestCrossBorderResult();
        //c2e90474-0efd-4dc1-9b01-f23a2bea2c45
        
        $this -> set_app_key( 5423999 );
        $this -> set_sec_key( 'GWianlTz6fT' );
        $token = 'b1ca08b9-b569-4c4f-9007-2d35f29a6375';
        $cls_product_param -> setKeyWord( $key );
        $req_policy = new RequestPolicy ();
        $req_policy -> httpMethod = "POST";
        $req_policy -> needAuthorization = true;
        $req_policy -> requestSendTimestamp = false;
        $req_policy -> useHttps = false;
        $req_policy -> useSignture = true;
        $req_policy -> accessPrivateApi = false;
    
        $request = new APIRequest ();
        $api_id = new APIId ( "com.alibaba.product" , "alibaba.product.suggest.crossBorder" , 1 );
        $request -> apiId = $api_id;
    
        //应用级参数输入
        $request -> requestEntity = $cls_product_param;
        $request -> accessToken = $token;
        $result = $this -> get_api_client() -> send( $request , $cls_product_result , $req_policy );
        return $result->getResultList();
    }
    
    /**
     * 通过1688产品ID获取1688产品信息
     * @author 王银龙
     * @param AlibabaAgentProductSimpleGetParam $cls_product_param
     * @param AlibabaAgentProductSimpleGetResult $cls_product_result
     * @param $token
     * @return mixed
     */
    function get_ali_product_info_by_id( AlibabaAgentProductSimpleGetParam $cls_product_param, AlibabaAgentProductSimpleGetResult $cls_product_result, $token  )
    {
        $req_policy = new RequestPolicy ();
        $req_policy -> httpMethod = "POST";
        $req_policy -> needAuthorization = true;
        $req_policy -> requestSendTimestamp = false;
        $req_policy -> useHttps = false;
        $req_policy -> useSignture = true;
        $req_policy -> accessPrivateApi = false;
        
        $request = new APIRequest ();
        $api_id = new APIId ( "com.alibaba.product" , "alibaba.agent.product.simple.get" , 1 );
        $request -> apiId = $api_id;
        //应用级参数输入
        $request -> requestEntity = $cls_product_param;
        $request -> accessToken = $token;
        $result = $this -> get_api_client() -> send( $request , $cls_product_result , $req_policy );
    
        $list = new AlibabaProductProductInfo();
        if (is_object($result))
        {
            $list = $result -> getProductInfo();
        }
        return $list;
    }
    
    /**
     * 创建1688订单
     * @author 王银龙
     * @param AlibabaTradeCreateCrossOrderParam $order_param    应用参数
     * @param AlibabaTradeCreateCrossOrderResult $order_result  执行结果
     * @param $token                                            授权TOKEN
     * @return array                                            执行结果
     */
    function create_aliba_order(AlibabaTradeCreateCrossOrderParam $order_param , AlibabaTradeCreateCrossOrderResult $order_result , $token)
    {
        $req_policy = new RequestPolicy ();
        $req_policy -> httpMethod = "POST";
        $req_policy -> needAuthorization = true;
        $req_policy -> requestSendTimestamp = false;
        $req_policy -> useHttps = false;
        $req_policy -> useSignture = true;
        $req_policy -> accessPrivateApi = false;
        $request = new APIRequest ();
        $api_id = new APIId ("com.alibaba.trade" , "alibaba.trade.createCrossOrder" , 1);
        $request -> apiId = $api_id;
        //应用级参数输入
        $request -> requestEntity = $order_param;
        $request -> accessToken = $token;
        return $this -> get_api_client() -> send($request , $order_result , $req_policy);
    }
    
    /**
     * 关注阿里产品
     * @author 王银龙
     * @param AlibabaProductFollowCrossborderParam $param   应用参数
     * @param AlibabaProductFollowCrossborderResult $result 执行结果
     * @param $token    授权TOKEN
     * @return mixed    执行结果
     */
    function follow_aliba_product( AlibabaProductFollowCrossborderParam $param , AlibabaProductFollowCrossborderResult $result , $token )
    {
        $req_policy = new RequestPolicy ();
        $req_policy -> httpMethod = "POST";
        $req_policy -> needAuthorization = true;
        $req_policy -> requestSendTimestamp = false;
        $req_policy -> useHttps = false;
        $req_policy -> useSignture = true;
        $req_policy -> accessPrivateApi = false;
        $request = new APIRequest ();
        $api_id = new APIId ( "com.alibaba.product" , "alibaba.product.follow.crossborder" , 1 );
        $request -> apiId = $api_id;
        //应用级参数输入
        $request -> requestEntity = $param;
        $request -> accessToken = $token;
        return $this -> get_api_client() -> send( $request , $result , $req_policy );
    }
}