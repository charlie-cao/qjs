<?php
require_once '../config.php';
require_once '../lib/fun.php';
check_login();
$sql = "select * from sc_user_cls where cls_id=".$_SESSION['cls_id'];
$res = $db->query($sql);

$cls_users = $res->fetchAll();

foreach($cls_users as $key=>$val){
    $sql = "select * from sc_user where id=".$val['user_id'];
    $res = $db->query($sql);
    $user = $res->fetch();
    $user['is_teacher'] = $val['is_teacher'];
    $cls_users[$key] = $user;
}

//var_dump($cls_users);

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
            <h1 class="weui-header-title">成员列表</h1>
            <div class="weui-header-right"> </div>
        </div>


        <div class="weui_cells" style="margin-top: 0px;">
            <div class="weui_cells_title">成员</div>
            <?php foreach($cls_users as $key=>$val){?>
            <div class="weui_cell">
                <div class="weui_cell_hd">
                    <img src="<?=$val['headimgurl']?>" style="width:20px;margin-right:5px;display:block">
                </div>
                <div class="weui_cell_bd weui_cell_primary">
                    <p><?=$val['username']?> </p>
                </div>
                <div class="weui_cell_ft">
                    <?=$val['is_teacher']?"班主任":"普通成员"?>
                </div>
            </div>
            <?php } ?>


        </div>
    </div>
</div>
</body>
</html>
