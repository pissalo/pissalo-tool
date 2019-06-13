<?php
/**
 * 用户类
 * @author:     我不是稻草人 <junqing124@126.com>
 * @version:    v1.0.3
 * @package class
 * @since 1.0.8
 */

namespace OA;

/**
 * Class ClsUser
 * @package OA
 */
class ClsUser extends ClsData
{
    /**
     * 用户名
     * @var string
     * @access private
     */
    private $username;
    /**
     * 密码
     * @var string
     * @access private
     */
    private $password;
    /**
     * 当前用户名
     * @var string
     * @access private
     */
    private $adminU;
    /**
     * 用户ID
     * @var
     * @access private
     */
    private $id;
    /**
     * 账套ID
     * @var int
     * @access private
     */
    private $ztId;

    /**
     * OA系统操作权限
     * @var array
     * @access private
     */
    private $oaOptionPerArr;

    /**
     * OA系统读权限
     * @var array
     * @access private
     */
    private $oaReadPerArr;

    /**
     * 用户下属列表
     * @var array
     * @access private
     */
    private $userSubList = array();

    /**
     * 用户上级列表
     * @var array
     * @access private
     */
    private $userSupList = array();

    /**
     * 构建用户类
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $ztId 账套ID
     */
    public function __construct($username = '', $password = '', $ztId = '')
    {
        //密码应该为加密后的字符串
        $this->username = $username;
        $this->password = $password;
        $this->ztId = $ztId;
        parent::__construct('@#@user');
    }

    /**
     * 验证用户名
     * @return boolean
     */
    public function yz()
    {
        $info = parent::selectOneEx(array(
            'col' => 'u_id,u_username,u_ticket',
            'where' => "u_username='{$this->username}' and u_password='{$this->password}' and u_status=1"
        ));
        $info = $info['msg'];
        //echo parent::getLastSql()['msg'];

        $this->id = $info['u_id'];
        $this->adminU = $info['u_user_name'];

        $loginError = '';
        if ($info['u_id'] < 1) {
            $isLoginOk = 0;
            $loginError = '您输入的用户名或密码不正确！';
        } else {
            $isLoginOk = 1;
        }
        $result = array();
        if ($isLoginOk) {
            $result['ack'] = 1;
            $result['msg'] = '登录成功';
            $result['ticket'] = $info['u_ticket'];
            //获取权限信息
            $clsRp = new ClsRolePermission();
            $perTmpArr = $clsRp->getUserPerByUid($this->id);
            $this->oaOptionPerArr = $perTmpArr['msg']['option_per_arr'];
            $this->oaReadPerArr = $perTmpArr['msg']['read_per_arr'];
        } else {
            $result['ack'] = 0;
            $result['msg'] = $loginError;
        }
        return $result;
    }


    /**
     * 登陆
     * @param $zone 区域编号
     * @return boolean
     */
    public function login($zone = null)
    {
        $ticket = time() . get_rand_str(20);
        //登陆
        $_SESSION['adminU'] = $this->username;
        $_SESSION['adminP'] = $this->password;
        $_SESSION['adminId'] = $this->id;
        $_SESSION['adminZtId'] = $this->ztId;
        $_SESSION['adminOptionPer'] = $this->oaOptionPerArr;
        $_SESSION['adminReadPer'] = $this->oaReadPerArr;

        $loginInfo = array(
            'u_login_ip' => get_ip(),
            'u_login_time' => time(),
            'u_login_count' => 'u_login_count+1',
            'u_session_id' => session_id(),
            'u_ticket' => $ticket,
        );

        $val = parent::update($loginInfo, "u_username='{$this->username}'");

        $clsLog = new ClsLog('oa');
        $clsLog->setCollection('oa_user_login');
        $val = $clsLog->addLog(array(
            'o_zt_id' => $loginInfo['u_zt_id'],
            'o_username' => $this->username,
            'o_login_time' => $loginInfo['u_login_time'],
            'o_login_ip' => $loginInfo['u_login_ip'],
        ));

        return array('ack' => 1);
    }

    /**
     * 获取用户信息
     * @param array $param 查询条件数组
     * @return array 查询结果数组
     * @author 王银龙
     */
    public function getUserInfo($param)
    {
        if (!$param['order']) {
            $param['order'] = 'u_id desc';
        }
        return $this->selectEx($param);
    }

