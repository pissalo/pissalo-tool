var formSelects = layui.formSelects;
var form = layui.form;
var table = layui.table;
var element = layui.element;
//审批配置列表监听复选框（是否启用审批）
form.on('switch(approvalIsValid)', function (data) {
    var acId = data.value;
    if (data.elem.checked) {
        ajax_post({acId: acId, value: 1}, '/c.php?m=System&f=approvalIsValid')
    } else {
        ajax_post({acId: acId, value: 0}, '/c.php?m=System&f=approvalIsValid')
    }
});

/**
 * 添加审批按钮监听
 */
$('.approval-main').on('click', '.approval-add-btn', function () {
    window.open('approval_config_edit.php', '_blank');
})

/**
 * 层级编辑确定按钮
 */
$('.approval-pop-btn').on('click', '.add', function () {
    var countersign = $("input[name='countersign']:checked").val();
    var approval = $("input[name='approval']:checked").val();
    var action = $('#action').val();
    var tip = '';
    var tips = '';
    var maxLevel = $('#maxLevel').val();
    switch (approval) {
        case '上级'://上级审核
            var supLevel = $('#userSupLevel').val();
            if( !supLevel )
            {
                layer.msg('请选择上级级别!', {icon: 2});
                return false;
            }
            tip = '<li class="splLevel" style="width: 140px"><div class="border-div">' + approval + '(' + supLevel + ') <i class="layui-icon layui-icon-close splLevelClose"></i></div><p class="showApprovalEdit" id="checkLevel_' + maxLevel + '">' + countersign + '</p><input type="hidden" value="1-' + supLevel + '-' + countersign + '" class="approvalCheckInfo"></li>';
            tips = '<div class="border-div">' + approval + '(' + supLevel + ') <i class="layui-icon layui-icon-close splLevelClose"></i></div><p class="showApprovalEdit" id="checkLevel_' + maxLevel + '">' + countersign + '</p><input type="hidden" value="1-' + supLevel + '-' + countersign + '" class="approvalCheckInfo">';
            break;
        case '指定成员'://特定人员审核
            var str = layui.formSelects.value('splCheckUser', 'valStr');
            if( !str )
            {
                layer.msg('请选择指定成员!', {icon: 2});
                return false;
            }
            var count = str.split(',').length;
            tip = '<li class="splLevel" style="width: 140px"><div class="border-div">指定成员(' + count + '人) <i class="layui-icon layui-icon-close splLevelClose"></i></div><p class="showApprovalEdit" id="checkLevel_' + maxLevel + '">' + countersign + '</p><input type="hidden" value="2-' + str + '-' + countersign + '" class="approvalCheckInfo"></li>';
            tips = '<div class="border-div">指定成员(' + count + '人) <i class="layui-icon layui-icon-close splLevelClose"></i></div><p class="showApprovalEdit" id="checkLevel_' + maxLevel + '">' + countersign + '</p><input type="hidden" value="2-' + str + '-' + countersign + '" class="approvalCheckInfo">';

            break;
        case '发起人自己'://自己审核
            tip = '<li class="splLevel" style="width: 140px"><div class="border-div">发起人自己<i class="layui-icon layui-icon-close splLevelClose"></i></div><p class="showApprovalEdit" id="checkLevel_' + maxLevel + '">申请人自己审核</p><input type="hidden" value="3" class="approvalCheckInfo"></li>';
            tips = '<div class="border-div">发起人自己<i class="layui-icon layui-icon-close splLevelClose"></i></div><p class="showApprovalEdit" id="checkLevel_' + maxLevel + '">申请人自己审核</p><input type="hidden" value="3" class="approvalCheckInfo">';
            break;
        default:
            break;
    }
    if ('edit' == action) {
        var obj = $('#checkLevel_' + maxLevel, window.parent.document).parent();
        obj.html('');
        obj.html(tips);
    } else {
        $('#spl_check_list', window.parent.document).append(tip);
    }
    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
    parent.layer.close(index);
})

/**
 * 保存审批配置
 */
