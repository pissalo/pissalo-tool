<?php

namespace OA;

use OA\ClsData;

/**
 * abstract:审批配置类
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年2月20日
 * Time:16:16:34
 */
class ClsApprovalConfig extends \OA\ClsData
{
    /**
     * 构造函数
     * ClsApprovalConfig constructor.
     */
    public function __construct()
    {
        parent::__construct('oa_approval_config');
    }

    /**
     * 获取配置列表
     * @author 王银龙
     * @param $param    查询参数
     * @return array    查询结果数组
     */
    public function getApprovalConfigList($param)
    {
        return $this->selectEx($param);
    }

    /**
     * 通过配置ID获取审批配置
     * @author  王银龙
     * @param $acId     配置ID
     * @return array    查询结果数组
     */
    public function getApprovalConfigInfoById($acId)
    {
        return $this->getApprovalConfigList(
            array(
                'where' => "ac_id in ({$acId})"
            )
        );
    }

    /**
     * 获取配置详情信息
     * @author 王银龙
     * @param $param    查询参数
     * @return array    查询结果数组
     */
    public function getApprovalConfigDetailInfo($param)
    {
        if (!$param['join']) {
            $param['join'] = 'inner join oa_approval_config_detail on ac_id = acd_ac_id';
        }
        return $this->selectEx($param);
    }

    /**
     * 通过配置ID获取配置明细
     * @author 王银龙
     * @param $acId     配置ID
     * @return array    查询结果数组
     */
    public function getApprovalConfigDetailById($acId)
    {
        return $this->getApprovalConfigDetailInfo(
            array(
                'where' => "ac_id = {$acId}",
                'order' => "acd_check_level asc"
            )
        );
    }

