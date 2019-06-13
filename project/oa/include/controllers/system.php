<?php
namespace Controller;

class System
{
    private $data;

    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * 重新初始化系统权限
     */
    public function recallSystemPermission()
    {
        global $adminId;
        $clsRp = new \OA\ClsRolePermission();
        $perTmpArr = $clsRp->getUserPerByUid($adminId);
        $_SESSION['adminOptionPer'] = $perTmpArr['msg']['option_per_arr'];
        $_SESSION['adminReadPer'] = $perTmpArr['msg']['read_per_arr'];

        return array('ack' => 1);
    }

    /**
     * 获取公司列表
     */
    public function getCompanyList()
    {
        $clsData = new \OA\ClsData('oa_config_company');

        $returnMsg = array('code' => 'OK', 'ack' => 1, 'item' => array());

        $companyList = $clsData->selectEx()['msg'];
        foreach ($companyList as $key => $companyInfo) {
            $companyList[$key]['cc_add_time'] = date('Y-m-d', $companyInfo['cc_add_time']);
        }
        $returnMsg['item'] = $companyList;

        return $returnMsg;
    }

    /**
     * 配置子系统
     * @return array ack=1
     */
    public function companyConfig()
    {
        $cls_data = new \OA\ClsData('oa_config_company');
        global $adminId;
        $data = $this->data;
        foreach ($data['name'] as $key => $name) {
            if ($name) {
                $db_info = array();
                $db_info['cc_id'] = $data['id'][$key];
                $db_info['cc_update_time'] = time();
                $db_info['cc_add_time'] = time();
                $db_info['cc_approval_status'] = SH_YSH;
                $db_info['cc_add_user_id'] = $adminId;
                $db_info['cc_name'] = $name;
                $db_info['cc_note'] = $data['note'][$key];

                //添加
                $has_info = $cls_data->selectOneEx(
                    array(
                        'col' => 'cc_id',
                        'where' =>
                            "cc_name='{$db_info['cc_name']}' "
                    )
                );
                //p_r($has_info);
                if ($has_info['msg']) {
                    $cls_data->updateOne($db_info, "cc_id={$has_info['msg'][0]['cc_id']}");
                } else {
                    $cls_data->insertEx($db_info);
                }
                //p_r($cls_data);
            }
        }
        return array('ack' => 1);
    }

    /**
     * 配置子系统
     * @return array ack=1
     */
    public function systemSubConfig()
    {
        $cls_data = new \OA\ClsData('oa_config_system_sub');
        global $adminId;
        $data = $this->data;
        foreach ($data['name'] as $system_key => $system_name) {
            if ($system_name && $data['url_inner'][$system_key] && $data['url_remote'][$system_key]) {
                $db_info = array();
                $db_info['css_add_time'] = time();
                $db_info['css_update_time'] = time();
                $db_info['css_approval_status'] = SH_YSH;
                $db_info['css_add_user_id'] = $adminId;
                $db_info['css_name'] = $system_name;
                $db_info['css_url_remote'] = $data['url_remote'][$system_key];
                $db_info['css_url_inner'] = $data['url_inner'][$system_key];

                //添加
                $has_info = $cls_data->selectOneEx(
                    array(
                        'col' => 'css_id',
                        'where' =>
                            " css_name='{$db_info['css_name']}' "
                    )
                );
                //p_r($has_info);
                if ($has_info['msg']) {
                    $cls_data->updateOne($db_info, "css_id={$has_info['msg'][0]['css_id']}");
                } else {
                    $cls_data->insertEx($db_info);
                }
                //p_r($cls_data);
            }
        }
        return array('ack' => 1);
    }
    
    /**
     * 修改审批配置是否有效
     */
    public function approvalIsValid()
    {
        $clsApprovalConfig = new \OA\ClsApprovalConfig();
        $returnMsg = $clsApprovalConfig->updateApprovalConfigIsValid($this->data['acId'], $this->data['value']);
        return $returnMsg;
    }
    
