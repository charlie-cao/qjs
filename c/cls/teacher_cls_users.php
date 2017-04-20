<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
check_login();
//
$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();

$sql = "select * from sc_user_cls as c left join sc_user as u on c.user_id=u.id where cls_id=" . $_SESSION['cls']->id." order by c_time desc ";
$res = $db->query($sql);
$cls_users = $res->fetchAll();
//var_dump($cls_users);
//在学校表中判定是否为校园老师

foreach ($cls_users as $key => $user) {
    $sql = "select * from sc_user_school where school_id=" . $_SESSION['school_id'] . " and user_id=" . $user['id'];
    $res = $db->query($sql);
    $school_user_info = $res->fetch();
    $cls_users[$key]['school_teacher'] = $school_user_info['is_teacher'];
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
    <link rel="stylesheet" href="../public/style/weui3.css?1"/>
    <script src="../public/zepto.min.js"></script>
    <script>
        $(function () {
            $(".change").click(function (i, item) {
                var user_id = $(this).data('id');
                $.confirm("您确定要转让班级吗?", "确认转让?", function () {
                    $.ajax({
                        type: 'POST',
                        url: '../api/cls.php?a=change_teacher',
                        dataType: 'json',
                        data: {'user_id': user_id, 'cls_id': '<?=$_SESSION['cls']->id?>'},
                        success: function (data) {
                            $.hideLoading();
                            if (data.msg == "success") {
                                $.alert("转让班级成功,稍后将重新登录", "系统消息", function () {
                                    location.href = "index.php?state=<?=$_SESSION['state']?>";
                                });
                            } else {
                                alert(data.msg);
                            }
                        },
                        error: function (xhr, type) {
                            $.hideLoading();
                            console.log('Ajax error!');
                        }
                    });


                }, function () {
                    //取消操作
                });


                // 班主任转让的时候更新当前用户为当前班主任，原班主任为普通用户
            });
            $(".delete").click(function (i, item) {
                var user_id = $(this).data('id');
                $.confirm("您确定要删除该用户么?", "确认删除?", function () {
                    $.ajax({
                        type: 'POST',
                        url: '../api/cls.php?a=del_user',
                        dataType: 'json',
                        data: {'user_id': user_id, 'cls_id': '<?=$_SESSION['cls']->id?>'},
                        success: function (data) {
                            $.hideLoading();
                            if (data.msg == "success") {
                                $.alert("删除用户成功", "系统消息", function () {
                                    location.reload();
                                });
                            } else {
                                alert(data.msg);
                            }
                        },
                        error: function (xhr, type) {
                            $.hideLoading();
                            console.log('Ajax error!');
                        }
                    });


                }, function () {
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
    <div class="weui-header-right"></div>
</div>


<div class="weui_cells" style="margin-top: 0px;">
    <div class="weui_cells_title">成员</div>
    <?php foreach ($cls_users as $key => $val) { ?>
        <div class="weui_cell">
            <div class="weui_cell_hd">
                <img src="<?= $val['headimgurl'] ?>" style="width:20px;margin-right:5px;display:block">
            </div>
            <div class="weui_cell_bd weui_cell_primary">
                <p><?= $val['username'] ?>
                    <?php if ($val['is_teacher'] == 1) { ?>
                        <span>班主任</span>
                    <?php } else if ($val['school_teacher'] == true) { ?>
                        <span>本校老师</span>
                    <?php } else { ?>
                        <span>家长</span>
                    <?php } ?>
                </p>
            </div>
            <div class="weui_cell_ft">
                <?php if ($val['is_teacher'] == 1) { ?>

                <?php } else if ($val['school_teacher'] == true) { ?>
                    <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_warn change"
                       data-id="<?= $val['id'] ?>">转让</a>
                    <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_warn delete"
                       data-id="<?= $val['id'] ?>">删除</a>
                <?php } else { ?>
                    <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_warn delete"
                       data-id="<?= $val['id'] ?>">删除</a>
                <?php } ?>
            </div>
        </div>
    <?php } ?>


</div>
</body>
</html>
