<?php

namespace OA;

/**
 * abstract:角色用户关系类
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2018年12月21日
 * Time:11:14:53
 */
class ClsUserRole extends ClsData
{
    /**
     * cls_user_role constructor.
     */
    public function __construct()
    {
        parent::__construct('oa_user_role');
    }

    /**
     * 获取角色关系信息
     * @author 王银龙
     * @param array $param 查询参数
     * @return 查询结果数组
     */
    public function getUrInfo($param)
    {
        $urInfo = $this->selectEx($param);
        return $urInfo;
    }

    /**
     * 获取该角色用户列表
     * @author 王银龙
     * @param int $roleId 角色ID
     * @return array 查询结果数组
     */
    public function getRoleUserList($roleId)
    {
        return $this->getUrInfo(array('where' => " ur_role_id = {$roleId}"));
    }

    /**
     * 获取该角色用户ID列表
     * @author 王银龙
     * @param int $roleId 角色ID
     * @return array 查询结果数组
     */
    public function getRoleUserIdList($roleId)
    {
        $userList = $this->getRoleUserList($roleId);
        $userIdList = array_column($userList['msg'], 'ur_u_id');
        return $userIdList;
    }

    /**
     * 角色分配
     * @author 王银龙
     * @param array $userIdArr 用户ID数组
     * @param int $roleId 角色ID
     * @return array 执行结果数组
     */
    public function allotRole(array $userIdArr, $roleId)
    {
        $addFlag['ack'] = 1;          //添加执行标记
        $deleteFlag['ack'] = 1;       //删除执行标记
        $returnMsg = array(); //返回结果
        //获取该角色已分配的用户ID数组
        $roleUserIdList = $this->getRoleUserIdList($roleId);
        $this->transactionBegin();
        //添加用户角色
        $addRoleUserIdArr = array_diff($userIdArr, $roleUserIdList);
        if ($addRoleUserIdArr) {
            $addFlag = $this->addUserRole($addRoleUserIdArr, $roleId);
        }
        //删除用户角色
        $deleteRoleUserIdArr = array_diff($roleUserIdList, $userIdArr);
        if ($deleteRoleUserIdArr) {
            $deleteFlag = $this->deleteUserRole($deleteRoleUserIdArr, $roleId);
        }
        if ($addFlag['ack'] && $deleteFlag['ack']) {
            $this->transactionCommit();
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '分配成功!';
            //日志
            $this->userRoleLog($addRoleUserIdArr, $deleteRoleUserIdArr, $roleId);
        } else {
            $this->transactionRollback();
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '分配失败，请重新尝试!';
        }
        return $returnMsg;
    }

