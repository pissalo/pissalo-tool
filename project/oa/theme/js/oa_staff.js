/**
 * staff模块JS
 * 2018年12月30日
 */
var table = layui.table;
var formSelects = layui.formSelects;
/**
 * 输入时间控件
 * user_list.php
 */
layui.use( 'laydate', function () {
    var laydate = layui.laydate;
    //执行一个laydate实例
    laydate.render( {
        elem: '#entry_time' //指定元素
    } );
} );

/**
 * 初始化table
 * user_list.php
 */
function tableInitUserList( where )
{
    var read_per = $( '#read_per' ).val();
    if ( read_per )
    {
        if ( where )
        {
            where.read_per = read_per;
        } else
        {
            where = { read_per: read_per }
        }
    }

    layui.use( [ 'table', 'element' ], function () {
        var loading = layer.msg( '页面加载中，请稍后...' );
        tableobj = table.render( {
            elem: '#allocation_table'
            , url: "/c.php?m=Staff&f=showUserList"
            , method: 'post'
            , request: {
                pageName: 'page' //页码的参数名称，默认：page
                , limitName: 'limit' //每页数据量的参数名，默认：limit
            }
            , response: {
                statusName: 'code' //数据状态的字段名称，默认：code
                , statusCode: 'OK' //成功的状态码，默认：0
                , msgName: 'desc' //状态信息的字段名称，默认：msg
                , countName: 'total' //数据总数的字段名称，默认：count
                , dataName: 'item' //数据列表的字段名称，默认：data
                , pageName: 'page' //数据分页
            }
            , page: true //开启分页
            , limits: [ 10, 20, 50, 100 ]
            //field
            , cols: [ [ //表头
                { type: 'checkbox' }
                , {
                    field: 'u_id',
                    title: '序号',
                    width: 60,
                    templet: '<div><span title="{{d.u_id}}">{{d.u_id}}</span></div>'
                }
                , {
                    field: 'u_work_numb',
                    title: '工号',
                    width: 120,
                    templet: '<div><span title="{{d.u_work_numb}}">{{d.u_work_numb}}</span></div>'
                }
                , {
                    field: 'u_username',
                    title: '用户名',
                    width: 120,
                    templet: '<div><span title="{{d.u_username}}">{{d.u_username}}</span></div>'
                }
                , {
                    field: 'u_position',
                    title: '职位',
                    width: 80,
                    templet: '<div><span title="{{d.u_position}}">{{d.u_position}}</span></div>'
                }, {
                    field: 'organization',
                    title: '组织',
                    width: 350,
                    templet: '<div><span title="{{d.organization}}">{{d.organization}}</span></div>'
                }
                , {
                    field: 'user_status',
                    title: '状态',
                    width: 100,
                    templet: '<div><span title="{{d.user_status}}">{{d.user_status}}</span></div>'
                }
                , {
                    field: 'u_name',
                    title: '姓名',
                    width: 100,
                    templet: '<div><span title="{{d.u_name}}">{{d.u_name}}</span></div>'
                }
                , {
                    field: 'entry_time',
                    title: '入职时间',
                    width: 110,
                    templet: '<div><span title="{{d.entry_time}}">{{d.entry_time}}</span></div>'
                }, {
                    title: '操作',
                    width: 300,
                    toolbar: '#user_edit_option'
                }
            ] ]
            //where代表异步发送data数据
            , where: where
            , done: function ( d, curr, count ) {
                layer.close( loading );
            }
        } );
    } )

    //监听工具条
    table.on( 'tool(allocation_table)', function ( obj ) {
        var data = obj.data;
        if ( obj.event === 'user_edit' )
        {
            //用户编辑事件
            add_or_edit_user( data.u_id );
        } else if ( obj.event === 'user_allot_role' )
        {
            //用户分配事件
            allot_user_role( data.u_id );
        } else if ( obj.event === 'user_look' )
        {
            //查看用户信息
            add_or_edit_user( data.u_id, 'look' );
        } else if ( obj.event === 'user_edit_log' )
        {
            //查看日志
            show_log( data.u_id, 'log_user' );
        } else if( obj.event == 'user_change_password' )
        {
            //修改密码
            changeUserPassword(data.u_id);
        }
    } );
}

