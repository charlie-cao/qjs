<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
check_login();

$sql = "select * from sc_user where openid = '".$_SESSION['user']->openid."'";
$res = $db->query($sql);
$res->setFetchMode(PDO::FETCH_OBJ);
$rs = $res->fetch();
if(isset($rs)){
	$_SESSION["user"] = $rs;
}

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
    <script>
        $(function () {

            var $form = $("#form");
            $form.form();
            $("#btn").click(function (e) {
                $form.validate(function(error){
                    if(error){
                    }else{
                        $.showLoading("更新中");
                                        $.ajax({
                                            type: 'GET',
                                            url: 'http://schoolcms.isqgame.com/api.php?m=Api&c=Index&a=updateUserInfo',
                                            dataType: 'jsonp',
                                            data:$form.serialize(),
                                            success: function (data) {
                                                $.hideLoading();
                                                $.toast("更新成功");
                                            },
                                            error: function (xhr, type) {
                                                $.hideLoading();
                                                console.log('Ajax error!');
                                            }
                                            });
                    }
                });
            })
        });
    </script>
    <style>
        .weui_cell_hd .icon {
            font-size: 24px;
            line-height: 40px;
            margin: 4px;
            color: #18b4ed;
            -webkit-transition: font-size 0.25s ease-out 0s;
            -moz-transition: font-size 0.25s ease-out 0s;
            transition: font-size 0.25s ease-out 0s;
        }
    </style>
</head>

<body ontouchstart style="background-color: #f8f8f8;">


    <div class="weui_tab tab-bottom">
        <div class="weui_tab_bd">

            <div class="weui-header bg-green">
                <div class="weui-header-left">
                <a href="zl_main.php" class="icon icon-109 f-white">返回</a>
                </div>
                <h1 class="weui-header-title">个人信息</h1>
                <div class="weui-header-right"> </div>
            </div>
<form id="form">
            <div class="weui_cells weui_cells_form">
                <div class="weui_cell">
                    <div class="weui_cell_hd"><label class="weui_label">个人备注</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" name="username" required="" tips="请输入备注名称" placeholder="比如：梁爽老师" value="<?=$_SESSION['user']->username?>" />
                        <input class="weui_input" name="openid"  type="hidden" value="<?=$_SESSION['user']->openid?>" />
                    </div>
                </div>
                <div class="weui_cell">
                    <div class="weui_cell_hd"><label class="weui_label">电话</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" name="phone" value="<?=$_SESSION['user']->phone?>"  type="tel" required="" pattern="[0-9]{11}" maxlength="11" placeholder="输入你现在的手机号" emptytips="请输入手机号" notmatchtips="请输入正确的手机号">
                    </div>
                </div>
            </div>
</form>
            <div class="weui_btn_area">
                <a class="weui_btn weui_btn_primary" href="javascript:" id="btn">更新</a>
            </div>
        </div>
    </div>
</body>

</html>