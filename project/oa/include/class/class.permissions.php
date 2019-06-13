<?php
/**
 * 权限条目类
 */
namespace OA;

/**
 * Class cls_permissions 权限条目管理类
 */
class ClsPermissions extends ClsData
{
    /** @var int $permissionId 权限ID */
    private $permissionId;

    /**
     * 构建用户类
     * @param string $permissionId 用户名
     * @return mixed
     */
    public function __construct($permissionId = '')
    {
        $this->permission_id = $permissionId;
        parent::__construct('@#@user_permission');
    }

    /**
     * 设置当前类的权限id
     * @param $permissionId 权限ID
     * @return 无
     */
    public function setPermissionId($permissionId)
    {
        $this->permissionId = $permissionId;
    }

    /**
     * 通过ID获取标题
     * @return array
     */
    public function getTitle()
    {
        global $permission_list;
        return array( 'ack' => 1, 'msg' => $permission_list[ $this->permission_id ][ 'name' ] );
    }

    /**
     * 得出全部的权限列表 如果有子的，则加sub_class
     * @param int system_id 系统ID
     * @param int parent_id 上级ID
     * @param int level 级别
     * @param string col 列
     * @param string where 条件
     * @return array 结果
     */
    public function getList($system_id, $parent_id = 0, $level = 1, $col = '', $where = '')
    {
        if (!$system_id) {
            return array( 'ack' => 0, 'msg' => '请指定系统' );
        }
        $where_finale = '';
        if ($where) {
            $where_finale = "{$where} and up_system_id={$system_id} and up_parent_id={$parent_id}";
        } else {
            $where_finale = "up_system_id={$system_id} and up_parent_id={$parent_id}";
        }

        $order = 'up_third_system_id asc';
        $cs = array( 'col' => $col, 'where' => $where_finale, 'order' => $order );
        $per_list = parent::selectEx($cs);

        if ($per_list[ 'ack' ]) {
            $per_list = $per_list[ 'msg' ];
            foreach ($per_list as $per_key => $per) {
                $per_sub = $this->getList($system_id, $per[ 'up_third_system_id' ], $level + 1, $col, $where);
                $per_list[ $per_key ][ 'class_level' ] = $level;

                if ($per_sub) {
                    $per_list[ $per_key ][ 'sub_class' ] = $per_sub;
                }
            }
        }

        return array( 'ack' => 1, 'msg' => $per_list );
    }

    /**
     * 输出select里的option
     * @param array $per_list get_list生成好的list
     * @param int $cur_id 当前的ID
     * @param string $option_value 判断的值
     * @return echo
     */
    public function getListSelect($per_list, $cur_id = 0, $option_value = 'up_id')
    {
        if ($per_list) {
            foreach ($per_list as $value) {
                echo '<option value="' . $value[ $option_value ] . '"';
                if ($cur_id == $value[ 'up_id' ] && $cur_id) {
                    echo 'selected="selected"';
                }
                echo '>' . str_repeat("----", $value[ 'class_level' ] - 1) . $value[ 'up_name' ] . '</option>';
                if ($value[ 'sub_class' ][ 'msg' ] && count($value[ 'sub_class' ][ 'msg' ])) {
                    $this->getListSelect($value[ 'sub_class' ][ 'msg' ], $cur_id, $option_value);
                }
            }
        } else {
            echo '<option value="0">没有权限列表</option>';
        }
    }

    /**
     * 获取权限列表
     * @param array $cs 跟cls_data->select_ex里的参数一样
     * @return array 子系统列表
     */
    public function getPermissionList($cs)
    {
        $cls_data = new \OA\ClsData('oa_user_permission');
        $list = $cls_data->selectEx($cs);
        return array( 'ack' => 1, 'msg' => $list );
    }
    
    /**
     * 通过权限ID获取权限名称（查表)
     * @author 王银龙
     * @param int/string $per_id 权限ID
     * @return array 查询结果数组
     */
    public function getPerNameById($per_id)
    {
        global $adminZtId;
        $per_sql = "SELECT
                        sup.up_name sup_name,sub.up_name sub_name,sub.up_id
                    FROM oa_user_permission sub
                    left join oa_user_permission sup on sub.up_parent_id = sup.up_id
                    where sub.up_id in ({$per_id}) and sub.zt_id = {$adminZtId}";
        return $this -> execute($per_sql);
    }
    
    /**
     * 生成缓存的define及相关信息
     * @return true
     */
    public function updateDefineList()
    {
        $cls_config = new \OA\ClsConfig();
        $oa_id_info = $cls_config->getSystemSubIdByName('OA');
        $list = parent::selectEx(array( 'where' => "up_system_id={$oa_id_info['msg']}" ));
        //$list = parent::selectEx( );
        $list = $list[ 'msg' ];

        require_once(WEB_CLASS . '/class.file.php');
        $cache_file = WEB_DR . '/share/cache/permissions.php';
        $cls_file = new ClsFile($cache_file);
        $content = "<?php\n";

        $content_detail = "\$permission_list = array(\n";

        foreach ($list as $value) {
            $content .= "define('{$value['up_define_name']}', {$value['up_id']});\n";
            $content_detail .= "    {$value['up_define_name']} => array( 'name'=> '{$value['up_name']}' ),\n";
        }
        $content_detail .= ");\n";
        $cls_file->setText($content . $content_detail);

        $r_val = $cls_file->write();
        $result = array();
        if ($r_val[ 'ack' ]) {
            $result = array( 'ack' => 1 );
        } else {
            $result = array( 'ack' => 0, 'error_id' => 1000, 'msg' => $r_val[ 'msg' ] );
        }

        return $result;
    }

    /**
     * 添加权限item
     * @param array $info 要添加的内容
     * @param boolean $is_oa 是不是OA系统，如果是OA，则要up_third_system_id=up_id
     * @return array 结果
     */
    public function add($info, $is_oa = 0)
    {
        if ($is_oa) {
            unset($info[ 'up_third_system_id' ]);
        } else {
            if (!$info[ 'up_third_system_id' ]) {
                return array( 'ack' => 0, 'error_id' => 1000, 'msg' => '没有第三方system id' );
            }
        }
        //是不是已经了这个item
        $has_info = $this->selectOneEx(array( 'col' => 'up_id', 'where' => "up_define_name='{$info['up_define_name']}' and up_system_id={$info['up_system_id']}" ));
        if ($has_info[ 'msg' ][ 'up_id' ]) {
            return array( 'ack' => 0, 'error_id' => 1001, 'msg' => '已经存在这个item' );
        }
        $this->transactionBegin();
        $update_result = array( 'ack' => 1 );
        unset($info['up_id']);
        $insert_result = $this->insertEx($info, 1);
        //echo $this->get_last_sql();
        if ($is_oa) {
            $info = array( 'up_third_system_id' => $insert_result[ 'insert_id' ] );
            $update_result = $this->updateOne($info, "up_id={$insert_result['insert_id']}");
            //echo $this->get_last_sql();
        }
        //$this->transactionRollback();
        //exit;
        $is_error = 0;
        if ($update_result[ 'ack' ] && $insert_result[ 'ack' ]) {
            $is_error = 0;
            $this->transactionCommit();
        } else {
            $is_error = 1;
            $this->transactionRollback();
        }
        //p_r( $update_result );
        //p_r( $insert_result );

        if ($is_error) {
            $result = array( 'ack' => 0, 'msg' => "更新失败-{$insert_result['msg']}", 'error_id' => 1002 );
        } else {
            $result = array( 'ack' => 1 );
        }
        return $result;
    }
}
