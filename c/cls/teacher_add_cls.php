<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
check_login();
//
$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();

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
        function code(){
            return Math.floor(Math.random()*9000)+1000;
        }
        $(function () {

            $("#cls_key").val(code());

            $("#btn_new").click(function (){
                $("#cls_key").val(code());
            });



            var $form = $("#form");
            $form.form();
            $("#btn").click(function (e) {
                $form.validate(function (error) {
                    if (error) {
                    } else {
                        $.showLoading("更新中");
                        $.ajax({
                            type: 'POST',
                            url: '../api/cls.php?a=add_cls',
                            dataType: 'json',
                            data: $form.serialize(),
                            success: function (data) {
                                $.hideLoading();
                                if(data.msg=="success"){
                                    $.alert("创建班级成功,请邀请用户加入", "系统消息",function (){
                                        location.href = "teacher_send_inv.php?id="+data.id;
                                    });
                                }else{
                                    alert(data.msg);
                                }
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
</head>

<body ontouchstart style="background-color: #f8f8f8;">


<div class="weui_tab tab-bottom">
    <div class="weui_tab_bd">

        <div class="weui-header bg-green">
            <div class="weui-header-left">  <a href="#" onclick="history.go(-1)" class="icon icon-109 f-white">返回</a>  </div>
            <h1 class="weui-header-title">添加班级</h1>
            <div class="weui-header-right"> </div>
        </div>

    <form id="form">
        <div class="weui_cells_title">班级信息</div>
        <div class="weui_cells weui_cells_form">
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">班级名称</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" name="name" maxlength="12" required="" tips="请输入班级名称" placeholder="比如：小一班" />
                    <input name="user_id" type="hidden" value="<?= $_SESSION['user']->id ?>"/>
                    <input name="school_id" type="hidden" value="<?=$_SESSION['school']->id ?>"/>
                </div>
            </div>
            <div class="weui_cell weui_vcode">
                <div class="weui_cell_hd"><label class="weui_label">邀请码</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" id="cls_key" name="cls_key" required="" pattern="[0-9]{4}" maxlength="4"
                        placeholder="口令为4位纯数字" emptytips="口令为4位纯数字" notmatchtips="口令为4位纯数字">
                </div>
                <div class="weui_cell_ft">
                     <a id="btn_new" href="javascript:;" class="weui-vcode-btn">随机生成</a>
                </div>
            </div>
        </div>

        </form>
        <div class="weui_btn_area">
            <a class="weui_btn weui_btn_primary" href="javascript:;" id="btn">创建班级</a>
        </div>
    </div>
</div>
</body>
</html>
