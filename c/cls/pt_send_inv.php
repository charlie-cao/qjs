<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
check_login();
//
$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();


$sql = "select * from sc_cls where id=" . $_REQUEST['id'];
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
    <link rel="stylesheet" href="../public/style/weui3.css"/>
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
                // 更新本分享链接
                wx.showMenuItems({
                    menuList: ["menuItem:share:appMessage", "menuItem:share:timeline"]
                    // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
                });

                wx.onMenuShareAppMessage({
                    title: '<?=$_SESSION['user']->username?>邀请您加入<?=$_SESSION['school']->name?> <?=$cls['name']?>', // 分享标题
                    desc: '班级邀请码为<?=$cls['cls_key']?>，请输入正确的邀请码加入班级', // 分享描述
                    link: '<?= $server_host ?>/c/cls/pt_enter_cls.php?state=<?=$_SESSION['school']->id?>-<?=$_REQUEST['id']?>', // 分享链接
                    imgUrl: '<?= $server_host ?>/s_icon.jpg', // 分享图标
                    type: '', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        alert("发送成功");
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

                wx.onMenuShareTimeline({
                    title: '<?=$_SESSION['user']->username?>邀请您加入<?=$_SESSION['school']->name?> <?=$cls['name']?>', // 分享标题
                    link: '<?= $server_host ?>/c/cls/pt_enter_cls.php?state=<?=$_SESSION['school']->id?>-<?=$_REQUEST['id']?>', // 分享链接
                    imgUrl: '<?= $server_host ?>/s_icon.jpg', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

            });

        });

    </script>
</head>

<body ontouchstart style="background-color: #f8f8f8;">
<div class="weui_msg " id="msg1">
    <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_info"></i></div>
    <div class="weui_text_area">
        <h2 class="weui_msg_title"><?= $cls['name'] ?></h2>
        <p class="weui_msg_desc">邀请码 <?= $cls['cls_key'] ?>，请分享该链接
            到家长的微信。并告知邀请码，家长即可加入本班级。</p>
    </div>
    <div class="weui_opr_area">
        <p class="weui_btn_area">

        <div class="page-bd-15">

            <div class="weui-share" onclick="$(this).fadeOut();$(this).removeClass('fadeOut')">
                <div class="weui-share-box">
                    点击右上角发送给指定朋友或分享到朋友圈 <i></i>
                </div>
            </div>
            <a onclick="$('.weui-share').show().addClass('fadeIn');" class="weui_btn weui_btn_primary"
               href="javascript:void(0)"><i class='icon icon-12 f20'></i>分享到家长</a>
            <a href="pt_menu.php" class="weui_btn weui_btn_default">返回班级管理</a>
        </p>
    </div>
    <div class="weui_extra_area">

    </div>
</div>
</body>
</html>
