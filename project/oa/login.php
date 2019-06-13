<?php
require_once('include/common.inc.php');
session_start();

if ($back_url) {
    $_SESSION['backUrl'] = $back_url;
} else {
    $_SESSION['backUrl'] = '/index/index.php';
}
if ($_SESSION['adminU'] && $_SESSION['adminP'] && $_SESSION['adminZtId']) {
    //判断是不是合法用法
    $adminZtId = $_SESSION['adminZtId'];
    $clsUser = new \OA\ClsUser($_SESSION['adminU'], $_SESSION['adminP'], $_SESSION['adminZtId']);
    $yzInfo = $clsUser->yz();
    //echo "Location: {$_SESSION['backUrl']}";
    if ($yzInfo['ack']) {
        $backUrl = $_SESSION['backUrl'];
        $pathInfo = parse_url($backUrl);
        if (!empty($pathInfo['port'])) {
            $urlMain = 'http://' . $pathInfo['host'] . ':' . $pathInfo['port'] . $pathInfo['path'];
            $_SESSION['backUrl'] = null;
        } else {
            $urlMain = 'http://' . $pathInfo['host'] . $pathInfo['path'];
        }
        $urlOption = array();//url的后缀如：?page=1&typeid=1
        array_push($urlOption, "ticket={$yzInfo['ticket']}");
        array_push($urlOption, "username={$_SESSION['adminU']}");
        if ($pathInfo['query']) {
            //url有参数
            $urlArr = preg_split('/&/', $pathInfo['query']);
            if (is_array($urlArr)) {
                foreach ($urlArr as $key => $value) {
                    $c = preg_split('/=/', $value);
                    array_push($urlOption, $c[0] . '=' . $c[1]);
                }
            }
        } else {
        }

        if (is_array($urlOption)) {
            $urlOptionStrT = implode('&', $urlOption);
        }
        if (strlen($urlOptionStrT) > 0) {
            $urlOptionStr .= '&' . $urlOptionStrT;
        }
        $urlOptionStr = ltrim($urlOptionStr, '&');

        $backUrlStr = "Location: " . $urlMain . '?' . $urlOptionStr;
        //echo $backUrlStr;
        //exit;
        header($backUrlStr);
        //header("Location: http://www.baidu.com");
    } else {
        //exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登录</title>
    <link rel="stylesheet" href="theme/css/reset.css">
    <style>
        html, body, .oa-register-main {
            height: 100%;
            width: 100%;
        }

        .oa-register-main {
            background: url("theme/img/u0.jpg") no-repeat;
            background-size: 100% 100%;
            position: relative;
        }

        .oa-register-form {
            position: absolute;
            width: 500px;
            height: 550px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(223, 230, 240, 0.8);
            text-align: center;
        }

        .ze-oa-logo {
            margin: 50px 0;
        }

        .ze-oa-logo > span {
            font-size: 30px;
            color: #025BAF;
            vertical-align: middle;
            font-weight: bold;
            margin-left: 10px;
        }

        .same-input-box {
            width: 80%;
            text-align: left;
            margin: 0 auto 30px;
            position: relative;
        }

        .same-input-box > span {
            display: inline-block;
            width: 45px;
            height: 45px;
            background-color: #CCCCCC;
            vertical-align: top;
            background-position: 6.5px 6.5px;
        }

        .same-input-box:first-child > span {
            background-image: url("theme/img/company.png");
            background-repeat: no-repeat;
        }

        .same-input-box:nth-child(2) > span {
            background-image: url("theme/img/u6.png");
            background-repeat: no-repeat;
        }

        .same-input-box:nth-child(3) > span {
            background-image: url("theme/img/u11.png");
            background-repeat: no-repeat;
        }

        .same-input-box:last-child > span {
            background-image: url("theme/img/u16.png");
            background-repeat: no-repeat;
        }

        .same-input-box > input {
            width: 88%;
            height: 45px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            padding-left: 5px;
            font-size: 16px;
            outline: none;
            border-left: none;
            transition: all 0.5s;
        }

        .same-input-box > input:focus {
            border-color: #025BAF;
        }

        .same-input-box:last-child > input {
            width: 40%;
        }

        .verification-code {
            display: inline-block;
            margin-left: 20px;
            height: 45px;
            vertical-align: middle;
            cursor: pointer;
        }

        .register-btn {
            width: 60%;
            margin: 0 auto;
            height: 50px;
            text-align: center;
            line-height: 50px;
            border-radius: 5px;
            color: #fff;
            background: #2E558E;
            font-size: 20px;
            margin-top: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .register-btn:hover {
            background: #19355f;
        }

        .err-text {
            position: absolute;
            left: 100px;
            top: 110px;
            color: red;
            transition: all .3s;
            font-size: 16px;
        }

        input.red {
            border-color: red;
        }

        .company-list {
            position: absolute;
            width: 88%;
            left: 45px;
            background: #2E558E;
            z-index: 10;
            display: none;
        }

        .company-list li {
            text-align: center;
            color: #fff;
            font-size: 14px;
            height: 40px;
            line-height: 40px;
            cursor: pointer;
            border-bottom: 1px solid #ccc;
        }

        .company-list li:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
<div class="oa-register-main">
    <div class="oa-register-form">
        <div class="ze-oa-logo">
            <img src="theme/img/u20.png" alt="">
            <span>OA系统</span>
        </div>
        <form id="dataForm">
            <input id="zt_id" name="zt_id" type="hidden" value="">
            <p class="err-text"></p>
            <div>
                <div class="same-input-box">
                    <span></span><input value="" type="text" readonly name="user_company" class="user_company"
                                        id="user_company">
                    <ul class="company-list">
                        <?php
                        foreach ($zt_list as $zt_id => $zt_name) {
                            echo "<li id='{$zt_id}'>{$zt_name}</li>";
                        }
                        ?>
                    </ul>
                </div>
                <div class="same-input-box">
                    <span></span><input placeholder="输入账号" type="text" name="user_id" class="user_id">
                </div>
                <div class="same-input-box">
                    <span></span><input placeholder="输入密码" type="password" name="user_password" class="user_password">
                </div>
                <div class="same-input-box">
                    <span></span><input placeholder="验证码" type="text" name="verification_code"
                                        class="verification_code">
                    <div class="verification-code"><img id="img_captcha" src="/include/yzm/captcha.php" alt=""
                                                        height="45"></div>
                </div>
            </div>
        </form>
        <button class="register-btn">登 录</button>
    </div>
</div>
</body>
<script src="theme/js/jquery.min.js"></script>
<script>
    $("#img_captcha").on("click", function () {
        img_captcha.src = "../include/yzm/captcha.php?t=" + Number(new Date);
        return false;
    });
    $(".register-btn").on("click", function () {
        if ($("#zt_id").val() == "") {
            $(".err-text").text("请选择所属公司！");
            $(".user_company").addClass("red");
            return false;
        }
        if ($(".user_id").val() == "") {
            $(".err-text").text("请填写账号！");
            $(".user_id").addClass("red");
            return false;
        }
        if ($(".user_password").val() == "") {
            $(".err-text").text("请填写密码！");
            $(".user_password").addClass("red");
            return false;
        }
        if ($(".verification_code").val() == "") {
            $(".err-text").text("请填写验证码！");
            $(".verification_code").addClass("red");
            return false;
        }
        var username = $(".user_id").val();
        var password = $(".user_password").val();
        var captcha = $(".verification_code").val();
        ;
        var zt_id = $("#zt_id").val();
        var ds = {username: username, password: password, captcha: captcha, zt_id: zt_id};
        $.post('./ajax/login.php', ds, function (data) {
            var info = JSON.parse(data);
            if (1 == info.ack) {
                window.location.href = "index.php";
            }
            else {
                $(".err-text").text(info.msg);
            }
        });
    });
    $("input").on("focus", function () {
        $(this).removeClass("red");
        $(".err-text").text("");
    })

    $('.verification_code').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            if ($("#zt_id").val() == "") {
                $(".err-text").text("请选择所属公司！");
                $(".user_company").addClass("red");
                return false;
            }
            if ($(".user_id").val() == "") {
                $(".err-text").text("请填写账号！");
                $(".user_id").addClass("red");
                return false;
            }
            if ($(".user_password").val() == "") {
                $(".err-text").text("请填写密码！");
                $(".user_password").addClass("red");
                return false;
            }
            if ($(".verification_code").val() == "") {
                $(".err-text").text("请填写验证码！");
                $(".verification_code").addClass("red");
                return false;
            }
            var username = $(".user_id").val();
            var password = $(".user_password").val();
            var captcha = $(".verification_code").val();
            ;
            var zt_id = $("#zt_id").val();
            var ds = {username: username, password: password, captcha: captcha, zt_id: zt_id};
            $.post('./ajax/login.php', ds, function (data) {
                var info = JSON.parse(data);
                if (1 == info.ack) {
                    window.location.href = "index.php";
                }
                else {
                    $(".err-text").text(info.msg);
                }
            });
        }
    });

    $(".user_company").on("click", function () {
        $(".company-list").stop().slideToggle("fast");
    });
    $(".company-list li").on("click", function () {
        $(".user_company").val($(this).text())
        $("#zt_id").val($(this).attr('id'))
        $(".company-list").slideUp("fast")
    });
    $(document).ready(function () {
        $(".user_company").val($(".company-list li:first-child").text());
        $("#zt_id").val($(".company-list li:first-child").first().attr('id'))
    });
</script>
</html>