    /**
     * 通过用户ID获取用户信息
     * @param int /string $userId 用户ID
     * @return array 查询结果数组
     * @author 王银龙
     */
    public function getUserInfoById($userId)
    {
        return $this->getUserInfo(array('where' => "u_id in ({$userId})"));
    }

    /**
     * 通过用户姓名获取用户信息
     * @param string $userName 用户姓名
     * @return array 查询结果数组
     * @author 王银龙
     */
    public function getUserInfoByName($userName)
    {
        return $this->getUserInfo(array('where' => "u_username = '{$userName}'"));
    }

    /**
     * 通过用户ID获取用户名
     * @param int /string $u_id 用户ID
     * @return array 执行结果
     */
    public function getUserNameById($u_id)
    {
        return $this->getUserInfo(
            array(
                'col' => 'GROUP_CONCAT(u_username) name',
                'where' => "u_id in ( $u_id )"
            )
        );
    }

    /**
     * 获取用户ID列表
     * @param array $param 查询参数
     * @return array 查询结果数组
     * @author 王银龙
     */
    public function getUserIdList($param)
    {
        $userList = $this->getUserInfo($param);
        $user_id_list = array_column($userList['msg'], 'u_id');
        return $user_id_list;
    }

    /**
     * 通过组织ID获取用户ID
     * @param $esId     组织ID
     * @return array    查询结果数组
     * @author          王银龙
     */
    public function getUserIdByEsId($esId)
    {
        return $this->getUserIdList(
            array(
                'where' => "u_es_id in ({$esId})"
            )
        );
    }

    /**
     * 获取用户所有下级列表
     * @param int $u_id 用户id
     * @return array 用户列表
     */
    public function getUserSubList($u_id)
    {
        $userList = $this->getUserInfo(
            array(
                'where' => "u_sup_id = {$u_id}"
            )
        );
        //$userList['msg'] = array ();
        foreach ($userList['msg'] as $userInfo) {
            //跳过上级是自己的情况
            if ($u_id == $userInfo['u_id']) {
                continue;
            }
            array_push($this->userSubList, $userInfo['u_id']);
            $this->getUserSubList($userInfo['u_id']);
        }
        $returnMsg['ack'] = 1;
        $returnMsg['msg'] = $this->userSubList;
        return $returnMsg;
    }

    /**
     * 添加OA用户
     * @param array $userInfo 用户信息
     * @param array $uaUserInfo 用户附加信息
     * @return array 执行结果数组
     * @author 王银龙
     */
    public function addUser($userInfo, $uaUserInfo)
    {
        global $adminU;
        global $adminZtId;
        $returnMsg = array();
        $cls_ua = new \OA\ClsData('oa_user_addition');
        $isError = false;
        //检测密码
        if (strlen($userInfo['u_password']) < 8 || strlen($userInfo['u_password']) > 20) {
            $returnMsg['msg'] .= '新密码长度应该在8-20之间';
            $isError = true;
        }
        if (!passwordCheck($userInfo['u_password'])) {
            $returnMsg['msg'] .= '密码强度太弱!必须包含字母数字或特殊字符';
            $isError = true;
        }
        if ($isError) {
            $returnMsg['ack'] = 0;
            return $returnMsg;
        }
        $userInfo['u_password'] = encrypt($userInfo['u_password']);
        //获取最大员工编号
        $u_info = $this->getUserInfo(
            array(
                /*'where' => "ua_woke_place = {$uaUserInfo['ua_woke_place']}" ,*/
                'col' => 'max(ua_numb) num ',
                'join' => 'left join oa_user_addition on ua_u_id = u_id'
            )
        );
        //获取分公司编号

        $esCode = '';
        $clsEs = new \OA\ClsEnterpriseStructure();
        $esCodeInfo = $clsEs->getEsCodeById($userInfo['u_es_id']);
        if (1 == $esCodeInfo['ack']) {
            $esCode = $esCodeInfo['msg'];
        } else {
            return $esCodeInfo;
        }
        $uaUserInfo['ua_numb'] = $u_info['msg'][0]['num'] + 1;
        for ($i = 1; $i < 5; $i++) {
            if (strlen($uaUserInfo['ua_numb']) < 5) {
                $uaUserInfo['ua_numb'] = '0' . $uaUserInfo['ua_numb'];
            }
        }
        //生成工号、用户名
        $userInfo['u_username'] = $userInfo['u_name'] . $uaUserInfo['ua_numb'];
        $userInfo['u_work_numb'] = $esCode . $uaUserInfo['ua_numb'];
        $this->transactionBegin();
        $flag = $this->insertEx($userInfo, 1);
        $uaUserInfo['ua_u_id'] = $flag['insert_id'];
        $ua_flag = $cls_ua->insertEx($uaUserInfo);
        if ($flag['ack'] && $ua_flag['ack']) {
            $this->transactionCommit();
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '添加成功!';
            //日志
            $this->setLog(
                array(
                    'lu_user' => $adminU,
                    'lu_add_time' => time(),
                    'lu_option' => "添加用户:{$userInfo['u_username']}",
                    'lu_zt_id' => $adminZtId,
                    'lu_uid' => (string)$uaUserInfo['ua_u_id'],
                )
            );
        } else {
            $this->transactionRollback();
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '添加失败,数据库操作失败，请重新尝试!';
        }
        return $returnMsg;
    }

