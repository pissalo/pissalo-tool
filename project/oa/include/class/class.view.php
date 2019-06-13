<?php
/**
 * 视图类.
 */
namespace OA;

defined('IN_DCR') or exit('No permission.');

/**
 * Class ClsView
 */
class ClsView
{

    /**
     * 获取一个搜索的按钮
     * @param $btnText 按钮文字
     * @param $clickEventName click绑定的事件名
     * @param int $permissionId 权限ID
     * @param string $idName 按键的id
     * @return array 结果
     */
    public static function getSearchButton($btnText, $clickEventName, $permissionId = 0, $idName = '')
    {
        $btnInfo = self::getButton($btnText, $clickEventName, 'layui-btn layui-btn-sm layui-btn-normal', $permissionId, $idName);
        return $btnInfo;
    }

    /**
     * 获取一个新增的按钮
     * @param $btnText 按钮文字
     * @param $clickEventName click绑定的事件名
     * @param int $permissionId 权限ID
     * @param string $idName 按键的id
     * @return array 结果
     */
    public static function getAddButton($btnText, $clickEventName, $permissionId = 0, $idName = '')
    {
        $btnInfo = self::getButton($btnText, $clickEventName, 'layui-btn layui-btn-sm', $permissionId, $idName);

        return $btnInfo;
    }

    /**
     * 获取一个按钮
     * @param $btnText 按钮文字
     * @param $clickEventName click绑定的事件名
     * @param $className 类名
     * @param int $permissionId 权限ID
     * @param string $idName 按键的id
     * @return array 结果
     */
    public static function getButton($btnText, $clickEventName, $className, $permissionId = 0, $idName = '')
    {
        $hasPermission = 1;
        if ($permissionId) {
            global $adminOptionPer;
            //p_r( $permissionId );
            //p_r( $adminOptionPer );
            if (!in_array($permissionId, $adminOptionPer)) {
                $hasPermission = 0;
            }
        }
        //var_dump( $btnText );
        //var_dump( $hasPermission );
        if (!$hasPermission) {
            $str = '';
        } else {
            $str = "<button class=\"{$className}\" onclick=\"{$clickEventName}\" type=\"button\" id='{$idName}'>{$btnText}</button>";
        }
        return array('ack' => 1, 'msg' => $str);
    }
}
