<?php
/**
 * abstract:组织结构类
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2018年12月18日
 * Time:15:05:27
 */

namespace OA;

class ClsEnterpriseStructure extends ClsData
{
    /**
     * 组织结构数组
     * @var array
     */
    public $esList = array();

    /**
     * 上级组织结构数组
     * @var array
     */
    private $esSupList = array();

    /**
     * cls_enterprise_structure constructor.
     */
    public function __construct()
    {
        parent::__construct('oa_enterprise_structure');
    }

    /**
     * 获取组织结构信息
     * @author 王银龙
     * @param array $where 查询条件
     * @param string $col 信息字段字符串
     * @return array 返回查询结果
     */
    public function getEsInfo(array $where = array(), $col = '*')
    {
        return $this->selectEx(
            array(
                'col' => $col,
                'where' => $where
            )
        );
    }

    /**
     * 通过组织名称获取组织信息
     * @param $es_name  组织名称
     * @param int $es_type 组织类型（可选）
     * @return array    返回结果集
     */
    public function getEsInfoByName($es_name, $es_type = 0)
    {
        $where = array();
        $where[] = "es_name = '{$es_name}'";
        if ($es_type) {
            $where[] = "es_type = {$es_type}";
        }
        return $this->getEsInfo($where);
    }

    /**
     * 通过组织ID获取组织信息
     * @author 王银龙
     * @param int/string $esId 组织ID
     * @return array 组织信息数组
     */
    public function getEsInfoById($esId)
    {
        return $this->getEsInfo(array("es_id in ({$esId})"));
    }

    /**
     * 通过组织ID获取组织名称
     * @author 王银龙
     * @param int/string $esId 组织ID
     * @return array 查询结果数组
     */
    public function getEsNameById($esId)
    {
        return $this->getEsInfo(array("es_id in ({$esId})"), "GROUP_CONCAT(es_name) name");
    }

    /**
     * 获取组织结构列表(单一数组)
     * @author 王银龙
     * @return array 返回执行结果
     */
    public function getEsList()
    {
        $this->esList = array();
        $oneLevelEsList = $this->getOneLevelEsInfo();
        $oneLevelEsList = $oneLevelEsList['msg'];
        foreach ($oneLevelEsList as $key => $oneLevelEsInfo) {
            array_push($this->esList, $oneLevelEsInfo);
            $sonList = $this->getSonEsList($oneLevelEsInfo['es_id']);
            $oneLevelEsList[$key]['son_list'] = $sonList;
        }
        $sonList = $this->esList;
        $this->esList = array();
        return array('ack' => 1, 'msg' => $sonList);
    }

    /**
     * 获取所有组织结构以格式返回JSON(树形结构)
     */
    public function getEsListTree()
    {
        #获取所有组织列表
        $allEsList = $this->getEsList();
        $allEsList = $allEsList['msg'];
        $allEsList = change_main_key($allEsList, 'es_id');
        $sameSupArr = array();#同上级列表
        foreach ($allEsList as $allEsInfo) {
            $allEsInfo['name'] = $allEsInfo['es_name'];
            $allEsInfo['value'] = $allEsInfo['es_id'];
            $sameSupArr[$allEsInfo['es_sup_id']][] = $allEsInfo;
        }
        //获取最小级别组织
        $esMaxLevelInfo = $this->selectOneEx(
            array('col' => 'max(es_level) maxLevel')
        );
        $esMaxLevel = $esMaxLevelInfo['msg']['maxLevel'];
        for ($i = $esMaxLevel; $i > 0; $i--) {
            foreach ($sameSupArr as $supId => $sameSupInfo) {
                if ($i != $sameSupInfo[0]['es_level']) {
                    continue;
                }
                foreach ($sameSupArr[$allEsList[$supId]['es_sup_id']] as $key => $info) {
                    if ($info['es_id'] == $supId) {
                        $sameSupArr[$allEsList[$supId]['es_sup_id']][$key]['children'] = $sameSupInfo;
                        unset($sameSupArr[$supId]);
                    }
                }
            }
        }
        return $sameSupArr;
    }

