<?php

namespace OA;

/**
 * abstract:审批流接口
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2019年2月20日
 * Time:10:12:15
 */
interface Approval
{
    /**
     * 审核审批
     * @param array $approvalInfo 审批信息数组
     * @param array $configInfo 审批配置数组
     * @return mixed                执行结果数组
     */
    public function checkApproval(array $approvalInfo, array $configInfo);

    /**
     * 添加审批
     * @param $approvalInfo 审批信息数组
     * @param int $otherId 其它ID
     * @return mixed        执行结果数组
     */
    public function addApproval($approvalInfo, $otherId = 0, $type = 1);

    /**
     * 完结审批
     * @param $otherId  其它ID
     * @return mixed    执行结果数组
     */
    public function endApproval($otherId);

    /**
     * 编辑审批内容
     * @param $Info     审批信息
     * @return mixed    执行结果数组
     */
    public function editApproval($Info);

    /**
     * 具体审批内容
     * @param $approvalId   审批ID
     * @return mixed        审批内如数组
     */
    public function getApprovalContent($approvalId);
}