    /**
     * 更新用户
     * @param array $userInfo 用户信息数组
     * @param int $user_id 用户ID
     * @return array 执行结果数组
     * @author 王银龙
     */
    public function updateUser($userInfo, $user_id)
    {
        //获取当前用户信息
        $oldUserInfo = $this->getUserInfoById($user_id);
        //检查组织
        if ($userInfo['u_es_id']) {
            $clsEs = new \OA\ClsEnterpriseStructure();
            $esCodeInfo = $clsEs->getEsCodeById($userInfo['u_es_id']);
            if (!$esCodeInfo['ack']) {
                return $esCodeInfo;
            }
        }
        $flag = $this->updateOne($userInfo, "u_id = {$user_id}");
        if ($flag['ack']) {
            $flag['msg'] = '修改成功';
            //添加日志
            $this->setUserLog($oldUserInfo['msg'][0], $userInfo);
        } else {
            $flag['msg'] = '修改失败';
        }
        return $flag;
    }

    /**
     * 更新用户扩展信息
     * @param array $userInfoAddition 用户扩展信息数组
     * @param int $uId 用户ID
     * @return array 执行结果数组
     * @author 王银龙
     */
    public function updateUaUser($userInfoAddition, $uId)
    {
        $cls_ua = new \OA\ClsData('oa_user_addition');
        //获取旧UA信息
        $old_ua_info = $cls_ua->selectOneEx(
            array(
                'where' => "ua_u_id = {$uId}"
            )
        );
        $flag = $cls_ua->updateOne($userInfoAddition, "ua_u_id = {$uId}");
        if ($flag['ack']) {
            $flag['msg'] = '修改成功';
            //添加日志
            $this->setUaLog($old_ua_info['msg'], $userInfoAddition);
        } else {
            $flag['msg'] = '修改失败';
        }
        return $flag;
    }

    /**
     * 退出管理
     * @return true
     */
    public function logout()
    {
        unset($_SESSION['admin_u']);
        unset($_SESSION['admin_p']);
        unset($_SESSION['adminId']);
        unset($_SESSION['adminU']);
        unset($_SESSION['adminP']);
    }

