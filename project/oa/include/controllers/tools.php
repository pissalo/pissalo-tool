<?php
namespace Controller;

class Tools
{
    private $data;
    
    public function setData($data)
    {
        $this -> data = $data;
    }
    
    /*
     * 根据munu配置生成permission列表
     * @return array 结果
     */
    public function permissionScByMenu()
    {
        global $menu_config , $clsPermissions;
        //system_id
        $cls_config = new \OA\ClsConfig();
        $id_info = $cls_config -> getSystemSubIdByName('OA');
        $this -> permissionScByMenuInfo($menu_config, $id_info[ 'msg' ]);
        //更新define
        $clsPermissions -> updateDefineList();
        return array ( 'ack' => 1 );
    }
    
    /*
     * 生成系统的缓存
     * @return array 结果
     */
    public function updateCache()
    {
        $data = $this -> data;
        foreach ($data[ 'cache_name' ] as $cache_option => $cache_cur_name) {
            $option_arr = explode('#', $cache_cur_name);
            $className = '\OA\Cls' . $option_arr[ 0 ];
            $cls = new $className;
            $val = $cls ->{$option_arr[ 1 ]}();
        }
        return array ( 'ack' => 1 );
    }

    private function permissionScByMenuInfo($menu_config, $system_id)
    {
        global $clsPermissions;
        foreach ($menu_config as $menu_info) {
            //先判断有没有这个define值
            $has_info = $clsPermissions -> selectOneEx(array ( 'col' => 'up_id' , 'where' => "up_define_name='{$menu_info['define']}'  and up_system_id={$system_id}" ));
            //得上级ID
            $parent_id = 0;
            $define_arr = explode('_', $menu_info[ 'define' ]);
            if (count($define_arr) < 3) {
                $parent_id = 0;
            } else {
                //得出上级ID
                unset($define_arr[ count($define_arr) - 1 ]);
                $define_parent = implode('_', $define_arr);
                $parent_info = $clsPermissions -> selectOneEx(array ( 'col' => 'up_id' , 'where' => "up_define_name='{$define_parent}' and up_system_id={$system_id}" ));
                $parent_id = $parent_info[ 'msg' ][ 'up_id' ];
            }
            $parent_id = intval($parent_id);
            if (!$has_info[ 'msg' ]) {
                //添加
                $db_info = array ();
                $db_info[ 'up_add_time' ] = time();
                $db_info[ 'up_update_time' ] = time();
                $db_info[ 'up_approval_status' ] = 1;
                $db_info[ 'up_add_user_id' ] = '0';
                $db_info[ 'zt_id' ] = 0;
                $db_info[ 'up_parent_id' ] = $parent_id;
                $db_info[ 'up_name' ] = $menu_info[ 'title' ];
                $db_info[ 'up_define_name' ] = $menu_info[ 'define' ];
                $db_info[ 'up_system_id' ] = $system_id;
                $db_info[ 'up_source' ] = 'system';
                $db_info[ 'up_type' ] = 2;
                //添加
                $result = $clsPermissions -> add($db_info, 1);
            }
            //添加子的
            if ($menu_info[ 'sub' ]) {
                $this -> permissionScByMenuInfo($menu_info[ 'sub' ], $system_id);
            }
        }
    }
    
    /*
     * 添加api
     * @return array 处理结果
     */
    public function editApi()
    {
        global $adminId;
        $data = $this -> data;
        $cls_api = new \OA\ClsApi();
        $db_info = array ();
        $db_info[ 'a_add_time' ] = time();
        $db_info[ 'a_update_time' ] = time();
        $db_info[ 'a_approval_status' ] = 1;
        $db_info[ 'a_add_user_id' ] = $adminId;
        $db_info[ 'zt_id' ] = 0;
        $db_info[ 'a_name' ] = $data[ 'a_name' ];
        $db_info[ 'a_token' ] = $data[ 'a_token' ];
        $db_info[ 'a_module_name' ] = $data[ 'a_module_name' ];
        $result = $cls_api -> insertEx($db_info, 1);
        return $result;
    }
    
