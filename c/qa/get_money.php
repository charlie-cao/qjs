<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
check_login();
//
$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();

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
                });
            });

        </script>
    </head>

    <body ontouchstart style="background-color: #f8f8f8;">
        <div class="weui_msg " id="msg1">
            <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_info"></i></div>
            <div class="weui_text_area">
                <p class="weui_msg_desc">由于微信功能限制，提现需要“千家师”客服人工操作，请联系电话18610661282</p>
            </div>
            <div class="weui_opr_area">
                <p class="weui_btn_area">
                <div class="page-bd-15">
                    <a href="javascript:;" onclick="history.back()" class="weui_btn weui_btn_default">返回</a>
                    </p>
                </div>
                <div class="weui_extra_area">

                </div>
            </div>
    </body>
</html>