/**
 * 显示修改用户密码页面
 * @param userId
 */
function changeUserPassword(userId) {
    $.ajax({
        url: 'user_change_password_iframe.php',
        type: 'post',
        data: {userId: userId},
        success: function (res) {
            $(':focus').blur();
            layer.open({
                title: '修改密码',
                type: 1,
                area: ['30%', '40%'], //宽高
                content: res
            });
        }
    })
}

$('.changeUserMain').on('click','.changePass',function () {
    var userId = $('#changeUserId').val();
    var userPassword = $('#userPassword').val();
    ajax_post({userId: userId,userPassword:userPassword}, '/c.php?m=Staff&f=changeUserPassword', 2);
})

/**
 * 添加修改用户
 * user_list.php
 * @param u_id 用户ID
 * @param type  添加或者修改，默认修改
 */
function add_or_edit_user( u_id, type )
{
    var title_name = '编辑用户';
    if ( 'add' == type )
    {
        title_name = '添加用户';
    } else if ( 'look' == type )
    {
        title_name = '查看用户';
    } else
    {
        type = 'edit';
    }
    u_id = u_id ? u_id : 0;
    $.ajax( {
        url: 'user_edit_iframe.php',
        type: 'post',
        data: { type: type, u_id: u_id },
        success: function ( res ) {
            $( ':focus' ).blur();
            layer.open( {
                title: title_name,
                type: 1,
                area: [ '80%', '80%' ], //宽高
                content: res
            } );
        }
    } )
}

/**
 * 给用户分配角色
 * user_list.php
 * @param u_id 用户ID
 */
function allot_user_role( u_id )
{
    $.ajax( {
        url: 'user_allot_role_iframe.php',
        type: 'post',
        data: { u_id: u_id },
        success: function ( res ) {
            $( ':focus' ).blur();
            layer.open( {
                title: '分配角色',
                type: 1,
                area: [ '80%', '80%' ], //宽高
                content: res
            } );
        }
    } )
}

/**
 * 角色列表查询
 * role_list.php
 */
function RoleListSearch() {
    var where = {};
    var role_name = $( '#role_name' ).val() ? $( '#role_name' ).val() : 0;
    var department_id = $( '#department_id' ).val() ? $( '#department_id' ).val() : 0;
    var qx_wfp = $("#qx_wfp").is(':checked');
    var qx_yfp = $("#qx_yfp").is(':checked');
    where = {
        roleName: role_name,
        departmentId: department_id,
        qxWfp: qx_wfp,
        qxYfp: qx_yfp
    }
    tableInitRoleList( where );
}

/**
 * 用户列表查询
 * user_list.php
 */
function UserListSearch() {
    var where = {};
    var u_username = $( '#username' ).val() ? $( '#username' ).val() : 0;
    var department_id = $( '#department_id' ).val() ? $( '#department_id' ).val() : 0;
    var u_status = $( '#status' ).val() ? $( '#status' ).val() : 0;
    var group_id = $( '#u_group' ).val() ? $( '#u_group' ).val() : 0;
    var entry_time = $( '#entry_time' ).val() ? $( '#entry_time' ).val() : 0;
    var organization = layui.formSelects.value('companyOrganization', 'val');
    where = {
        u_username: u_username,
        department_id: department_id,
        u_status: u_status,
        group_id: group_id,
        entry_time: entry_time,
        organization:organization
    }
    tableInitUserList( where );
}

/**
 * 移除用户角色
 * user_allot_role_iframe.php
 */
$( '.staff-name-main' ).on( 'click', " .delete_role", function () {
    $( this ).parent( 'li' ).remove();
} )



/**
 * 初始化角色列表table
 * role_list.php
 * @param where 查询条件
 */
