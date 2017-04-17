<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";

$sql = "select * from sc_user where is_del=0 ";
$q = $db->query($sql);
$rs = $q->fetchAll();

$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();

$sql = "select * from sc_school where id=" . $_REQUEST['id'];
$q = $db->query($sql);
$school = $q->fetch();


$sql = "select * from sc_user_school where school_id=" . $_REQUEST['id']." and is_leader=1";
$q = $db->query($sql);
$leader = $q->fetch();
//var_dump($leader);
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
                    'hideAllNonBaseMenuItem'
                ]
            });

            wx.ready(function () {
                wx.hideAllNonBaseMenuItem();
            });
        });

        $(function () {
            $("#formSubmitBtn").click(function () {

                if ($.trim($("#school_name").val()) == "") {
                    alert("公共号不能为空");
                    return true;
                }
                if ($.trim($("#school_name").val()).length >12) {
                    alert("公共号最多允许12个字符");
                    return true;
                }
//                return true;

                var d = $('#form').serializeArray();
//修改学校名称和管理员
                $.ajax({
                    type: 'POST',
                    data: d,
                    url: '../api/admin.php?a=update_school',
                    dataType: 'json',
                    success: function (data) {
                        if (data.msg == "success") {
                            $.toast("成功");
                            location.href = "school.php";
                        } else {
                            $.toast("失败", "forbidden");
                        }
                    },
                    error: function (xhr, type, e) {
                        $.toast("失败", "forbidden");
                    }
                });
            })
        })
    </script>
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
        <a href="school.php" class="icon icon-109 f-white">返回</a>
    </div>
    <h1 class="weui-header-title">修改校园</h1>
    <div class="weui-header-right">
    </div>
</div>

<div class="weui_cells weui_cells_form">
    <form id="form">
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">公共号</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <input id="school_name" name="school_name" class="weui_input" placeholder="请输入公共号" value="<?=$school['name']?>">
                <input id="school_id" name="school_id" hidden="hidden" value="<?=$school['id']?>">
                <input id="old_leader_id" name="old_leader_id" hidden="hidden" value="<?=$leader['user_id']?>">

            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">园长</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <select class="weui_select" name="user_id">
                    <?php foreach ($rs as $r) { ?>
                        <option value="<?= $r['id'] ?>"<?php if($r['id']==$leader['user_id']){ echo "selected";} ?>><?= $r['nickname'] ?>:<?= $r['username'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </form>
</div>


<div class="weui_btn_area">
    <a id="formSubmitBtn" href="javascript:" class="weui_btn weui_btn_primary">提交</a>
</div>

</body>
</html>