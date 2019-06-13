<?php
/**
 * 角色权限类
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2018年12月29日
 * Time:14:43:53
 */

namespace OA;

class ClsRolePermission extends ClsData
{
    /**
     * cls_role_permission constructor.
     */
    public function __construct()
    {
        parent::__construct('oa_role_permission');
    }

    /**
     * 获取角色特定权限信息
     * @param int $role_id 角色ID
     * @param int/string $option_per_id 权限ID
     * @return array 执行结果
     */
    public function getRolePermissionInfo($role_id, $option_per_id)
    {
        return $this->selectEx(
            array(
                'where' =>
                    " rp_role_id = {$role_id} " .
                    " and rp_option_per_id in ({$option_per_id}) "
            )
        );
    }

    /**
     * 获取角色权限列表
     * @author 王银龙
     * @param int/string $role_id 角色ID或者ID字符串
     * @param int $system_id 系统ID
     * @return array 查询结果集
     */
    public function getRolePermissionList($role_id, $system_id)
    {
        $returnMsg = array('ack' => 1);
        $role_per_list = $this->selectEx(
            array(
                'where' =>
                    array(
                        "rp_role_id in ({$role_id})",
                        "up_system_id = {$system_id}"
                    ),
                'join' => 'left join oa_user_permission on up_id = rp_option_per_id'
            )
        );
        $role_read_per_list = array();
        //查看权限
        foreach ($role_per_list['msg'] as $role_per_info) {
            if ($role_per_info['rp_read_per_id']) {
                if (in_array($role_read_per_list[$role_per_info['rp_option_per_id']], array(1, 3))) {
                    continue;
                } else {
                    $role_read_per_list[$role_per_info['rp_option_per_id']] = $role_per_info['rp_read_per_id'];
                }
            }
        }
        $returnMsg['msg']['option_per'] = $role_per_list['msg'];
        $returnMsg['msg']['read_per'] = $role_read_per_list;
        return $returnMsg;
    }

    /**
     * 通过用户ID获取用户操作、读权限
     * @author 王银龙
     * @param int $u_id 用户ID
     * @param $system_id 系统ID
     * @return array 结果数组
     */
    public function getUserPerByUid($u_id, $system_id = 2)
    {
        $returnMsg = array(
            'ack' => 1,
            'msg' => array(
                'option_per_str' => '',
                'read_per_arr' => array()
            )
        );
        //获取用户角色列表
        $cls_ur = new ClsUserRole();
        $user_role_list = $cls_ur->getUserRoleList($u_id);
        //p_r( $user_role_list );
        $user_role_id_str = implode(',', array_remove_empty(array_column($user_role_list['msg'], 'ur_role_id')));
        //获取用户权限
        $user_per_list = $this->getRolePermissionList($user_role_id_str, $system_id);
        //获取用户额外权限
        $clsUserExtendPermission = new ClsUserExtendPermission();
        $userExtendPermissionList = $clsUserExtendPermission->getExPermissionArrByUserId($u_id);
        //p_r($userExtendPermissionList);
        if ($user_per_list['ack']) {
            //合并角色操作权限和用户扩展操作权限
            $rolePerArr = array_remove_empty(array_column($user_per_list['msg']['option_per'], 'up_third_system_id'));
            $optionPerArr = $rolePerArr ? $rolePerArr : array();
            $exOptionPerArr = $userExtendPermissionList['option_per_arr'] ? $userExtendPermissionList['option_per_arr'] : array();
            $returnMsg['msg']['option_per_arr'] = array_unique(array_merge($optionPerArr, $exOptionPerArr));
            //合并角色读权限和用户扩展读权限
            $readPerArr = $user_per_list['msg']['read_per'];
            $exReadPerArr = $userExtendPermissionList['read_per_arr'];
            $newReadPerArr = array();
            if ($readPerArr) {
                foreach ($readPerArr as $key => $readPer) {
                    $newReadPerArr[$key] = $readPer;
                    switch ($exReadPerArr[$key]) {
                        case '1':
                            if (3 != $readPer) {
                                $newReadPerArr[$key] = $exReadPerArr[$key];
                            }
                            break;
                        case '2':
                            if (!in_array($readPer, array(1, 3))) {
                                $newReadPerArr[$key] = $exReadPerArr[$key];
                            }
                            break;
                        case '3':
                            $newReadPerArr[$key] = $exReadPerArr[$key];
                            break;
                    }
                }
            }
            if ($exReadPerArr) {
                foreach ($exReadPerArr as $key => $exReadPer) {
                    $newReadPerArr[$key] = $exReadPer;
                    switch ($readPerArr[$key]) {
                        case 1:
                            if (3 != $exReadPer) {
                                $newReadPerArr[$key] = $readPerArr[$key];
                            }
                            break;
                        case 2:
                            if (!in_array($exReadPer, array(1, 3))) {
                                $newReadPerArr[$key] = $readPerArr[$key];
                            }
                            break;
                        case 3:
                            $newReadPerArr[$key] = $readPerArr[$key];
                            break;
                    }
                }
            }
            $returnMsg['msg']['read_per_arr'] = $newReadPerArr;
        } else {
            $returnMsg['msg'] = 0;
            $returnMsg['msg'] = '获取信息失败!';
        }
        return $returnMsg;
    }

