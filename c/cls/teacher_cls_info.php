<?php
require_once '../config.php';
require_once '../lib/fun.php';
check_login();
$sql = "select * from sc_cls where id=".$_SESSION['cls_id'];
$res = $db->query($sql);
$cls = $res->fetch();
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="../public/style/weui.css" />
    <link rel="stylesheet" href="../public/style/weui2.css" />
    <link rel="stylesheet" href="../public/style/weui3.css" />
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
                            type: 'POST',
                            url: '../api/cls.php?a=update_cls_info',
                            dataType: 'json',
                            data: $form.serialize(),
                            success: function (data) {
                                $.hideLoading();
                                if(data.msg=="success"){
                                    $.alert("更新班级信息成功");
//                                    $.alert("更新成功,稍后将重新登录", "系统消息",function (){
//                                        location.href = "index.php?state=<?=$_SESSION['state']?>";
//                                    });
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


            <div class="weui-header bg-green">
                <div class="weui-header-left"> <a href="teacher_menu.php" class="icon icon-109 f-white">返回</a> </div>
                <h1 class="weui-header-title">班级信息</h1>
                <div class="weui-header-right"> </div>
            </div>




    <form id="form">
        <div class="weui_cells weui_cells_form">
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">班级名称</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" name="name" required="" tips="请输入班级名称" placeholder="比如：小一班" value="<?= $cls['name'] ?>"/>
                    <input class="weui_input" name="id" type="hidden" value="<?= $cls['id'] ?>"/>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">班级口令</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" name="cls_key" value="<?= $cls['cls_key'] ?>"
                        required="" pattern="[0-9]{4}" maxlength="4"
                        placeholder="口令为4位纯数字" emptytips="口令为4位纯数字" notmatchtips="口令为4位纯数字">
                </div>
            </div>
        </div>
    </form>
    <div class="weui_btn_area">
        <a class="weui_btn weui_btn_primary" href="javascript:" id="btn">更新班级信息</a>
    </div>

</body>

</html>