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
        });
    </script>
</head>

<body ontouchstart style="background-color: #f8f8f8;">


<div class="weui_tab tab-bottom">
    <div class="weui_tab_bd">

        <div class="weui-header bg-green">
                <div class="weui-header-left">
                    <a href="teacher_menu.php" class="icon icon-109 f-white">返回</a>
                </div>
            <h1 class="weui-header-title">班级管理</h1>
            <div class="weui-header-right"><a href="cls_2_11.php" class="icon icon-36 f-white"> 创建班级</a></div>
        </div>


        <div class="weui_cells" style="margin-top: 0px;">
            <div class="weui_cells_title">我创建的班级</div>

            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>小一班级</p>
                </div>
                <div class="weui_cell_ft">
                    <a href="cls_2_2.php" class="weui_btn weui_btn_mini weui_btn_warn">进入</a>
                    <a href="cls_2_3.php" class="weui_btn weui_btn_mini weui_btn_warn">管理</a>
                    <a href="#" class="weui_btn weui_btn_mini weui_btn_warn">删除</a>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>小二班级 <label class="weui-label-s">当前班级</label></p>
                </div>
                <div class="weui_cell_ft">
                    
                    <a href="cls_2_3.php" class="weui_btn weui_btn_mini weui_btn_warn">管理</a>
                    <a href="#" class="weui_btn weui_btn_mini weui_btn_warn">删除</a>
                </div>
            </div>
        </div>
        <div class="weui_cells" style="margin-top: 0px;">
            <div class="weui_cells_title">我加入的班级</div>

            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>小三班级</p>
                </div>
                <div class="weui_cell_ft">
                    <a href="cls_1_2.php" class="weui_btn weui_btn_mini weui_btn_warn">进入</a>
                    <a href="#" class="weui_btn weui_btn_mini weui_btn_warn">退出</a>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>小四班级</p>
                    
                </div>
                <div class="weui_cell_ft">
                    <a href="cls_1_2.php" class="weui_btn weui_btn_mini weui_btn_warn">进入</a>
                    <a href="#" class="weui_btn weui_btn_mini weui_btn_warn">退出</a>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>小五班级</p>
                </div>
                <div class="weui_cell_ft">
                    <a href="cls_1_2.php" class="weui_btn weui_btn_mini weui_btn_warn">进入</a>
                    <a href="#" class="weui_btn weui_btn_mini weui_btn_warn">退出</a>
                </div>
            </div>
        </div>
        <div>备注 园长可以管理和删除所有班级</div>
    </div>
    
</div>
</body>
</html>
