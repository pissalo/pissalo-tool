<div class="ao-header layui-nav">
    <div class="ao-header-logo">
        泽汇OA系统
    </div>
    <ul class="fl ao-header-nav">
        <?php foreach ($menu_config as $menu_detail) {
            if (!in_array($menu_detail['permission_id'], $adminOptionPer)) {
                continue;
            }
            //一级
            $top_option = '';
            if ($menu_detail['top_url']) {
                $top_option = "onclick=\"window.location.href='{$menu_detail['top_url']}'\"";
            }
            ?>
            <!--没有二级的给li加链接-->
            <li class="layui-nav-item" <?php echo $top_option; ?> >
                <i class="layui-icon <?php echo $menu_detail['class'] ?> nav-icon"></i>
                <a href="javascript:;"><?php echo $menu_detail['title'] ?></a>
                <?php if ($menu_detail['sub']) {
                    echo '<ul class="layui-nav-child nav-list"> <!-- 二级菜单 -->';
                    foreach ($menu_detail['sub'] as $menu_detail_sub_2) {
                        if (!in_array($menu_detail_sub_2['permission_id'], $adminOptionPer)) {
                            continue;
                        }
                        ?>
                            <li>
                                <a target="<?php echo $menu_detail_sub_2['target']; ?>" href="<?php echo $menu_detail_sub_2['href']; ?>"><?php echo  $menu_detail_sub_2['title']; ?></a>
                                <?php
                                if ($menu_detail_sub_2[ 'sub' ]) {
                                    echo '<ul>';
                                    foreach ($menu_detail_sub_2[ 'sub' ] as $menu_detail_sub_3) {
                                        if (!in_array($menu_detail_sub_3['permission_id'], $adminOptionPer)) {
                                            continue;
                                        }
                                        ?>
                                    <li>
                                        <a target="<?php echo $menu_detail_sub_2['target']; ?>" href="<?php echo $menu_detail_sub_3['href']; ?>"><?php echo  $menu_detail_sub_3['title']; ?></a>
                                        <?php
                                        if ($menu_detail_sub_3[ 'sub' ]) {
                                            echo '<ul>';
                                            foreach ($menu_detail_sub_3[ 'sub' ] as $menu_detail_sub_4) {
                                                if (!in_array($menu_detail_sub_4['permission_id'], $adminOptionPer)) {
                                                    continue;
                                                }
                                                ?>
                                                <li>
                                                    <a target="<?php echo $menu_detail_sub_2['target']; ?>" href="<?php echo $menu_detail_sub_4['href']; ?>"><?php echo  $menu_detail_sub_4['title']; ?></a></li>
                                            <?php }
                                            echo '</ul>'
                                            ?>
                                        <?php } ?>
                                    </li>
                                    <?php }
                                    echo '</ul>'
                                    ?>
                                <?php } ?>
                            </li>
                        <?php
                    }
                    echo '</ul>';
                    ?>

                <?php } ?>
            </li>
            <?php
        } ?>
    </ul>
    <ul class="fr">
        <li class="layui-nav-item">
            <a href="javascript:;" style="color: #fff"><img src="/theme/img/user.jpg" class="layui-nav-img"><?php echo $adminU; ?></a>
            <dl class="layui-nav-child welcome-dl">
                <span>欢迎!</span>
                <!--<dd><a href="javascript:;" class="header-center"> 个人中心</a></dd>-->
                <dd><a href="javascript:RecallPermission();void(0);" class="header-center"> 重获权限</a></dd>
                <dd><a href="/logout.php" class="header-out"> 退出</a></dd>
            </dl>
        </li>
    </ul>
</div>
