<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
check_login();

$sql = "select * from sc_user  ";
$res = $db->query($sql);
$res->setFetchMode(PDO::FETCH_OBJ);
$user = $res->fetchAll();

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
            $(".cancelA").click(function (e) {
            var uid = $(this).data('id');
                $.confirm("您确定要取消该用户权限吗?", "确认取消?", function() {
$.showLoading("执行中");
                                                                    $.ajax({
                                                                        type: 'GET',
                                                                        url: 'http://schoolcms.isqgame.com/api.php?m=Api&c=Index&a=cancelAssistant',
                                                                        dataType: 'jsonp',
                                                                        data:{'id':uid},
                                                                        success: function (data) {
                                                                            $.hideLoading();
                                                                            $.toast("成功");
                                                                            location.reload()
                                                                        },
                                                                        error: function (xhr, type) {
                                                                            $.hideLoading();
                                                                            console.log('Ajax error!');
                                                                        }
                                                                        });
                }, function() {
                    //取消操作
                });
            })

            $(".cancelT").click(function (e) {
            var uid = $(this).data('id');
                $.confirm("您确定要取消该用户权限吗?", "确认取消?", function() {
$.showLoading("执行中");
                                                                    $.ajax({
                                                                        type: 'GET',
                                                                        url: 'http://schoolcms.isqgame.com/api.php?m=Api&c=Index&a=cancelTeacher',
                                                                        dataType: 'jsonp',
                                                                        data:{'id':uid},
                                                                        success: function (data) {
                                                                            $.hideLoading();
                                                                            $.toast("成功");
                                                                            location.reload()
                                                                        },
                                                                        error: function (xhr, type) {
                                                                            $.hideLoading();
                                                                            console.log('Ajax error!');
                                                                        }
                                                                        });
                }, function() {
                    //取消操作
                });
            })


            $(".weui_btn_primary").click(function (e) {
                var uid = $(this).data('id');
                $.modal({
                    title: "任命人员",
                    text: "助理可以在大家庭中发布内容，班主任可以创建和管理班级。",
                    buttons: [
                        { text: "助理", onClick: function(){

                                $.showLoading("任命中");
                                                                    $.ajax({
                                                                        type: 'GET',
                                                                        url: 'http://schoolcms.isqgame.com/api.php?m=Api&c=Index&a=setAssistant',
                                                                        dataType: 'jsonp',
                                                                        data:{'id':uid},
                                                                        success: function (data) {
                                                                            $.hideLoading();
                                                                            $.toast("任命成功");
                                                                            location.reload()
                                                                        },
                                                                        error: function (xhr, type) {
                                                                            $.hideLoading();
                                                                            console.log('Ajax error!');
                                                                        }
                                                                        });

                        } },
                        { text: "班主任", onClick: function(){
                                $.showLoading("任命中");
                                                                    $.ajax({
                                                                        type: 'GET',
                                                                        url: 'http://schoolcms.isqgame.com/api.php?m=Api&c=Index&a=setTeacher',
                                                                        dataType: 'jsonp',
                                                                        data:{'id':uid},
                                                                        success: function (data) {
                                                                            $.hideLoading();
                                                                            $.toast("任命成功");
                                                                            location.reload()
                                                                        },
                                                                        error: function (xhr, type) {
                                                                            $.hideLoading();
                                                                            console.log('Ajax error!');
                                                                        }
                                                                        });

                        } },
                        { text: "取消", className: "default"},
                    ]
                });
            })
        });

    </script>
</head>

<body ontouchstart style="background-color: #f8f8f8;">

<div class="weui-header bg-green">
    <div class="weui-header-left"> <a href="yz_menu.php" class="icon icon-109 f-white">返回</a>  </div>
    <h1 class="weui-header-title">人事任命</h1>
    <div class="weui-header-right"></div>
</div>

<div class="weui_cells">
    <div class="weui_cells_title">助理</div>
    <?php
        if(count($user)>0){
            foreach($user as $u){
            if($u->state==1){
    ?>
    <div class="weui_cell">
        <div class="weui_cell_hd"><img
                src="<?=$u->headimgurl?>"
                alt="" style="width:20px;margin-right:5px;display:block"></div>
        <div class="weui_cell_bd weui_cell_primary">
            <p><?=$u->username?></p>
        </div>
        <div class="weui_cell_ft">
            <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_warn cancelA" data-id="<?=$u->id?>">取消</a>
        </div>
    </div>
        <?php
                    }
                }//end foreach
            }//end if
        ?>

    <div class="weui_cells_title">班主任</div>
    <?php
        if(count($user)>0){
            foreach($user as $u){
            if($u->is_teacher==1){
    ?>
    <div class="weui_cell">
        <div class="weui_cell_hd"><img
                src="<?=$u->headimgurl?>"
                alt="" style="width:20px;margin-right:5px;display:block"></div>
        <div class="weui_cell_bd weui_cell_primary">
            <p><?=$u->username?></p>
        </div>
        <div class="weui_cell_ft">
            <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_warn cancelT" data-id="<?=$u->id?>">取消</a>
        </div>
    </div>
        <?php
                    }
                }//end foreach
            }//end if
        ?>

</div>

<div class="weui_cells">
    <div class="weui_cells_title">所有用户</div>
    <?php
        if(count($user)>0){
            foreach($user as $u){
    ?>
    <div class="weui_cell">
        <div class="weui_cell_hd"><img
                src="<?=$u->headimgurl?>"
                alt="" style="width:20px;margin-right:5px;display:block"></div>
        <div class="weui_cell_bd weui_cell_primary">
            <p><?=$u->nickname?></p>
        </div>
        <div class="weui_cell_ft">
            <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_primary" data-id="<?=$u->id?>">任命</a>
        </div>
    </div>
    <?php
            }//end foreach
        }//end if
    ?>
</div>



</body>
</html>
