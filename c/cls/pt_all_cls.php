<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
check_login();
//
$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();

$sql = "select *,sc.name as school_name,c.name as cls_name,uc.is_teacher as is_cls_teacher from sc_user_cls as uc left join sc_school as sc on uc.school_id=sc.id left join sc_cls as c on uc.cls_id=c.id  where uc.school_id=" . $_SESSION['school_id'] . " and  uc.user_id=" . $_SESSION['user']->id . " order by c.c_time desc";
$res = $db->query($sql);
$my_cls = $res->fetchAll();

$sql = "select * from sc_cls  where school_id=" . $_SESSION['school_id'] . " order by c_time desc";
$res = $db->query($sql);
$school_cls = $res->fetchAll();

$my_cls_ids = array();
foreach ($my_cls as $cls) {
    $my_cls_ids[] = $cls['id'];
}

//exit;
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
    <script src="../public/jweixin-1.2.0.js"></script>
    <script>
        $(function () {
            wx.config({
                debug: false,
                appId: '<?= $signPackage["appId"]; ?>',
                timestamp: <?= $signPackage["timestamp"]; ?>,
                nonceStr: '<?= $signPackage["nonceStr"]; ?>',
                signature: '<?= $signPackage["signature"]; ?>',
                jsApiList: [
                    'checkJsApi',
                    'onMenuShareAppMessage',
                    'onMenuShareTimeline',
                    'hideAllNonBaseMenuItem',
                    'showMenuItems'
                ]
            });
            wx.ready(function () {
                wx.hideAllNonBaseMenuItem();
            });
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
            <h1 class="weui-header-title">其他班级</h1>
            <div class="weui-header-right"></div>
        </div>


        <div class="weui_cells" style="margin-top: 0px;">
            <div class="weui_cells_title">本校未加入的班级</div>
            <?php foreach ($school_cls as $key => $val) {
                if(!in_array($val['id'],$my_cls_ids)){
                    ?>
                    <div class="weui_cell">
                        <div class="weui_cell_bd weui_cell_primary">
                            <p><?= $_SESSION['school']->name ?>-<?= $val['name'] ?> </p>
                        </div>
                        <div class="weui_cell_ft">
                            <a href="<?= $server_host ?>/c/cls/pt_enter_cls.php?state=<?= $_SESSION['school_id'] ?>-<?= $val['id'] ?>"
                               class="weui_btn weui_btn_mini weui_btn_primary">进入</a>
                        </div>
                    </div>
                <?php }
            } ?>
        </div>
    </div>
</div>
</body>
</html>