form.on( 'submit(approvalConfigEdit)', function ( data ) {
    var approvalName = $('#spl_name').val();
    var useRange = layui.formSelects.value('splUseRange', 'valStr');
    //var useRange = '';
    /*$('.xm-select-this').each(function () {
        //console.log($(this).attr('lay-value'));
        if( $(this).attr('lay-value') )
        {
            useRange += $(this).attr('lay-value') + ',';
        }
    });*/
    var note = $('#spl_note').val();
    var action = $('#action').val();
    var acId = $('#acId').val() ? $('#acId').val() : 0;
    var ac_path = $('#ac_path').val();
    var ac_class = $('#ac_class').val();
    var ac_copy_to_user = layui.formSelects.value('approvalCopyTo', 'valStr');
    var checkLevel = {};
    var count = 1;
    $('.approvalCheckInfo').each(function () {
        checkLevel['check' + count] = $(this).val();
        count++;
    })
    if(!ac_copy_to_user)
    {
        layer.msg('请选择抄送人!',{icon:2})
        return false;
    }
    var checkLevelStr = JSON.stringify(checkLevel);
    var data = {
        approvalName: approvalName,
        useRange: useRange,
        note: note,
        checkLevelStr: checkLevelStr,
        action: action,
        acId: acId,
        ac_path: ac_path,
        ac_class: ac_class,
        ac_copy_to_user: ac_copy_to_user,
    };
    //console.log(data);return false;
    $.ajax({
        url: '/c.php?m=System&f=addApprovalConfig',
        type: "post",
        data: data,
        dataType: "json",
        success: function (result) {
            var returnMsg = JSON.parse(result);
            if (1 == returnMsg.ack) {
                layer.alert(returnMsg.msg, 5, function () {
                    window.open('/system/approval_config_list.php', '_self');
                })
            } else {
                layer.alert(returnMsg.msg, 3);
            }
        }
    })
})

/**
 * 删除层级
 */
$('#spl_check_list').on('click', '.splLevelClose', function () {
    $(this).parents('li').next('.no-border').remove();
    $(this).parents('li').remove();
})


/**
 * 审批流配置选择特定审批人限制
 */
formSelects.on('splUseRange', function (id, vals, val, isAdd, isDisabled) {
    var editUseRange = $('#edit_use_range').val();
    //console.log(val.value + '-' + isAdd);
    var tipIndex = layer.load(2, {time: 10 * 1000});
    $.post('/ajax/get_es_info.php', {esId: val.value, action: 'getEsSonIdArr'}, function (msg) {
        var returnMsg = JSON.parse(msg);
        var valudArr = returnMsg.msg.split(',');
        if (isAdd) {
            formSelects.value('splUseRange', valudArr, true);
        } else {
            formSelects.value('splUseRange', valudArr, false);
        }
        layer.close(tipIndex);
    })
    /*if (isAdd) {

    } else {

    }*/
    /*if ($("span[fsw='xm-select']").length >= 2) {
        $(".approval-pop-countersign-hide").show();
    } else {
        $(".approval-pop-countersign-hide").hide();
    }
    if (isAdd && val.children) {
        var treeId = $("span[name='" + val.name + "']").parents("dd").attr("tree-id");
        var dd = $(".xm-select-dl dd");
        for (var i = 0; i < dd.length; i++) {
            if (dd.eq(i).attr("tree-id")) {
                var id1 = dd.eq(i).attr("tree-id").slice(0, -4);
                var reg_str = '/^' + treeId + '/';
                if (id1 && id1.match(eval(reg_str))) {
                    var a = dd.eq(i).attr("tree-id");
                    $("dl[xid='splUseRange'] dd[tree-id='"+a+"']").addClass("xm-select-this").children(".xm-unselect").addClass("xm-select-this")
                    var b = $("dd[tree-id='" + a + "']").attr("lay-value");
                    $(".xm-select span[value='" + b + "']").remove();
                }
            }
            console.log()
        }
    } else {
        var treeId1 = $("span[name='" + val.name + "']").parents("dd").attr("tree-id");
        var dd1 = $(".xm-select-dl dd");
        for (var i = 0; i < dd1.length; i++) {
            if (dd1.eq(i).attr("tree-id")) {
                var id2 = dd1.eq(i).attr("tree-id").slice(0, -4);
            }
            if (id2 && id2.match(treeId1) && treeId1.slice(0, 3) == id2.slice(0, 3)) {
                var a1 = dd1.eq(i).attr("tree-id");
                $("dl[xid='splUseRange'] dd[tree-id='"+a1+"']").removeClass("xm-select-this").children(".xm-unselect").removeClass("xm-select-this");
            }
        }
    }*/
}, true);
layui.formSelects.opened('splCheckUser', function (id) {
    $(".xm-form-checkbox i").remove()
    $(".xm-form-checkbox span").css("padding-left", 0)
    $("dd[tree-folder='false']").find("span").before('<i class="xm-iconfont"></i>').css("padding-left", "30px")
});