    /**
     * 设置用户操作日志
     * @param array $u_old_info 更新前用户信息数组
     * @param array $u_new_info 更新后用户信息数组
     * @return array 执行结果
     * @author 王银龙
     */
    public function setUserLog(array $userOldInfo, array $userNewInfo)
    {
        global $adminZtId;
        global $adminU;
        $flag = array('ack' => 1);   //返回执行结果
        $optionMsg = '';

        //修改组织
        if ($userOldInfo['u_es_id'] != $userNewInfo['u_es_id'] && isset($userNewInfo['u_es_id'])) {
            $clsEs = new \OA\ClsEnterpriseStructure();
            $clsEs->esList = array();
            $oldEsList = $clsEs->getSupEsList($userOldInfo['u_es_id']);
            $clsEs->esList = array();
            $newEsList = $clsEs->getSupEsList($userNewInfo['u_es_id']);
            $oldEsStr = '';
            foreach ($oldEsList['msg'] as $oldEsInfo) {
                if ($oldEsStr) {
                    $oldEsStr .= '->';
                }
                $oldEsStr .= $oldEsInfo['es_name'];
            }
            $newEsStr = '';
            foreach ($newEsList['msg'] as $newEsInfo) {
                if ($newEsStr) {
                    $newEsStr .= '->';
                }
                $newEsStr .= $newEsInfo['es_name'];
            }
            $optionMsg .= "组织由：{$oldEsStr}，改为：{$newEsStr};";
        }
        //修改职位
        if ($userOldInfo['u_position_id'] != $userNewInfo['u_position_id'] && isset($userNewInfo['u_position_id'])) {
            global $position_list;
            $optionMsg .= "职位由：{$position_list[$userOldInfo['u_position_id']]}，修改为：{$position_list[$userNewInfo['u_position_id']]};";
        }
        //修改状态
        if ($userOldInfo['u_status'] != $userNewInfo['u_status'] && isset($userNewInfo['u_status'])) {
            global $user_status_list;
            $optionMsg .= "状态由：{$user_status_list[$userOldInfo['u_status']]}，修改为：{$user_status_list[$userNewInfo['u_status']]};";
        }
        //修改领导
        if ($userOldInfo['u_sup_id'] != $userNewInfo['u_sup_id'] && isset($userNewInfo['u_sup_id'])) {
            $clsUser = new \OA\ClsUser();
            $oldSupUserInfo = $clsUser->getUserInfoById($userOldInfo['u_sup_id']);
            $newSupUserInfo = $clsUser->getUserInfoById($userNewInfo['u_sup_id']);
            $optionMsg .= "直属领导由：{$oldSupUserInfo['msg']['0']['u_username']}，修改为：{$newSupUserInfo['msg']['0']['u_username']};";
        }
        //修改密码
        if ($userOldInfo['u_password'] != $userNewInfo['u_password'] && isset($userNewInfo['u_password'])) {
            $optionMsg .= "修改了密码;";
        }
        if ($optionMsg) {
            $flag = $this->setLog(
                array(
                    'lu_user' => $adminU,
                    'lu_add_time' => time(),
                    'lu_option' => $optionMsg,
                    'lu_zt_id' => $adminZtId,
                    'lu_uid' => $userOldInfo['u_id'],
                )
            );
        }
        return $flag;
    }

    /**
     * 设置用户扩展表日志
     * @param array $uaOldInfo 更新前用户扩展信息数组
     * @param array $uaNewInfo 更新后用户扩展信息数组
     * @return array $flag 执行结果数组
     * @author 王银龙
     */
    public function setUaLog(array $uaOldInfo, array $uaNewInfo)
    {
        global $adminZtId;
        global $adminU;
        global $userCharacterList;
        $flag = array('ack' => 1);   //返回执行结果
        $option_msg = '';
        //修改政治面貌
        if ($uaOldInfo['ua_politics_status'] != $uaNewInfo['ua_politics_status'] && isset($uaNewInfo['ua_politics_status'])) {
            global $politics_status_list;
            $option_msg .= "政治面貌由：{$politics_status_list[ $uaOldInfo[ 'ua_politics_status' ] ]}，改为：{$politics_status_list[ $uaNewInfo[ 'ua_politics_status' ] ]}；";
        }
        //修改所属公司
        if ($uaOldInfo['ua_belong_company'] != $uaNewInfo['ua_belong_company'] && isset($uaNewInfo['ua_belong_company'])) {
            $clsConfig = new \OA\ClsConfig();
            $companyNameList = $clsConfig->getCompanyList(array('order' => 'cc_id desc', 'col' => 'cc_id,cc_name'));
            $companyNameList = change_main_key($companyNameList['msg'], 'cc_id');

            $option_msg .= "所属公司由：{$companyNameList[ $uaOldInfo[ 'ua_belong_company' ] ]['cc_name']}，改为：{$companyNameList[ $uaNewInfo[ 'ua_belong_company' ] ]['cc_name']}；";
        }
        //修改入职时间
        if ($uaOldInfo['ua_entry_time'] != $uaNewInfo['ua_entry_time'] && isset($uaNewInfo['ua_entry_time'])) {
            $new_time = $uaNewInfo['ua_entry_time'] ? date('Y-m-d', $uaNewInfo['ua_entry_time']) : '';
            $old_time = $uaOldInfo['ua_entry_time'] ? date('Y-m-d', $uaOldInfo['ua_entry_time']) : '';
            $option_msg .= "入职时间由：{$old_time}，改为：{$new_time}";
        }
        //修改所在地
        if ($uaOldInfo['ua_woke_place'] != $uaNewInfo['ua_woke_place'] && isset($uaNewInfo['ua_woke_place'])) {
            //获取组织信息
            $cls_es = new \OA\ClsEnterpriseStructure();
            $es_list = $cls_es->getEsInfo();
            $es_list = change_main_key($es_list['msg'], 'es_id');
            $option_msg .= "所在地由：{$es_list[ $uaOldInfo[ 'ua_woke_place' ] ][ 'es_name' ]}，修改为：{$es_list[ $uaNewInfo[ 'ua_woke_place' ] ][ 'es_name' ]}；";
        }
        //修改性别
        if ($uaOldInfo['ua_sex'] != $uaNewInfo['ua_sex'] && isset($uaNewInfo['ua_sex'])) {
            $option_msg .= "性别地由：{$uaOldInfo[ 'ua_sex' ]}，修改为：{$uaNewInfo[ 'ua_sex' ]}；";
        }
        //修改毕业学校
        if ($uaOldInfo['ua_college'] != $uaNewInfo['ua_college'] && isset($uaNewInfo['ua_college'])) {
            $option_msg .= "毕业学校地由：{$uaOldInfo[ 'ua_college' ]}，修改为：{$uaNewInfo[ 'ua_college' ]}；";
        }
        //籍贯
        if ($uaOldInfo['ua_native_place'] != $uaNewInfo['ua_native_place'] && isset($uaNewInfo['ua_native_place'])) {
            $option_msg .= "籍贯地由：{$uaOldInfo[ 'ua_native_place' ]}，修改为：{$uaNewInfo[ 'ua_native_place' ]}；";
        }
        //修改员工性质
        if ($uaOldInfo['ua_user_character'] != $uaNewInfo['ua_user_character'] && isset($uaNewInfo['ua_user_character'])) {
            $option_msg .= "员工性质由：{$userCharacterList[$uaOldInfo[ 'ua_user_character' ]]}，修改为：{$userCharacterList[$uaNewInfo[ 'ua_user_character' ]]}；";
        }
        if ($option_msg) {
            $flag = $this->setLog(
                array(
                    'lu_user' => $adminU,
                    'lu_add_time' => time(),
                    'lu_option' => $option_msg,
                    'lu_zt_id' => $adminZtId,
                    'lu_uid' => $uaOldInfo['ua_u_id'],
                )
            );
        }
        return $flag;
    }

