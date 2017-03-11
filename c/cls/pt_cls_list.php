<?php
require_once '../config.php';
require_once '../lib/fun.php';
check_login();

$sql = "select * from sc_user_cls where user_id=".$_SESSION['user']->id;
$res = $db->query($sql);
$my_cls = $res->fetchAll();

foreach($my_cls  as $key=>$val){
    $sql = "select * from sc_cls where id=".$val['cls_id']." limit 1";
    $res = $db->query($sql);
    $my_cls[$key]['cls'] = $res->fetch();
}
//var_dump($my_cls);
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
        function exit(id){

        }

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
                    <a href="pt_menu.php" class="icon icon-109 f-white">返回</a>
                </div>
            <h1 class="weui-header-title">班级列表</h1>
            <div class="weui-header-right"></div>
        </div>


        <div class="weui_cells" style="margin-top: 0px;">
            <div class="weui_cells_title">我加入的班级</div>
            <?php foreach($my_cls as $key=>$val ) { ?>
            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <p><?=$val['cls']['name']?></p>
                </div>
                <div class="weui_cell_ft">
                <?php if($val['cls']['id'] == $_SESSION['cls_id']){ ?>
                    <a href="pt_main.php?cls_id=<?=$val['cls']['id']?>" class="weui_btn weui_btn_mini weui_btn_warn">当前班级</a>
                <?php }else{ ?>
                    <a href="pt_main.php?cls_id=<?=$val['cls']['id']?>" class="weui_btn weui_btn_mini weui_btn_warn">进入</a>
                <?php } ?>
                    <a href="javascript:;" onclick="exit('<?=$val['cls']['id']?>')" class="weui_btn weui_btn_mini weui_btn_warn">退出</a>
                </div>
            </div>
            <?php } ?>
        </div>

    </div>
    
</div>
</body>
</html>