    /**
     * 添加审批配置
     */
    public function addApprovalConfig()
    {
        //p_r(getReqData());//exit;
        global $adminId;
        global $adminZtId;
        global $splMethodList;
        
        $clsApprovalConfig = new \OA\ClsApprovalConfig();
        $clsEs = new \OA\ClsEnterpriseStructure();
        $clsUser = new \OA\ClsUser();
        $returnMsg = array();
        
        $checkLevelArr = json_decode($this->data['checkLevelStr'], true);
        $methodList = array_flip($splMethodList);
        $supLevelArr = array(
            '直接上级' => 1,
            '第二上级' => 2,
            '第三上级' => 3,
            '第四上级' => 4,
            '第五上级' => 5,
            '第六上级' => 6,
            '第七上级' => 7,
            '第八上级' => 8,
            '第九上级' => 9,
            '第十上级' => 10,
        );
        $approvalLevelNum = count($checkLevelArr);
        //处理抄送人
        $copyToUserArr = explode(',', $this->data['ac_copy_to_user']);
        $copyToUserArrNew = array();
        foreach ($copyToUserArr as $copyInfo) {
            if (strstr($copyInfo, 'u_')) {
                array_push($copyToUserArrNew, str_replace('u_', '', $copyInfo));
            } else {
                $esStr = $copyInfo;
                $sonEsList = $clsEs->getSonEsIdArr($copyInfo);
                $sonEsList = $sonEsList['msg'];
                if ($sonEsList) {
                    $esStr = implode(',', array_remove_empty($sonEsList));
                }
                $userList = $clsUser->getUserIdByEsId($esStr);
                foreach ($userList as $userInfo) {
                    array_push($copyToUserArrNew, $userInfo);
                }
            }
        }
        $copyToUserStr = implode(',', array_unique(array_remove_empty($copyToUserArrNew)));
        //处理主表数组
        $approvalInfo = array(
            'ac_spl_name' => $this->data['approvalName'],
            'ac_level' => $approvalLevelNum,
            'ac_use_range' => $this->data['useRange'],
            //'ac_cope_to_user'=>$this->data['approvalName'],
            'ac_note' => $this->data['note'],
            'ac_update_time' => time(),
            'zt_id' => $adminZtId,
            'ac_copy_to_user' => $copyToUserStr
        );
        //开发信息
        if ($this->data['ac_path']) {
            $approvalInfo['ac_path'] = $this->data['ac_path'];
        }
        if ($this->data['ac_class']) {
            $approvalInfo['ac_class'] = $this->data['ac_class'];
        }
        //处理明细
        $count = 1;
        $approvalDetailArr = array();
        foreach ($checkLevelArr as $key => $checkLevelInfo) {
            $tmpArr = explode('-', $checkLevelInfo);
            $approvalDetailInfo['acd_check_level'] = $count;
            $approvalDetailInfo['acd_check_type'] = $tmpArr[0];
            $approvalDetailInfo['acd_check_method'] = $methodList[$tmpArr[2]] ? $methodList[$tmpArr[2]] : 0;
            $approvalDetailInfo['acd_add_time'] = time();
            $approvalDetailInfo['acd_update_time'] = time();
            $approvalDetailInfo['acd_approval_status'] = 1;
            $approvalDetailInfo['acd_add_user_id'] = $adminId;
            $approvalDetailInfo['zt_id'] = $adminZtId;
            switch ($tmpArr[0]) {
                case 1://上级审核
                    $approvalDetailInfo['acd_check_sup_level'] = $supLevelArr[$tmpArr[1]];
                    break;
                case 2://指的成员审核
                    $userIdArr = explode(',', $tmpArr[1]);
                    foreach ($userIdArr as $key => $userId) {
                        if (!strstr($userId, 'u_')) {
                            unset($userIdArr[$key]);
                        } else {
                            $userIdArr[$key] = str_replace('u_', '', $userId);
                        }
                    }
                    if (!$userIdArr) {
                        return json_encode(array('ack' => 0, 'msg' => '请选择审核人!'));
                    }
                    $approvalDetailInfo['acd_check_user_id'] = implode(',', array_remove_empty($userIdArr));
                    break;
                default:
                    break;
            }
            array_push($approvalDetailArr, $approvalDetailInfo);
            $count++;
        }
        if (!$approvalDetailArr) {
            return json_encode(array('ack' => 0, 'msg' => '保存失败，请选择审核人！'));
        }
        /*p_r($approvalInfo);
        p_r($approvalDetailArr);
        exit;*/
        if ('edit' == $this->data['action']) {
            //获取更新前信息
            $configOldInfo = $clsApprovalConfig->getApprovalConfigInfoById($this->data['acId']);
            $configDetailOldList = $clsApprovalConfig->getApprovalConfigDetailById($this->data['acId']);
            $approvalInfo['ac_id'] = $this->data['acId'];
            $returnMsg = $clsApprovalConfig->updateConfig($approvalInfo, $approvalDetailArr);
            if ($returnMsg['ack']) {
                //获取更新后信息
                $configNewInfo = $clsApprovalConfig->getApprovalConfigInfoById($this->data['acId']);
                $configDetailNewList = $clsApprovalConfig->getApprovalConfigDetailById($this->data['acId']);
                //日志
                $logFlag = $clsApprovalConfig->setConfigLog($configNewInfo['msg'][0], $configOldInfo['msg'][0], $configDetailNewList['msg'], $configDetailOldList['msg']);
            }
        } else {
            $approvalInfo['ac_add_user_id'] = $adminId;
            $approvalInfo['ac_add_time'] = time();
            $approvalInfo['ac_approval_status'] = 1;
            $returnMsg = $clsApprovalConfig->addConfig($approvalInfo, $approvalDetailArr);
        }
        return json_encode($returnMsg);
    }
}
