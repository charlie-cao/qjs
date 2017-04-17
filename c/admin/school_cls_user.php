<?php
require_once '../config.php';
require_once '../lib/fun.php';

$sql = "select * from sc_user_cls as us left join sc_user as u on us.user_id=u.id where us.cls_id=" . $_REQUEST['id'];
$q = $db->query($sql);
$rs = $q->fetchAll();
//var_dump($rs);
$sql = "select * from sc_cls where id=" . $_REQUEST['id'] . "";
$q = $db->query($sql);
$cls = $q->fetch();
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
        .weui_cell_primary {
            padding-left: 4px;
        }

        .weui_cells {
            margin-top: 0px !important;
        }
    </style>
</head>
<body>
<div class="weui-header bg-blue">
    <div class="weui-header-left">
        <a href="school_cls.php?id=<?=$_REQUEST['school_id']?>" class="icon icon-109 f-white">返回</a>
    </div>
    <h1 class="weui-header-title"><?=$cls['name']?> 班成员</h1>
    <div class="weui-header-right">
    </div>
</div>
<div class="weui_cells">


    <?php foreach ($rs as $r) { ?>
        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary">
                <?= $r['username'] ?> : <?= $r['nickname'] ?>

            </div>
            <div class="weui_cell_ft">
                <?= $r['is_teacher'] ? "班主任" : "成员" ?>
            </div>
        </div>
    <?php } ?>
    <?php if (count($rs) == 0) { ?>
        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary">
                还没有成员
            </div>
        </div>
    <?php } ?>


</div>


</body>
</html>