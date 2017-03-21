<?php
require_once '../config.php';
require_once '../lib/fun.php';
check_login();
$sql = "select * from sc_user_cls where cls_id=".$_SESSION['cls_id'];
$res = $db->query($sql);

$cls_users = $res->fetchAll();

foreach($cls_users as $key=>$val){
    $sql = "select * from sc_user where id=".$val['user_id'];
    $res = $db->query($sql);
    $user = $res->fetch();
    $user['is_now_cls_teacher'] = $val['is_teacher'];
    $cls_users[$key] = $user;
}

//var_dump($cls_users);

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
            $(".weui_btn_warn").click(function (i,item){
                var user_id = $(this).data('id');
                $.confirm("您确定要转让班级吗?", "确认转让?", function() {
                $.ajax({
                                            type: 'POST',
                                            url: '../api/cls.php?a=change_teacher',
                                            dataType: 'json',
                                            data: {'user_id':user_id,'cls_id':'<?=$_SESSION['cls']->id?>'},
                                            success: function (data) {
                                                $.hideLoading();
                                                if(data.msg=="success"){
                                                    $.alert("转让班级成功,稍后将重新登录", "系统消息",function (){
                                                        location.href = "index.php?state=<?=$_SESSION['state']?>";
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



                        }, function() {
                          //取消操作
                        });




                // 班主任转让的时候更新当前用户为当前班主任，原班主任为普通用户
            });
//            $('.weui_tab').tab();
        });
    </script>
</head>

<body ontouchstart style="background-color: #f8f8f8;">
        <div class="weui-header bg-green">
                <div class="weui-header-left">
                    <a href="teacher_menu.php" class="icon icon-109 f-white">返回</a>
                </div>
            <h1 class="weui-header-title">成员列表</h1>
            <div class="weui-header-right"> </div>
        </div>


        <div class="weui_cells" style="margin-top: 0px;">
            <div class="weui_cells_title">成员</div>
            <?php foreach($cls_users as $key=>$val){?>
            <div class="weui_cell">
                <div class="weui_cell_hd">
                    <img src="<?=$val['headimgurl']?>" style="width:20px;margin-right:5px;display:block">
                </div>
                <div class="weui_cell_bd weui_cell_primary">
                    <p><?=$val['username']?> </p>
                </div>
                <div class="weui_cell_ft">
                    <?php if($val['is_now_cls_teacher']==1 && $val['is_teacher']==1){ ?>
                        本班班主任
                    <?php } else if($val['is_teacher']==1) { ?>
                                        <div class="weui_cell_ft">
                                            <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_warn" data-id="<?= $val['id']?>">转让</a>
                                        </div>
                    <?php  }else{ ?>
                        家长
                    <?php }?>
                </div>
            </div>
            <?php } ?>


        </div>
</body>
</html>
