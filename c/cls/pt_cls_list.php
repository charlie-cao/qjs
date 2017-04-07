<?php
require_once '../config.php';
require_once '../lib/fun.php';
check_login();

//var_dump($_SESSION);

$sql = "select *,sc.name as school_name,c.name as cls_name from sc_user_cls as uc left join sc_school as sc on uc.school_id=sc.id left join sc_cls as c on uc.cls_id=c.id  where uc.school_id=".$_SESSION['school_id']." and  uc.user_id=".$_SESSION['user']->id;
$res = $db->query($sql);
$my_cls = $res->fetchAll();

//判断该用户在学校中是否为班主任
$sql = "select * from sc_user_school where school_id=".$_SESSION['school_id']." and user_id=".$_SESSION['user']->id;
$q = $db->query($sql);
$r = $q->fetch();


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
            <div class="weui-header-right">
                <?php if($r['is_teacher']) { ?>
                <a href="pt_add_cls.php" class="icon icon-36 f-white"> 创建班级</a>
                <?php } ?>
            </div>

        </div>


        <div class="weui_cells" style="margin-top: 0px;">
            <div class="weui_cells_title">我加入的班级</div>
            <?php foreach($my_cls as $key=>$val ) { ?>
                <div class="weui_cell">
                    <div class="weui_cell_bd weui_cell_primary">
                        <p><?=$val['school_name']?>-<?=$val['cls_name']?></p>
                    </div>
                    <div class="weui_cell_ft">
                        <?php if($val['cls_id'] == $_SESSION['cls_id']){ ?>
                            <a href="index.php?state=<?=$_SESSION['school_id']?>-<?=$val['cls_id']?>" class="weui_btn weui_btn_mini weui_btn_warn">当前班级</a>
                        <?php }else{ ?>
                            <a href="index.php?state=<?=$_SESSION['school_id']?>-<?=$val['cls_id']?>" class="weui_btn weui_btn_mini weui_btn_warn">进入</a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
    
</div>
</body>
</html>
