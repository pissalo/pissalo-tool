<div id="systemFooter">
    <p class="fl" style="margin: 16px 0">
        <?php echo $zt_list[$adminZtId]; ?>
    </p>
    <ul>
        <?php
        $cls_config = new \OA\ClsConfig();
        $list_ss = $cls_config->getSystemSubList(array( 'col'=> 'css_name,css_url_inner,css_url_remote' ));
        $list_ss = $list_ss['msg'];
        foreach ($list_ss as $info_ss) {
            echo "<li>{$info_ss['css_name']} <a href='http://{$info_ss['css_url_inner']}' target='_blank'>坂田</a> <a href='http://{$info_ss['css_url_remote']}' target='_blank'>非坂田</a></li>";
        }
        ?>
    </ul>
    <div class="execution-time-box fr">
        <span>页面执行时间：</span>
        <span class="execution-time">
            <?php
            $page_end_time = microtime();
            $start_time_page_my = explode(" ", $page_start_ime);
            $end_time_page_my = explode(" ", $page_end_time);
            $total_time_page_my = ( $end_time_page_my[ 0 ] - $start_time_page_my[ 0 ] ) + ( $end_time_page_my[ 1 ] - $start_time_page_my[ 1 ] );
            $time_cost = sprintf("%s", $total_time_page_my);
            echo $time_cost . "秒";
            ?>
        </span>
    </div>
</div>
<script src="/theme/layui/layui.all.js"></script>
<script src="/theme/js/common.js"></script>
<script>
    $(document).ready(function () {
        $("ul.nav-list li").hover(function () {
                $(this).addClass("on");
                var w = $(this).children("ul").width();
                $(this).children("ul").css("right", -w + "px")
            },
            function () {
                $(this).removeClass("on");
            })
        if ($("ul.nav-list>li").find("ul").html() != "") {
            $("ul.nav-list ul").siblings("a").append("<span class='sub'></span>");
        }
    });
</script>