    /**
     * 获取用户日志
     * @param int $uId 用户ID
     * @param int $page 页数
     * @param int $showNum 每页显示数
     * @return array 日志数组
     * @author 王银龙
     */
    public function getUserLog($uId, $page, $showNum)
    {
        global $adminZtId;
        $returnMsg = array('ack' => 1);
        $clsLog = new ClsLog('oa');
        $clsLog->setCollection('log_user');
        $where['lu_zt_id'] = $adminZtId;
        if ($uId) {
            $where['lu_uid'] = $uId;
        }
        $log_list = $clsLog->getList(intval($page), intval($showNum), $where);
        $new_log_list = array();
        foreach ($log_list['msg'] as $log_key => $log_info) {
            $new_log_list[$log_key]['user'] = $log_info['lu_user'];
            $new_log_list[$log_key]['add_time'] = $log_info['lu_add_time'];
            $new_log_list[$log_key]['option'] = $log_info['lu_option'];
        }
        $returnMsg['msg'] = $new_log_list;
        $returnMsg['count'] = $clsLog->getNum($where);
        return $returnMsg;
    }

    /**
     * 设置日志
     * @param array $log_content 日志内容
     * @return array 执行结果
     * @author 王银龙
     */
    public function setLog($log_content)
    {
        $clsLog = new ClsLog('oa');
        $clsLog->setCollection('log_user');
        return $clsLog->addLog($log_content);
    }

    /**
     * 获取用户指定层级上级
     * @param $u_id     用户ID
     * @param $level    层级级别
     * @return array    返回结果数组
     * @author          王银龙
     */
    public function getUserSupByIdLevel($userId, $level = 1)
    {
        $returnMsg = array('ack' => 1);
        $userInfo = $this->getUserInfoById($userId);
        $userInfo = $userInfo['msg'][0];
        if ($userInfo['u_sup_id'] && 0 != $level) {
            array_push($this->userSupList, $userInfo['u_sup_id']);
            $level--;
            $this->getUserSupByIdLevel($userInfo['u_sup_id'], $level);
        }
        $returnMsg['msg'] = $this->userSupList;
        return $returnMsg;
    }

    /**
     * 获取属性userSupList
     * @return array
     */
    public function getUserSupList()
    {
        return array('ack' => 1, 'msg' => $this->userSupList,);
    }

    /**
     * 设置属性userSupList
     * @param $userSupList
     */
    public function setUserSupList($userSupList)
    {
        $this->userSupList = $userSupList;
    }
}
