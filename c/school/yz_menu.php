<?php
require_once '../config.php';
require_once '../lib/fun.php';
check_login();
//var_dump($_SESSION['user']);
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
            var $form = $("#form");
            $form.form();
            $("#btn").click(function (e) {
                $form.validate(function (error) {
                    if (error) {
                    } else {
                        $.showLoading("更新中");
                        $.ajax({
                            type: 'GET',
                            url: 'http://schoolcms.isqgame.com/api.php?m=Api&c=Index&a=updateUserInfo',
                            dataType: 'jsonp',
                            data: $form.serialize(),
                            success: function (data) {
                                $.hideLoading();

                                $.alert("更新成功,请重新登录", "系统消息",function (){
                                    location.href = "index.php?state=<?=$_SESSION['state']?>";
                                });

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
<div class="weui-header bg-green">
    <div class="weui-header-left"><a href="yz_main.php" class="icon icon-109 f-white">返回</a></div>
    <h1 class="weui-header-title">园长管理</h1>
    <div class="weui-header-right"></div>
</div>
<div>
    <div class="weui_cells_title">个人信息</div>
    <form id="form">
        <div class="weui_cells weui_cells_form">
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">备注名称</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" name="username" required="" tips="请输入备注名称" placeholder="比如：梁爽园长"
                           value="<?= $_SESSION['user']->username ?>"/>
                    <input class="weui_input" name="id" type="hidden" value="<?= $_SESSION['user']->id ?>"/>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">电话</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" name="phone" value="<?= $_SESSION['user']->phone ?>" type="tel"
                           required="" pattern="[0-9]{11}" maxlength="11" placeholder="输入你现在的手机号" emptytips="请输入手机号"
                           notmatchtips="请输入正确的手机号">
                </div>
            </div>
            <!--div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">身份</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" disabled="true" value="<?= $_SESSION['user']->state ?>"/>
                </div>
            </div-->
        </div>
    </form>
    <div class="weui_btn_area">
        <a class="weui_btn weui_btn_primary" href="javascript:" id="btn">更新</a>
    </div>
    <div class="weui_cells_title">园区管理</div>
    <div class="weui_cells weui_cells_access">
        <a class="weui_cell" href="./yz_user_list.php">
            <div class="weui_cell_hd"><span class="icon icon-100"></span></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p>人员管理</p>
            </div>
            <div class="weui_cell_ft"></div>
        </a>
        <a class="weui_cell" href="./yz_school_info.php">
            <div class="weui_cell_hd"><span class="icon icon-29"></span></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p>校园管理</p>
            </div>
            <div class="weui_cell_ft"></div>
        </a>
    </div>
</body>
</html>