/**
 * 审批流编辑页面，选择审批方式
 */
$(".approval-pop-nav-list>li").on("click", function () {
    var type = $("input[name='approval']:checked").val();
    showApprovalLevelInfo(type);
    /*var num = $(this).index();
    $(".approval-pop-main-list>div").eq(num).show().siblings().hide()*/
})

/**
 * 显示审批级别信息
 * @param type
 */
function showApprovalLevelInfo(type) {
    switch (type) {
        case '上级':
            $('.approvalType_1').show();
            $('.approvalMethod').show();
            $('.approvalType_2').hide();
            $('.approvalType_3').hide();
            break;
        case '指定成员':
            $('.approvalType_1').hide();
            $('.approvalMethod').show();
            $('.approvalType_2').show();
            $('.approvalType_3').hide();
            break;
        case '发起人自己':
            $('.approvalType_1').hide();
            $('.approvalMethod').hide();
            $('.approvalType_2').hide();
            $('.approvalType_3').show();
            break;
        default:
            break;
    }
}

/**
 * 审批流添加、编辑审批层级
 */
$(".approval-people-add").on('click', function () {
    var maxLevel = 0;
    $('.showApprovalEdit').each(function () {
        var tmpArr = this.id.split('_');
        if (maxLevel < tmpArr[1]) {
            maxLevel = tmpArr[1];
        }
    })
    maxLevel = parseInt(maxLevel) + 1;
    $(':focus').blur();
    layer.open({
        title: '添加',
        type: 2,
        area: ['60%', '80%'], //宽高
        content: 'approval_level_edit_iframe.php?maxLevel=' + maxLevel
    });

})
/**
 * 编辑审批层级
 */
$(".approval-set-main").on('click', '.showApprovalEdit', function () {
    var tmpArr = this.id.split('_');
    var maxLevel = tmpArr[1];
    var approvalLevelInfo = $(this).siblings('input').val();
    $(':focus').blur();
    layer.open({
        title: '编辑审批',
        type: 2,
        area: ['60%', '80%'], //宽高
        content: 'approval_level_edit_iframe.php?approvalLevelInfo=' + approvalLevelInfo + '&maxLevel=' + maxLevel + '&action=edit'
    });
})

/**
 * 初始化审批列表表格
 * @param where
 */
/*function tableInitApprovalList(where) {
    //where = {u_username:'王银龙'}
    layui.use(['table', 'element'], function () {
        var loading = layer.msg('页面加载中，请稍后...');
        tableobj = table.render({
            elem: '#approvalList'
            , url: "/c.php?m=Approval&f=showApprovalList"
            , method: 'post'
            , request: {
                pageName: 'page' //页码的参数名称，默认：page
                , limitName: 'limit' //每页数据量的参数名，默认：limit
            }
            , response: {
                statusName: 'ack' //数据状态的字段名称，默认：code
                , statusCode: '1' //成功的状态码，默认：0
                , msgName: 'desc' //状态信息的字段名称，默认：msg
                , countName: 'total' //数据总数的字段名称，默认：count
                , dataName: 'item' //数据列表的字段名称，默认：data
                , pageName: 'page' //数据分页
            }
            , page: true //开启分页
            , limits: [10, 20, 50, 100]
            //field
            , cols: [[ //表头
                {type: 'checkbox', width: 40}
                , {
                    field: 'approval_id',
                    title: '审批编号',
                    width: 100,
                    templet: '<div><span title="{{d.approval_id}}">{{d.approval_id}}</span></div>'
                }
                , {
                    field: 'ac_spl_name',
                    title: '审批名称',
                    width: 200,
                    templet: '<div><span title="{{d.ac_spl_name}}">{{d.ac_spl_name}}</span></div>'

                }
                , {
                    field: 'approval_status',
                    title: '审批状态',
                    width: 100,
                    templet: '<div><span title="{{d.approval_status}}">{{d.approval_status}}</span></div>'
                },
                {
                    field: 'add_username',
                    title: '申请人',
                    width: 150,
                    toolbar: '<div><span title="{{d.add_username}}">{{d.add_username}}</span></div>'
                },
                {
                    title: '申请时间',
                    field: 'add_time',
                    width: 200,
                    toolbar: '<div><span title="{{d.add_time}}">{{d.add_time}}</span></div>'
                },
                {
                    field: 'check_username',
                    title: '待审核人',
                    width: 250,
                    toolbar: '<div><span title="{{d.check_username}}">{{d.check_username}}</span></div>'
                },
                {
                    field: 'options',
                    title: '操作',
                    fixed: 'right',
                    width: 360,
                    align: 'center',
                }
            ]]
            //where代表异步发送data数据
            , where: where
            , done: function (d, curr, count) {
                layer.close(loading);
            }
        });
    })
}*/

