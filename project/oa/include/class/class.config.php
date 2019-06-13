<?php
/**
 * 配置类
 * @author     我不是稻草人 <junqing124@126.com>
 * @version    v1.0
 * @package OA
 * @since 1.1.4
 */
namespace OA;

defined('IN_DCR') or exit('No permission.');

class ClsConfig
{
    /**
     * 获取子系统列表
     * @param array $cs 跟cls_data->select_ex里的参数一样
     * @return array 子系统列表
     */
    public function getSystemSubList($cs = array())
    {
        $cls_data_ss = new \OA\ClsData('oa_config_system_sub');
        $list_ss = $cls_data_ss->selectEx($cs);
        return $list_ss;
    }

    /**
     * 获取子系统列表
     * @param array $cs 跟cls_data->select_ex里的参数一样
     * @return array 子系统列表
     */
    public function getCompanyList($cs = array())
    {
        $cls_data_ss = new \OA\ClsData('oa_config_company');
        $list_ss = $cls_data_ss->selectEx($cs);
        return $list_ss;
    }

    /**
     * 通过公司名称获取公司ID
     * @author 王银龙
     * @param $cc_name 公司名称
     * @return array 结果数组
     */
    public function getCompanyIdByName($cc_name)
    {
        $clsConfigCompany = new \OA\ClsData('oa_config_company');
        return $clsConfigCompany->selectOneEx(array(
            'where' => "cc_name = '{$cc_name}'"
        ));
    }

    /**
     * 通过ID获取子系统名称
     * @param int $system_id 子系统ID
     * @return array 子系统名称
     */
    public function getSystemSubNameById($system_id)
    {
        $cs['where'] = 'css_id=' . $system_id;
        $cs['col'] = 'css_name';
        $cls_data_ss = new \OA\ClsData('oa_config_system_sub');
        $info_ss = $cls_data_ss->selectOneEx($cs);
        return array( 'ack'=> 1, 'msg'=> $info_ss['msg']['css_name'] );
    }

    /**
     * 通过ID获取子系统名称
     * @param string $system_name 子系统名称
     * @return array 子系统名称
     */
    public function getSystemSubIdByName($system_name)
    {
        $cs['where'] = "css_name='{$system_name}'";
        $cs['col'] = 'css_id';
        $cls_data_ss = new \OA\ClsData('oa_config_system_sub');
        $info_ss = $cls_data_ss->selectOneEx($cs);
        return array( 'ack'=> 1, 'msg'=> $info_ss['msg']['css_id'] );
    }
}
