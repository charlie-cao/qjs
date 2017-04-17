<?php
require_once '../config.php';
require_once '../lib/fun.php';
//check_login();

$sql = "select * from sc_cls where school_id=" . $_SESSION['school_id']." order by c_time desc";
$res = $db->query($sql);
$cls = $res->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="../public/style/weui.css"/>
    <link rel="stylesheet" href="../public/style/weui2.css"/>
    <link rel="stylesheet" href="../public/style/weui3.css?1"/>
    <script src="../public/zepto.min.js"></script>
</head>

<body ontouchstart style="background-color: #f8f8f8;">


<div class="weui_tab tab-bottom">
    <div class="weui_tab_bd">

        <div class="weui-header bg-green">
            <div class="weui-header-left">

            </div>
            <h1 class="weui-header-title">本校班级</h1>
            <div class="weui-header-right"></div>
        </div>


        <div class="weui_cells" style="margin-top: 0px;">
            <div class="weui_cells_title">请选择班级</div>
            <?php foreach ($cls as $key => $val) { ?>
                <div class="weui_cell">
                    <div class="weui_cell_bd weui_cell_primary">
                        <p><?= $val['name'] ?></p>
                    </div>
                    <div class="weui_cell_ft">
                            <a href="pt_enter_cls.php?state=<?=$_SESSION['school_id']?>-<?= $val['id'] ?>"
                               class="weui_btn weui_btn_mini weui_btn_warn">进入</a>
                    </div>
                </div>
            <?php } ?>
            <?php if(count($cls)==0){
                ?>
                <div class="weui_cell">
                    <div class="weui_cell_bd weui_cell_primary">
                        <p>还没有班级</p>
                    </div>
                    <div class="weui_cell_ft">
                    </div>
                </div>
            <?php
            }?>
        </div>

    </div>

</div>
</body>
</html>