function tableInitRoleList( where )
{
    //where = {u_username:'王银龙'}
    layui.use( [ 'table', 'element' ], function () {
        var loading = layer.msg( '页面加载中，请稍后...' );
        tableobj = table.render( {
            elem: '#role_list'
            , url: "/c.php?m=Staff&f=getRoleList"
            , method: 'post'
            , request: {
                pageName: 'page' //页码的参数名称，默认：page
                , limitName: 'limit' //每页数据量的参数名，默认：limit
            }
            , response: {
                statusName: 'code' //数据状态的字段名称，默认：code
                , statusCode: 'OK' //成功的状态码，默认：0
                , msgName: 'desc' //状态信息的字段名称，默认：msg
                , countName: 'total' //数据总数的字段名称，默认：count
                , dataName: 'item' //数据列表的字段名称，默认：data
                , pageName: 'page' //数据分页
            }
            , page: true //开启分页
            , limits: [ 10, 20, 50, 100 ]
            //field
            , cols: [ [ //表头
                { type: 'checkbox', width: 40 }
                , {
                    field: 'r_name',
                    title: '角色名',
                    width: 150,
                    templet: '<div><span title="{{d.r_name}}">{{d.r_name}}</span></div>'
                }
                , {
                    field: 'es_name',
                    title: '所属部门',
                    width: 200,
                    templet: '<div><span title="{{d.es_name}}">{{d.es_name}}</span></div>'

                }
                , {
                    field: 'r_note',
                    title: '角色职责',
                    minWidth: 200,
                    templet: '<div><span title="{{d.r_note}}">{{d.r_note}}</span></div>'
                },
                {
                    title: '操作',
                    fixed: 'right',
                    width: 360,
                    align: 'center',
                    toolbar: '#barDemo'
                }
            ] ]
            //where代表异步发送data数据
            , where: where
            , done: function ( d, curr, count ) {
                layer.close( loading );
            }
        } );

        //监听工具条
        table.on( 'tool(role_list)', function ( obj ) {
            var data = obj.data;
            if ( obj.event === 'delete_role' )
            {
                if ( confirm( '是否确定要删除该角色？' ) )
                {
                    delete_role( data.r_id );
                }
            } else if ( obj.event === 'allot' )
            {
                show_role_allot_page( data.r_id, "'" + data.r_use_range + "'" );
            } else if ( obj.event === 'edit' )
            {
                show_add_role_page( 'edit', data.r_id );
            } else if ( obj.event === 'allot_permission' )
            {
                window.open( '../tools/permission.php?system_id=2&role_id=' + data.r_id );
            } else if ( obj.event === 'role_log' )
            {
                show_log( data.r_id, 'log_role' );
            }
        } );
    } )
}

/**
 * 显示角色添加、修改页面
 * role_list.php
 * @param type  操作类型
 * @param role_id   角色ID
 */
function show_add_role_page( type, role_id )
{
	var title = '添加角色';
	if( 'edit' == type ) 
	{
		title = '编辑角色';
	}
    role_id = role_id ? role_id : 0;
    $.ajax( {
        url: 'role_edit_iframe.php',
        type: 'post',
        data: { type: type, role_id: role_id },
        success: function ( res ) {
            $( ':focus' ).blur();
            layer.open( {
                title: title,
                type: 1,
                area: [ '40%', '80%' ], //宽高
                content: res
            } );
        }
    } )
}

/**
 * 显示分配页面
 * role_list.php
 * @param role_id 角色ID
 * @param role_use_range 角色应用范围
 */
function show_role_allot_page( role_id, role_use_range )
{
    var index = $.ajax( {
        url: 'role_allot_iframe.php',
        type: 'post',
        data: { role_id: role_id, role_use_range: role_use_range },
        success: function ( res ) {
            $( ':focus' ).blur();
            layer.open( {
                title: "角色分配",
                type: 1,
                area: [ '80%', '85%' ], //宽高
                content: res,
            } );
        }
    } )
}

/**
 * 删除角色
 * role_list.php
 * @param role_id 角色ID
 */
function delete_role( role_id )
{
    ajax_post( { role_id: role_id }, '/c.php?m=Staff&f=deleteRole' );
}

