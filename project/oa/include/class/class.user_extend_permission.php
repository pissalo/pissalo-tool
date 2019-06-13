<?php

namespace OA;

/**
 * abstract:用户额外权限类
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年2月19日
 * Time:11:19:13
 */
class ClsUserExtendPermission extends \OA\ClsData
{
    /**
     * 构造函数
     * ClsUserExtendPermission constructor.
     */
    public function __construct()
    {
        parent::__construct('oa_user_extends_permission');
    }

    /**
     * 添加用户额外权限
     * @author 王银龙
     * @param $perInfo 权限信息数组
     * @return array   执行结果数组
     */
    public function addExtendsPermission(array $perInfo)
    {
        return $this->insertEx($perInfo, true);
    }

    /**
     * 获取额外权限信息
     * @author 王银龙
     * @param $param 查询参数数组
     * @return array 查询结果数组
     */
    public function getExtendsPermissionInfo($param)
    {
        return $this->selectEx($param);
    }

    /**
     * 通过ID获取额外权限信息
     * @author 王银龙
     * @param int/string $eupId 额外权限表ID
     * @return array 查询结果数组
     */
    public function getExtendsPermissionByEupId($eupId)
    {
        return $this->getExtendsPermissionInfo(
            array(
                'where' => "uep_id in ({$eupId})"
            )
        );
    }

    /**
     * 通过用户ID获取额外权限数组
     * @author 王银龙
     * @param int/string $userId 用户ID
     * @param int $isValid 是否有效
     * @return array 执行结果数组
     */
    public function getExtendPermissionByUserId($userId, $isValid = 0)
    {
        $param['where'][] = "uep_user_id in ({$userId})";
        if ($isValid) {
            $time = time();
            $param['where'][] = "uep_valid_time >= {$time}";
            $param['where'][] = "uep_approval_status = 1";
        }
        return $this->getExtendsPermissionInfo($param);
    }

    /**
     * 通过用户获取具体操作、读权限
     * @author 王银龙
     * @param $userId   用户ID
     * @return array    处理结果数组
     */
    public function getExPermissionArrByUserId($userId)
    {
        $returnMsg = array('ack' => 1, 'option_per_arr' => array(), 'read_per_arr' => array());
        $exPermissionList = $this->getExtendPermissionByUserId($userId, 1);
        foreach ($exPermissionList['msg'] as $permissionInfo) {
            array_push($returnMsg['option_per_arr'], $permissionInfo['uep_option_per']);
            if ($permissionInfo['uep_read_per']) {
                if ($returnMsg['read_per_arr'][$permissionInfo['uep_option_per']] == 2 || !$returnMsg['read_per_arr'][$permissionInfo['uep_option_per']]) {
                    $returnMsg['read_per_arr'][$permissionInfo['uep_option_per']] = $permissionInfo['uep_read_per'];
                }
            }
        }
        return $returnMsg;
    }

    /**
     * 更新
     * @author 王银龙
     * @param array $updateInfo 更新数组
     * @param $uepId            更新ID
     * @return array            执行结果数组
     */
    public function updateUserExPermission(array $updateInfo, $uepId)
    {
        $returnMsg = array();
        $flag = $this->update($updateInfo, "uep_id in ({$uepId})");
        if ($flag['ack']) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '更新成功!';
            //日志
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '更新失败';
        }
        return $returnMsg;
    }

    /**
     * 设置日志
     * @param $content  日志内容
     */
    public function setLog($content)
    {
    }

    /**
     * 获取日志
     * @param $where    查询条件
     */
    public function getLog($where)
    {
    }
}
