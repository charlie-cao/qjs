<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
check_login();

$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();

//var_dump($_SESSION);

$sql = "select *,sc.name as school_name,c.name as cls_name,uc.is_teacher as is_cls_teacher from sc_user_cls as uc left join sc_school as sc on uc.school_id=sc.id left join sc_cls as c on uc.cls_id=c.id  where uc.school_id=" . $_SESSION['school_id'] . " and  uc.user_id=" . $_SESSION['user']->id . " order by c.c_time desc";
$res = $db->query($sql);
$my_cls = $res->fetchAll();

//判断该用户在学校中是否为班主任
$sql = "select * from sc_user_school where school_id=" . $_SESSION['school_id'] . " and user_id=" . $_SESSION['user']->id;
$q = $db->query($sql);
$r = $q->fetch();

//var_dump($my_cls);
//exit;
//$sql = "select * from sc_user"

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
    <script src="../public/iscroll.js"></script>
    <script>
        function cannot_exit(type) {
            if(type==1){
                $.alert("您不能退出您管理的班级");
            }else{
                $.alert("您不能退出当前班级");
            }
        }

        function exit(school_id,cls_id) {
            $.actions({
                actions: [
                    {
                        text: "退出班级",
                        className: "bg-orange f-white",
                        onClick: function () {
                            d = {};
                            d.school_id = school_id;
                            d.cls_id = cls_id;
                            d.user_id = <?=$_SESSION['user']->id?>;
                            $.ajax({
                                type: 'POST',
                                data: d,
                                url: '../api/cls.php?a=exit_cls',
                                dataType: 'json',
                                success: function (data) {
                                    $.toast("退出成功");
                                    window.location.reload();
                                    //班主任不能退出自己为班主任的班级
                                    //当前所在的班级不能退出
                                },
                                error: function (xhr, type, e) {
                                    alert(type);
                                }
                            });
                        }
                    }
                ]
            });

        }

        $(function () {
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
                <?php if ($r['is_teacher']) { ?>
                    <a href="pt_add_cls.php" class="icon icon-36 f-white"> 创建班级</a>
                <?php } ?>
            </div>

        </div>


        <div class="weui_cells" style="margin-top: 0px;">
            <div class="weui_cells_title">我加入的班级</div>

            <?php foreach ($my_cls as $key => $val) { ?>
                <div class="weui_cell">
                    <div class="weui_cell_bd weui_cell_primary">
                        <p><?= $val['school_name'] ?>-<?= $val['cls_name'] ?></p>
                    </div>
                    <div class="weui_cell_ft">
                        <?php if ($val['cls_id'] == $_SESSION['cls_id']) { ?>
                            <a href="index.php?state=<?= $_SESSION['school_id'] ?>-<?= $val['cls_id'] ?>"
                               class="weui_btn weui_btn_mini weui_btn_primary">当前</a>
                        <?php } else  { ?>
                            <a href="index.php?state=<?= $_SESSION['school_id'] ?>-<?= $val['cls_id'] ?>"
                               class="weui_btn weui_btn_mini weui_btn_primary">进入</a>
                        <?php } ?>
                        <?php if($val['is_cls_teacher']==1){?>
                            <a href="javascript:;" onclick="cannot_exit(1)"  class="weui_btn weui_btn_mini weui_btn_default">退出</a>
                        <?php }else if($val['cls_id'] == $_SESSION['cls_id']){?>
                            <a href="javascript:;" onclick="cannot_exit(2)"  class="weui_btn weui_btn_mini weui_btn_default">退出</a>
                        <?php }else{ ?>
                            <a href="javascript:;"  onclick="exit(<?= $_SESSION['school_id'] ?>,<?= $val['cls_id'] ?>);"
                               class="weui_btn weui_btn_mini weui_btn_warn">退出</a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>

</div>
</body>
</html>
