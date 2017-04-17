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
    <script src="../public/iscroll.js"></script>
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


            $(".weui_btn_warn").click(function (e) {
                $.confirm("确认退出?", "确认?", function () {
                    $.toast("成功!");
                }, function () {
                    //取消操作
                });
            })
        })
    </script>
</head>

<body ontouchstart style="background-color: #f8f8f8;">


<div class="weui_tab tab-bottom">
    <div class="weui_tab_bd">

        <div class="weui-header bg-green">
            <div class="weui-header-left"><a href="teacher_main.php" class="icon icon-109 f-white">返回</a></div>
            <h1 class="weui-header-title">设置</h1>
            <div class="weui-header-right"></div>
        </div>

        <!--div class="weui_cells_title">班级信息</div>
        <div class="weui_cells weui_cells_form">
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">班级名称</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input"  value="小二班" placeholder="请输入班级名称">
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">班级口令</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input"  value="******">
                </div>
            </div>

        </div>
                <div class="weui_btn_area">
                    <a class="weui_btn weui_btn_primary" href="javascript:" id="btn">更新班级信息</a>
                </div-->


        <div class="weui_cells_title">个人</div>
        <div class="weui_cells weui_cells_access">
            <a class="weui_cell " href="teacher_my_info.php">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>个人信息</p>
                </div>
                <div class="weui_cell_ft"></div>
            </a>
        </div>
        <div class="weui_cells_title">管理班级 - <?= $_SESSION['cls']->name ?></div>
        <div class="weui_cells weui_cells_access">

            <a class="weui_cell " href="teacher_cls_info.php">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>修改信息</p>
                </div>
                <div class="weui_cell_ft"></div>
            </a>
            <a class="weui_cell " href="teacher_cls_users.php">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>成员列表</p>
                </div>
                <div class="weui_cell_ft"></div>
            </a>


            <a class="weui_cell " href="teacher_send_inv.php?id=<?= $_SESSION['cls']->id ?>">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>邀请家长</p>
                </div>
                <div class="weui_cell_ft"></div>
            </a>

        </div>


        <div class="weui_cells_title">切换班级</div>
        <div class="weui_cells weui_cells_access">
            <a class="weui_cell " href="teacher_cls_list.php">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>我的班级</p>
                </div>
                <div class="weui_cell_ft"></div>
            </a>
            <a class="weui_cell " href="teacher_all_cls.php">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>其他班级</p>
                </div>
                <div class="weui_cell_ft"></div>
            </a>
        </div>

    </div>


</div>

</div>
</body>

</html>