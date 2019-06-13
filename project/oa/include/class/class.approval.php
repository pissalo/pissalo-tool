<?php

namespace OA;

/**
 * abstract:审批流接口
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年1月26日
 * Time:10:21:53
 */
class ClsApproval extends ClsData
{

    /**
     * 构造函数
     * ClsApproval constructor.
     */
    public function __construct()
    {
        parent::__construct('oa_approval');
    }

    /**
     * 获取审批配置信息
     * @author                  王银龙
     * @param $approvalConfigId 配置ID
     * @return array            配置数组
     */
    public function getApprovalConfig($approvalConfigId)
    {
        $clsApprovalConfig = new ClsApprovalConfig();
        return $clsApprovalConfig->getApprovalConfigList(array('where' => "ac_id = {$approvalConfigId}"));
    }

    /**
     * 获取审批信息
     * @author          王银龙
     * @param $param    查询参数
     * @return array    查询结果
     */
    public function getApprovalInfo($param)
    {
        return $this->selectEx($param);
    }

    /**
     * 通过审批ID获取审批信息
     * @author               王银龙
     * @param $approvalId    审批ID
     * @param int $hasDetail 是否获取明细
     * @return array         查询结果数组
     */
    public function getApprovalInfoById($approvalId, $hasDetail = 0)
    {
        $param = array();
        $param['where'][] = "approval_id = {$approvalId}";
        if ($hasDetail) {
            $param['join'] = 'left join oa_approval_detail on ad_approval_id = approval_id';
        }
        return $this->getApprovalInfo($param);
    }