    /**
     * 解析读权限返回HTML ID
     * @author 王银龙
     * @param array $read_arr 读权限数组
     * @return array 读权限HTML ID数组
     */
    public function analysisReadPer(array $read_arr)
    {
        $html_id_arr = array();
        foreach ($read_arr as $per_id => $read_info) {
            if (1 == $read_info) {
                //读全部
                array_push($html_id_arr, "read_all_" . $per_id);
            } elseif (2 == $read_info) {
                //读自己和下级
                array_push($html_id_arr, "read_some_" . $per_id);
            } elseif (3 == $read_info) {
                array_push($html_id_arr, "read_all_" . $per_id);
                array_push($html_id_arr, "read_some_" . $per_id);
            }
        }
        return array('ack' => 1, 'msg' => $html_id_arr);
    }

    /**
     * 解析度权限返回用户ID
     * @author 王银龙
     * @param int $read_per 读权限
     * @return array 结果数组
     */
    public function analysisReadPerForUid($read_per)
    {
        global $adminId;
        $u_id_str = '';
        $clsUser = new \OA\ClsUser();
        $returnMsg = array('ack' => 1);
        if (2 == $read_per) {
            //读自己和下级
            $sub_id_list = $clsUser->getUserSubList($adminId);
            $sub_id_list = $sub_id_list['msg'];
            array_push($sub_id_list, $adminId);
            $u_id_str = implode(',', array_remove_empty($sub_id_list));
        } elseif (in_array($read_per, array(1, 3))) {
            $u_id_str = '';
        } else {
            $u_id_str = 10000000;
        }
        $returnMsg['msg'] = $u_id_str;
        return $returnMsg;
    }

    /**
     * 获取角色权限ID列表
     * @author 王银龙
     * @param int $role_id 角色ID
     * @param int $system_id 系统ID
     * @return array 权限ID数组
     */
    public function getRolePermissionIdList($role_id, $system_id)
    {
        $per_list = $this->getRolePermissionList($role_id, $system_id);
        $per_id_list = array_column($per_list['msg']['option_per'], 'rp_option_per_id');
        return array('ack' => 1, 'msg' => $per_id_list);
    }