    /**
     * 获取组织树(包括用户)
     * @author 王银龙
     * @return 组织数组
     */
    public function getEsUserListTree()
    {
        #获取所有组织列表
        $clsUser = new \OA\ClsUser();
        $allEsList = $this->getEsList();
        $allEsList = $allEsList['msg'];
        $allEsList = change_main_key($allEsList, 'es_id');
        $sameSupArr = array();#同上级列表
        foreach ($allEsList as $allEsInfo) {
            $allEsInfo['name'] = $allEsInfo['es_name'];
            $allEsInfo['value'] = $allEsInfo['es_id'];
            $sameSupArr[$allEsInfo['es_sup_id']][] = $allEsInfo;
        }
        //获取最小级别组织
        $esMaxLevelInfo = $this->selectOneEx(
            array('col' => 'max(es_level) maxLevel')
        );
        $esMaxLevel = $esMaxLevelInfo['msg']['maxLevel'];
        for ($i = $esMaxLevel; $i > 0; $i--) {
            foreach ($sameSupArr as $supId => &$sameSupInfo) {
                if ($i != $sameSupInfo[0]['es_level']) {
                    continue;
                }
                foreach ($sameSupInfo as &$esInfo) {
                    //获取下属用户
                    $userList = $userList = $clsUser->getUserInfo(array(
                        'where' =>
                            " u_es_id = {$esInfo['es_id']}".
                            " and u_approval_status = 1"
                    ));
                    foreach ($userList['msg'] as $userInfo) {
                        $tmpArr = array();
                        $tmpArr['name'] = $userInfo['u_username'];
                        $tmpArr['value'] = 'u_' . $userInfo['u_id'];
                        $esInfo['children'][] = $tmpArr;
                    }
                    //获取下属组织
                    foreach ($sameSupArr[$esInfo['es_id']] as $sonInfo) {
                        $esInfo['children'][] = $sonInfo;
                    }
                    unset($sameSupArr[$esInfo['es_id']]);
                }
            }
        }
        return $sameSupArr;
    }

    /**
     * 获取一级组织结构信息
     * @author 王银龙
     * @return array 返回一级组织结构数组
     */
    public function getOneLevelEsInfo()
    {
        return $this->getEsInfo(array("es_level=1"));
    }

    /**
     * 获取下级组织结构列表（返回单一数组)
     * @author 王银龙
     * @param int/string $esId 结构ID
     * @return array 下级组织数组
     */
    public function getSonEsList($esId)
    {
        $esList = $this->getEsInfo(array("es_sup_id in ({$esId})"));
        $esList = $esList['msg'];
        foreach ($esList as $key => $esInfo) {
            array_push($this->esList, $esInfo);
            if ($esInfo['es_id']) {
                $son_info = $this->getSonEsList($esInfo['es_id']);
                if ($son_info) {
                    $esList[$key]['son_list'] = $son_info['msg'];
                }
            }
        }
        return $esList;
    }

    /**
     * 获取组织直接下级
     * @param $esId
     * @return array
     */
    public function getEsSonList($esId)
    {
        return $this->getEsInfo(array("es_sup_id = {$esId}"));
    }

    /**
     * 获取下属组织ID数组
     * @author 王银龙
     * @param $esId 组织ID
     * @return array 结果数组
     */
    public function getSonEsIdArr($esId)
    {
        $this->esList = array();
        $this->getSonEsList($esId);
        $sonEsIdArr = array_remove_empty(array_column($this->esList, 'es_id'));
        return array('ack' => 1, 'msg' => $sonEsIdArr);
    }

    /**
     * 获取上级组织列表
     * @author 王银龙
     * @param $esId 组织ID
     * @return array 上级组织列表
     */
    public function getSupEsList($esId)
    {
        $esInfo = $this->getEsInfo(array("es_id = {$esId}"));
        array_unshift($this->esList, $esInfo['msg'][0]);
        if ($esInfo['msg'][0]['es_sup_id']) {
            $this->getSupEsList($esInfo['msg'][0]['es_sup_id']);
        }
        return array('ack' => 1, 'msg' => $this->esList);
    }

