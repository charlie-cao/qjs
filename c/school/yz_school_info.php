<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
check_login();

$sql = "select * from sc_school where id=0 ";
$res = $db->query($sql);
$school = $res->fetch();
//var_dump($school);
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
            //            $('.weui_tab').tab();
//            $(".weui_btn_warn").click(function (e) {
//                $.confirm("您确定要转让该校园么?转让条件。。。", "确认转让?", function () {
//                    $.toast("转让成功!");
//                }, function () {
//                    //取消操作
//                });
//            })
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


    <div class="weui_tab tab-bottom">
        <div class="weui_tab_bd">

            <div class="weui-header bg-green">
                <div class="weui-header-left"> <a href="yz_menu.php" class="icon icon-109 f-white">返回</a> </div>
                <h1 class="weui-header-title">园长管理</h1>
                <div class="weui-header-right"> </div>
            </div>

            <div class="weui_cells_title">更改校园信息</div>
            <div class="weui_cells weui_cells_form">
                <div class="weui_cell">
                    <div class="weui_cell_hd"><label class="weui_label">校园名称</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" value="<?=$school['name']?>" placeholder="校园名称" />
                    </div>
                </div>
                <div class="weui_cell">
                    <div class="weui_cell_hd"><label class="weui_label">联系电话</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" value="<?=$school['phone']?>" placeholder="联系电话" />
                    </div>
                </div>


            </div>

            <div class="weui_btn_area">
                <a class="weui_btn weui_btn_primary" href="javascript:" id="btn">更新</a>
            </div>




        </div>

    </div>
</body>

</html>