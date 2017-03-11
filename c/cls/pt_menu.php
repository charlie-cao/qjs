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
    <link rel="stylesheet" href="../public/style/weui.css" />
    <link rel="stylesheet" href="../public/style/weui2.css" />
    <link rel="stylesheet" href="../public/style/weui3.css" />
    <script src="../public/zepto.min.js"></script>
    <script src="../public/iscroll.js"></script>
    <script>
        $(function () {
            //            $('.weui_tab').tab();
            $(".weui_btn_warn").click(function (e) {
                $.confirm("您确定要退出该班级么?", "确认退出?", function () {
                    $.toast("确认退出!");
                }, function () {
                    //取消操作
                });
            })
        });
    </script>
</head>

<body ontouchstart style="background-color: #f8f8f8;">


    <div class="weui_tab tab-bottom">
        <div class="weui_tab_bd">

            <div class="weui-header bg-green">
                <div class="weui-header-left"> <a href="pt_main.php" class="icon icon-109 f-white">返回</a> </div>
                <h1 class="weui-header-title">设置</h1>
                <div class="weui-header-right"> </div>
            </div>


            <div class="weui_cells_title">个人</div>
            <div class="weui_cells weui_cells_access">
                <a class="weui_cell " href="pt_my_info.php">
                    <div class="weui_cell_bd weui_cell_primary">
                        <p>个人信息</p>
                    </div>
                    <div class="weui_cell_ft"></div>
                </a>
            </div>

            <div class="weui_cells_title">当前班级 - <?= $_SESSION['cls']->name ?></div>
            <div class="weui_cells weui_cells_access">

                <a class="weui_cell " href="pt_cls_list.php">
                    <div class="weui_cell_bd weui_cell_primary">
                        <p>班级列表</p>
                    </div>
                    <div class="weui_cell_ft"></div>
                </a>
                

            </div>


        </div>

    </div>
</body>

</html>