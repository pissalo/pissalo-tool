<?php
namespace API;

class ClsPutPermission extends ApiTpl
{
    /*
     * API文档说明
     * @return array 结果
     */
    public function baseGetInfo()
    {
        $txt = <<<string
            本API是用来第三方系统推送个自的权限item到OA系统<br>
            参数如下:<br>
            parent_id:上级ID //必填 如果没有则为0<br>
            name:名称 //必填<br>
            define_name:define值 //必填<br>
            system_id://查询OA子系统配置得知<br>
            source://分为system和push<br>
            type://1是操作 2是查看 Notice操作是没有下级的<br>
            third_system_id://第三方系统的permission item id<br>
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

        $dbInfo = array();
        $dbInfo['up_add_time'] = time();
        $dbInfo['up_update_time'] = time();
        $dbInfo['up_approval_status'] = 1;
        $dbInfo['up_add_user_id'] = 0;
        $dbInfo['zt_id'] = 0;
        $dbInfo['up_parent_id'] = $data['parent_id'];
        $dbInfo['up_name'] = $data['name'];
        $dbInfo['up_define_name'] = $data['define_name'];
        $dbInfo['up_system_id'] = $data['system_id'];
        $dbInfo['up_source'] = $data['source'];
        $dbInfo['up_type'] = $data['type'];
        $dbInfo['up_third_system_id'] = $data['third_system_id'];

        $clsPermissions = new \OA\ClsPermissions();

        //判断是不是存在，不存在就添加
        $has_info = $clsPermissions->selectOneEx(array(
            'col' => 'up_id',
            'where' => "up_define_name='{$dbInfo['up_define_name']}' and up_system_id{$dbInfo['up_system_id']}"
        ));
        $result = $clsPermissions->add($dbInfo);
        return $result;
    }
}
