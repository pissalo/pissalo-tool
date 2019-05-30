<?php

/**
 * abstract:阿里消息类
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年4月9日
 * Time:09:58:27
 */
class cls_aliba_msg extends cls_data
{
    /**
     * cls_aliba_msg constructor.
     */
    public function __construct()
    {
        parent::__construct('v2_aliba_msg');
    }

    public function get_msg_by_msg_id($msg_id, $col = '/*slave*/*')
    {
        return $this->select_one_ex(
            array(
                'where' => "am_msg_id = '{$msg_id}'",
                'col' => $col
            )
        );
    }

    /**
     * 添加消息
     * @author 王银龙
     * @param string $msg 消息内容
     * @return array 执行结果数组
     */
    public function add_msg($msg)
    {
        $return_msg = array();  //返回数组
        $type = 0;              //消息类型
        $am_msg = '';           //消息内容
        //将JSON转换为数组
        $msg_str = str_replace('\\', '', $msg);
        $msg_arr = json_decode($msg_str, true, 512, JSON_BIGINT_AS_STRING);
        //task_defeate_log(json_encode($msg_arr));
        //判断消息是否已经存在
        $msg_info = $this->get_msg_by_msg_id($msg_arr['msgId'], '/*slave*/am_id');
        if ($msg_info) {
            return array('ack' => 1, 'msg' => '该消息已经存在!');
        }
        //判断消息类型
        if (in_array($msg_arr['type'], array('ORDER_BUYER_VIEW_BUYER_MAKE', 'ORDER_BUYER_VIEW_ANNOUNCE_SENDGOODS', 'ORDER_BUYER_VIEW_ORDER_PAY', 'ORDER_BUYER_VIEW_ORDER_PRICE_MODIFY', 'ORDER_BUYER_VIEW_PART_PART_SENDGOODS'))) {
            $type = ALIBA_MSG_TYPE_ORDER;
            //订单消息
            if ('ORDER_BUYER_VIEW_BUYER_MAKE' == $msg_arr['type']) {
                //创建订单
                $am_msg = "创建阿里订单！";
            } elseif ('ORDER_BUYER_VIEW_ANNOUNCE_SENDGOODS' == $msg_arr['type']) {
                //卖家发货
                $am_msg = "卖家已发货！";
            } elseif ('ORDER_BUYER_VIEW_ORDER_PAY' == $msg_arr['type']) {
                $am_msg = "买家付款！";
            } elseif ('ORDER_BUYER_VIEW_ORDER_PRICE_MODIFY' == $msg_arr['type']) {
                $am_msg = "修改订单价格！";
            } elseif ('ORDER_BUYER_VIEW_PART_PART_SENDGOODS' == $msg_arr['type']) {
                $am_msg = '订单部分发货！';
            } elseif ('ORDER_BUYER_VIEW_ORDER_BOPS_CLOSE' == $msg_arr['type']) {
                $am_msg = '运营后台关闭订单！';
            } elseif ('ORDER_BUYER_VIEW_ORDER_SELLER_CLOSE' == $msg_arr['type']) {
                $am_msg = '卖家关闭订单！';
            }
            $am_msg .= "阿里订单号:{$msg_arr['data']['orderId']}。";
            $insert_info['am_order_id'] = $msg_arr['data']['orderId'];
            $insert_info['am_msg_status'] = $msg_arr['data']['currentStatus'];
        }
        if ($msg_arr['msgId'] && $type ) {
            $insert_info['am_type'] = $type;
            $insert_info['am_msg'] = $am_msg;
            $insert_info['am_add_time'] = substr($msg_arr['gmtBorn'], 0, -3);
            $insert_info['am_msg_id'] = $msg_arr['msgId'];
            $flag = $this->insert_ex($insert_info);
            if ($flag) {
                $return_msg['ack'] = 1;
                $return_msg['msg'] = '插入成功!';
            } else {
                $return_msg['ack'] = 0;
                $return_msg['msg'] = '插入失败!数据库操作失败。';
            }
        } else {
            $return_msg['ack'] = 0;
            $return_msg['msg'] = '插入失败!没有消息ID';
        }
        return $return_msg;
    }

    /**
     * 获取消息内容
     * @author 王银龙
     * @param array $param 重新参数数组
     */
    public function get_aliba_msg(array $param)
    {
    }

    /**
     * 通过ID获取消息
     * @author 王银龙
     * @param $id
     * @param $type
     */
    public function get_aliba_msg_by_id($id, $type)
    {
    }
}