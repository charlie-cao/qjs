<?php
require_once '../config.php';
require_once '../lib/fun.php';
check_login();
$sql = "select * from sc_question where answer_user_id = ".$_SESSION['user']->id." and is_del=0 order by c_time desc";
$res = $db->query($sql);
$questions = $res->fetchAll();
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
    <script src="../public/updown.js"></script>
    <script src="../public/lazyimg.js"></script>
    <style>
        .paragraphExtender {
            background-color: #35C535;
            color: white;
            padding: 4px;
            width: 70%;
            float: left;
            border-radius: 20px;
            padding-left: 20px;
            line-height: 22px;
        }
    </style>


    <style>

        
        .weui_cells:before {
            top: 0;
            border-top: 0px solid #d9d9d9;
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
        }
    </style>


</head>

<body ontouchstart="" style="background-color: #f8f8f8;">

<div class="weui-header bg-green">
    <div class="weui-header-left"> <a href="zj_main.php" class="icon icon-109 f-white">返回</a>  </div>
    <h1 class="weui-header-title">家长提问</h1>
    <div class="weui-header-right"></div>
</div>
    <div class="weui_cells">
        <?php foreach($questions as $q){?>
        <div class="weui_cell">
            <div class="weui_cell_hd"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC4AAAAuCAMAAABgZ9sFAAAAVFBMVEXx8fHMzMzr6+vn5+fv7+/t7e3d3d2+vr7W1tbHx8eysrKdnZ3p6enk5OTR0dG7u7u3t7ejo6PY2Njh4eHf39/T09PExMSvr6+goKCqqqqnp6e4uLgcLY/OAAAAnklEQVRIx+3RSRLDIAxE0QYhAbGZPNu5/z0zrXHiqiz5W72FqhqtVuuXAl3iOV7iPV/iSsAqZa9BS7YOmMXnNNX4TWGxRMn3R6SxRNgy0bzXOW8EBO8SAClsPdB3psqlvG+Lw7ONXg/pTld52BjgSSkA3PV2OOemjIDcZQWgVvONw60q7sIpR38EnHPSMDQ4MjDjLPozhAkGrVbr/z0ANjAF4AcbXmYAAAAASUVORK5CYII=" alt="" style="width:20px;margin-right:5px;display:block"></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p><?= $q['question_content']?><?= formatTime($q['c_time'])?></p>
            </div>
            <div class="weui_cell_ft"><a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_primary">回答</a></div>
        </div>
        <?php } ?>
        <?php if(count($questions)==0) {?>
        <div class="weui_cell" >
            还没有问题
        </div>
        <?php } ?>
    </div>
</body>

</html>