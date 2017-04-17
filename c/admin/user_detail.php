<?php
require_once '../config.php';
require_once '../lib/fun.php';

$sql = "select * from sc_user where id=".$_REQUEST['id']." limit 1";
$q = $db->query($sql);
$rs = $q->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>千家师系统</title>
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
        <link rel="stylesheet" href="../public/style/weui.css"/>
        <link rel="stylesheet" href="../public/style/weui2.css"/>
        <link rel="stylesheet" href="../public/style/weui3.css?1"/>
        <style>
        .weui_cell_primary{
            padding-left:4px;
        }
        .weui_cells{
                margin-top: 0px !important;
        }
        </style>
</head>
<body>
        <div class="weui-header bg-blue">
            <div class="weui-header-left">
                <a href="user.php" class="icon icon-109 f-white">返回</a>
            </div>
            <h1 class="weui-header-title">详细信息</h1>
            <div class="weui-header-right">
            </div>
        </div>
<div class="weui_cells">


    <?php foreach($rs as $key=>$r){ ?>
    <div class="weui_cell">
        <div class="weui_cell_hd">
            <b><?= $key ?></b>
        </div>
                   <div class="weui_cell_bd weui_cell_primary">
                       <?= $r ?>
                   </div>
                   <div class="weui_cell_ft">
                   </div>
    </div>
    <?php } ?>
</div>




</body>
</html>