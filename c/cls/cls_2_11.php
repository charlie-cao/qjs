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
            <div class="weui-header-left">  <a href="cls_2_10.php" class="icon icon-109 f-white">返回</a>  </div>
            <h1 class="weui-header-title">添加班级</h1>
            <div class="weui-header-right"> </div>
        </div>

        <div class="weui_cells_title">班级信息</div>
        <div class="weui_cells weui_cells_form">
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">班级名称</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="number" pattern="[0-9]*" placeholder="请输入班级名称"/>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">邀请码</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="number" pattern="[0-9]*" placeholder="请设定邀请码"/>
                </div>
            </div>






        </div>

        <div class="weui_btn_area">
            <a class="weui_btn weui_btn_primary" href="cls_2_12.php" id="btn">创建</a>
        </div>
    </div>
    
</div>
</body>
</html>
