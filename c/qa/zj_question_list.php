<?php
require_once '../config.php';
require_once '../lib/fun.php';
check_login();
$sql = "select * from sc_question where answer_user_id = ".$_SESSION['user']->id." and is_del=0 order by c_time desc";
$res = $db->query($sql);
$questions = $res->fetchAll();

    foreach ($questions as $key => $q) {
        $sql_u = "select * from sc_user where id=" . $q['question_user_id'] . "   ";
        $u_res = $db->query($sql_u);
        $questions[$key]['question_user'] = $u_res->fetchAll();

        $sql_u = "select * from sc_user where id=" . $q['answer_user_id'] . "   ";
        $u_res = $db->query($sql_u);
        $questions[$key]['answer_user'] = $u_res->fetchAll();

        $questions[$key]['c_time'] = formatTime($questions[$key]['c_time']);
    }
//    var_dump($questions);
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
            <div class="weui_cell_hd">
            <img src="<?=$q['question_user'][0]['headimgurl']?>" alt="" style="width:20px;margin-right:5px;display:block"></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p><?= $q['question_content']?></p>
            </div>
            <div class="weui_cell_ft">
            <?php if($q['answer_content']==""){ ?>
                <a href="zj_send_answer.php?id=<?= $q['id']?>&user_id=<?= $q['question_user_id']?>" class="weui_btn weui_btn_mini weui_btn_primary">回答</a>
            <?php }else{ ?>
                已回答
                            <?php }?>
            </div>
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