/**
 * 显示组织结构操作日志页面
 * zzjg.php
 */
$( '#frameworkMain' ).on( 'click', '.zzjg_log_btn', function () {
    show_log( 0, 'log_enterprise_structure' );
} )
/**
 * 删除组织结构
 * zzjg.php
 */
$( '#frameworkMain' ).on( 'click', '.del-btn', function () {
    var es_id = this.name;
    layer.confirm( '是否要删除该组织？', function () {
        ajax_post( { es_id: es_id }, '/c.php?m=Staff&f=deleteEs', 2 );
    } )

} )
/**
 * 添加子组织结构
 * zzjg.php
 */
$( "#frameworkMain" ).on( "click", ".add-btn", function () {
    var es_info = this.id;
    var tmp_arr = es_info.split( '_' );
    var data = {
        es_sup_id: tmp_arr[ 1 ],
        es_level: tmp_arr[ 3 ],
        type: 'add'
    }
    $.ajax( {
        url: 'zzjg_edit_iframe.php',
        type: 'post',
        data: data,
        success: function ( res ) {
            $( ':focus' ).blur();
            layer.open( {
                title: "新增",
                type: 1,
                area: [ '420px', '540px' ], //宽高
                content: res
            } );
        }
    } )
} )
/**
 * 编辑组织结构
 * zzjg.php
 */
$( "#frameworkMain" ).on( "click", ".edi-btn", function () {
    var es_info = this.id;
    var tmp_arr = es_info.split( '_' );
    var data = {
        es_id: tmp_arr[ 1 ],
        type: 'edit'
    }
    $.ajax( {
        url: 'zzjg_edit_iframe.php',
        type: 'post',
        data: data,
        success: function ( res ) {
            $( ':focus' ).blur();
            layer.open( {
                title: "编辑",
                type: 1,
                area: [ '420px', '540px' ], //宽高
                content: res
            } );
        }
    } )
} )

/**
 * 展开、收起组织结构
 * zzjg.php
 */
$( "#frameworkMain" ).on( "click", ".include-btn", function () {
    var include = $( this ).parents( ".same-ul" ).siblings( ".include" );
    if ( include.height() <= 0 )
    {
        include.addClass( "heightActive" );
        $( this ).text( "-" )
    } else
    {
        include.removeClass( "heightActive" );
        $( this ).text( "+" )
    }
} );

/**
 * 删除角色日志
 * role_list.php
 */
function show_del_role_log()
{
    show_log( 0, 'delete_role_log' );
}

/**
 * 用户分配角色,添加角色
 * user_allot_role_iframe.php
 */
function show_role_to_user( id, name )
{
    var need_add = 1;   //是否主要追加
    var html = '<li><i class="layui-icon layui-icon-close delete_role"></i><span name="' + id + '">' + name + '</span></li>';
    $( '.delete_role' ).next( 'span' ).each( function ( index, item ) {
        if ( name == $( this ).html() )
        {
            need_add = 0;
        }
    } )
    if ( need_add )
    {
        $( '#user_allot_role_ul' ).append( html );
    }

}

/**
 * 用户分配角色，移除角色
 * user_allot_role_iframe.php
 */
$( '.staff-pop-main' ).on( 'click', ".delete_role", function () {
    $( this ).parent( 'li' ).remove();
} )


/**
 * 角色分配给用户，移除用户
 * role_allot_iframe.php
 */
$( '.allocation-main' ).on( 'click', " .delete_role", function () {
    $( this ).parent( 'li' ).remove();
} )


/**
 * 角色分配用户，搜索
 * role_allot_iframe.php
 * @param type
 */
function search_user_info( type )
{
    var name = $( '#' + type ).val();
    var where = { role_id: role_id, role_use_range: role_use_range };
    switch ( type )
    {
        case 'u_username':
            where.u_username = name;
            break;
        case 'u_department':
            where.u_department = name;
            break;
        case 'u_group':
            where.u_group = name;
            break;
    }
    role_allot_table_init( where );
}

/**
 * 角色分配用户，初始化用户信息表
 * role_allot_iframe.php
 */