    /*
     * 更新指定api token
     * @return array 处理结果
     */
    public function apiUpdateToken()
    {
        $data = $this -> data;
        $cls_api = new \OA\ClsApi();
        $db_info = array ();
        $token_info = $cls_api -> getRandToken();
        $db_info[ 'a_token' ] = $token_info[ 'msg' ];
        $result = $cls_api -> updateOne($db_info, "a_id={$data['a_id']}");
        return $result;
    }
    
    /*
     * 取消指定api
     * @return array 处理结果
     */
    public function apiInvalid()
    {
        $data = $this -> data;
        $cls_api = new \OA\ClsApi();
        $db_info = array ();
        $db_info[ 'a_is_valid' ] = 0;
        $result = $cls_api -> updateOne($db_info, "a_id={$data['a_id']}");
        return $result;
    }
    
    /*
     * 权限添加或修改
     * @return array 修改结果
     */
    public function permissionEdit()
    {
        $cls_data = new \OA\ClsData('oa_user_permission');
        //p_r( $cls_data );
        global $adminId;
        $data = $this -> data;
        $db_info = get_data_form_req_data($data, 'up_');
        $db_info[ 'up_update_time' ] = time();
        $db_info[ 'up_approval_status' ] = 1;
        $db_info[ 'up_source' ] = 'system';
        $db_info[ 'up_parent_id' ] = intval($db_info[ 'up_parent_id' ]);
        if (!$db_info[ 'up_name' ]) {
            return array ( 'ack' => 0 , 'error_id' => 1000 , 'msg' => '没有权限名' );
        }
        if ($db_info[ 'up_parent_id' ] > 0) {
            //判断define对不对
            $parent_info = $cls_data -> selectOneEx(array ( 'col' => 'up_define_name' , 'where' => "up_id={$db_info[ 'up_parent_id' ]}" ));
            //p_r( $parent_info );
            $parent_define_num = count(explode('_', $parent_info[ 'msg' ][ 'up_define_name' ]));
            $cur_define_num = count(explode('_', $db_info[ 'up_define_name' ]));
            if ($cur_define_num - $parent_define_num != 1) {
                return array ( 'ack' => 0 , 'error_id' => 1001 , 'msg' => "Define不对_{$cur_define_num}_{$parent_define_num}" );
            }
        }
        //p_r( $cls_data );
        $db_info[ 'up_add_time' ] = time();
        $db_info[ 'up_add_user_id' ] = $adminId;
        $clsPermissions = new \OA\ClsPermissions();
        return $clsPermissions -> add($db_info, 1);
    }
    
    /**
     * 分配权限
     */
    public function allotPermission()
    {
        global $adminId;
        global $adminZtId;
        $cls_rp = new \OA\ClsRolePermission();
        $update_info = array ();
        foreach ($this -> data[ 'permission' ] as $per_id) {
            $per_num = 0;
            if (in_array($per_id, $this -> data[ 'read_all' ])) {
                $per_num += 1;
            }
            if (in_array($per_id, $this -> data[ 'read_sub' ])) {
                $per_num += 2;
            }
            $update_info[ $per_id ][ 'rp_option_per_id' ] = $per_id;
            $update_info[ $per_id ][ 'rp_read_per_id' ] = $per_num;
            $update_info[ $per_id ][ 'rp_role_id' ] = $this -> data[ 'role_id' ];
            $update_info[ $per_id ][ 'rp_add_time' ] = time();
            $update_info[ $per_id ][ 'rp_update_time' ] = time();
            $update_info[ $per_id ][ 'rp_approval_status' ] = 1;
            $update_info[ $per_id ][ 'rp_add_user_id' ] = $adminId;
            $update_info[ $per_id ][ 'zt_id' ] = $adminZtId;
            $update_info[ $per_id ][ 'rp_system_id' ] = $this->data['system_id'];
        }
        $flag = $cls_rp->updateRolePermission($update_info, $this->data['role_id'], $this->data['system_id']);
        return $flag;
    }
}
