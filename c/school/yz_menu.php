<?php
require_once '../config.php';
require_once '../lib/fun.php';
check_login();
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
    <script src="../public/iscroll.js"></script>
    <script>
        $(function () {
            var $form = $("#form");
            $form.form();
            $("#btn").click(function (e) {
                $form.validate(function (error) {
                    if (error) {
                    } else {
                        $.showLoading("更新中");
                        $.ajax({
                            type: 'POST',
                            url: '../api/school.php?a=update_user_info',
                            dataType: 'json',
                            data: $form.serialize(),
                            success: function (data) {
                                $.hideLoading();
                                if (data.msg == "success") {
                                    $.alert("更新成功,稍后将重新登录", "系统消息", function () {
                                        location.href = "index.php?state=<?=$_SESSION['state']?>";
                                    });
                                } else {
                                    alert(data.msg);
                                }


                            },
                            error: function (xhr, type) {
                                $.hideLoading();
                                console.log('Ajax error!');
                            }
                        });
                    }
                });
            })
        });
    </script>
    <style>
        .weui_cell_hd .icon {
            font-size: 24px;
            line-height: 40px;
            margin: 4px;
            color: #18b4ed;
            -webkit-transition: font-size 0.25s ease-out 0s;
            -moz-transition: font-size 0.25s ease-out 0s;
            transition: font-size 0.25s ease-out 0s;
        }
    </style>
</head>
<body ontouchstart style="background-color: #f8f8f8;">
<div class="weui-header bg-green">
    <div class="weui-header-left"><a href="yz_main.php" class="icon icon-109 f-white">返回</a></div>
    <h1 class="weui-header-title">园长管理</h1>
    <div class="weui-header-right"></div>
</div>
<div>
    <div class="weui_cells_title">个人</div>
    <div class="weui_cells weui_cells_access">
        <a class="weui_cell " href="yz_my_info.php">
            <div class="weui_cell_bd weui_cell_primary">
                <p>个人信息</p>
            </div>
            <div class="weui_cell_ft"></div>
        </a>
    </div>

    <div class="weui_cells_title">园区管理</div>
    <div class="weui_cells weui_cells_access">
        <a class="weui_cell" href="./yz_user_list.php">
            <div class="weui_cell_bd weui_cell_primary">
                <p>人员管理</p>
            </div>
            <div class="weui_cell_ft"></div>
        </a>
        <a class="weui_cell" href="./yz_school_info.php">
            <div class="weui_cell_bd weui_cell_primary">
                <p>校园管理</p>
            </div>
            <div class="weui_cell_ft"></div>
        </a>
    </div>
</body>
</html>