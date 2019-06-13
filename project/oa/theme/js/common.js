//用ajax提交数据到指定的url
//save_btn_name:触发提交的按钮id
//frm_id:表单的id
//url:接收处理数据的url
//is_refresh:是否刷新当前页
function ajax_data( save_btn_name, frm_id, url, is_refresh )
{
    //console.log(data);
    $( "#" + save_btn_name ).on( "click", function () {
        ajax_post( $( "#" + frm_id ).serialize(), url, is_refresh );
    } );
}

function ajax_post( data, url, is_refresh )
{
    $.ajax( {
        url: url,
        type: "post",
        //data: $("#"+frm_id).serialize(),
        data: data,
        dataType: "json",
        success: function ( result ) {
            $( ':focus' ).blur();
            if ( result.ack == 1 )
            {
                if ( typeof result.msg == "undefined" )
                {
                    result.msg = '成功';
                }
                layer.alert( result.msg, function () {
                    window.location.reload();
                } )
            } else
            {
                if ( typeof result.msg == "undefined" )
                {
                    result.msg = '失败';
                }
                layer.alert( result.msg, 3 );
            }
        },
        error: function () {

        }
    } )

}

/**
 * 初始化日志表格
 * @param where
 * @param table_id HTML表格ID
 */
function log_table( where )
{
    var table = layui.table;
    layui.use( [ 'table', 'element' ], function () {
        var loading = layer.msg( '页面加载中，请稍后...' );
        tableobj = table.render( {
            elem: '#log_table'
            , url: "/c.php?m=Staff&f=getLog"
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
                {
                    field: 'user',
                    title: '操作人',
                    width: 120,
                    templet: '<div><span title="{{d.user}}">{{d.user}}</span></div>'
                }
                , {
                    field: 'option',
                    title: '操作内容',
                    templet: '<div><span title="{{d.option}}">{{d.option}}</span></div>'
                }
                , {
                    field: 'add_time',
                    title: '操作时间',
                    width: 180,
                    templet: '<div><span title="{{d.add_time}}">{{d.add_time}}</span></div>'
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
 * 查看日志
 */
function show_log( id, table )
{
    $.ajax( {
        url: 'log_iframe.php',
        type: 'post',
        data: { id: id, log_table: table },
        success: function ( res ) {
            $( ':focus' ).blur();
            layer.open( {
                title: '日志',
                type: 1,
                area: [ '80%', '80%' ], //宽高
                content: res
            } )
        }
    } )
}

function RecallPermission()
{
    ajax_post( {}, '/c.php?m=System&f=recallSystemPermission' );
}
