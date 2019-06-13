<?php
/**
 * api类
 */
namespace OA;

/**
 * Class ClsApi
 * @package OA
 */
class ClsApi extends ClsData
{

    /**
     * ClsApi constructor.
     */
    public function __construct()
    {
        parent:: setTable('@#@api');
    }

    /**
     * 获取随机token
     * @param int $len 长度
     * @return array
     */
    public function getRandToken($len = 40)
    {
        return array( 'ack' => 1, 'msg' => get_rand_str($len) );
    }
}