    /**
     * 更新角色权限
     * @author 王银龙
     * @param array $rp_list 角色权限关系数组
     * @param int $role_id 角色ID
     * @param int $system_id 系统ID
     * @return array 执行结果
     */
    public function updateRolePermission(array $rp_list, $role_id, $system_id)
    {
        $returnMsg = array('ack' => 1);                 //返回结果
        $add_flag = array('ack' => 1);       //添加执行结果
        $delete_flag = array('ack' => 1);    //删除执行结果
        $update_flag = array('ack' => 1);    //更新执行结果
        $role_per_id_list = $this->getRolePermissionIdList($role_id, $system_id);
        //已分配权限ID数组
        $rp_per_id_arr = array_column($rp_list, 'rp_option_per_id');
        //需要添加权限的ID数组
        $add_per_id_arr = array_diff($rp_per_id_arr, $role_per_id_list['msg']);
        //需要删除权限的ID数组
        $delete_per_id_arr = array_diff($role_per_id_list['msg'], $rp_per_id_arr);
        //需要更新权限的ID数组
        $update_id_arr = array_intersect($rp_per_id_arr, $role_per_id_list['msg']);
        $this->transactionBegin();
        //添加权限
        if ($add_per_id_arr) {
            $add_list = array();
            foreach ($rp_list as $rp_info) {
                if (in_array($rp_info['rp_option_per_id'], $add_per_id_arr)) {
                    array_push($add_list, $rp_info);
                }
            }
            $add_flag = $this->addPerForRole($add_list);
        }
        //删除权限
        if ($delete_per_id_arr) {
            $delete_flag = $this->deletePerFromRole($delete_per_id_arr, $role_id);
        }
        //更新权限
        if ($update_id_arr) {
            //获取更新前信息数组
            $update_id_str = implode(',', array_remove_empty($update_id_arr));
            $old_role_per_list = $this->getRolePermissionInfo($role_id, $update_id_str);

            $update_list = array();
            foreach ($rp_list as $rp_info) {
                if (in_array($rp_info['rp_option_per_id'], $update_id_arr)) {
                    unset($rp_info['rp_add_time']);
                    unset($rp_info['rp_add_user_id']);
                    array_push($update_list, $rp_info);
                }
            }
            $update_flag = $this->updateRpInfo($update_list);
        }

        if ($add_flag['ack'] && $delete_flag['ack'] && $update_flag['ack']) {
            $this->transactionCommit();
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '操作成功!';
            //添加日志
            $this->setRolePerLog($add_list, $delete_per_id_arr, $update_list, $old_role_per_list['msg'], $role_id);
        } else {
            $this->transactionRollback();
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '操作失败，数据库异常，请重新操作';
        }
        return $returnMsg;
    }


    /**
     * 添加权限
     * @author 王银龙
     * @param array $add_list 权限ID数组
     * @return array 执行结果
     */
    public function addPerForRole(array $add_list)
    {
        return $this->insertBulk($add_list);
    }

    /**
     * 更新信息
     * @author 王银龙
     * @param array $update_list 更新信息数组
     * @return array 执行结果
     */
    private function updateRpInfo(array $update_list)
    {
        $returnMsg = array();
        $error_num = 0;
        foreach ($update_list as $update_info) {
            $update_where = "rp_role_id = {$update_info['rp_role_id']} and rp_option_per_id = {$update_info['rp_option_per_id']}";
            $flag = $this->updateOne($update_info, $update_where);
            $error_num += $flag['ack'] ? 0 : 1;
        }
        if ($error_num > 0) {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '操作失败，数据库操作异常，请重新尝试!';
        } else {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '操作成功!';
        }
        return $returnMsg;
    }


    /**
     * 删除权限
     * @author 王银龙
     * @param array $delete_per_id_list 删除权限ID数组
     * @param int $role_id 删除角色ID
     * @return array 执行结果
     */
    public function deletePerFromRole(array $delete_per_id_list, $role_id)
    {
        $returnMsg = array();
        $error_num = 0;
        foreach ($delete_per_id_list as $per_id) {
            $flag = $this->deleteEx(
                "rp_role_id = {$role_id} " .
                " and rp_option_per_id = {$per_id}"
            );
            $error_num += $flag['ack'] ? 0 : 1;
        }
        if ($error_num > 0) {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '失败';
        } else {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '成功';
        }
        return $returnMsg;
    }

