<?php

namespace Controller;

use OA\ClsApproval;
use OA\ClsApprovalConfig;

/**
 * 页面说明：
 * ===========================================================
 * 版权所有 (C) 2005-2006 我不是稻草人，并保留所有权利。
 * 网站地址: http://www.bullfrog.com
 * ----------------------------------------------------------
 * 这是免费开源的软件；您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * 不允许对程序修改后再进行发布。
 * ==========================================================
 * @author:     蒋尚君 <exeweb@163.com>
 * @version:    v2.0
 * @package class
 * @since 1.0.8
 */
class Staff
{
    private $data;

    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * 添加组织结构
     */
    public function addEnterpriseStructure()
    {
        //p_r(getReqData());//exit;
        global $adminId;
        global $adminZtId;
        $requestArr = getReqData();
        $clsEs = new \OA\ClsEnterpriseStructure();
        $returnMsg = array();
        if ('add' == $requestArr['type']) {
            $insert_info = array(
                'es_type' => $requestArr['es_type'],
                'es_name' => trim($requestArr['es_name']),
                'es_level' => $requestArr['es_level'],
                'es_sup_id' => $requestArr['es_sup_id'],
                'es_code' => $requestArr['es_code'],
                'es_leader_user_id' => $requestArr['es_leader_user_id'],
                'es_add_time' => time(),
                'es_add_user_id' => $adminId,
                'zt_id' => $adminZtId,
                'es_update_time' => time(),
                'es_approval_status' => 1,
            );
            $returnMsg = $clsEs->addEsInfo($insert_info);
        } else {
            $update_info = array(
                'es_type' => $requestArr['es_type'],
                'es_name' => trim($requestArr['es_name']),
                'es_code' => $requestArr['es_code'],
                'es_leader_user_id' => $requestArr['es_leader_user_id'],
                'es_update_time' => time(),
            );
            $returnMsg = $clsEs->updateEsInfo($update_info, $requestArr['es_id']);
        }
        return $returnMsg;
    }

    /**
     * 添加用户
     */
    public function addUser()
    {
        global $adminId;
        global $adminZtId;
        $clsUser = new \OA\ClsUser();
        $requestArr = getReqData();
        //插入oa_user表
        $userInsertInfo = array(
            'u_sup_id' => $requestArr['u_sup_id'],
            'u_es_id' => $requestArr['organizationEdit'],
            'zt_id' => $adminZtId,
            'u_status' => $requestArr['u_status'],
            'u_name' => trim($requestArr['u_name']),
            'u_password' => $requestArr['u_password'],
            'u_add_user_id' => $adminId,
            'u_approval_status' => 1,
            'u_update_time' => time(),
            'u_add_time' => time(),
            'u_position_id' => $requestArr['u_position_id']
        );
        $uaInsertInfo = array(
            'ua_native_place' => $requestArr['ua_native_place'],
            'ua_sex' => $requestArr['ua_sex'],
            'ua_belong_company' => $requestArr['ua_belong_company'],
            'ua_college' => $requestArr['ua_college'],
            'ua_politics_status' => $requestArr['ua_politics_status'],
            'ua_phone_num' => $requestArr['ua_phone_num'],
            'ua_entry_time' => strtotime($requestArr['ua_entry_time']),
            'ua_add_time' => time(),
            'ua_update_time' => time(),
            'ua_approval_status' => 1,
            'ua_add_user_id' => $adminId,
            'zt_id' => $adminZtId,
            'ua_user_character' => $requestArr['ua_user_character'],
        );
        $flag = $clsUser->addUser($userInsertInfo, $uaInsertInfo);
        return $flag;
    }

    /**
     * 编辑用户
     */
    public function editUser()
    {
        global $adminZtId;
        $clsUser = new \OA\ClsUser();
        $requestArr = getReqData();
        $returnMsg = array();
        //更新用户表
        $updateInfo = array(
            'u_sup_id' => $requestArr['u_sup_id'],
            'u_es_id' => $requestArr['organizationEdit'],
            'u_status' => $requestArr['u_status'],
            'zt_id' => $adminZtId,
            'u_position_id' => $requestArr['u_position_id'],
            'u_update_time' => time()
        );
        //更新附加表
        $updateUaInfo = array(
            'ua_native_place' => $requestArr['ua_native_place'],
            'ua_sex' => $requestArr['ua_sex'],
            'ua_belong_company' => $requestArr['ua_belong_company'],
            'ua_college' => $requestArr['ua_college'],
            'ua_politics_status' => $requestArr['ua_politics_status'],
            'ua_phone_num' => $requestArr['ua_phone_num'],
            'ua_entry_time' => strtotime($requestArr['ua_entry_time']),
            'zt_id' => $adminZtId,
            'ua_update_time' => time(),
            'ua_user_character' => $requestArr['ua_user_character'],
        );
        $clsUser->transactionBegin();
        $userFlag = $clsUser->updateUser($updateInfo, $requestArr['u_id']);
        $userAdditionFlag = $clsUser->updateUaUser($updateUaInfo, $requestArr['u_id']);
        if ($userAdditionFlag['ack'] && $userFlag['ack']) {
            $clsUser->transactionCommit();
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '成功';
        } else {
            $clsUser->transactionRollback();
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '更新失败，';
            if (!$userFlag['ack']) {
                $returnMsg['msg'] = '失败-' . $userFlag['msg'];
            }
            if (!$userAdditionFlag['ack']) {
                $returnMsg['msg'] = '失败-' . $userAdditionFlag['msg'];
            }
        }
        return $returnMsg;
    }

