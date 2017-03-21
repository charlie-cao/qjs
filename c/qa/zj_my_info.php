<?php
require_once '../config.php';
require_once '../lib/fun.php';
//var_dump($_SESSION);
check_login();

$sql = "SELECT count(*) as c FROM `sc_question` WHERE `answer_user_id` = ".$_SESSION['user']->id;
$res = $db->query($sql);
$re = $res->fetch();
$money_count = $re['c'];

$sql = "SELECT count(*) as c FROM `sc_question` WHERE answer_content is not null and `answer_user_id` = ".$_SESSION['user']->id;
$res = $db->query($sql);
$re = $res->fetch();
$answer_count = $re['c'];


$sql = "SELECT sum(play_num) as c FROM `sc_question` WHERE answer_content is not null and `answer_user_id` = ".$_SESSION['user']->id;
$res = $db->query($sql);
$re = $res->fetch();
$play_count = $re['c'];

$sql = "SELECT sum(up_num) as c FROM `sc_question` WHERE answer_content is not null and `answer_user_id` = ".$_SESSION['user']->id;
$res = $db->query($sql);
$re = $res->fetch();
$up_num_count = $re['c'];

var_dump($money_count);
var_dump($answer_count);
var_dump($play_count);
var_dump($up_num_count);


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
        .weui_grid_icon2 {
            text-align: center;
        }
    </style>


</head>

<body ontouchstart="" style="background-color: #f8f8f8;">
            <div class="weui-header bg-green">
                <div class="weui-header-left"> <a href="zj_main.php" class="icon icon-109 f-white">返回</a> </div>
                <h1 class="weui-header-title">个人面板</h1>
                <div class="weui-header-right"> </div>
            </div>

     <div class="weui_cells_title">个人</div>
     <div class="weui_cells weui_cells_access">
         <a class="weui_cell" href="./zj_change_info.php">
             <div class="weui_cell_hd"></div>
             <div class="weui_cell_bd weui_cell_primary">
                 <p>个人信息</p>
             </div>
             <div class="weui_cell_ft"></div>
         </a>
     </div>



    </div>
     <div class="weui_cells_title">统计</div>
            <div class="weui_grids grids-small" style="background-color: #fff;">
            <a href="javascript:;" class="grid">
                <div class="weui_grid_icon2">
                    <?=$money_count?>
                </div>
                <p class="weui_grid_label">
                    总收入
                </p>
            </a>
            <a href="javascript:;" class="grid">
                <div class="weui_grid_icon2">
                    <?=$answer_count?>
                </div>
                <p class="weui_grid_label">
                    回答次数
                </p>
            </a>
            <a href="javascript:;" class="grid">
                <div class="weui_grid_icon2">
                    <?=$play_count?>
                </div>
                <p class="weui_grid_label">
                    被偷听次数
                </p>
            </a>
            <a href="javascript:;" class="grid">
                <div class="weui_grid_icon2">
                    <?=$up_num_count?>
                </div>
                <p class="weui_grid_label">
                    被点赞次数
                </p>
            </a>

        </div>





</body>

</html>