    /**
     * 添加审批配置
     * @author 王银龙
     * @param array $approvalConfigInfo 配置信息主表信息数组
     * @param array $approvalConfigDetainInfo 配置信息明细表信息数组
     * @return array    执行结果数组
     */
    public function addConfig(array $approvalConfigInfo, array $approvalConfigDetainInfo)
    {
        $returnMsg = array();//返回数组
        //插入主表
        $this->transactionBegin();
        $acFlag = $this->addApprovalConfig($approvalConfigInfo);
        //插入明细表
        $errorNum = 0;
        foreach ($approvalConfigDetainInfo as &$detailInfo) {
            $detailInfo['acd_ac_id'] = $acFlag['insert_id'];
            $acdFlag = $this->addApprovalConfigDetail($detailInfo);
            $errorNum += $acdFlag['ack'] ? 0 : 1;
        }
        if ($acFlag['ack'] && !$errorNum) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '添加成功!';
            $this->transactionCommit();
            //记录日志
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '添加失败!';
            $this->transactionRollback();
        }
        return $returnMsg;
    }

    /**
     * 添加配置主表信息
     * @author 王银龙
     * @param array $approvalConfigInfo 配置主表信息数组
     * @return array    执行结果数组
     */
    public function addApprovalConfig(array $approvalConfigInfo)
    {
        $returnMsg = array();
        $flag = $this->insertEx($approvalConfigInfo, true);
        if ($flag['ack']) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '添加成功!';
            $returnMsg['insert_id'] = $flag['insert_id'];
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '添加失败!';
        }
        return $returnMsg;
    }

    /**
     * 添加配置明细表
     * @author 王银龙
     * @param array $approvalConfigDetainInfo 配置明细表数组
     * @return array    执行结果数组
     */
    public function addApprovalConfigDetail(array $approvalConfigDetainInfo)
    {
        $clsApprovalConfigDetail = new ClsData('oa_approval_config_detail');
        return $clsApprovalConfigDetail->insertEx($approvalConfigDetainInfo);
    }

    /**
     * 更新配置(主、明细表)
     * @author 王银龙
     * @param array $approvalConfigInfo 配置主表数组
     * @param array $approvalDetailList 配置明细表数组
     * @return array    执行结果数组
     */
    public function updateConfig(array $approvalConfigInfo, array $approvalDetailList)
    {
        $returnMsg = array();
        $clsConfigDetail = new ClsData('oa_approval_config_detail');
        $this->transactionBegin();
        //更新主表
        $approvalFlag = $this->updateApprovalConfig($approvalConfigInfo, $approvalConfigInfo['ac_id']);
        //清空明细表
        $deleteFlag = $clsConfigDetail->deleteEx("acd_ac_id = {$approvalConfigInfo['ac_id']}");
        //更新明细表
        $errorNum = 0;
        foreach ($approvalDetailList as $approvalConfigDetainInfo) {
            $approvalConfigDetainInfo['acd_ac_id'] = $approvalConfigInfo['ac_id'];
            $flag = $this->addApprovalConfigDetail($approvalConfigDetainInfo);
            $errorNum += $flag['ack'] ? 0 : 1;
        }
        if ($approvalFlag['ack'] && !$errorNum && $deleteFlag['ack']) {
            $this->transactionCommit();
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '更新成功!';
            //记录日志
            //$this->setConfigLog($approvalConfigInfo, $configInfoOld, $approvalDetailList, $detailListOld);
        } else {
            $this->transactionRollback();
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '更新失败!';
        }
        return $returnMsg;
    }

    /**
     * 更新配置(主表)
     * @author 王银龙
     * @param array $approvalConfigInfo 主表信息数组
     * @return bool 执行结果
     */
    public function updateApprovalConfig(array $approvalConfigInfo, $acId)
    {
        return $this->updateOne($approvalConfigInfo, "ac_id = '{$acId}'");
    }

    /**
     * 标记审批配置是否有效
     * @author              王银龙
     * @param $acId         配置ID
     * @param $validValue   是否有效
     * @return array        执行结果数组
     */
    public function updateApprovalConfigIsValid($acId, $validValue)
    {
        $returnMsg = array();
        //获取旧信息数组
        $configOldInfo = $this->getApprovalConfigInfoById($acId);
        $updateInfo = array('ac_approval_status' => $validValue);
        $flag = $this->updateOne($updateInfo, "ac_id = {$acId}");
        if ($flag['ack']) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '更新成功！';
            //日志
            $this->setApprovalConfigLog($updateInfo, $configOldInfo['msg'][0]);
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '更新失败!';
        }
        return $returnMsg;
    }

    /**
     * 设置配置日志
     * @param $configNewInfo
     * @param $configOldInfo
     * @param $detailNewInfo
     * @param $detailOldInfo
     */
    public function setConfigLog($configNewInfo, $configOldInfo, $detailNewInfo, $detailOldInfo)
    {
        $this->setApprovalConfigLog($configNewInfo, $configOldInfo);
        $this->setApprovalDetailLog($detailNewInfo, $detailOldInfo);
    }

    /**
     * 设置主表日志
     * @author                  王银龙
     * @param $configNewInfo    配置主表更新后信息数组
     * @param $configOldInfo    配置主管更新前信息数组
     */
    public function setApprovalConfigLog($configNewInfo, $configOldInfo)
    {
        global $adminId;
        global $adminU;
        global $adminZtId;
        $options = '';  //操作内容
        //启用、弃用配置
        if ($configOldInfo['ac_approval_status'] != $configNewInfo['ac_approval_status'] && isset($configNewInfo['ac_approval_status'])) {
            if (1 == $configNewInfo['ac_approval_status']) {
                $options .= "启用审批！";
            } else {
                $options .= "弃用审批！";
            }
        }
        //审批名称
        if ($configOldInfo['ac_spl_name'] != $configNewInfo['ac_spl_name'] && isset($configNewInfo['ac_spl_name'])) {
            $options .= "审批名称由：{$configOldInfo['ac_spl_name']}，改为：{$configNewInfo['ac_spl_name']}；";
        }
        //配置路径
        if ($configOldInfo['ac_path'] != $configNewInfo['ac_path'] && isset($configNewInfo['ac_path'])) {
            $options .= "配置路径由：{$configOldInfo['ac_path']}，改为：{$configNewInfo['ac_path']}；";
        }
        //类文件
        if ($configOldInfo['ac_class'] != $configNewInfo['ac_class'] && isset($configNewInfo['ac_class'])) {
            $options .= "类文件由：{$configOldInfo['ac_class']}，改为：{$configNewInfo['ac_class']}；";
        }
        //应用范围
        if ($configOldInfo['ac_use_range'] != $configNewInfo['ac_use_range'] && isset($configNewInfo['ac_use_range'])) {
            $oldEsArr = array_remove_empty(explode(',', $configOldInfo['ac_use_range']));
            $newEsArr = array_remove_empty(explode(',', $configNewInfo['ac_use_range']));
            $addEsStr = implode(',', array_diff($newEsArr, $oldEsArr));
            $deleteEsStr = implode(',', array_diff($oldEsArr, $newEsArr));
            $clsEs = new ClsEnterpriseStructure();
            if ($addEsStr) {
                $addEsName = $clsEs->getEsNameById($addEsStr);
                $options .= "应用范围添加：{$addEsName['msg'][0]['name']}（{$addEsStr}）；";
            }
            if ($deleteEsStr) {
                $deleteEsName = $clsEs->getEsNameById($deleteEsStr);
                $options .= "应用范围删除：{$deleteEsName['msg'][0]['name']}（{$deleteEsStr}）；";
            }
        }
        //抄送人
        if ($configOldInfo['ac_copy_to_user'] != $configNewInfo['ac_copy_to_user'] && isset($configNewInfo['ac_copy_to_user'])) {
            $oldUserArr = explode(',', $configOldInfo['ac_copy_to_user']);
            $newUserArr = explode(',', $configNewInfo['ac_copy_to_user']);
            $addUserStr = implode(',', array_diff($newUserArr, $oldUserArr));
            $deleteUserStr = implode(',', array_diff($oldUserArr, $newUserArr));
            $clsUser = new ClsUser();
            if ($addUserStr) {
                $addUserName = $clsUser->getUserNameById($addUserStr);
                $options .= "抄送人添加：{$addUserName['msg'][0]['name']}；";
            }
            if ($deleteUserStr) {
                $deleteUserName = $clsUser->getUserNameById($deleteUserStr);
                $options .= "抄送人删除：{$deleteUserName['msg'][0]['name']}；";
            }
        }
        if ($options) {
            $logContent = array(
                'lacUser' => $adminU,
                'lacTime' => time(),
                'lacOption' => $options,
                'lacAcId' => $configOldInfo['ac_id'],
            );
            $this->setLog($logContent);
        }
    }

    /**
     * 设置明细表日志
     * @author                  王银龙
     * @param $detailNewInfo    配置明细表更新后信息数组
     * @param $detailOldInfo    配置主管更新前信息数组
     */
    public function setApprovalDetailLog($detailNewInfo, $detailOldInfo)
    {
        global $splCheckTypeList;
        global $splMethodList;
        global $adminU;
        $detailNewInfo = change_main_key($detailNewInfo, 'acd_check_level');
        $detailOldInfo = change_main_key($detailOldInfo, 'acd_check_level');
        /*p_r($detailOldInfo);
        p_r($detailNewInfo);*/
        $options = '';
        $newCount = count($detailNewInfo);
        $oldCount = count($detailOldInfo);
        if ($newCount != $oldCount) {
            $options .= "审核级数由：{$oldCount}，改为：{$newCount}；";
        }
        foreach ($detailNewInfo as $newKey => $newInfo) {
            //审批类别
            if ($newInfo['acd_check_type'] != $detailOldInfo[$newKey]['acd_check_type']) {
                $options .= "第{$newKey}级审核的审核类别由：{$splCheckTypeList[$detailOldInfo[$newKey]['acd_check_type']]}，改为：{$splCheckTypeList[$newInfo['acd_check_type']]}；";
            } else {
                //审批人
                switch ($newInfo['acd_check_type']) {
                    case 1://上级审核
                        if ($newInfo['acd_check_sup_level'] != $detailOldInfo[$newKey]['acd_check_sup_level']) {
                            $options .= "第{$newKey}级审核的审核上级由：第{$detailOldInfo[$newKey]['acd_check_sup_level']}级上级，改为：{$newInfo['acd_check_sup_level']}；";
                        }
                        break;
                    case 2://指定成员
                        if ($newInfo['acd_check_user_id'] != $detailOldInfo[$newKey]['acd_check_user_id']) {
                            $oldUserArr = explode(',', $detailOldInfo[$newKey]['acd_check_user_id']);
                            $newUserArr = explode(',', $newInfo['acd_check_user_id']);
                            $addUserStr = implode(',', array_diff($newUserArr, $oldUserArr));
                            $deleteUserStr = implode(',', array_diff($oldUserArr, $newUserArr));
                            $clsUser = new ClsUser();
                            if ($addUserStr) {
                                $addUserName = $clsUser->getUserNameById($addUserStr);
                                $options .= "第{$newKey}级审核添加审核人：{$addUserName['msg'][0]['name']}";
                            }
                            if ($deleteUserStr) {
                                $deleteUserName = $clsUser->getUserNameById($deleteUserStr);
                                $options .= "第{$newKey}级审核删除审核人：{$deleteUserName['msg'][0]['name']}";
                            }
                        }
                        break;
                    default:
                        break;
                }
                //审批方式
                if ($newInfo['acd_check_method'] != $detailOldInfo[$newKey]['acd_check_method']) {
                    $options .= "第{$newKey}级审核的审核方式由：{$splMethodList[$detailOldInfo[$newKey]['acd_check_method']]}，改为：{$splMethodList[$newInfo['acd_check_method']]}；";
                }
            }
        }
        //echo $options;
        if ($options) {
            $logContent = array(
                'lacUser' => $adminU,
                'lacTime' => time(),
                'lacOption' => $options,
                'lacAcId' => $detailOldInfo[1]['acd_ac_id'],
            );
            $this->setLog($logContent);
        }
    }

    /**
     * 获取组织结构日志
     * @author 王银龙
     * @param int $esId 组织结构ID
     * @param int $page 页数
     * @param int $show_num 每页显示数
     * @return array 日志数组
     */
    public function getLog($id, $page, $show_num)
    {
        global $adminZtId;
        $returnMsg = array('ack' => 1);
        $clsLog = new ClsLog('oa');
        $clsLog->setCollection('log_approval_config');
        $where['zt_id'] = $adminZtId;
        if ($id) {
            $where['lacAcId'] = $id;
        }
        $logList = $clsLog->getList(intval($page), intval($show_num), $where);
        $newLogList = array();
        foreach ($logList['msg'] as $logKey => $logInfo) {
            $newLogList[$logKey]['user'] = $logInfo['lacUser'];
            $newLogList[$logKey]['add_time'] = $logInfo['lacTime'];
            $newLogList[$logKey]['option'] = $logInfo['lacOption'];
        }
        $returnMsg['msg'] = $newLogList;
        $returnMsg['count'] = $clsLog->getNum($where);
        return $returnMsg;
    }

    /**
     * 获取日志
     * @author              王银龙
     * @param $logContent   日志内容
     * @return array        执行结果数组
     */
    private function setLog($logContent)
    {
        global $adminZtId;
        //判断是否有账套
        if (!isset($logContent['zt_id'])) {
            $logContent['zt_id'] = $adminZtId;
        }
        $clsLog = new ClsLog('oa');
        $clsLog->setCollection('log_approval_config');
        return $clsLog->addLog($logContent);
    }
}
