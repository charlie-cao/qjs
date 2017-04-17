<?php
/*
慢成长入口 根据校园id 分库
*/

require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();


$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

//交验唯一ID
$state = $_GET['state'];
if (!isset($_GET['state'])) {
    echo "ID错误";
    exit;
} else {
    $s = explode("-", $_GET['state']);

    $school_id = $s[0];
    $cls_id = $s[1];

    $_SESSION['state'] = $_GET['state'];
    $_SESSION['school_id'] = $school_id;
    $_SESSION['cls_id'] = $cls_id;
}
//交验校园
$school = get_school_info($_SESSION['school_id']);
if (!($school)) {
    //如果没有学校ID则报错
    echo "校区不存在";
    exit;
} else {
    $_SESSION['school'] = $school;
}


$state = $_SESSION['state'];
//检查用户是否已经登录
if (!isset($_SESSION['user']->openid)) {
    //未登录 微信登录
    $_SESSION['user'] = wx_userinfo($appid, $secret, $redirect_uri, $state);
}

//检查用户并更新用户SESSION信息
$_SESSION['user'] = check_user($_SESSION['user']);


if ($_SESSION['cls_id'] == "") {
    //检查用户有没有最终登录的班级ID如果有则
    //需要记录一下用户最后登录的那个学校 和 班级
//    if($_SESSION['user']->last_school_id==)
    v("./index_cls_list.php?school_id=" . $_SESSION['school_id']);
    exit;
}
//交验班级
$cls = get_cls_info($_SESSION['cls_id']);
if (!isset($cls)) {
    //如果没有学校ID则报错
    echo "班级ID不存在";
    exit;
} else {
    $_SESSION['cls'] = $cls;
}
//注册 tag
//var_dump($_SESSION);
$tags = get_tags($school->id, "cls");
if (!isset($tags)) {
    echo "获取系统tag错误";
    exit;
} else {
    $_SESSION['tags'] = $tags;
}


$sql = "select * from sc_cls where id='" . $_SESSION['cls_id'] . "';";
$res = $db->query($sql);
$cls = $res->fetch();


?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="../public/style/weui.css"/>
    <link rel="stylesheet" href="../public/style/weui2.css"/>
    <link rel="stylesheet" href="../public/style/weui3.css?1"/>
    <script src="../public/zepto.min.js"></script>
    <script src="../public/jweixin-1.2.0.js"></script>
    <script>
        $(function () {
            wx.config({
                debug: false,
                appId: '<?= $signPackage["appId"]; ?>',
                timestamp: <?= $signPackage["timestamp"]; ?>,
                nonceStr: '<?= $signPackage["nonceStr"]; ?>',
                signature: '<?= $signPackage["signature"]; ?>',
                jsApiList: [
                    'checkJsApi',
                    'onMenuShareAppMessage',
                    'onMenuShareTimeline',
                    'hideAllNonBaseMenuItem',
                    'showMenuItems'
                ]
            });
            wx.ready(function () {
                wx.hideAllNonBaseMenuItem();
            });
        });
    </script>
    <style>
        body {
            background-color: #f8f8f8;
        }

        .icon {
            font-size: 20px;
        }

        .weui_cells:before {
            top: 0;
            border-top: 0px solid #d9d9d9;
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
            left: 0px;
        }


    </style>
</head>
<body ontouchstart style="background-color: #fff;">
<div class="weui_msg " id="msg1" style="padding-top:10px;">
    <div class="weui_icon_area" style="margin-bottom: 10px;">
        <img src="../public/images/wx_inv.jpg" style="width: 60px;"/>
    </div>
    <div class="weui_text_area" style="">
        <h2 class="weui_msg_title" style="font-size: 14px;"><?= $_SESSION['school']->name ?> : <?= $cls['name'] ?></h2>
        <p class="weui_msg_desc" style="font-size: 12px; text-align: left">班级邀请码为<?= $cls['cls_key'] ?>
            ，快来加入我们的班级圈！比微信群更好用，消息分类显示、永久保存，不再担心错过，还可以像朋友圈一样点赞，评论。</p>
        <br>
        <div class="weui_msg_desc" style="font-size: 12px; text-align: left">步骤：
            <ol style="margin-left: 40px;">
                <li>关注学校服务号 <b><?= $_SESSION['school']->name ?></b></li>
                <li>打开"班级"菜单,找到 <b><?= $cls['name'] ?></b></li>
                <li>点击"加入"按钮，输入邀请码 <b><?= $cls['cls_key'] ?></b></li>
            </ol>
        </div>
        <br>
        <p class="weui_msg_desc" style="font-size: 12px; text-align: left">
            加入成功后，您以后打开"班级"菜单，就会自动打开我们的班级圈。
        </p>
    </div>
    <div class="weui-footer weui-footer-fixed-bottom">
        <p class="weui-footer-links">
            <a href="javascript:;" class="weui-footer-link">千家师</a>
        </p>
        <p class="weui-footer__text">Copyright © 2008-2017 </p>
    </div>
</div>
</body>
</html>