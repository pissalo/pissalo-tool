<?php
/**
 * abstract:角色类
 * Created by PhpStorm.
 * User: 王银龙
 * Date:2018年12月20日
 * Time:14:39:18
 */
namespace OA;

class ClsRole extends ClsData
{
    /**
     * cls_role constructor.
     */
    public function __construct()
    {
        parent ::__construct('oa_role');
    }
    
    /**
     * 添加角色
     * @author 王银龙
     * @param array $role_info 角色信息数组
     * @return array 执行结果数组
     */
    public function addRole(array $role_info)
    {
        global $adminZtId;
        global $adminU;
        //检测角色名是否存在
        $has_role_name = $this -> getRoleInfo(
            array (
                'where' => "r_name = '{$role_info['r_name']}'"
            )
        );
        if ($has_role_name[ 'msg' ]) {
            return array ( 'ack' => 0 , 'msg' => '添加失败，该角色名已被使用!' );
        }
        $flag = $this -> insertEx($role_info, true);
        if ($flag[ 'ack' ]) {
            $flag[ 'msg' ] = '添加成功！';
            //日志
            $this -> setLog(
                array (
                    'lr_user' => $adminU ,
                    'lr_add_time' => time() ,
                    'lr_option' => '添加角色' ,
                    'lr_zt_id' => $adminZtId ,
                    'lr_role_id' => (string)$flag[ 'insert_id' ] ,
                )
            );
        } else {
            $flag[ 'msg' ] = '添加失败!';
        }
        return $flag;
    }
    
    /**
     * 获取角色信息
     * @author 王银龙
     * @param array $param 查询条件数组
     * @return array 查询结果数组
     */
    public function getRoleInfo($param)
    {
        return $this -> selectEx($param);
    }
    
    /**
     * 通过角色ID获取角色信息
     * @author 王银龙
     * @param int/string $role_id  角色ID
     * @return array 角色信息数组
     */
    public function getRoleInfoById($role_id)
    {
        return $this -> getRoleInfo(array ( 'where' => "r_id in ({$role_id})" ));
    }
    
    /**
     * 通过角色ID获取角色名称
     * @param int/string $role_id 角色ID
     * @return array 查询结果数组
     */
    public function getRoleNameById($role_id)
    {
        return $this -> getRoleInfo(
            array (
                'col' => 'GROUP_CONCAT(r_name) name' ,
                'where' => "r_id in ($role_id)"
            )
        );
    }
    
    /**
     * 修改角色
     * @author 王银龙
     * @param array $role_info 角色信息数组
     * @param int $role_id 角色ID
     * @return array 执行结果数组
     */
    public function editRole(array $role_info, $role_id)
    {
        //获取更新前角色信息
        $old_role_info = $this -> getRoleInfoById($role_id);
        $flag = $this -> updateOne($role_info, "r_id = {$role_id}");
        if ($flag[ 'ack' ]) {
            $flag[ 'msg' ] = '修改成功！';
            //添加日志
            $this -> setRoleLog($old_role_info[ 'msg' ][ 0 ], $role_info);
        } else {
            $flag[ 'msg' ] = '修改失败!';
        }
        return $flag;
    }
    
    /**
     * 删除角色
     * @author 王银龙
     * @param int $role_id 角色ID
     * @return array 执行结果
     */
    public function deleteRole($role_id)
    {
        global $adminU;
        global $adminZtId;
        $option_msg = '删除了: ';
        $cls_ur = new ClsUserRole();
        //检查该角色下是否还有用户，如果有禁止删除。
        $user_list = $cls_ur -> getUrInfo(
            array (
                'where' => "ur_role_id = {$role_id}"
            )
        );
        if ($user_list[ 'msg' ]) {
            $return_msg[ 'ack' ] = 0;
            $return_msg[ 'msg' ] = '删除失败，该角色名下还有用户！';
            return $return_msg;
        }
        //获取该角色信息
        $role_info = $this -> getRoleInfo(
            array (
                'col' => 'r_name,es_name' ,
                'where' => "r_id = {$role_id}" ,
                'join' => 'left join oa_enterprise_structure on r_belong_department = es_id' ,
            )
        );
        $flag = $this -> deleteEx("r_id = {$role_id}", 1);
        if ($flag[ 'ack' ]) {
            $return_msg[ 'ack' ] = 1;
            $return_msg[ 'msg' ] = '删除成功!';
            //日志
            $option_msg .= $role_info[ 'msg' ][ 0 ][ 'es_name' ] . '-' .
                $role_info[ 'msg' ][ 0 ][ 'r_name' ];
            $this -> setLog(
                array (
                    'lr_user' => $adminU ,
                    'lr_add_time' => time() ,
                    'lr_option' => $option_msg ,
                    'lr_zt_id' => $adminZtId ,
                    'lr_role_id' => $role_id ,
                    'lr_option_type' => 2 ,
                )
            );
        } else {
            $return_msg[ 'ack' ] = 0;
            $return_msg[ 'msg' ] = $flag[ 'msg' ];
        }
        return $return_msg;
    }
    