function tableInitApprovalListTest(where,id) {
    //where = {u_username:'王银龙'}
    layui.use(['table', 'element'], function () {
        var loading = layer.msg('页面加载中，请稍后...');
        tableobj = table.render({
            elem: '#'+id
            , url: "/c.php?m=Staff&f=showApprovalList"
            , method: 'post'
            , request: {
                pageName: 'page' //页码的参数名称，默认：page
                , limitName: 'limit' //每页数据量的参数名，默认：limit
            }
            , response: {
                statusName: 'ack' //数据状态的字段名称，默认：code
                , statusCode: '1' //成功的状态码，默认：0
                , msgName: 'desc' //状态信息的字段名称，默认：msg
                , countName: 'total' //数据总数的字段名称，默认：count
                , dataName: 'item' //数据列表的字段名称，默认：data
                , pageName: 'page' //数据分页
            }
            , page: true //开启分页
            , limits: [10, 20, 50, 100]
            //field
            , cols: [[ //表头
                {type: 'checkbox', width: 40}
                , {
                    field: 'approval_id',
                    title: '审批编号',
                    width: 100,
                    templet: '<div><span title="{{d.approval_id}}">{{d.approval_id}}</span></div>'
                }
                , {
                    field: 'ac_spl_name',
                    title: '审批名称',
                    width: 200,
                    templet: '<div><span title="{{d.ac_spl_name}}">{{d.ac_spl_name}}</span></div>'

                }
                , {
                    field: 'approval_status',
                    title: '审批状态',
                    width: 100,
                    templet: '<div><span title="{{d.approval_status}}">{{d.approval_status}}</span></div>'
                },
                {
                    field: 'add_username',
                    title: '申请人',
                    width: 150,
                    toolbar: '<div><span title="{{d.add_username}}">{{d.add_username}}</span></div>'
                },
                {
                    title: '申请时间',
                    field: 'add_time',
                    width: 200,
                    toolbar: '<div><span title="{{d.add_time}}">{{d.add_time}}</span></div>'
                },
                {
                    field: 'check_username',
                    title: '待审核人',
                    width: 250,
                    toolbar: '<div><span title="{{d.check_username}}">{{d.check_username}}</span></div>'
                },
                {
                    field: 'options',
                    title: '操作',
                    fixed: 'right',
                    width: 360,
                    align: 'center',
                }
            ]]
            //where代表异步发送data数据
            , where: where
            , done: function (d, curr, count) {
                layer.close(loading);
            }
        });
    })
}

/**
 * 撤销审批
 * @param approvalId
 */
function cancelApproval(approvalId) {
    layer.confirm('是否要撤销审批?', {icon: 3, title: '提示'}, function (index) {
        ajax_post({approvalId: approvalId}, '/c.php?m=Staff&f=cancelApproval')
        layer.close(index);
    });
}

/**
 * 审核审批
 * @param approvalId
 */
