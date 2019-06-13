/**
 * tools目录下JS
 * 2018年12月28日
 *
 */

/**
 * permission.php提交按钮
 */
var form = layui.form;

$('#permission').on('click', '.permission-btn', function () {
    var ac_id = $('#ac_id').val();
    var approvalType = $('#approvalType').val();
    if (!approvalType) {
        ajax_post($('#permission_form').serialize(), '/c.php?m=Tools&f=allotPermission')
    } else {
        //ajax_post($('#permission_form').serialize(), '/c.php?m=Approval&f=addApproval')
        $.ajax({
            url: '/c.php?m=Staff&f=addApproval',
            type: "post",
            data: $('#permission_form').serialize(),
            dataType: "json",
            success: function (result) {
                $(':focus').blur();
                if (result.ack == 1) {
                    layer.alert(result.msg, function () {
                        window.open('/staff/approval.php', '_self');
                    })
                } else {
                    layer.alert(result.msg, 3);
                }
            },
            error: function () {
            }
        })
    }
})
/**
 * 监听权限复选框
 */
form.on('checkbox(permission_allot)', function (data) {
    var id_pre_str = data.elem.attributes.id.value;
    var tmp_arr = id_pre_str.split('_');
    var id_pre = tmp_arr[0];
    if (data.elem.checked) {
        if ('page' == id_pre) {
            syn_sup_per_check_status(tmp_arr[2]);
        } else if ('read' == id_pre) {
            var id = data.elem.attributes.check_child.value;
            syn_sup_per_check_status(id, data.elem.checked);
        } else if ('option' == id_pre) {
            var id = data.elem.attributes.check_child.value;
            syn_sup_per_check_status(id)
            check_one_checkbox('read_some_' + id, data.elem.checked);
            form.render('checkbox');
        }
    } else {
        if ('page' == id_pre) {
            syn_sub_per_check_status('check_child', tmp_arr[2]);
        } else if ('read' == id_pre) {
            console.log(check_read_per_is_check(tmp_arr[2]));
            var has_check_read_per_num = check_read_per_is_check(tmp_arr[2]);
            if (has_check_read_per_num == 0) {
                syn_sub_per_check_status('option-per', tmp_arr[2]);
            }
        }
    }
});


/**
 * 同步勾选上级权限
 * @author 王银龙
 * @param int id 上级ID
 */
function syn_sup_per_check_status(id) {
    document.getElementById('page_per_' + id).checked = true;
    if ($('#page_per_' + id).attr('check_child')) {
        syn_sup_per_check_status($('#page_per_' + id).attr('check_child'));
    }
    form.render('checkbox');
}

/**
 * 同步取消下级勾选
 * @author 王银龙
 * @param string attr_name 属性名称
 * @param string attr_value 属性值
 */
function syn_sub_per_check_status(attr_name, attr_value) {
    $("[" + attr_name + "='" + attr_value + "']").each(function (index, item) {
        item.checked = false;
        var tmp_arr = item.id.split('_');
        $("[" + attr_name + "='" + tmp_arr[2] + "']").each(function (index, item) {
            if (tmp_arr[0] != 'read') {
                if ($(this).attr(attr_name) > 0) {
                    syn_sub_per_check_status(attr_name, $(this).attr(attr_name));
                }
            }
        })
    });
    form.render('checkbox');
}

/**
 * 判断父权限读操作权限是否被选择
 * @param int id 上级ID
 * @return int 下属权限勾选数量
 */
function check_read_per_is_check(id) {
    var count = 0;
    $("[read-per='" + id + "'").each(function (index, item) {
        if (item.checked) {
            count++;
        }
    })
    return count;
}

/**
 * 勾选特定ID复选框
 * @author 王银龙
 * @param int id 控件ID
 * @param boolean 是否选中
 */
function check_one_checkbox(id, check) {
    document.getElementById(id).checked = check;
}
