<?php
namespace API;

class ClsGetUserInfo extends ApiTpl
{
    /*
     * API文档说明
     * @return array 结果
     */
    public function baseGetInfo()
    {
        $txt = <<<string
            本API是用来获取用户信息<br>
            参数如下:<br>
            user_name:用户名 //必填<br>
            ticket:登陆返回的ticket //必填<br>
            system_id://查询OA子系统配置得知<br>
            zt_id://帐套id,比如是泽汇是1<br>
string;
        return array('ack' => 1, 'author' => '黄焕军', 'msg' => $txt);
    }

    /*
     * 处理结果
     * @return array 结果
     */
    public function baseOption()
    {
        $data = parent::baseGetData();
        $data = $data['msg'];

        $clsUser = new \OA\ClsUser();
        $userInfo = $clsUser->selectOneEx(array(
            'col' => 'u_id,u_username,u_password',
            'where' => "u_ticket='{$data['ticket']}' and u_username='{$data['user_name']}'"
        ));
        if ($userInfo) {
            $userInfo['ack'] = 1;
        } else {
            $userInfo['ack'] = 0;
        }
        $clsRp = new \OA\ClsRolePermission();
        $systemId = $data['system_id'];
        $perTmpArr = $clsRp->getUserPerByUid($userInfo['msg']['u_id'], $systemId);

        $oaOptionPerArr = $perTmpArr['msg']['option_per_arr'];
        $oaReadPerArr = $perTmpArr['msg']['read_per_arr'];
        $userInfo['msg']['option'] = $oaOptionPerArr;
        $userInfo['msg']['read'] = $oaReadPerArr;
        return $userInfo;
    }
}