function checkApproval(approvalId) {
    layer.confirm('是否要审核审批?', {icon: 3, title: '提示'}, function (index) {
        layer.close(index);
        ajax_post({approvalId: approvalId}, '/c.php?m=Staff&f=checkApproval');
    });
}

/**
 * 打回审批
 * @param approvalId
 */
function backApproval(approvalId) {
    layer.confirm('是否要打回审批?', {icon: 3, title: '提示'}, function (index) {
        ajax_post({approvalId: approvalId}, '/c.php?m=Staff&f=backApproval')
        layer.close(index);
    });
}

/**
 * 重新提交审批
 * @param approvalId
 */
function resubmitApproval(approvalId) {
    layer.confirm('是否要重新提交?', {icon: 3, title: '提示'}, function (index) {
        ajax_post({approvalId: approvalId}, '/c.php?m=Staff&f=resubmitApproval')
        layer.close(index);
    });
}

/**
 * 显示审批详情
 * @param approvalId
 */
function showApprovalDetail(approvalId, type) {
    /*$.post('../ajax/approval.php', {approvalId: approvalId, type: type}, function (msg) {
        console.log(msg);
        window.open(msg + '&type=' + type);
    });*/
    layer.open( {
        title: '审批详情',
        type: 2,
        area: [ '60%', '80%' ], //宽高
        content: 'approval_detail_iframe.php?approvalId='+approvalId
    } )
}

function editApprovalDetail(approvalId,type) {
    $.post('../ajax/approval.php', {approvalId: approvalId, type: type}, function (msg) {
        window.open(msg + '&type=' + type);
    });
}

/**
 * 申请审批
 */
function applyApproval( approvalConfigId ) {
    //var approvalConfigId = $('#approvalConfigId').val();//审批类型
    //判断审批类型
    if (!approvalConfigId) {
        layer.msg('请选择审批类型', {icon: 2});
        return false;
    }
    $.post('../ajax/approval.php', {approvalConfigId: approvalConfigId}, function (msg) {
        var url = msg + '&ac_id=' + approvalConfigId;
        window.open(url, '_self');
    })
}

/**
 * 审批列表搜索条件
 */
$('#approvalSearchBtn').on('click', function () {
    var searchKey = $('#searchKey').val();
    var serachValue = $('#searchValue').val();
    var dateStart = $('#dateStart').val();
    var dateEnd = $('#dateEnd').val();
    var where = {
        searchKey: searchKey,
        serachValue: serachValue,
        dateStart: dateStart,
        dateEnd: dateEnd,
    }
    tableInitApprovalList(where);
})

/**
 * 监听选项卡
 */
element.on('tab(approvalIndex)', function (data) {
    $('#tabId').val(data.index);
    searchApprovalInfo(data.index,1);
});

/**
 * 获取审批列表数据
 * @param index
 * @param type
 * @returns {boolean}
 */
function searchApprovalInfo(index,type) {
    if( index == null )
    {
        index = parseInt($('#tabId').val());
    }
    var userId = $('#myUserId').val();
    var searchValue = $.trim($('#searchValue').val());
    var searchKey = $('#searchKey').val();
    var where = {};
    var tableId = '';
    $('#searchDiv').show();
    switch (index) {
        case 0:
            $('#searchDiv').hide();
            return false;
            break;
        case 1://待我审批
            where = {
                checkUser: userId,
                having: 1
            }
            tableId = 'waitMeCheckList'
            break;
        case 2://我已审批
            where = {
                checkUser: userId,
                having:2
            }
            tableId = 'myCheckList'
            break;
        case 3://我申请的
            where = {
                applyUser: userId,
                having:3
            }
            tableId = 'myApplyList'
            break;
        case 4://抄送我的
            where = {
                copyToUser:userId,
                having:4
            }
            tableId = 'copyToMeList'
            break;
        default:
            break;
    }
    //搜索
    if (2 == type) {
        var acId = $('#acId').val() ? $('#acId').val() : 0;
        where.acId = acId;
    }
    if (searchKey && searchValue) {
        where.searchKey = searchKey;
        where.searchValue = searchValue;
    }
    tableInitApprovalListTest(where, tableId);
    //location.hash = 'filter='+ this.getAttribute('lay-id');
}


