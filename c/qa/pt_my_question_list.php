<?php
require_once '../config.php';
require_once '../lib/fun.php';
check_login();
$sql = "select * from sc_question where question_user_id = ".$_SESSION['user']->id." and is_del=0 order by c_time desc";
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
    <div class="weui-header-left"> <a href="pt_main.php" class="icon icon-109 f-white">返回</a>  </div>
    <h1 class="weui-header-title">我的提问</h1>
    <div class="weui-header-right"></div>
</div>
    

    <div class="weui_cells">
        <?php foreach($questions as $q){?>
        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary">
                <p>问题：<?= $q['question_content']?></p>
            </div>

            <div class="weui_cell_ft"><?= formatTime($q['c_time'])?></div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary">
                <p>回答：<?= $q['answer_content']?></p>
            </div>

            <div class="weui_cell_ft">
            <?= formatTime($q['answer_time'])?>
            </div>
        </div>
        <?php } ?>
    </div>


</body>

</html>