    /**
     * 删除用户角色
     * @author 王银龙
     * @param array $userIdArr 用户ID数组
     * @param int $roleId 角色ID
     * @return array 执行结果数组
     */
    public function deleteUserRole($userIdArr, $roleId)
    {
        global $adminZtId;
        if (!is_array($userIdArr)) {
            $userIdArr = array($userIdArr);
        }
        $count = count($userIdArr);
        $returnMsg = array();
        $userIdStr = implode(',', array_remove_empty($userIdArr));
        $where = "ur_u_id in( {$userIdStr}) and ur_role_id = {$roleId}";
        $flag = $this->deleteEx($where, $count);
        if ($flag['ack']) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '删除成功!';
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '删除失败!';
        }
        return $returnMsg;
    }

    /**
     * 添加用户角色
     * @author 王银龙
     * @param array $userIdArr 用户ID数组
     * @param int $roleId 角色ID
     * @return array 执行结果数组
     */
    public function addUserRole($userIdArr, $roleId)
    {
        global $adminId;
        global $adminZtId;
        $errorNum = 0;         //执行错误数
        $returnMsg = array(); //返回结果
        if (!is_array($userIdArr)) {
            $userIdArr = array($userIdArr);
        }
        foreach ($userIdArr as $userId) {
            $insertInfo = array(
                'ur_u_id' => $userId,
                'ur_role_id' => $roleId,
                'ur_add_time' => time(),
                'ur_update_time' => time(),
                'ur_approval_status' => 1,
                'ur_add_user_id' => $adminId,
                'zt_id' => $adminZtId,
            );
            $flag = $this->insertEx($insertInfo);
            $errorNum += $flag['ack'] ? 0 : 1;
        }
        if ($errorNum > 0) {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '添加失败';
        } else {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '添加成功';
        }
        return $returnMsg;
    }

    /**
     * 获取用户角色列表
     * @author 王银龙
     * @param int $userId 用户ID
     * @return array 角色数组
     */
    public function getUserRoleList($userId)
    {
        $info = $this->getUrInfo(
            array(
                'where' => "ur_u_id = {$userId}",
                'join' => 'left join oa_role on r_id = ur_role_id'
            )
        );
        return $info;
    }

    /**
     * 获取用户角色ID列表
     * @author 王银龙
     * @param $userId 用户ID
     * @return array 结果集合
     */
    public function getUserRoleIdList($userId)
    {
        $userRoleList = $this->getUserRoleList($userId);
        $roleIdArr = array_column($userRoleList['msg'], 'ur_role_id');
        return array('ack' => 1, 'msg' => $roleIdArr);
    }

    /**
     * 获取部门可用角色列表
     * @author 王银龙
     * @param int/array $department_id 部门ID
     * @return array 角色数组
     */
    public function getDepartmentRoleList($department_id)
    {
        if (is_array($department_id)) {
            $tmpArr = array();
            foreach ($department_id as $d_id) {
                array_push($tmpArr, "FIND_IN_SET({$d_id},r_use_range)");
            }
            $whereStr = implode(' or ', array_remove_empty($tmpArr));
            $where = $whereStr;
        } else {
            $where = "FIND_IN_SET({$department_id},r_use_range)";
        }
        $clsRole = new \OA\ClsRole();
        $roleList = $clsRole->getRoleInfo(
            array(
                'where' => $where,
                'order' => 'r_belong_department asc'
            )
        );
        return $roleList;
    }

    /**
     * 获取用户可用角色列表
     * @author 王银龙
     * @param int $userId 用户ID
     * @return array 角色数组
     */
    public function getUserDepartmentRoleList($userId)
    {
        $clsUser = new \OA\ClsUser();
        $userInfo = $clsUser->getUserInfoById($userId);
        //获取用户部门
        $clsEs = new \OA\ClsEnterpriseStructure();
        $supEsList = $clsEs->getSupEsList($userInfo['msg'][0]['u_es_id']);
        $departmentIdArr = array();
        foreach ($supEsList['msg'] as $supEsInfo) {
            if (ES_TYPE_DEPARTMENT == $supEsInfo['es_type']) {
                $departmentIdStr = $supEsInfo['es_id'];
                array_push($departmentIdArr, $supEsInfo['es_id']);
            }
        }
        //如果上级中没有部门，则查找下级。
        if (!$departmentIdArr) {
            $sonEsList = $clsEs->getSonEsList($userInfo['msg'][0]['u_es_id']);
            foreach ($sonEsList as $sonEsInfo) {
                if (ES_TYPE_DEPARTMENT == $sonEsInfo['es_type']) {
                    array_push($departmentIdArr, $sonEsInfo['es_id']);
                }
            }
        }
        //p_r($departmentIdArr);//exit;
        $roleList = $this->getDepartmentRoleList($departmentIdArr);
        return $roleList;
    }

    /**
     * 角色分配用户日志
     * @author 王银龙
     * @param array $addArr 添加用户ID数组
     * @param array $delArr 删除用户ID数组
     * @param int $roleId 角色ID
     * @return array 执行结果
     */
    public function userRoleLog($addArr, $delArr, $roleId)
    {
        global $adminU;
        global $adminZtId;
        $clsUser = new \OA\ClsUser();
        $clsRole = new \OA\ClsRole();
        //获取角色信息
        $roleInfo = $clsRole->getRoleInfoById($roleId);
        if ($addArr) {
            $addUserIdStr = implode(',', array_remove_empty($addArr));
            $add_user_info = $clsUser->getUserInfoById($addUserIdStr);
            //用户列表日志
            foreach ($add_user_info['msg'] as $addInfo) {
                $option = "添加:“{$roleInfo['msg'][0]['r_name']}”角色";
                $flag = $clsUser->setLog(
                    array(
                        'lu_user' => $adminU,
                        'lu_add_time' => time(),
                        'lu_option' => $option,
                        'lu_zt_id' => $adminZtId,
                        'lu_uid' => $addInfo['u_id'],
                    )
                );
            }
            //角色列表日志
            $addNameStr = $clsUser->getUserNameById($addUserIdStr);
            $roleOption = "添加用户:{$addNameStr[ 'msg' ][ 0 ][ 'name' ]}";
            $flag = $clsRole->setLog(array(
                'lr_user' => $adminU,
                'lr_add_time' => time(),
                'lr_option' => $roleOption,
                'lr_zt_id' => $adminZtId,
                'lr_role_id' => $roleId,
            ));
        }
        if ($delArr) {
            $delUserIdStr = implode(',', array_remove_empty($delArr));
            $delUserInfo = $clsUser->getUserInfoById($delUserIdStr);
            //用户列表日志
            foreach ($delUserInfo['msg'] as $delInfo) {
                $option = "移除:“{$roleInfo['msg'][0]['r_name']}”角色";
                $clsUser->setLog(array(
                    'lu_user' => $adminU,
                    'lu_add_time' => time(),
                    'lu_option' => $option,
                    'lu_zt_id' => $adminZtId,
                    'lu_uid' => $delInfo['u_id'],
                ));
            }
            //角色列表日志
            $delNameStr = $clsUser->getUserNameById($delUserIdStr);
            $roleOption = "移除用户:{$delNameStr[ 'msg' ][ 0 ][ 'name' ]}";
            $flag = $clsRole->setLog(array(
                'lr_user' => $adminU,
                'lr_add_time' => time(),
                'lr_option' => $roleOption,
                'lr_zt_id' => $adminZtId,
                'lr_role_id' => $roleId,
            ));
        }
    }

    /**
     * 用户分配角色日志
     * @author 王银龙
     * @param array $addArr 添加角色ID数组
     * @param array $delArr 删除角色ID数组
     * @param int $userId 用户ID
     * @return array 执行结果
     */
    public function userAllotRoleLog($addArr, $delArr, $userId)
    {
        global $adminU;
        global $adminZtId;
        $clsUser = new \OA\ClsUser();
        $clsRole = new \OA\ClsRole();
        //获取用户信息
        $userInfo = $clsUser->getUserInfoById($userId);
        if ($addArr) {
            $addRoleIdStr = implode(',', array_remove_empty($addArr));
            $addRoleInfo = $clsRole->getRoleNameById($addRoleIdStr);
            //用户列表日志
            $option = "添加角色:“{$addRoleInfo[ 'msg' ][ 0 ][ 'name' ]}”；";
            $clsUser->setLog(array(
                'lu_user' => $adminU,
                'lu_add_time' => time(),
                'lu_option' => $option,
                'lu_zt_id' => $adminZtId,
                'lu_uid' => $userId,
            ));
            //角色列表日志
            $addRoleList = $clsRole->getRoleInfoById($addRoleIdStr);
            foreach ($addRoleList['msg'] as $addRoleInfos) {
                $roleOption = "添加用户：{$userInfo['msg'][0]['u_username']}；";
                $clsRole->setLog(array(
                    'lr_user' => $adminU,
                    'lr_add_time' => time(),
                    'lr_option' => $roleOption,
                    'lr_zt_id' => $adminZtId,
                    'lr_role_id' => $addRoleInfos['r_id'],
                ));
            }
        }
        if ($delArr) {
            $delRoleIdStr = implode(',', array_remove_empty($delArr));
            $delRoleInfo = $clsRole->getRoleInfoById($delRoleIdStr);
            //角色列表日志
            foreach ($delRoleInfo['msg'] as $delInfo) {
                $option = "移除:“{$userInfo['msg'][0]['u_username']}”用户";
                $clsRole->setLog(array(
                    'lr_user' => $adminU,
                    'lr_add_time' => time(),
                    'lr_option' => $option,
                    'lr_zt_id' => $adminZtId,
                    'lr_role_id' => $delInfo['r_id'],
                ));
            }
            //用户列表日志
            $delRoleIdStr = implode(',', array_remove_empty($delArr));
            $delNameStr = $clsRole->getRoleNameById($delRoleIdStr);
            $roleOption = "移除角色:{$delNameStr[ 'msg' ][ 0 ][ 'name' ]}";
            $clsUser->setLog(array(
                'lu_user' => $adminU,
                'lu_add_time' => time(),
                'lu_option' => $roleOption,
                'lu_zt_id' => $adminZtId,
                'lu_uid' => $userId,
            ));
        }
    }
}