    /**
     * 审核审批流
     * @author                    王银龙
     * @param array $approvalInfo 审批信息数组
     * @param array $configInfo 配置信息数组
     * @return array              执行结果数组
     */
    public function checkApproval(array $approvalInfo, array $configInfo)
    {
        global $adminId;
        $returnMsg = array();                 //返回结果数组
        $approvalFlag = array('ack' => 1);      //主表执行结果数组
        $approvalDetailFlag = array('ack' => 1);//明细表执行结果数组
        $endFlag = array('ack' => 1);

        //检查审批信息
        $validateApprovalFlag = $this->validateApproval($approvalInfo['approval_id'], array(APPROVAL_STATUS_CHECKING, APPROVAL_STATUS_UNCHECK), $adminId);
        if (!$validateApprovalFlag['ack']) {
            return array('ack' => 0, 'msg' => '审核失败，审批信息错误，请刷新页面之后再尝试!');
        }
        $approvalUpdateInfo = array(
            'approval_update_time' => time(),
        );
        $approvalDetaiUpdateInfo = array(
            'ad_update_time' => time(),
            'ad_check_time' => time(),
        );
        //判断状态
        if ($approvalInfo['approval_check_level'] == $approvalInfo['approval_end_check_level']) {
            $approvalUpdateInfo['approval_status'] = APPROVAL_STATUS_END;
            $approvalUpdateInfo['approval_end_time'] = time();
            $returnMsg['end'] = 1;
        } else {
            $approvalUpdateInfo['approval_status'] = APPROVAL_STATUS_CHECKING;
            $approvalUpdateInfo['approval_check_level'] = $approvalInfo['approval_check_level'] + 1;
        }
        //WHERE
        $where = "ad_check_user_id = {$adminId}";
        $where .= " and ad_approval_id = {$approvalInfo['approval_id']}";
        $where .= " and ad_check_level = {$approvalInfo['approval_check_level']}";
        //根据审批方式处理审批
        if (in_array($configInfo['acd_check_method'], array(SPL_METHOD_SORT, SPL_METHOD_SOME))) {
            //依次审核、或签直接进入下一级
            $approvalFlag = $this->updateApproval($approvalUpdateInfo, $approvalInfo['approval_id']);
            $approvalDetailFlag = $this->updateApprovalDetail($approvalDetaiUpdateInfo, $where);
        } else {
            //会签(全审核)
            $approvalDetailFlag = $this->updateApprovalDetail($approvalDetaiUpdateInfo, $where);
            //获取当前级别总审核人数和已审核人数
            $numInfo = $this->getCheckApprovalUserNum($approvalInfo['approval_id'], $approvalInfo['ad_check_level']);
            if ($numInfo['msg']['check_num'] == $numInfo['msg']['all_num']) {
                //全审核，进行下一级。
                $approvalFlag = $this->updateApproval($approvalUpdateInfo, $approvalInfo['approval_id']);
            } else {
                //需同级全部审批才进入下一级。
                unset($approvalUpdateInfo['approval_check_level']);
                unset($returnMsg['end']);
                $approvalUpdateInfo['approval_status'] = APPROVAL_STATUS_CHECKING;
                $approvalFlag = $this->updateApproval($approvalUpdateInfo, $approvalInfo['approval_id']);
            }
        }
        /*p_r($approvalFlag);
        p_r($approvalDetailFlag);
        p_r($endFlag);*/
        if ($approvalFlag['ack'] && $approvalDetailFlag['ack'] && $endFlag['ack']) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '审核成功';
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '审核失败';
        }
        return $returnMsg;
    }

    /**
     * 获取审批的某一级审核人数信息
     * @author              王银龙
     * @param $approvalId   审批ID
     * @param $checkLevel   审批级数
     * @return array        数量数组
     */
    public function getCheckApprovalUserNum($approvalId, $checkLevel)
    {
        $returnMsg = array();
        $checkApprovalUserNumInfo = $this->getApprovalInfo(
            array(
                'where' =>
                    " approval_id = {$approvalId}" .
                    " and ad_check_level = {$checkLevel}",
                'join' => 'left join oa_approval_detail on ad_approval_id = approval_id',
                'col' => 'sum(IF(ad_check_time>0,1,0)) check_num,count(DISTINCT ad_id) all_num'
            )
        );
        $returnMsg['ack'] = $checkApprovalUserNumInfo['ack'];
        $returnMsg['msg'] = $checkApprovalUserNumInfo['msg'][0];
        return $returnMsg;
    }

    /**
     * 添加审批
     * @author              王银龙
     * @param $approvalInfo 审批信息数组
     * @param $otherId      具体业务表ID
     * @return array        执行结果数组
     */
    public function addApproval($approvalInfo, $otherId, $type = 1)
    {
        global $adminZtId;
        global $adminId;

        $returnMsg = array();   //返回数组

        $clsUser = new \OA\ClsUser();
        $clsApprovalDetail = new ClsData('oa_approval_detail');
        $clsApprovalConfig = new ClsApprovalConfig();

        //获取审批配置信息
        $detailInsertArr = array();
        $approvalConfigList = $clsApprovalConfig->getApprovalConfigDetailInfo(
            array(
                'where' => "acd_ac_id = {$approvalInfo['ac_id']}",
            )
        );
        $approvalConfigList = $approvalConfigList['msg'];
        //插入主表
        $insertInfo = array(
            'approval_status' => APPROVAL_STATUS_UNCHECK,
            'approval_type' => $approvalInfo['ac_id'],
            'approval_other_id' => $otherId,
            'approval_add_time' => time(),
            'approval_update_time' => time(),
            'approval_approval_status' => 1,
            'approval_add_user_id' => $adminId,
            'approval_check_level' => 1,
            'zt_id' => $adminZtId,
            'approval_copy_to_user' => $approvalConfigList[0]['ac_copy_to_user']
        );
        $approvalFlag = $this->insertEx($insertInfo, true);
        //插入明细表
        //不同类型进行特殊处理
        $count = 1;
        foreach ($approvalConfigList as $approvalConfigInfo) {
            switch ($approvalConfigInfo['acd_check_type']) {
                case 1://上级审核
                    $clsUser->setUserSupList(array());
                    $userArr = $clsUser->getUserSupByIdLevel($adminId, $approvalConfigInfo['acd_check_sup_level']);
                    $userArr = $userArr['msg'];
                    break;
                case 2://指定成员审核
                    $userArr = array_unique(array_remove_empty(explode(',', $approvalConfigInfo['acd_check_user_id'])));
                    break;
                case 3://申请人自己审核
                    $userArr = array($adminId);
                    break;
            }
            foreach ($userArr as $key => $userId) {
                if (SPL_METHOD_SORT == $approvalConfigInfo['acd_check_method'] && 0 != $key) {
                    $count++;
                }
                $tmpArr = array();
                $tmpArr['ad_add_time'] = time();
                $tmpArr['ad_update_time'] = time();
                $tmpArr['ad_approval_status'] = 1;
                $tmpArr['ad_add_user_id'] = $adminId;
                $tmpArr['zt_id'] = $adminZtId;
                $tmpArr['ad_check_user_id'] = $userId;
                $tmpArr['ad_check_time'] = 0;
                $tmpArr['ad_check_level'] = $count;
                $tmpArr['ad_approval_id'] = $approvalFlag['insert_id'];
                $tmpArr['ad_config_level'] = $approvalConfigInfo['acd_check_level'];
                array_push($detailInsertArr, $tmpArr);
            }
            $count++;
        }
        $count--;
        $detailFlag = $clsApprovalDetail->insertBulk($detailInsertArr);
        //更新审核总级别
        $updateFlag = $this->updateApproval(array('approval_end_check_level' => $count), $approvalFlag['insert_id']);
        if ($approvalFlag['ack'] && $detailFlag['ack'] && $updateFlag['ack']) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '审批主、明细表添加成功!';
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '审批主、明细表添加失败!';
        }
        return $returnMsg;
    }

    /**
     * 修改审批状态
     * @author              王银龙
     * @param $approvalId   审批ID
     * @param $status       审批状态
     * @return array        执行结果数组
     */
    public function updateApprovalStatus($approvalId, $status)
    {
        $updateInfo = array(
            'approval_status' => $status
        );
        return $this->updateApproval($updateInfo, $approvalId);
    }

    /**
     * 更新审批
     * @author                    王银龙
     * @param array $approvalInfo 更新数组
     * @param $approvalId         更新审批ID
     * @return array              执行结果数组
     */
    public function updateApproval(array $approvalInfo, $approvalId)
    {
        $approvalInfoOld = $this->getApprovalInfoById($approvalId);
        if (!$approvalInfo['approval_update_time']) {
            $approvalInfo['approval_update_time'] = time();
        }
        $returnMsg = array();
        $flag = $this->updateOne($approvalInfo, "approval_id = {$approvalId}");
        if ($flag['ack']) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '更新成功!';
            //日志
            $this->setApprovalLog($approvalInfo, $approvalInfoOld['msg'][0]);
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '更新失败!';
        }
        return $returnMsg;
    }

    /**
     * 更新审批明细表
     * @author                          王银龙
     * @param array $approvalDetailInfo 更新审批明细数组
     * @param $where                    更新条件
     * @return bool                     执行结果数组
     */
    public function updateApprovalDetail(array $approvalDetailInfo, $where)
    {
        $clsApprovalDetail = new ClsData('oa_approval_detail');
        if (!$approvalDetailInfo['ad_update_time']) {
            $approvalDetailInfo['ad_update_time'] = time();
        }
        return $clsApprovalDetail->update($approvalDetailInfo, $where);
    }

    /**
     * 检查审批信息
     * @author 王银龙
     * @param $approvalId   审批ID
     * @param array $approvalStatusArr 审批状态数组
     * @return array 验证结果数组
     */
    public function validateApproval($approvalId, array $approvalStatusArr, $adminId)
    {
        $returnMsg = array();
        $statusFlag = $this->validateApprovalStatus($approvalId, $approvalStatusArr);
        $userFlag = $this->validateApprovalCheckUser($approvalId, $adminId);
        if ($statusFlag['ack'] && $userFlag['ack']) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '审批信息正确!';
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '审批信息错误!';
        }
        return $returnMsg;
    }

    /**
     * 检查审批状态
     * @author                  王银龙
     * @param $approvalId       审批ID
     * @param array $approvalStatusArr 审批状态
     * @return array            验证结果
     */
    public function validateApprovalStatus($approvalId, array $approvalStatusArr)
    {
        $returnMsg = array();   //返回数组
        $isSuccess = 1;         //验证是否通过
        //获取审批信息
        $approvalInfo = $this->getApprovalInfoById($approvalId);
        //检查审批状态
        if (!in_array($approvalInfo['msg'][0]['approval_status'], $approvalStatusArr)) {
            $isSuccess = 0;
        }
        //检查审批是否有效
        if (!$approvalInfo['msg'][0]['approval_approval_status']) {
            $isSuccess = 0;
        }
        if ($isSuccess) {
            $returnMsg['ack'] = 1;
        } else {
            $returnMsg['ack'] = 0;
        }
        return $returnMsg;
    }

    /**
     * 检查审批审核人
     * @author              王银龙
     * @param $approvalId   审批ID
     * @param $userId       审核人ID
     * @return array        执行结果
     */
    public function validateApprovalCheckUser($approvalId, $userId)
    {
        $returnMsg = array();
        $approvalInfo = $this->getApprovalInfo(
            array(
                'where' =>
                    "approval_id = {$approvalId}" .
                    " and approval_check_level = ad_check_level" .
                    " and ad_check_user_id = {$userId}" .
                    " and ad_check_time = 0",
                'join' => 'left join oa_approval_detail on approval_id = ad_approval_id'
            )
        );
        if ($approvalInfo['msg']) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '审批审核人正确!';
        } else {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '审批审核人错误!';
        }
        return $returnMsg;
    }

    /**
     * 撤销审批
     * @author            王银龙
     * @param $approvalId 审批ID
     * @return array      执行结果数组
     */
    public function cancelApproval($approvalId)
    {
        $returnMsg = array();
        //检查状态
        $checkFlag = $this->validateApprovalStatus(
            $approvalId,
            array(
                APPROVAL_STATUS_CHECKING,
                APPROVAL_STATUS_UNCHECK,
                APPROVAL_STATUS_BACK
            )
        );
        if (!$checkFlag['ack']) {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '撤销失败，审批状态错误，请刷新页面之后再尝试！';
        } else {
            $updateInfo = array(
                'approval_status' => APPROVAL_STATUS_CANCEL,
            );
            $flag = $this->updateApproval($updateInfo, $approvalId);
            if ($flag['ack']) {
                $returnMsg['ack'] = 1;
                $returnMsg['msg'] = '撤销成功!';
            } else {
                $returnMsg['ack'] = 0;
                $returnMsg['msg'] = '撤销失败，请重新尝试!';
            }
        }
        return $returnMsg;
    }

    /**
     * 重新提交审批
     * @author              王银龙
     * @param $approvalId   审批ID
     * @return array        执行结果
     */
    public function resubmitApproval($approvalId)
    {
        $returnMsg = array();
        //检查状态
        $checkFlag = $this->validateApprovalStatus(
            $approvalId,
            array(
                APPROVAL_STATUS_BACK
            )
        );
        $clsApprovalConfig = new ClsData('oa_approval_detail');
        $detailUpdateInfo = array(
            'ad_update_time' => time(),
            'ad_check_time' => 0
        );
        $detailFlag = $clsApprovalConfig->update($detailUpdateInfo, "ad_approval_id = {$approvalId}");
        //p_r($detailFlag);
        if (!$checkFlag['ack']) {
            return $checkFlag;
        }
        $updateInfo = array(
            'approval_status' => APPROVAL_STATUS_CHECKING,
            'approval_check_level' => 1
        );
        $approvalFlag = $this->updateApproval($updateInfo, $approvalId);
        //p_r($approvalFlag);
        if ($approvalFlag['ack'] && $detailFlag['ack']) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '重新提交成功!';
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '重新提交失败！';
        }
        return $returnMsg;
    }

    /**
     * 打回审批
     * @author              王银龙
     * @param $approvalId   审批ID
     * @return array        执行结果
     */
    public function backApproval($approvalId)
    {
        //检查状态、审批人
        global $adminId;
        $returnMsg = array();
        $checkFlag = $this->validateApproval(
            $approvalId,
            array(
                APPROVAL_STATUS_UNCHECK,
                APPROVAL_STATUS_CHECKING
            ),
            $adminId
        );
        if (!$checkFlag['ack']) {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '打回失败，审批信息错误，请刷新页面再尝试!';
        } else {
            $flag = $this->updateApprovalStatus($approvalId, APPROVAL_STATUS_BACK);
            if ($flag['ack']) {
                $returnMsg['ack'] = 1;
                $returnMsg['msg'] = '打回成功!';
            } else {
                $returnMsg['ack'] = 0;
                $returnMsg['msg'] = '打回失败，请重新尝试!';
            }
        }
        return $returnMsg;
    }

    /**
     * 添加审批日志
     * @author 王银龙
     * @param $approvalInfoNew  更新后审批数组
     * @param $approvalInfoOld  更新前审批数组
     */
    public function setApprovalLog($approvalInfoNew, $approvalInfoOld)
    {
        global $approvalStatusList;
        global $adminU;
        $options = '';
        //状态
        if ($approvalInfoOld['approval_status'] != $approvalInfoNew['approval_status'] && isset($approvalInfoNew['approval_status'])) {
            if (APPROVAL_STATUS_BACK == $approvalInfoOld['approval_status']) {
                $options = "重新提交审批";
            } elseif (APPROVAL_STATUS_BACK == $approvalInfoNew['approval_status']) {
                $options = "打回审批";
            } elseif (APPROVAL_STATUS_CANCEL == $approvalInfoNew['approval_status']) {
                $options = '撤销审批';
            }
        }
        //撤销
        if ($approvalInfoNew['approval_approval_status'] != $approvalInfoOld['approval_approval_status'] && isset($approvalInfoNew['approval_approval_status'])) {
            $options = "撤销审批";
        }
        if ($options) {
            $logInfo = array(
                'laApprovalId' => $approvalInfoOld['approval_id'],
                'laUser' => $adminU,
                'laTime' => time(),
                'laOptions' => $options,
            );
            $this->setLog($logInfo);
        }
    }

    /**
     * 获取日志
     * @author              王银龙
     * @param $approvalId   审批ID
     * @param $page         页数
     * @param $show_num     每页显示数
     * @return array        查询结果数组
     */
    public function getLog($approvalId, $page, $show_num)
    {
        global $adminZtId;
        $returnMsg = array('ack' => 1);
        $clsLog = new ClsLog('oa');
        $clsLog->setCollection('log_approval');
        $where['zt_id'] = $adminZtId;
        if ($approvalId) {
            $where['laApprovalId'] = $approvalId;
        }
        $logList = $clsLog->getList(intval($page), intval($show_num), $where);
        $newLogList = array();
        foreach ($logList['msg'] as $logKey => $logInfo) {
            $newLogList[$logKey]['user'] = $logInfo['laUser'];
            $newLogList[$logKey]['add_time'] = $logInfo['laTime'];
            $newLogList[$logKey]['option'] = $logInfo['laOptions'];
        }
        $returnMsg['msg'] = $newLogList;
        $returnMsg['count'] = $clsLog->getNum($where);
        return $returnMsg;
    }

    /**
     * 设置日志
     * @author                    王银龙
     * @param array $logContent 日志内容数组
     * @return array              执行结果数组
     */
    public function setLog($logContent)
    {
        global $adminZtId;
        $clsLog = new ClsLog('oa');
        if (!$logContent['zt_id']) {
            $logContent['zt_id'] = $adminZtId;
        }
        $clsLog->setCollection('log_approval');
        return $clsLog->addLog($logContent);
    }

    /**
     * 获取审批过程
     * @author 王银龙
     * @param array $approvalList 审批信息数组
     */
    public function getApprovalProcess(array $approvalList)
    {
        //p_r($approvalList);
        $returnMsg = array('ack' => 1);
        $checkLevelArr = array();
        //同级别的放一起
        $sameLevelArr = array();
        foreach ($approvalList as $approvalInfo) {
            $sameLevelArr[$approvalInfo['ad_check_level']][] = $approvalInfo;
        }
        //p_r($sameLevelArr);
        foreach ($sameLevelArr as $sameLevelInfo) {
            $tmpArr = array();
            if (SPL_METHOD_SOME == $sameLevelInfo[0]['acd_check_method']) {
                //或签
                $isCheck = false;
                $checkUserArr = array();
                foreach ($sameLevelInfo as $levelInfo) {
                    if ($levelInfo['ad_check_time']) {
                        $isCheck = true;
                        $checkUserArr = array($levelInfo['check_username']);
                        $checkTime = date('Y-m-d H:i:s', $levelInfo['ad_check_time']);
                        break;
                    } else {
                        array_push($checkUserArr, $levelInfo['check_username']);
                        $checkTime = '';
                    }
                }
                $tmpArr['checkUser'] = implode(',', $checkUserArr);
                $tmpArr['checkTime'] = $checkTime;
            } else {
                $tmpArr['checkUser'] = $sameLevelInfo[0]['check_username'];
                $tmpArr['checkTime'] = $sameLevelInfo[0]['ad_check_time'] ? date('Y-m-d H:i:s', $sameLevelInfo[0]['ad_check_time']) : '';
            }
            array_push($checkLevelArr, $tmpArr);
        }
        $returnMsg['msg'] = $checkLevelArr;
        return $returnMsg;
    }
}