    /**
     * 设置角色权限修改日志
     * @author 王银龙
     * @param array $add_per_arr 添加权限ID数组
     * @param array $del_per_arr 删除权限ID数组
     * @param array $upd_per_arr_new 更新后权限明细数组
     * @param array $upd_per_arr_old 更新前权限明细数组
     * @param int $role_id 角色ID
     * @return array 执行结果数组
     */
    public function setRolePerLog($add_per_arr, $del_per_arr, $upd_per_arr_new, $upd_per_arr_old, $role_id)
    {
        global $adminU;
        global $adminZtId;
        $cls_permission = new \OA\ClsPermissions();
        $clsLog = new ClsLog('oa');
        $clsLog->setCollection('log_role');
        $add_option = '';   //添加权限记录
        $del_option = '';   //删除权限记录
        //添加权限
        if ($add_per_arr) {
            //获取添加权限的ID
            $add_per_id_str = implode(',', array_remove_empty(array_column($add_per_arr, 'rp_option_per_id')));
            $add_per_name_list = $cls_permission->getPerNameById($add_per_id_str);
            $add_per_name_list = change_main_key($add_per_name_list['msg'], 'up_id');
            $add_option .= "添加以下权限：";
            foreach ($add_per_name_list as $add_per) {
                if ($add_per['sup_name']) {
                    $add_option .= "{$add_per['sup_name']}-";
                }
                $add_option .= "{$add_per['sub_name']}；";
            }
            //查看是否添加了读权限
            foreach ($add_per_arr as $add_info) {
                if (!$add_info['rp_read_per_id']) {
                    continue;
                }
                if ($add_per_name_list[$add_info['rp_option_per_id']]['sup_name']) {
                    $add_option .= add_per_name_list[$add_info['rp_option_per_id']]['sup_name'] . '-';
                }
                switch ($add_info['rp_read_per_id']) {
                    case 1:
                        $add_option .= "{$add_per_name_list[$add_info['rp_option_per_id']]['sub_name']}-查看全部权限；";
                        break;
                    case 2:
                        $add_option .= "{$add_per_name_list[$add_info['rp_option_per_id']]['sub_name']}-查看自己（下级）权限；";
                        break;
                    case 3:
                        $add_option .= "{$add_per_name_list[$add_info['rp_option_per_id']]['sub_name']}-查看全部权限；";
                        $add_option .= "{$add_per_name_list[$add_info['rp_option_per_id']]['sub_name']}-查看自己（下级）权限；";
                        break;
                    default:
                        break;
                }
            }
        }
        //删除操作权限
        if ($del_per_arr) {
            $del_per_str = implode(',', array_remove_empty($del_per_arr));
            $del_per_name_list = $cls_permission->getPerNameById($del_per_str);
            $del_option .= "删除了以下权限:";
            foreach ($del_per_name_list['msg'] as $del_per) {
                if ($del_per['sup_name']) {
                    $del_option .= "{$del_per['sup_name']}-";
                }
                $del_option .= "{$del_per['sub_name']}；";
            }
        }
        //读权限添加/修改判断
        if ($upd_per_arr_new) {
            //获取更新的权限信息
            $upd_per_str = implode(',', array_remove_empty(array_column($upd_per_arr_new, 'rp_option_per_id')));
            $upd_per_name_list = $cls_permission->getPerNameById($upd_per_str);
            $upd_per_name_list = $upd_per_name_list['msg'];
            $upd_per_name_list = change_main_key($upd_per_name_list, 'up_id');

            $new_list = change_main_key($upd_per_arr_new, 'rp_option_per_id');
            $old_list = change_main_key($upd_per_arr_old, 'rp_option_per_id');
            foreach ($new_list as $new_key => $new_info) {
                if ($new_info['rp_read_per_id'] != $old_list[$new_key]['rp_read_per_id']) {
                    //获取具体编号
                    $numb_arr = $this->getReadPerNumb($new_info['rp_read_per_id'], $old_list[$new_key]['rp_read_per_id']);
                    if ($numb_arr['add_per']) {
                        //添加读权限
                        if (!$add_option) {
                            $add_option = '添加以下权限：';
                        }
                        $read_per_name = '';
                        $read_per_info = $this->readPerToCh(array_shift($numb_arr['add_per']));
                        $read_per_name .= $upd_per_name_list[$new_key]['sub_name'] . '-' . $read_per_info['msg'] . '；';
                        $add_option .= $read_per_name;
                    }
                    if ($numb_arr['del_per']) {
                        //删除读权限
                        if (!$del_option) {
                            $del_option = '删除了以下权限：';
                        }
                        $read_per_name = '';
                        $read_per_info = $this->readPerToCh(array_shift($numb_arr['del_per']));
                        $read_per_name .= $upd_per_name_list[$new_key]['sub_name'] . '-' . $read_per_info['msg'] . '；';
                        $del_option .= $read_per_name;
                    }
                }
            }
        }
        $flag = array('ack' => 1);
        if ($add_option || $del_option) {
            $flag = $clsLog->addLog(
                array(
                    'lr_user' => $adminU,
                    'lr_add_time' => time(),
                    'lr_option' => $add_option . '||' . $del_option,
                    'lr_zt_id' => $adminZtId,
                    'lr_role_id' => $role_id,
                )
            );
        }
        return $flag;
    }

