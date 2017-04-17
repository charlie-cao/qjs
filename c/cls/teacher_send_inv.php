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
                    jsApiList:
                            [
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
                        menuList: ["menuItem:share:appMessage","menuItem:share:timeline"]
                                    // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
                    });

                    wx.onMenuShareAppMessage({
                        title: '<?=$_SESSION['user']->username?>邀请您加入<?=$_SESSION['school']->name?> <?=$cls['name']?>', // 分享标题
                        desc: '班级邀请码为<?=$cls['cls_key']?>，请快来加入我们的班级圈！比微信更好用，消息分类显示、永久保存，不在担心错过，还可以像朋友圈一样点赞，评论。', // 分享描述
                        link: '<?= $server_host ?>/c/cls/pt_view_inv.php?state=<?=$_SESSION['school']->id?>-<?=$_REQUEST['id']?>', // 分享链接
                        imgUrl: '<?= $server_host ?>/c/public/images/wx_inv.jpg', // 分享图标
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
                        link: '<?= $server_host ?>/c/cls/pt_view_inv.php?state=<?=$_SESSION['school']->id?>-<?=$_REQUEST['id']?>', // 分享链接
                        imgUrl: '<?= $server_host ?>/c/public/images/wx_inv.jpg', // 分享图标
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

    <body ontouchstart style="background-color: #fff;">
        <div class="weui_msg " id="msg1" style="padding-top:10px;">
            <div class="weui_icon_area" style="margin-bottom: 10px;">
                <img src="../public/images/wx_inv.jpg" style="width: 60px;"/>
            </div>
            <div class="weui_text_area" style="">
                <h2 class="weui_msg_title" style="font-size: 14px;"><?= $_SESSION['school']->name ?> : <?= $cls['name'] ?></h2>
                <p class="weui_msg_desc"  style="font-size: 12px; text-align: left">班级邀请码为<?=$cls['cls_key']?>，快来加入我们的班级圈！比微信群更好用，消息分类显示、永久保存，不再担心错过，还可以像朋友圈一样点赞，评论。</p>
                <br>
                <div class="weui_msg_desc"  style="font-size: 12px; text-align: left">步骤：
                <ol style="margin-left: 40px;">
                    <li>关注学校服务号 <b><?= $_SESSION['school']->name ?></b></li>
                    <li>打开"班级"菜单,找到 <b><?= $cls['name'] ?></b></li>
                    <li>点击"加入"按钮，输入邀请码 <b><?=$cls['cls_key']?></b></li>
                </ol>
                </div>
                <br>
                <p class="weui_msg_desc"  style="font-size: 12px; text-align: left">
                    加入成功后，您以后打开"班级"菜单，就会自动打开我们的班级圈。
                </p>
            </div>
            <div class="weui_opr_area">
                <p class="weui_btn_area">

                <div class="page-bd-15">

                    <div class="weui-share" onclick="$(this).fadeOut();$(this).removeClass('fadeOut')">
                        <div class="weui-share-box">
                            点击右上角发送给指定家长 <i></i>
                        </div>
                    </div>
                    <a onclick="$('.weui-share').show().addClass('fadeIn');" class="weui_btn weui_btn_primary" href="javascript:void(0)" ><i class='icon icon-12 f20'></i>分享到家长</a>
                    <a href="teacher_menu.php" class="weui_btn weui_btn_default">返回班级管理</a>
                    </p>
                </div>
                <div class="weui_extra_area">

                </div>
            </div>
    </body>
</html>
