<?php
namespace OA;

$curDir = dirname(__FILE__);
$pageAuthor = '黄焕军';
$pageComment = '首页';
include_once('../include/common.inc.php');
$pagePermissionId = PRE_INDEX;
session_start();
require_once('../yz.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../header_common.php'); ?>
</head>
<body>
<div id="oaIndex">
    <?php require_once('../header.php'); ?>
    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md4">
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md12">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                待审批流程
                                <a href="" class="fr">更多 ></a>
                            </div>
                            <div class="layui-card-body">
                                <div class="layui-carousel layadmin-carousel layadmin-backlog">
                                    <!--<div class="approve-btn">
                                        <button class="layui-btn layui-btn-sm layui-btn-danger">您当前还有13条待审核任务</button>
                                    </div>-->
                                    <!--<ul class="sp-list">
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-danger">待审批</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-danger">待审批</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-danger">待审批</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-danger">待审批</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-danger">待审批</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                    </ul>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md12">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                我发起的流程
                                <a href="" class="fr">更多 ></a>
                            </div>
                            <div class="layui-card-body">
                                <div class="layui-carousel layadmin-carousel layadmin-backlog">
                                    <!--<ul class="flow-list">
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-normal" style="vertical-align: baseline">运行</button>
                                            <span class="new-fade">
                                                <a href="">查看</a>
                                                <a href="">催办</a>
                                                刘亮申请事假（2015/10/8-2015/10/16
                                            </span>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-warm" style="vertical-align: baseline">完成</button>
                                            <span class="new-fade">
                                                <a href="">查看</a>
                                                刘亮申请事假刘亮申请事假刘亮申请事假刘亮申请事假（2015/10/8-2015/10/16
                                            </span>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                    </ul>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md4">
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md12">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                今日代办清单
                                <span class="layui-badge">7</span>
                            </div>
                            <div class="layui-card-body">
                                <div class="layui-carousel layadmin-carousel layadmin-backlog">
                                    <!--<div class="layui-collapse" lay-accordion="">
                                        <div class="layui-colla-item">
                                            <h2 class="layui-colla-title">上午</h2>
                                            <div class="layui-colla-content">
                                                <p>因为不适合。如果希望开发长期的项目或者制作产品类网站因为不适合。如果希望开发长期的项目或者制作产品类网站因为不适合。如果希望开发长期的项目或者制作产品类网站，那么就需要实现特定的设计，为了在维护项目中可以方便地按设计师要求快速修改样式，肯定会逐步编写出各种业务组件、工具类，相当于为项目自行开发一套框架。——来自知乎@Kayo</p>
                                            </div>
                                        </div>
                                        <div class="layui-colla-item">
                                            <h2 class="layui-colla-title">下午</h2>
                                            <div class="layui-colla-content">
                                                <p>因为不适合。如果希望开发长期的项目或者制作产品类网站因为不适合。如果希望开发长期的项目或者制作产品类网站因为不适合。如果希望开发长期的项目或者制作产品类网站，那么就需要实现特定的设计，为了在维护项目中可以方便地按设计师要求快速修改样式，肯定会逐步编写出各种业务组件、工具类，相当于为项目自行开发一套框架。——来自知乎@Kayo</p>
                                            </div>
                                        </div>
                                        <div class="layui-colla-item">
                                            <h2 class="layui-colla-title">拖延</h2>
                                            <div class="layui-colla-content layui-show">
                                                <p>因为不适合。如果希望开发长期的项目或者制作产品类网站因为不适合。如果希望开发长期的项目或者制作产品类网站因为不适合。如果希望开发长期的项目或者制作产品类网站，那么就需要实现特定的设计，为了在维护项目中可以方便地按设计师要求快速修改样式，肯定会逐步编写出各种业务组件、工具类，相当于为项目自行开发一套框架。——来自知乎@Kayo</p>
                                            </div>
                                        </div>
                                    </div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md12">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                今日完成清单
                                <a href="" class="fr">更多 ></a>
                            </div>
                            <div class="layui-card-body">
                                <div class="layui-carousel layadmin-carousel layadmin-backlog">
                                    <!--<ul class="sp-list">
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-warm">已完成</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-warm">已完成</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-warm">已完成</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-warm">已完成</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-warm">已审核</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                    </ul>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md4">
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md12">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                公告新闻
                                <a href="" class="fr">更多 ></a>
                            </div>
                            <div class="layui-card-body">
                                <div class="layui-carousel layadmin-carousel layadmin-backlog" style="height: 174px">
                                    <!--<ul class="notice-list">
                                        <li>
                                            <span class="notice-title">【通知】</span>
                                            <p class="notice-content"><a href="">5月1日放假说明5月1日放假说明5月1日放假说明5月1日放假说明5月1日放假说明5月1日放假说明</a></p>
                                            <span class="same-time">4-18</span>
                                        </li>
                                        <li>
                                            <span class="notice-title">【新闻】</span>
                                            <p class="notice-content"><a href="">本公司项目落地实行</a></p>
                                            <span class="same-time">4-20</span>
                                        </li>
                                        <li>
                                            <span class="notice-title">【通知】</span>
                                            <p class="notice-content"><a href="">5月1日放假说明</a></p>
                                            <span class="same-time">4-18</span>
                                        </li>
                                        <li>
                                            <span class="notice-title">【新闻】</span>
                                            <p class="notice-content"><a href="">本公司项目落地实行</a></p>
                                            <span class="same-time">4-20</span>
                                        </li>
                                        <li>
                                            <span class="notice-title">【通知】</span>
                                            <p class="notice-content"><a href="">5月1日放假说明</a></p>
                                            <span class="same-time">4-18</span>
                                        </li>
                                        <li>
                                            <span class="notice-title">【新闻】</span>
                                            <p class="notice-content"><a href="">本公司项目落地实行</a></p>
                                            <span class="same-time">4-20</span>
                                        </li>
                                        <li>
                                            <span class="notice-title">【通知】</span>
                                            <p class="notice-content"><a href="">5月1日放假说明</a></p>
                                            <span class="same-time">4-18</span>
                                        </li>
                                    </ul>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md12">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                消息
                                <a href="" class="fr">更多 ></a>
                            </div>
                            <div class="layui-card-body">
                                <div class="layui-carousel layadmin-carousel layadmin-backlog" style="height: 174px">
                                    <!--<ul class="sp-list message-list">
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-danger">未读</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-danger">未读</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs">已读</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs">已读</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs layui-btn-danger">未读</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                        <li>
                                            <button class="layui-btn layui-btn-xs">已读</button>
                                            <p class="sp-content">付款单申请待审批：深圳市玩具供应商公司待付款1220元</p>
                                            <span class="same-time">-10/02 -张三</span>
                                        </li>
                                    </ul>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md12">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                最近登录情况
                            </div>
                            <div class="layui-card-body">
                                <div class="layui-carousel layadmin-carousel layadmin-backlog tScroll"
                                     style="height: 174px">
                                    <ul>
                                        <?php
                                        $clsLog = new ClsLog('oa');
                                        $clsLog->setCollection('oa_user_login');
                                        $log_login = $clsLog->getList(0, 100, array( "o_zt_id" => $adminZtId ,'o_username'=> $adminU ));
                                        if ($log_login['ack']) {
                                            foreach ($log_login['msg'] as $log_info) {
                                                ?>
                                                <li>
                                                    <p class="layui-col-md4"><?php echo date('Y/m/d', $log_info['o_login_time']) ?></p>
                                                    <p class="layui-col-md4"><?php echo $log_info['o_login_ip']; ?></p>
                                                    <p class="layui-col-md4"><?php echo date('H:i', $log_info['o_login_time']) ?></p>
                                                </li>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <li>
                                                <p class="layui-col-md4"><?php echo date('Y/m/d') ?></p>
                                                <p class="layui-col-md4"><?php echo $log_login['msg']; ?></p>
                                                <p class="layui-col-md4"><?php echo date('H:i') ?></p>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once('../footer.php');
?>
<script src="/theme/js/scroll.js"></script>
<script>
    $(function () {
        $('.tScroll').myScroll({
            speed: 50, //数值越大，速度越慢
            rowHeight: 30 //li的高度
        });
    });
</script>
</body>
</html>