    /**
     * 添加角色
     */
    public function addRole()
    {
        global $adminId;
        global $adminZtId;
        $requestArr = $this->data;
        $clsRole = new \OA\ClsRole();
        $insert_role_info = array(
            'r_name' => trim($requestArr['role_name']),
            'r_belong_department' => $requestArr['role_belong_department'],
            'r_use_range' => $requestArr['role_use_range'],
            'r_add_time' => time(),
            'r_update_time' => time(),
            'r_approval_status' => 1,
            'r_add_user_id' => $adminId,
            'zt_id' => $adminZtId,
            'r_note' => $requestArr['role_note'],
        );
        return $clsRole->addRole($insert_role_info);
    }

    /**
     * 编辑角色
     */
    public function editRole()
    {
        global $adminZtId;
        $requestArr = $this->data;
        $clsRole = new \OA\ClsRole();
        $updateInfo = array(
            'r_name' => trim($requestArr['role_name']),
            'r_belong_department' => $requestArr['role_belong_department'],
            'r_use_range' => $requestArr['role_use_range'],
            'r_update_time' => time(),
            'zt_id' => $adminZtId,
            'r_note' => $requestArr['role_note'],
        );
        return $clsRole->editRole($updateInfo, $requestArr['role_id']);
    }

    /**
     * 返回用户信息
     */
    public function showUserList()
    {
        global $position_list;
        global $user_status_list;
        $returnMsg = array('code' => 'OK', 'ack' => 1, 'item' => array());
        $clsUser = new \OA\ClsUser();
        $clsEs = new \OA\ClsEnterpriseStructure();
        //where条件
        $param = array();
        #用户名搜索
        if ($this->data['u_username']) {
            $param['where'][] = "u_username like '{$this->data['u_username']}%'";
        }
        #搜索状态
        if ($this->data['u_status']) {
            $param['where'][] = "u_status = {$this->data['u_status']}";
        }
        #搜索入职时间
        if ($this->data['entry_time']) {
            $entryTimeStart = strtotime($this->data['entry_time'] . ' 00:00:00');
            $entryTimeEnd = strtotime($this->data['entry_time'] . ' 23:59:59');
            $param['where'][] = "ua_entry_time >= {$entryTimeStart}";
            $param['where'][] = "ua_entry_time <= {$entryTimeEnd}";
        }
        #读权限限制
        if ($this->data['read_per']) {
            $param['where'][] = "u_id in ({$this -> data['read_per']})";
        }
        #角色应用范围限制
        if ($this->data['r_use_range']) {
            //获取应用部门下所有组织ID
            $roleUseRange = str_replace("'", '', $this->data['r_use_range']);
            $useRangArr = array_remove_empty(explode(',', $roleUseRange));
            $clsEs->esList = array();
            foreach ($useRangArr as $useRangId) {
                $clsEs->getSonEsList($useRangId);
            }
            $sonEsList = array_merge($useRangArr, array_column($clsEs->esList, 'es_id'));
            $sonEsListIdStr = implode(',', array_remove_empty($sonEsList));
            $param['where'][] = "u_es_id in ({$sonEsListIdStr})";
        }
        #组织搜索
        if ($this->data['organization']) {
            //获取所选组织下所有组织的ID
            $sonEsIdList = $clsEs->getSonEsIdArr($this->data['organization'][0]);
            $sonEsIdStr = implode(',', array_remove_empty(array_merge($sonEsIdList['msg'], array($this->data['organization'][0]))));
            $param['where'][] = "u_es_id in ({$sonEsIdStr})";
        }
        //设置查询参数
        $limitStart = ($this->data['page'] - 1) * $this->data['limit'];
        $limit = $this->data['limit'] ? $this->data['limit'] : 5;
        $param['limit'] = "{$limitStart},{$limit}";
        $param['col'] = 'oa_user.*,ua_entry_time';
        $param['join'] .= ' left join oa_user_addition on ua_u_id = u_id';
        $userList = $clsUser->getUserInfo($param);
        #获取记录总条数
        $param['col'] = 'count(*) num';
        $param['limit'] = '';
        $countInfo = $clsUser->getUserInfo($param);
        $returnMsg['total'] = $countInfo['msg'][0]['num'];
        $userList = $userList['msg'];
        //处理搜索结果
        foreach ($userList as $key => $userInfo) {
            $userList[$key]['u_position'] = $position_list[$userInfo['u_position_id']];
            $userList[$key]['user_status'] = $user_status_list[$userInfo['u_status']];
            $userList[$key]['entry_time'] = $userInfo['ua_entry_time'] ? date('Y-m-d', $userInfo['ua_entry_time']) : '';
            //获取组织上级列表
            $allEsStr = '';
            $clsEs->esList = array();
            $supList = $clsEs->getSupEsList($userInfo['u_es_id']);
            foreach ($supList['msg'] as $supInfo) {
                if ($allEsStr) {
                    $allEsStr .= '->';
                }
                $allEsStr .= $supInfo['es_name'];
            }
            $userList[$key]['organization'] = $allEsStr;
        }
        $returnMsg['item'] = $userList;
        return $returnMsg;
    }