    /**
     * 将读权限ID拆分为子权限
     * @author 王银龙
     * @param $read_per_id 读权限ID
     * @return array 结果数组
     */
    public function splitReadPer($read_per_id)
    {
        $returnMsg = array('ack' => 1);
        switch ($read_per_id) {
            case 1://读全部
                $returnMsg['msg'] = array(1);
                break;
            case 2://读自己（下级）
                $returnMsg['msg'] = array(2);
                break;
            case 3://读自己（下级）、读全部
                $returnMsg['msg'] = array(1, 2);
                break;
            default:
                $returnMsg['ack'] = 0;
                break;
        }
        return $returnMsg;
    }


    /**
     * 判断是添加读权限还是删除读权限
     * @author 王银龙
     * @param $new_per 修改后读ID
     * @param $old_per 修改前读ID
     * @return array 差集数组
     */
    public function getReadPerNumb($new_per, $old_per)
    {
        $returnMsg = array('ack' => 1);
        //获取更新前后读权限明细
        $new_arr = $this->splitReadPer($new_per);
        $old_arr = $this->splitReadPer($old_per);
        //防止空值
        $new_read_arr = $new_arr['msg'] ? $new_arr['msg'] : array();
        $old_read_arr = $old_arr['msg'] ? $old_arr['msg'] : array();
        //比较更新前后差异
        $arr_per = array_diff($new_read_arr, $old_read_arr);
        $del_per = array_diff($old_read_arr, $new_read_arr);
        //返回结果
        $returnMsg['add_per'] = $arr_per;
        $returnMsg['del_per'] = $del_per;
        return $returnMsg;
    }

    /**
     * 将读权限翻译成中文
     * @author 王银龙
     * @param $read_per_num 读权限编号
     * @return array 翻译后的结果
     */
    public function readPerToCh($read_per_num)
    {
        $returnMsg = array('ack' => 1);
        switch ($read_per_num) {
            case 1:
                $returnMsg['msg'] = '查看全部';
                break;
            case 2:
                $returnMsg['msg'] = '查看自己(下级)';
                break;
            default:
                $returnMsg['ack'] = 0;
                $returnMsg['msg'] = '';
                break;
        }
        return $returnMsg;
    }
}