    /**
     * 添加组织结构
     * @author 王银龙
     * @param array $esInfo 结构信息
     * @return array 执行结果
     */
    public function addEsInfo(array $esInfo)
    {
        $returnMsg = array();
        //检查同级别中是否有相同组织名
        $has_es_info = $this->getEsInfo(
            array(
                "es_level = {$esInfo['es_level']}",
                "es_name = '{$esInfo['es_name']}'",
                "es_sup_id = {$esInfo['es_sup_id']}"
            )
        );
        if ($has_es_info['msg']) {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '添加失败,该组织名已经存在';
            return $returnMsg;
        }
        //检查编号是否已被使用
        if ($esInfo['es_code']) {
            $has_code = $this->getEsInfo(
                array(
                    "es_code = '{$esInfo['es_code']}'"
                )
            );
            if ($has_code['msg']) {
                $returnMsg['ack'] = 0;
                $returnMsg['msg'] = '添加失败,该编号已经存在';
                return $returnMsg;
            }
        }
        $flag = $this->insertEx($esInfo);
        if ($flag['ack']) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '添加成功!';
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '添加失败,数据库操作失败，请重新尝试！';
        }
        return $returnMsg;
    }

    /**
     * 更新组织结构
     * @author 王银龙
     * @param array $esInfo 组织结构信息数组
     * @param int $esId 结构ID
     * @return array 执行结果
     */
    public function updateEsInfo(array $esInfo, $esId)
    {
        $returnMsg = array();
        $esInfoOld = $this->getEsInfo(array("es_id = {$esId}"));
        //检查同级别中是否有相同组织名
        $has_es_info = $this->getEsInfo(
            array(
                "es_level = {$esInfo['es_level']}",
                "es_name = '{$esInfo['es_name']}'",
                "es_sup_id = {$esInfo['es_sup_id']}",
                "es_id != {$esId}"
            )
        );
        if ($has_es_info['msg']) {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '更新失败，该组织名已经存在';
            return $returnMsg;
        }
        /*//检查编号是否已被使用
        if ( $esInfo['es_code'] )
        {
            $has_code = $this -> getEsInfo(
                array (
                    "es_code = '{$esInfo['es_code']}'"
                )
            );
            if ( $has_code['msg'] )
            {
                $returnMsg['ack'] = 0;
                $returnMsg['msg'] = '添加失败,该编号已经存在';
                return $returnMsg;
            }
        }*/
        $flag = $this->updateOne(
            $esInfo,
            "es_id = {$esId}"
        );
        if ($flag['ack']) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = '更新成功!';
            $this->setEsLog($esInfo, $esInfoOld['msg'][0]);
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '更新失败，数据库操作失败，请重新尝试！';
        }
        return $returnMsg;
    }

    /**
     * 设置组织结构日志
     * @author 王银龙
     * @param array $esInfoNew 新信息数组
     * @param array $esInfoOld 旧信息数组
     * @return array 执行结果
     */
    public function setEsLog(array $esInfoNew, array $esInfoOld)
    {
        global $adminU;
        $optionMsg = '';
        if ($esInfoNew['es_leader_user_id'] != $esInfoOld['es_leader_user_id']) {
            $clsUser = new \OA\ClsUser();
            $newUserInfo = $clsUser->getUserInfoById($esInfoNew['es_leader_user_id']);
            $oldUserInfo = $clsUser->getUserInfoById($esInfoOld['es_leader_user_id']);
            $optionMsg .= "{$esInfoOld['es_name']}负责人由:{$oldUserInfo['msg'][0]['u_username']}修改为:{$newUserInfo['msg'][0]['u_username']};";
        }
        if ($esInfoOld['es_name'] != $esInfoNew['es_name']) {
            $optionMsg .= "{$esInfoOld['es_name']} 组织名称改为: {$esInfoNew['es_name']};";
        }
        if ($optionMsg) {
            $flag = $this->setLog(
                array(
                    'les_user' => $adminU,
                    'les_add_time' => time(),
                    'les_option' => $optionMsg,
                    'les_zt_id' => $esInfoOld['zt_id'],
                    'les_es_id' => $esInfoOld['es_id'],
                )
            );
        }
        return $flag;
    }

    /**
     * 获取组织结构日志
     * @author 王银龙
     * @param int $esId 组织结构ID
     * @param int $page 页数
     * @param int $show_num 每页显示数
     * @return array 日志数组
     */
    public function getEsLog($esId, $page, $show_num)
    {
        global $adminZtId;
        $returnMsg = array('ack' => 1);
        $clsLog = new ClsLog('oa');
        $clsLog->setCollection('log_enterprise_structure');
        $where['les_zt_id'] = $adminZtId;
        /*if ( $esId )
        {
            $where[ 'les_es_id' ] = $esId;
        }*/
        $logList = $clsLog->getList(intval($page), intval($show_num), $where);
        $newLogList = array();
        foreach ($logList['msg'] as $logKey => $logInfo) {
            $newLogList[$logKey]['user'] = $logInfo['les_user'];
            $newLogList[$logKey]['add_time'] = $logInfo['les_add_time'];
            $newLogList[$logKey]['option'] = $logInfo['les_option'];
        }
        $returnMsg['msg'] = $newLogList;
        $returnMsg['count'] = $clsLog->getNum($where);
        return $returnMsg;
    }

    /**
     * 获取各公司、部门、组下人数
     * @author 王银龙
     * @param int $esId 组织ID
     * @return array 各组织人数数组
     */
    public function getEsNum($esId)
    {
        $clsUser = new \OA\ClsUser();
        $this->esList = array();
        $esInfo = $this->getEsInfo(array('where' => "es_id = {$esId}"));
        $sonList = $this->getSonEsList($esId);
        $sonList = array_column($this->esList, 'es_id');
        $esIdStr = implode(',', array_merge($sonList, array($esId)));
        $where = '';
        /*if (4 == $esInfo['msg'][0]['es_level']) {
            $where = "u_es_id = {$esId}";
        } else {*/
        $where = "u_es_id in ({$esIdStr})";
        /*}*/

        $esNumInfo = $clsUser->selectEx(
            array(
                'col' => 'count(*) num',
                'where' => $where,
            )
        );
        $esNumInfo = $esNumInfo['msg'][0];
        return array('ack' => 1, 'msg' => $esNumInfo['num']);
    }

    /**
     * 获取组织上级列表
     * @author 王银龙
     * @param int $supId 直属上级ID组织ID
     * @return array 组织上级列表
     */
    public function getEsSupList($supId)
    {
        $supInfo = $this->getEsInfo(
            array(
                "es_id = {$supId}"
            )
        );
        array_unshift($this->esSupList, $supInfo['msg'][0]);
        if ($supInfo['msg'][0]['es_sup_id']) {
            $this->getEsSupList($supInfo['msg'][0]['es_sup_id']);
        }
        return array('ack' => 1, 'msg' => $this->esSupList);
    }

    /**
     * 获取组织所在公司的CODE(编码)
     * @param $esId 组织ID
     * @author 王银龙
     * @return 结果数组
     */
    public function getEsCodeById($esId)
    {
        $this->esList = array();
        $returnMsg = array('ack' => 0);
        $esInfo = $this->getEsInfoById($esId);
        if ($esInfo) {
            if (2 == $esInfo['msg'][0]['es_level']) {
                $returnMsg['ack'] = 1;
                $returnMsg['msg'] = $esInfo['msg'][0]['es_code'];
            } else {
                $supEsList = $this->getSupEsList($esId);
                foreach ($supEsList['msg'] as $supEsInfo) {
                    if (2 == $supEsInfo['es_level']) {
                        $returnMsg['msg'] = $supEsInfo['es_code'];
                        $returnMsg['ack'] = 1;
                        break;
                    }
                }
                if (!$returnMsg['msg']) {
                    $returnMsg['msg'] = '请选择公司及以下组织!';
                }
            }
        } else {
            $returnMsg['msg'] = "组织信息不存在!";
        }
        return $returnMsg;
    }

    /**
     * 通过组织名称获取组织ID
     * @author 王银龙
     * @param $esName 组织ID
     * @return array 结果数组
     */
    public function getEsIdByName($esName)
    {
        $returnMsg = array('ack' => 1);
        $esInfo = $this->getEsInfoByName($esName);
        $returnMsg['msg'] = $esInfo['msg'][0]['es_id'];
        return $returnMsg;
    }


    /**
     * 通过输入组织字符串(上下级间用“/”隔开)获取组织ID
     * @author          王银龙
     * @param $esStr    组织字符串
     * @return array    返回数组
     */
    public function getEsIdByEsStr($esStr)
    {
        $returnMsg = array();
        $esNameArr = array_remove_empty(explode('/', $esStr));
        $parentId = 0;
        $esId = 0;
        $esLevel = 0;
        foreach ($esNameArr as $key => $esName) {
            $where = array();
            $where[] = "es_name = '{$esName}'";
            if ($parentId) {
                $where[] = "es_sup_id = {$parentId}";
            }
            $esInfo = $this->getEsInfo($where);
            if ($esInfo['msg']) {
                $parentId = $esInfo['msg'][0]['es_id'];
                $esId = $esInfo['msg'][0]['es_id'];
                $esLevel = $esInfo['msg'][0]['es_level'];
            } else {
                $esId = 0;
                break;
            }
        }
        if ($esId) {
            $returnMsg['ack'] = 1;
            $returnMsg['msg'] = $esId;
            $returnMsg['esLevel'] = $esLevel;
        } else {
            $returnMsg['ack'] = 0;
            $returnMsg['msg'] = '找不到组织!';
        }
        return $returnMsg;
    }

    /**
     * 通过组织名称获取组织所在公司CODE(编码)
     * @param $esName
     * @return 结果数组
     */
    public function getEsCodeByName($esName)
    {
        $esIdInfo = $this->getEsIdByName($esName);
        return $this->getEsCodeById($esIdInfo['msg']);
    }

    /**
     * 设置日志
     * @param array $log_content 日志内容
     * @return array 结果
     */
    public function setLog(array $log_content)
    {
        $clsLog = new ClsLog('oa');
        $clsLog->setCollection('log_enterprise_structure');
        $flag = $clsLog->addLog($log_content);
        return $flag;
    }
}