    /**
     * 分配角色
     */
    public function roleAllot()
    {
        $clsUserRole = new \OA\ClsUserRole();
        $clsUser = new \OA\ClsUser();
        $userStr = @implode("','", array_remove_empty(explode(',', $this->data['user_name_str'])));
        $userIdList = $clsUser->getUserIdList(array('where' => " u_username in ('{$userStr}') "));
        $flag = $clsUserRole->allotRole($userIdList, $this->data['allot_role_id']);
        return $flag;
    }

    /**
     * 删除组织
     */
    public function deleteEs()
    {
        $returnMsg = array('ack' => 0, 'msg' => '');
        $requestArr = $this->data;
        //检查这个组织是否有下属组织，如果有禁止删除.
        $clsEs = new \OA\ClsEnterpriseStructure();
        $clsEs->esList = array();
        $es_info = $clsEs->getSonEsList($requestArr['es_id']);
        if ($es_info) {
            $returnMsg['msg'] = '该组织有下属结构，禁止删除!';
            return $returnMsg;
        }
        //检查是否有用户属于这个组织
        $clsUser = new \OA\ClsUser();
        $seSonList = $clsEs->getSonEsList($requestArr['es_id']);
        $userInfo = $clsUser->getUserInfo(
            array(
                'where' =>
                    "u_es_id = {$requestArr['es_id']}"

            )
        );
        if ($userInfo['msg']) {
            $returnMsg['msg'] = '删除失败，该组织名下还有用户！';
            return $returnMsg;
        }
        //获取该组织信息
        $thisEsInfo = $clsEs->getEsInfoById($requestArr['es_id']);
        $flag = $clsEs->deleteEx("es_id = {$requestArr['es_id']}", 1);
        if ($flag['ack']) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '删除成功!';
            //日志
            $option = '删除了:';
            global $adminU;
            global $adminZtId;
            //获取上级列表
            $esSupList = $clsEs->getEsSupList($thisEsInfo['msg'][0]['es_sup_id']);
            foreach ($esSupList['msg'] as $esSupInfo) {
                $option .= $esSupInfo['es_name'] . '-';
            }
            $option .= $thisEsInfo['msg'][0]['es_name'];
            $clsEs->setLog(
                array(
                    'les_user' => $adminU,
                    'les_add_time' => time(),
                    'les_option' => $option,
                    'les_zt_id' => $adminZtId,
                    'les_es_id' => $requestArr['es_id'],
                )
            );
        }
        return $returnMsg;
    }

    /**
     * 获取用户组
     */
    public function showUserGroupInfo()
    {
        $returnMsg = array('ack' => 1);
        //p_r( getReqData() );
        $clsEs = new \OA\ClsEnterpriseStructure();
        $groupInfo = $clsEs->getEsInfo(
            array(
                "es_sup_id = {$this->data['department_id']}",
                "es_type = " . ES_TYPE_GROUP
            )
        );
        //p_r($groupInfo);
        foreach ($groupInfo['msg'] as $group) {
            $returnMsg['msg'] .= "<option value='{$group['es_id']}' class='group_option'>{$group['es_name']}</option>";
        }
        return $returnMsg;
    }

    /**
     * 获取角色列表
     */
    public function getRoleList()
    {
        $clsRole = new \OA\ClsRole();
        //返回数组
        $returnMsg = array('code' => 'OK', 'item' => array());
        //查询条件
        $data = $this->data;
        $whereOption = array();
        $param = array();
        if ($data['roleName']) {
            $whereOption['roleName'] = "r_name like '{$data['roleName']}%'";
        }
        if ($data['departmentId']) {
            $whereOption['departmentId'] = "r_belong_department={$data['departmentId']}";
        }
        if (('true' == $data['qxWfp'] && 'false' == $data['qxYfp']) || ('false' == $data['qxWfp'] && 'true' == $data['qxYfp'])) {
            //没有权限的用户
            //得出现有的已经配置的权限列表
            $clsRolePermission = new \OA\ClsRolePermission();
            $roleConfigList = $clsRolePermission->selectEx(array('col' => 'rp_role_id', 'group' => 'rp_role_id'));
            $roleConfigList = $roleConfigList['msg'];
            $roleConfigStr = implode(',', array_keys(change_main_key($roleConfigList, 'rp_role_id')));

            if ('true' == $data['qxWfp']) {
                $whereOption['qx'] = "r_id not in({$roleConfigStr})";
            }
            if ('true' == $data['qxYfp']) {
                $whereOption['qx'] = "r_id in({$roleConfigStr})";
            }
        }

        $limitStart = ($this->data['page'] - 1) * $this->data['limit'];
        $limit = $this->data['limit'] ? $this->data['limit'] : 5;

        $param['col'] = 'r_name,r_id,r_use_range,es_name,r_note';
        $param['limit'] = "{$limitStart},{$limit}";
        $param['where'] = $whereOption;
        $param['join'] = 'left join oa_enterprise_structure on es_id = r_belong_department';
        $param['order'] = 'r_id desc';
        $roleList = $clsRole->getRoleInfo($param);
        $returnMsg['item'] = $roleList['msg'];

        $param['col'] = 'count(*) num';
        $param['limit'] = '';
        $param['order'] = '';
        $numInfo = $clsRole->getRoleInfo($param);
        $returnMsg['total'] = $numInfo['msg'][0]['num'];
        return $returnMsg;
    }

    /**
     * 删除角色
     */
    public function deleteRole()
    {
        $clsRole = new \OA\ClsRole();
        return $clsRole->deleteRole($this->data['role_id']);
    }

    /**
     * 返回日志
     */
    public function getLog()
    {
        $returnMsg = array('code' => 'OK', 'item' => array());
        switch ($this->data['table']) {
            case 'log_enterprise_structure'://组织结构
                $clsEs = new \OA\ClsEnterpriseStructure();
                $logList = $clsEs->getEsLog($this->data['id'], $this->data['page'], $this->data['limit']);
                break;
            case 'log_user'://用户信息
                $clsUser = new \OA\ClsUser();
                $logList = $clsUser->getUserLog($this->data['id'], $this->data['page'], $this->data['limit']);
                break;
            case 'log_role'://角色日志
                $clsRole = new \OA\ClsRole();
                $logList = $clsRole->getRoleLog($this->data['id'], $this->data['page'], $this->data['limit']);
                break;
            case 'delete_role_log'://角色删除日志
                $clsRole = new \OA\ClsRole();
                $logList = $clsRole->getRoleLog($this->data['id'], $this->data['page'], $this->data['limit'], 2);
                break;
            case 'log_approval':
                $clsApproval = new ClsApproval();
                $logList = $clsApproval->getLog($this->data['id'], $this->data['page'], $this->data['limit']);
                break;
            case 'log_approval_config':
                $clsApprovalConfig = new ClsApprovalConfig();
                $logList = $clsApprovalConfig->getLog($this->data['id'], $this->data['page'], $this->data['limit']);
                break;
            default:
                break;
        }
        $returnMsg['total'] = $logList['count']['msg'];
        $logList = $logList['msg'];
        foreach ($logList as $log_key => $log_info) {
            $logList[$log_key]['add_time'] = date('Y-m-d H:i:s', $log_info['add_time']);
        }
        $returnMsg['item'] = $logList;
        //p_r($returnMsg);
        return $returnMsg;
    }

    /**
     * 用户分配角色
     */
    public function userAllotRole()
    {
        $returnMsg = array('ack' => 1);
        //获取该用户已有角色ID列表
        $role_id_arr = array_remove_empty(explode(',', $this->data['id_str']));
        $id_str = implode(',', $role_id_arr);
        $clsUserRole = new \OA\ClsUserRole();
        $user_role_list = $clsUserRole->getUserRoleIdList($this->data['u_id']);
        $user_role_id_arr = $user_role_list['msg'] ? $user_role_list['msg'] : array();
        //比较差异
        $add_role_id_arr = array_diff($role_id_arr, $user_role_id_arr);
        $del_role_id_arr = array_diff($user_role_id_arr, $role_id_arr);

        $clsUserRole->transactionBegin();
        $errorNum = 0;
        if ($add_role_id_arr) {
            //添加角色
            foreach ($add_role_id_arr as $add_role_id) {
                $flag = $clsUserRole->addUserRole($this->data['u_id'], $add_role_id);
                $errorNum += $flag['ack'] ? 0 : 1;
            }
        }
        if ($del_role_id_arr) {
            //删除角色
            foreach ($del_role_id_arr as $del_role_id) {
                $flag = $clsUserRole->deleteUserRole($this->data['u_id'], $del_role_id);
                $errorNum += $flag['ack'] ? 0 : 1;
            }
        }
        if (0 == $errorNum) {
            $returnMsg['msg'] = '分配成功!';
            $clsUserRole->transactionCommit();
            //日志
            $clsUserRole->userAllotRoleLog($add_role_id_arr, $del_role_id_arr, $this->data['u_id']);
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '分配失败,请重新尝试!';
            $clsUserRole->transactionRollback();
        }
        return $returnMsg;
    }

    /**
     * 修改用户密码
     */
    public function changeUserPassword()
    {
        $clsUser = new \OA\ClsUser();
        $password = $this->data['userPassword'];
        $isError = false;
        $returnMsg = array();
        if (strlen($password) < 8 || strlen($password) > 20) {
            $returnMsg['msg'] .= '新密码长度应该在8-20之间';
            $isError = true;
        }
        if (!passwordCheck($password)) {
            $returnMsg['msg'] .= '密码强度太弱!必须包含字母数字或特殊字符';
            $isError = true;
        }
        if (!$isError) {
            $updateInfo = array(
                'u_password' => encrypt($this->data['userPassword']),
                'u_update_time' => time(),
            );
            $flag = $clsUser->updateUser($updateInfo, $this->data['userId']);
            $returnMsg = $flag;
        } else {
            $returnMsg['ack'] = 0;
        }
        return $returnMsg;
    }

    /**
     * 导入用户信息
     */
    public function importUserInfo()
    {
        global $adminId;
        global $adminZtId;
        global $user_status_list;
        global $position_list;

        $clsUser = new \OA\ClsUser();
        $clsUserAddition = new \OA\ClsData('oa_user_addition');
        $clsEs = new \OA\ClsEnterpriseStructure();
        #返回结果数据
        $returnMsg = array('ack' => 0);
        #获取导入数据
        $importData = getUploadExcelContent('userFile');
        $importData = $importData['data'][0]['data'];
        //p_r($importData);//exit;
        #获取表头
        $tableTitle = array_shift($importData);
        #检查标题
        if (15 != count($tableTitle)) {
            $returnMsg['msg'] = '导入失败，模板列数错误。';
            return $returnMsg;
        }
        #导入模板所有表头
        $allTitleArr = array(
            '姓名*',
            '工号*',
            '用户名*',
            '性别*',
            '状态*',
            '组织*',
            '员工性质',
            '职位*',
            '直属领导*',
            '所属公司*',
            '入职时间*',
            '籍贯',
            '毕业学校',
            '政治面貌',
            '电话号码*'
        );
        foreach ($tableTitle as $title) {
            if (!in_array($title, $allTitleArr)) {
                $returnMsg['msg'] = '导入失败，模板表头名字错误!';
                return $returnMsg;
            }
        }
        //处理导入数据到新数组
        $newData = array();
        foreach ($importData as $tmpImportInfo) {
            $tmpArr = array_combine(array_values($tableTitle), $tmpImportInfo);
            array_push($newData, $tmpArr);
        }

        #失败条数
        $errorNum = 0;
        $clsUser->transactionBegin();
        foreach ($newData as $importInfo) {
            $name = trim($importInfo['姓名*']);
            if (!$name) {
                continue;
            }
            //检查组织（必填）
            $workPlace = trim($importInfo['组织*']);
            $esInfo = $clsEs->getEsIdByEsStr($workPlace);
            if (!$esInfo['msg'] || $esInfo['esLevel'] < 2) {
                $returnMsg['msg'] = "导入失败，{$name}的组织信息错误（所填组织信息必须是公司或者以下组织）";
                $errorNum++;
                break;
            }
            $userEsId = $esInfo['msg'];
            $esCodeInfo = $clsEs->getEsCodeById($userEsId);
            if ($esCodeInfo['ack']) {
                $esCode = $esCodeInfo['msg'];
            } else {
                $returnMsg['msg'] = "导入失败，{$name}的组织信息错误（所填组织信息必须是公司或者以下组织）";
                $errorNum++;
                break;
            }
            //检查工号（必填）
            $userAdditionNumb = trim($importInfo['工号*']);
            for ($i = 0; $i < 5; $i++) {
                if (strlen($userAdditionNumb) < 5) {
                    $userAdditionNumb = '0' . $userAdditionNumb;
                }
            }
            $workNumb = $esCode . $userAdditionNumb;
            if (!$userAdditionNumb) {
                $returnMsg['msg'] = "导入失败,{$name}没有填写工号!";
                $errorNum++;
                break;
            }
            $userInfo = $clsUser->getUserInfo(
                array(
                    'where' => "ua_numb = {$userAdditionNumb}",
                    'join' => 'left join oa_user_addition on ua_u_id = u_id'
                )
            );
            if ($userInfo['msg']) {
                $returnMsg['msg'] = "导入失败,工号：{$userAdditionNumb}已经被用户:{$userInfo['msg'][0]['u_username']}使用";
                $errorNum++;
                break;
            }
            //检查状态（必填）
            $userStatusName = trim($importInfo['状态*']);
            $userStatusArr = array_flip($user_status_list);
            $userStatusId = $userStatusArr[$userStatusName];
            if (!$userStatusId) {
                $returnMsg['msg'] = "导入失败,用户：{$name}的状态错误。";
                $errorNum++;
                break;
            }
            //获取上级（必填）
            $supName = trim($importInfo['直属领导*']);
            $supInfo = $clsUser->getUserInfoByName($supName);
            if (!$supInfo['msg']) {
                $returnMsg['msg'] = "导入失败,用户：{$name}的上级信息错误";
                $errorNum++;
                break;
            }
            $supId = $supInfo['msg'][0]['u_id'];
            //检查职位（必填）
            $positionName = trim($importInfo['职位*']);
            $positionArr = array_flip($position_list);
            if (!$positionArr[$positionName]) {
                $returnMsg['msg'] = "导入失败,用户：{$name}的职位信息错误";
                $errorNum++;
                break;
            }
            $positionId = $positionArr[$positionName];
            //检查用户名
            $userName = trim($importInfo['用户名*']);
            if (!$userName) {
                $returnMsg['msg'] = "导入失败,用户：{$name}的用户名信息错误";
                $errorNum++;
                break;
            } elseif (!strstr($userName, $userAdditionNumb)) {
                $returnMsg['msg'] = "导入失败,用户：{$name}的用户名格式错误,用户名必须包含工号（数字）";
                $errorNum++;
                break;
            } elseif (!strstr($userName, $name)) {
                $returnMsg['msg'] = "导入失败,用户：{$name}的用户名格式错误，用户名必须包含姓名";
                $errorNum++;
                break;
            }
            $userInsertInfo = array(
                'u_username' => $userName,
                'u_sup_id' => $supId,
                'u_es_id' => $userEsId,
                'zt_id' => $adminZtId,
                'u_status' => $userStatusId,
                'u_name' => $name,
                'u_password' => encrypt('a123456'),
                'u_add_user_id' => $adminId,
                'u_approval_status' => 1,
                'u_update_time' => time(),
                'u_add_time' => time(),
                'u_position_id' => $positionId,
                'u_work_numb' => $workNumb,
            );
            $userFlag = $clsUser->insertEx($userInsertInfo, true);
            $errorNum += $userFlag['ack'] ? 0 : 1;

            //检查性别（必填）
            $userSex = trim($importInfo['性别*']);
            if (!in_array($userSex, array('男', '女'))) {
                $returnMsg['msg'] = "导入失败,用户：{$name}的性别信息错误";
                $errorNum++;
                break;
            }
            //检查所属公司（必填）
            $clsConfig = new \OA\ClsConfig();
            $companyNameInfo = $clsConfig->getCompanyIdByName($importInfo['所属公司*']);
            if (!$companyNameInfo['msg']) {
                $returnMsg['msg'] = "导入失败,用户：{$name}的所属公司信息错误";
                $errorNum++;
                break;
            }
            $companyId = $companyNameInfo['msg']['cc_id'];
            //检查政治面貌（选填）
            $politicsStatusName = $importInfo['政治面貌'];
            $politicsStatusId = 0;
            if ($politicsStatusName) {
                global $politics_status_list;
                $politicsStatusArr = array_flip($politics_status_list);
                if (!$politicsStatusArr[$politicsStatusName]) {
                    $returnMsg['msg'] = "导入失败,用户：{$name}的政治面貌信息错误";
                    $errorNum++;
                    break;
                }
                $politicsStatusId = $politicsStatusArr[$politicsStatusName];
            }
            //检查入职时间（必填）
            $entryTime = trim($importInfo['入职时间*']);
            $entryTime = strtotime($entryTime);
            if (!$entryTime) {
                $returnMsg['msg'] = "导入失败,用户：{$name}的入职时间信息错误";
                $errorNum++;
                break;
            }
            //检查电话号码（必填）
            $userPhoneNumber = trim($importInfo['电话号码*']);
            if (!$userPhoneNumber) {
                $returnMsg['msg'] = "导入失败,用户：{$name}的电话号码信息错误";
                $errorNum++;
                break;
            }
            //员工性质（选填）
            $userCharacterName = trim($importInfo['员工性质']);
            $userCharacterId = 0;
            if ($userCharacterName) {
                global $userCharacterList;
                $userCharacterArr = array_flip($userCharacterList);
                if (!$userCharacterArr[$userCharacterName]) {
                    $returnMsg['msg'] = "导入失败,用户：{$name}的员工性质信息错误";
                    $errorNum++;
                    break;
                }
                $userCharacterId = $userCharacterArr[$userCharacterName];
            }
            $uaInsertInfo = array(
                'ua_native_place' => trim($importInfo['籍贯']),
                'ua_sex' => $userSex,
                'ua_belong_company' => $companyId,
                'ua_college' => trim($importInfo['毕业学校']),
                'ua_politics_status' => $politicsStatusId,
                'ua_phone_num' => $userPhoneNumber,
                'ua_entry_time' => $entryTime,
                'ua_add_time' => time(),
                'ua_update_time' => time(),
                'ua_approval_status' => 1,
                'ua_add_user_id' => $adminId,
                'zt_id' => $adminZtId,
                'ua_user_character' => $userCharacterId,
                'ua_numb' => $userAdditionNumb,
                'ua_u_id' => $userFlag['insert_id']
            );
            $userAdditionFlag = $clsUserAddition->insertEx($uaInsertInfo);
            $errorNum += $userAdditionFlag['ack'] ? 0 : 1;
        }
        if (0 == $errorNum) {
            $clsUser->transactionCommit();
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '导入成功!';
        } else {
            $clsUser->transactionRollback();
            $returnMsg['ack'] = 0;
            if (!$returnMsg['msg']) {
                $returnMsg['msg'] = '导入失败,数据库操作失败，请重新尝试!';
            }
        }
        return json_encode($returnMsg);
    }
    
    /**
     * 添加审批
     */
    public function addApproval()
    {
        $returnMsg = array();
        //获取配置信息
        $clsApprovalConfig = new ClsApprovalConfig();
        $acId = $this->data['ac_id'];
        if ($this->data['approvalId']) {
            $clsApproval = new ClsApproval();
            $approvalInfo = $clsApproval->getApprovalInfoById($this->data['approvalId']);
            $acId = $approvalInfo['msg'][0]['approval_type'];
        }
        $approvalConfigInfo = $clsApprovalConfig->getApprovalConfigInfoById($acId);
        //引入具体审批类
        require_once(WEB_CLASS . "/approval/class.{$approvalConfigInfo['msg'][0]['ac_class']}.php");
        $className = '\OA\Cls' . str_replace(' ', '', ucwords(str_replace('_', ' ', $approvalConfigInfo['msg'][0]['ac_class'])));
        $clsApprovalSpecific = new $className();
        if (2 == $this->data['approvalType']) {
            //添加具体审批
            $returnMsg = $clsApprovalSpecific->addApproval($this->data);
        } else {
            $returnMsg = $clsApprovalSpecific->editApproval($this->data);
        }
        return $returnMsg;
    }
    
    /**
     * 显示审批列表
     */
    public function showApprovalList()
    {
        global $approvalStatusList;
        global $adminId;
        $returnMsg = array('ack' => 1);//返回数组
        $clsApproval = new ClsApproval();
        $param = array();//查询参数数组
        //col
        $param['col'] = "
                        SQL_CALC_FOUND_ROWS
                        approval_id,
                        add_user.u_username add_username,
                        ac_spl_name,
                        approval_add_user_id,
                        FROM_UNIXTIME(approval_add_time,'%Y-%m-%d') add_time,
                        approval_status,
                        GROUP_CONCAT(if(approval_check_level = ad_check_level and ad_check_time = 0,check_user.u_username,'')) check_username,
                        GROUP_CONCAT(if(approval_check_level = ad_check_level and ad_check_time = 0,check_user.u_id,'')) check_user_id,
                        GROUP_CONCAT(if(ad_check_time>0,ad_check_user_id,0)) has_check,
                        approval_copy_to_user";
        //join
        $param['join'] = ' inner join oa_approval_config on ac_id = approval_type';
        $param['join'] .= ' left join oa_user add_user on add_user.u_id = approval_add_user_id';
        $param['join'] .= ' left join oa_approval_detail on ad_approval_id = approval_id ';
        $param['join'] .= ' left join oa_user check_user on check_user.u_id = ad_check_user_id';
        //group
        $param['group'] = 'approval_id';
        //where
        $param['where'][] = 'approval_approval_status = 1';
        //having
        if ($this->data['having']) {
            switch ($this->data['having']) {
                case 1://待我审批
                    $param['where'][] = 'ad_check_time = 0';
                    $param['where'][] = "approval_status in ( 1,2 )";
                    $param['having'] = "FIND_IN_SET( {$adminId}, check_user_id ) ";
                    break;
                case 2://我已审批
                    $param['having'] = "FIND_IN_SET( {$adminId}, has_check ) ";
                    break;
                case 3://我申请的
                    $param['where'][] = "approval_add_user_id = {$adminId}";
                    break;
                case 4://抄送我的
                    $param['where'][] = 'approval_status = 4';
                    $param['having'] = "FIND_IN_SET( {$adminId}, approval_copy_to_user ) ";
                    break;
                default:
                    break;
            }
        }
        //搜索
        if ($this->data['acId']) {
            $param['where'][] = "approval_type = {$this->data['acId']}";
        }
        if ($this->data['searchKey'] && $this->data['searchValue']) {
            switch ($this->data['searchKey']) {
                case 1://申请人
                    $param['where'][] = "add_user.u_username like '{$this->data['searchValue']}%'";
                    break;
                case 2://审批编号
                    $param['where'][] = "approval_id = {$this->data['searchValue']}";
                    break;
                default:
                    break;
            }
        }
        //order
        if (!$this->data['order']) {
            $param['order'] = 'approval_id desc';
        } else {
            $param['order'] = $this->data['order'];
        }
        //limit
        $limitStart = ($this->data['page'] - 1) * $this->data['limit'];
        $limit = $this->data['limit'] ? $this->data['limit'] : 5;
        $param['limit'] = "{$limitStart},{$limit}";
        //查询明细
        $approvalList = $clsApproval->selectEx($param);
        //p_r($clsApproval->getLastSql());exit;
        $approvalList = $approvalList['msg'];
        foreach ($approvalList as $key => &$approvalInfo) {
            $option = ''; //操作按钮
            //权限设置
            $userArr = array_remove_empty(explode(',', $approvalInfo['check_user_id']));
            //未审核、审核中的审批，审核人可以审核、打回
            if (in_array($adminId, $userArr)) {
                if (!in_array($approvalInfo['approval_status'], array(APPROVAL_STATUS_END, APPROVAL_STATUS_BACK, APPROVAL_STATUS_CANCEL))) {
                    $option .= '<input type="button" class="layui-btn layui-btn-sm layui-btn-warm" value="审核" onclick="checkApproval(' . $approvalInfo['approval_id'] . ')">';
                    $option .= '<input type="button" class="layui-btn layui-btn-sm layui-btn-normal" value="打回" onclick="backApproval(' . $approvalInfo['approval_id'] . ')">';
                }
            }
            //未完结的审批，申请人可以撤销
            if ($adminId == $approvalInfo['approval_add_user_id'] && !in_array($approvalInfo['approval_status'], array(APPROVAL_STATUS_CANCEL, APPROVAL_STATUS_END))) {
                $option .= '<input type="button" class="layui-btn layui-btn-sm layui-btn-danger" value="撤销" onclick="cancelApproval(' . $approvalInfo['approval_id'] . ')">';
            }
            //打回状态的审批单，申请人能够编辑和重新提交。
            if ($adminId == $approvalInfo['approval_add_user_id'] && $approvalInfo['approval_status'] == APPROVAL_STATUS_BACK) {
                //打回状态可以修改
                $option .= '<input type="button" class="layui-btn layui-btn-sm" value="编辑" onclick="editApprovalDetail(' . $approvalInfo['approval_id'] . ',3)">';
                $option .= '<input type="button" class="layui-btn layui-btn-sm" value="重新提交" onclick="resubmitApproval(' . $approvalInfo['approval_id'] . ')">';
            }
            $option .= '<input type="button" class="layui-btn layui-btn-sm" value="查看详情" onclick="showApprovalDetail(' . $approvalInfo['approval_id'] . ',1)">';
            $option .= '<input type="button" class="layui-btn layui-btn-sm" value="日志" onclick="show_log(' . $approvalInfo['approval_id'] . ',' . "'log_approval'" . ')">';
            $approvalInfo['options'] = $option;
            //待审核人
            if (in_array($approvalInfo['approval_status'], array(APPROVAL_STATUS_CANCEL, APPROVAL_STATUS_END, APPROVAL_STATUS_BACK))) {
                $approvalInfo['check_username'] = '';
            } else {
                $approvalInfo['check_username'] = implode(',', array_remove_empty(explode(',', $approvalInfo['check_username'])));
            }
            //审批状态
            $approvalInfo['approval_status'] = $approvalStatusList[$approvalInfo['approval_status']];
        }
        //查询总记录数
        /*$param['limit'] = '';
        $param['col'] = 'approval_id';
        $countInfo = $clsApproval->selectEx($param);*/
        /*p_r($clsApproval->getLastSql());
        p_r($countInfo);*/
        //处理输出
        $countInfo = $clsApproval->execute('SELECT FOUND_ROWS() num /*zt_id*/');
        $returnMsg['total'] = $countInfo['msg'][0]['num'];
        $returnMsg['item'] = $approvalList;
        
        return $returnMsg;
    }
    
    /**
     * 撤销审批
     */
    public function cancelApproval()
    {
        $clsApproval = new ClsApproval();
        return $clsApproval->cancelApproval($this->data['approvalId']);
    }
    
    /**
     * 打回审批
     * @return array
     */
    public function backApproval()
    {
        $clsApproval = new ClsApproval();
        return $clsApproval->backApproval($this->data['approvalId']);
    }
    
    /**
     * 重新提交
     */
    public function resubmitApproval()
    {
        $clsApproval = new ClsApproval();
        return $clsApproval->resubmitApproval($this->data['approvalId']);
    }
    
    /**
     * 审核审批
     */
    public function checkApproval()
    {
        global $adminId;
        $returnMsg = array();
        $clsApproval = new ClsApproval();
        $clsApprovalConfig = new ClsApprovalConfig();
        
        //获取审批信息
        $approvalInfo = $clsApproval->getApprovalInfo(
            array(
                'where' => "approval_id = {$this->data['approvalId']}",
                'join' => "left join oa_approval_detail on approval_id = ad_approval_id and approval_check_level = ad_check_level"
            )
        );
        $approvalInfo = $approvalInfo['msg'][0];
        //获取配置信息
        $configInfo = $clsApprovalConfig->getApprovalConfigDetailInfo(
            array(
                'where' =>
                    " ac_id = {$approvalInfo['approval_type']} " .
                    " and acd_check_level = {$approvalInfo['ad_config_level']}"
            )
        );
        $configInfo = $configInfo['msg'][0];
        if ($configInfo) {
            //实例化具体类
            require_once(WEB_CLASS . "/approval/class.{$configInfo['ac_class']}.php");
            $className = '\OA\Cls' . str_replace(' ', '', ucwords(str_replace('_', ' ', $configInfo['ac_class'])));
            $clsApprovalSpecific = new $className();
            $returnMsg = $clsApprovalSpecific->checkApproval($approvalInfo, $configInfo);
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '审核失败，审批信息错误，请刷新页面之后重新尝试!';
        }
        return $returnMsg;
    }
}