function role_allot_table_init( where )
{
    //where = {u_username:'王银龙'}
    layui.use( [ 'table', 'element' ], function () {
        var loading = layer.msg( '页面加载中，请稍后...' );
        tableobj = table.render( {
            elem: '#allocation_table'
            , url: "/c.php?m=Staff&f=showUserList"
            , method: 'post'
            , skin: 'line',
            request: {
                pageName: 'page' //页码的参数名称，默认：page
                , limitName: 'limit' //每页数据量的参数名，默认：limit
            }
            , response: {
                statusName: 'code' //数据状态的字段名称，默认：code
                , statusCode: 'OK' //成功的状态码，默认：0
                , msgName: 'desc' //状态信息的字段名称，默认：msg
                , countName: 'total' //数据总数的字段名称，默认：count
                , dataName: 'item' //数据列表的字段名称，默认：data
                , pageName: 'page' //数据分页
            }
            , page: true //开启分页
            , limits: [ 10, 20, 50, 100 ]
            //field
            , cols: [ [ //表头
                { type: 'checkbox', fixed: 'left' }
                , {
                    field: 'u_name',
                    title: '姓名',
                    width: 80
                }
                , {
                    field: 'u_work_numb',
                    title: '工号',
                    width: 80
                }
                , {
                    field: 'u_username',
                    title: '<i class="layui-icon layui-icon-search allocation-search-btn"></i> 用户名 <div class="allocation-search-box"><i class="triangle-up"></i><input type="text" id="u_username"><i class="layui-icon layui-icon-search small-search" onclick="search_user_info(\'u_username\')"></i></div>',
                    width: 80
                }
                , {
                    field: 'organization',
                    title: '组织',
                    width: 300,
                    templet: '<div><span title="{{d.organization}}">{{d.organization}}</span></div>'
                }

            ] ]
            //where代表异步发送data数据
            , where: where
            , done: function ( d, curr, count ) {
                layer.close( loading );
            }
        } );
    } )
}

/**
 * 监听复选框
 * role_allot_iframe.php
 */
table.on( 'checkbox(role_allot_table_filter)', function ( obj ) {
    if ( obj.type != 'all' )
    {
        if ( obj.checked == false )
        {
            $( '.role_name' ).each( function () {
                if ( $( this ).html() == obj.data.u_username )
                {
                    $( this ).parent( 'li' ).remove();
                }
            } )
        } else
        {
            var need_add = 1;
            $( '.role_name' ).each( function () {
                if ( $( this ).html() == obj.data.u_username )
                {
                    need_add = 0;
                }
            } )
            if ( need_add )
            {
                var html = '<li><span class="role_name">' + obj.data.u_username + '</span><i class="delete_role">×</i></li>';
                $( '#role_user_list' ).append( html );
            }
        }
    } else
    {
        //获取全选内容
        var checkStatus = table.checkStatus( 'allocation_table' )
            , data = checkStatus.data;
        if ( obj.checked == true )
        {
            for ( var i = 0; i < data.length; i ++ )
            {
                var need_add = 1;
                $( '.role_name' ).each( function () {
                    if ( $( this ).html() == data[ i ].u_username )
                    {
                        need_add = 0;
                    }
                } )
                if ( need_add )
                {
                    var html = '<li><span class="role_name">' + data[ i ].u_username + '</span><i class="delete_role">×</i></li>';
                    $( '#role_user_list' ).append( html );
                }
            }
        }
    }
    layui.use( 'allocation-main', function () {  //此段代码必不可少
        var form = layui.div;
        form.render();
    } );
} );

/**
 *
 */
$( '.staff-pop-main' ).on( 'click', '.user_add_role', function () {
    var id_str = '';
    var u_id = $( '#user_allot_u_id' ).val();
    $( '.delete_role' ).next( 'span' ).each( function ( index, item ) {
        id_str += $( this ).attr( 'name' ) + ',';
    } )
    ajax_post( { id_str: id_str, u_id: u_id }, '/c.php?m=Staff&f=userAllotRole' );
} )


