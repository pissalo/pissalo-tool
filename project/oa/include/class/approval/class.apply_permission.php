<?php

namespace OA;

require_once 'interface.approval.php';

/**
 * abstract:申请权限类
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年2月14日
 * Time:17:11:51
 */
class ClsApplyPermission extends \OA\ClsApproval implements Approval
{
    /**
     * 构造函数
     * ClsApplyPermission constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 添加审批
     * @author 王银龙
     * @param array $approvalInfo 审批信息数组
     * @param int $otherId 具体业务表ID
     * @return array        执行结果数组
     */
    public function addApproval($approvalInfo, $otherId = 0, $type = 1)
    {
        //p_r($approvalInfo);//exit;
        global $adminId;
        global $adminZtId;
        $returnMsg = array();   //返回数组
        //获取用户当前所有权限
        $clsRolePermission = new ClsRolePermission();
        $userPermissionList = $clsRolePermission->getUserPerByUid($adminId, $approvalInfo['system_id']);
        $userPermissionList = $userPermissionList['msg'];
        /*p_r($userPermissionList);
        p_r($approvalInfo);//exit;*/
        //获取用户已申请，且有效的权限列表
        $this->transactionBegin();
        //插入用户权限表
        $otherIdArr = array();
        $clsUserExtendPermission = new \OA\ClsUserExtendPermission();
        $errorNum = 0;  //执行错误数量
        foreach ($approvalInfo['permission'] as $per_id) {
            $per_num = 0;
            if (in_array($per_id, $approvalInfo['read_all'])) {
                $per_num += 1;
            }
            if (in_array($per_id, $approvalInfo['read_sub'])) {
                $per_num += 2;
            }
            if (in_array($per_id, $userPermissionList['option_per_arr'])) {
                //已有的权限跳过
                if ($per_num == $userPermissionList['read_per_arr'][$per_id]) {
                    continue;
                } elseif ($userPermissionList['read_per_arr'][$per_id] == 3) {
                    continue;
                }
            }
            $perInfo['uep_option_per'] = $per_id;
            $perInfo['uep_read_per'] = $per_num;
            $perInfo['uep_user_id'] = $adminId;
            $perInfo['uep_add_time'] = time();
            $perInfo['uep_update_time'] = time();
            $perInfo['uep_add_user_id'] = $adminId;
            $perInfo['uep_approval_status'] = 0;
            $perInfo['uep_valid_time'] = strtotime($approvalInfo['uep_valid_time']);
            $perInfo['zt_id'] = $adminZtId;
            $perInfo['uep_system_id'] = $approvalInfo['system_id'];
            $extendFlag = $clsUserExtendPermission->addExtendsPermission($perInfo);
            array_push($otherIdArr, $extendFlag['insert_id']);
            $errorNum += $extendFlag['ack'] ? 0 : 1;
        }
        //exit;
        //插入审批主表
        if (!$otherIdArr) {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '申请失败，您已拥有该权限!';
            $this->transactionRollback();
            return $returnMsg;
        }
        $otherId = implode(',', array_remove_empty($otherIdArr));
        $parentFlag = parent::addApproval($approvalInfo, $otherId);
        if ($parentFlag['ack'] && 0 == $errorNum && $otherId) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '申请成功！';
            $this->transactionCommit();
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '申请失败，请重新尝试!';
            $this->transactionRollback();
        }
        return $returnMsg;
    }

    /**
     * 获取审批详情URL
     * @author 王银龙
     * @param array $approvalInfo 审批信息数组
     * @param array $configInfo 配置信息数组
     * @param int $type 类型
     * @return array                URL数组
     */
    public function getApprovalDetailUrl(array $approvalInfo, array $configInfo, $type = 1)
    {
        $returnMsg = array('ack' => 1);
        $clsUserExtendPermission = new ClsUserExtendPermission();
        $uepInfo = $clsUserExtendPermission->getExtendsPermissionByEupId($approvalInfo['approval_other_id']);
        if (1 == $type) {
            $url = $configInfo['ac_path'] . '?action=approvalShowDetal&system_id=' . $uepInfo['msg'][0]['uep_system_id'] . '&approvalId=' . $approvalInfo['approval_id'];
        } else {
            $url = $configInfo['ac_path'] . '?system_id=2&type=2';
        }
        $returnMsg['msg'] = $url;
        return $returnMsg;
    }

    /**
     * 获取额外权限通过审批ID
     * @author 王银龙
     * @param $approvalId   审批ID
     * @return array        执行结果数组
     */
    public function getPermissionByApprovalId($approvalId)
    {
        $returnMsg = array('ack' => 1);
        $approvalInfo = $this->getApprovalInfoById($approvalId);
        //获取权限列表
        $clsUserExtendPermission = new ClsUserExtendPermission();
        $permissionList = $clsUserExtendPermission->getExtendsPermissionByEupId($approvalInfo['msg'][0]['approval_other_id']);
        $permissionList = $permissionList['msg'];
        //p_r($permissionList);
        foreach ($permissionList as $permissionInfo) {
            //处理读权限
            if ($permissionInfo['uep_read_per']) {
                $returnMsg['read_per'][$permissionInfo['uep_option_per']] = $permissionInfo['uep_read_per'];
            }
            //处理操作权限
            $returnMsg['option_per'][] = $permissionInfo['uep_option_per'];
        }
        //p_r($returnMsg);
        return $returnMsg;
    }

    /**
     * 审核审批
     * @author 王银龙
     * @param array $approvalInfo 审批信息数组
     * @param array $configInfo 配置信息数组
     * @return mixed|void           执行结果
     */
    public function checkApproval(array $approvalInfo, array $configInfo)
    {
        global $adminU;
        $returnMsg = array();
        $isSuccess = 1;
        $this->transactionBegin();
        $parentFlag = parent::checkApproval($approvalInfo, $configInfo);
        //p_r($parentFlag);//exit;
        //判断是否执行成功
        if ($parentFlag['ack']) {
            //判断是否完结
            if ($parentFlag['end']) {
                //已完结，子类执行完结函数。
                $endFlag = $this->endApproval($approvalInfo['approval_other_id']);
                if (!$endFlag['ack']) {
                    $isSuccess = 0;
                    $returnMsg['ack'] = 0;
                    $returnMsg['msg'] = '审核失败,请重新尝试!';
                    $this->transactionRollback();
                }
            }
        } else {
            $isSuccess = 0;
            $returnMsg = $parentFlag;
            $this->transactionRollback();
        }
        if ($isSuccess) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '审核成功!';
            //日志
            $logInfo = array(
                'laApprovalId' => $approvalInfo['approval_id'],
                'laUser' => $adminU,
                'laTime' => time(),
                'laOptions' => '审核审批',
            );
            $this->setLog($logInfo);
            $this->transactionCommit();
        }
        //p_r($returnMsg);
        return $returnMsg;
    }

    /**
     * 完结执行函数
     * @author              王银龙
     * @param $otherId      其它ID
     * @return array|mixed  执行结果数组
     */
    public function endApproval($otherId)
    {
        $clsUserExtendPermission = new ClsUserExtendPermission();
        $updateInfo = array(
            'uep_approval_status' => 1
        );
        return $clsUserExtendPermission->updateUserExPermission($updateInfo, $otherId);
    }

    /**
     * 编辑审批
     * @author 王银龙
     * @param array $Info 审批信息
     * @return array        执行结果数组
     */
    public function editApproval($Info)
    {
        global $adminZtId;
        global $adminId;
        $returnMsg = array();   //返回数组
        //获取审批信息
        $approvalInfo = $this->getApprovalInfoById($Info['approvalId']);
        $approvalInfo = $approvalInfo['msg'][0];
        //获取用户当前所有权限
        $clsRolePermission = new ClsRolePermission();
        $userPermissionList = $clsRolePermission->getUserPerByUid($adminId, $Info['system_id']);
        $userPermissionList = $userPermissionList['msg'];
        $this->transactionBegin();
        //清空之前额外权限的数据
        $clsUserExtendPermission = new ClsUserExtendPermission();
        $deleteFlag = $clsUserExtendPermission->deleteEx("uep_id in ({$approvalInfo['approval_other_id']})");
        $errorNum = 0;  //执行错误数量
        $otherIdArr = array();  //ID数组
        foreach ($Info['permission'] as $per_id) {
            $per_num = 0;
            if (in_array($per_id, $Info['read_all'])) {
                $per_num += 1;
            }
            if (in_array($per_id, $Info['read_sub'])) {
                $per_num += 2;
            }
            if (in_array($per_id, $userPermissionList['option_per_arr']) && $per_num == $userPermissionList['read_per_arr'][$per_id]) {
                //已有的权限跳过
                continue;
            } else {
                $perInfo['uep_option_per'] = $per_id;
                $perInfo['uep_read_per'] = $per_num;
                $perInfo['uep_user_id'] = $adminId;
                $perInfo['uep_add_time'] = time();
                $perInfo['uep_update_time'] = time();
                $perInfo['uep_add_user_id'] = $adminId;
                $perInfo['uep_approval_status'] = 0;
                $perInfo['uep_valid_time'] = strtotime($Info['uep_valid_time'].' 23:59:59');
                $perInfo['zt_id'] = $adminZtId;
                $perInfo['uep_system_id'] = $Info['system_id'];
                $extendFlag = $clsUserExtendPermission->addExtendsPermission($perInfo);
                array_push($otherIdArr, $extendFlag['insert_id']);
                $errorNum += $extendFlag['ack'] ? 0 : 1;
            }
        }
        //更新ID
        $idStr = implode(',', array_remove_empty($otherIdArr));
        $updateInfo = array(
            'approval_other_id' => $idStr,
        );
        $updateFlag = $this->updateApproval($updateInfo, $Info['approvalId']);
        if ($deleteFlag['ack'] && $updateFlag['ack'] && 0 == $errorNum) {
            $this->transactionCommit();
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '更新成功!';
        } else {
            $this->transactionRollback();
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '更新成功!';
        }
        return $returnMsg;
    }

    /**
     * 获取审批具体内如
     * @author              王银龙
     * @param $approvalId   审批信息ID
     * @return array        审批内容数组
     */
    public function getApprovalContent($approvalId)
    {
        $returnMsg = array();
        $contentList = array();
        $clsUserExPer = new ClsUserExtendPermission();
        $clsRolePer = new ClsRolePermission();
        $clsPermission = new \OA\ClsPermissions();
        $approvalInfo = $this->getApprovalInfoById($approvalId);
        $perList = $clsUserExPer->getExtendsPermissionByEupId($approvalInfo['msg'][0]['approval_other_id']);
        //p_r($perList);
        $perIdStr = implode(',', array_remove_empty(array_column($perList['msg'], 'uep_option_per')));
        //操作权限
        $perNameList = $clsPermission->getPerNameById($perIdStr);
        $perNameList = $perNameList['msg'];
        $perNameList = change_main_key($perNameList, 'up_id');
        foreach ($perNameList as $perInfo) {
            $tmpStr = "";
            if ($perInfo['sup_name']) {
                $tmpStr .= $perInfo['sup_name'] . '->';
            }
            $tmpStr .= $perInfo['sub_name'];
            array_push($contentList, $tmpStr);
        }
        //读权限
        foreach ($perList['msg'] as $perInfos) {
            if ($perInfos['uep_read_per']) {
                $readNumArr = $clsRolePer->splitReadPer($perInfos['uep_read_per']);
                foreach ($readNumArr['msg'] as $readInfo) {
                    $readName = $clsRolePer->readPerToCh($readInfo);
                    $tmpReadPerStr = '';
                    if ($perNameList[$perInfos['uep_option_per']]['sup_name']) {
                        $tmpReadPerStr .= $perNameList[$perInfos['uep_option_per']]['sup_name'] . "->";
                    }
                    $tmpReadPerStr .= $perNameList[$perInfos['uep_option_per']]['sub_name'] . '->' . $readName['msg'];
                    array_push($contentList, $tmpReadPerStr);
                }
            }
        }
        $validDate = date('Y-m-d H:i:s', $perList['msg'][0]['uep_valid_time']);
        array_push($contentList, "<span style='color: red'>有效期:{$validDate}</span>");
        $returnMsg['msg'] = implode('<br>', $contentList);
        return $returnMsg;
    }
}