    /**
     * 设置更新角色日志
     * @author 王银龙
     * @param array $old_role_info 更新前信息数组
     * @param array $new_role_info 更新后信息数组
     * @return array 执行结果
     */
    private function setRoleLog(array $old_role_info, array $new_role_info)
    {
        global $adminU;
        $option_msg = ''; //操作内容字符串
        //名称
        if ($old_role_info[ 'r_name' ] != $new_role_info[ 'r_name' ]) {
            $option_msg .= "角色名称由：{$old_role_info[ 'r_name' ]}，改为：{$new_role_info[ 'r_name' ]}；";
        }
        //所属部门
        if ($old_role_info[ 'r_belong_department' ] != $new_role_info[ 'r_belong_department' ]) {
            $cls_es = new \OA\ClsEnterpriseStructure();
            $old_bd_name = $cls_es -> getEsInfoById($old_role_info[ 'r_belong_department' ]);
            $new_bd_name = $cls_es -> getEsInfoById($new_role_info[ 'r_belong_department' ]);
            $option_msg .= "所属部门由：{$old_bd_name[ 'msg' ][ 0 ][ 'es_name' ]}，改为：{$new_bd_name[ 'msg' ][ 0 ][ 'es_name' ]}；";
        }
        //应用范围
        if ($old_role_info[ 'r_use_range' ] != $new_role_info[ 'r_use_range' ]) {
            $cls_es = new \OA\ClsEnterpriseStructure();
            $old_range = $cls_es -> getEsNameById($old_role_info[ 'r_use_range' ]);
            $new_range = $cls_es -> getEsNameById($new_role_info[ 'r_use_range' ]);
            $option_msg .= "应用范围由：{$old_range[ 'msg' ][ 0 ][ 'name' ]}，改为：{$new_range[ 'msg' ][ 0 ][ 'name' ]}；";
        }
        //职责描述
        if ($old_role_info[ 'r_note' ] != $new_role_info[ 'r_note' ]) {
            $option_msg .= "职责描述由：{$old_role_info[ 'r_note' ]}，改为：{$new_role_info[ 'r_note' ]}；";
        }
        if ($option_msg) {
            $flag = $this -> setLog(
                array (
                    'lr_user' => $adminU ,
                    'lr_add_time' => time() ,
                    'lr_option' => $option_msg ,
                    'lr_zt_id' => $old_role_info[ 'zt_id' ] ,
                    'lr_role_id' => $old_role_info[ 'r_id' ] ,
                )
            );
        }
        return $flag;
    }
    
    /**
     * 获取更新角色信息日志
     * @author 王银龙
     * @param $role_id 角色ID
     * @param int $page 页数
     * @param int $show_num 每页显示数
     * @param int $log_type 日志类型(2：删除记录)
     * @return array 日志数组
     */
    public function getRoleLog($role_id, $page, $show_num, $log_type)
    {
        global $adminZtId;
        $return_msg = array ( 'ack' => 1 );
        $clsLog = new ClsLog('oa');
        $clsLog -> setCollection('log_role');
        $where[ 'lr_zt_id' ] = $adminZtId;
        if ($role_id) {
            $where[ 'lr_role_id' ] = $role_id;
        }
        if ($log_type) {
            $where[ 'lr_option_type' ] = intval($log_type);
        }
        $log_list = $clsLog -> getList(intval($page), intval($show_num), $where);
        $new_log_list = array ();
        foreach ($log_list[ 'msg' ] as $log_key => $log_info) {
            $new_log_list[ $log_key ][ 'user' ] = $log_info[ 'lr_user' ];
            $new_log_list[ $log_key ][ 'add_time' ] = $log_info[ 'lr_add_time' ];
            $new_log_list[ $log_key ][ 'option' ] = $log_info[ 'lr_option' ];
        }
        $return_msg[ 'msg' ] = $new_log_list;
        $return_msg[ 'count' ] = $clsLog -> getNum($where);
        return $return_msg;
    }
    
    /**
     * 设置日志
     * @param array $log_content 日志内容
     * @return array 结果
     */
    public function setLog(array $log_content)
    {
        $clsLog = new ClsLog('oa');
        $clsLog -> setCollection('log_role');
        $flag = $clsLog -> addLog($log_content);
        return $flag;
    }
}
