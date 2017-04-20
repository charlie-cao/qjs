<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
check_login();

$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();

$sql = "select * from sc_user_school as s left join sc_user as u on s.user_id=u.id where s.is_leader!=true and s.school_id=" . $_SESSION['school_id']." order by u.add_time desc";
$res = $db->query($sql);
$res->setFetchMode(PDO::FETCH_OBJ);
$user = $res->fetchAll();

//var_dump($user);
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
        wx.config({
            debug: false,
            appId: '<?= $signPackage["appId"]; ?>',
            timestamp: <?= $signPackage["timestamp"];?>,
            nonceStr: '<?= $signPackage["nonceStr"]; ?>',
            signature: '<?= $signPackage["signature"]; ?>',
            jsApiList: [
                'checkJsApi',
                'chooseImage',
                'previewImage',
                'uploadImage',
                'onMenuShareAppMessage',
                'onMenuShareTimeline',
                'hideAllNonBaseMenuItem',
                'showMenuItems'
            ]
        });

        wx.ready(function () {
            wx.hideAllNonBaseMenuItem();
            // 更新本分享链接

            wx.onMenuShareAppMessage({
                title: '<?=$_SESSION['user']->username?>邀请您加入<?=$_SESSION['school']->name?>', // 分享标题
                desc: '<?=$_SESSION['school']->name?> 欢迎您', // 分享描述
                link: '<?= $server_host ?>/c/school/index.php?state=<?=$_SESSION['school']->id?>', // 分享链接
                imgUrl: '<?= $server_host ?>/c/public/images/wx_inv.jpg', // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            wx.onMenuShareTimeline({
                title: '<?=$_SESSION['user']->username?>邀请您加入<?=$_SESSION['school']->name?>', // 分享标题
                link: '<?= $server_host ?>/c/school/index.php?state=<?=$_SESSION['school']->id?>', // 分享链接
                imgUrl: '<?= $server_host ?>/c/public/images/wx_inv.jpg', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

        });


        $(function () {
            var school_id = <?=$_SESSION['school_id']?>;


            $(".cancelA").click(function (e) {
                var uid = $(this).data('id');
                $.confirm("您确定要取消该用户权限吗?", "确认取消?", function () {
                    $.showLoading("执行中");
                    $.ajax({
                        type: 'POST',
                        url: '../api/school.php?a=set_assistant',
                        dataType: 'json',
                        data: {'user_id': uid, 'school_id': school_id, 'state': 0},
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
                }, function () {
                    //取消操作
                });
            })

            $(".cancelT").click(function (e) {
                var uid = $(this).data('id');
                $.confirm("您确定要取消该用户权限吗?", "确认取消?", function () {
                    $.showLoading("执行中");
                    $.ajax({
                        type: 'POST',
                        url: '../api/school.php?a=set_teacher',
                        dataType: 'json',
                        data: {'user_id': uid, 'school_id': school_id, 'state': 0},
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
                }, function () {
                    //取消操作
                });
            })


            $(".weui_btn_primary").click(function (e) {
                var uid = $(this).data('id');
                $.modal({
                    title: "任命人员",
                    text: "管理员可以在大家庭中发布内容，老师可以创建和管理班级。",
                    buttons: [
                        {
                            text: "管理员", onClick: function () {

                            $.showLoading("任命中");
                            $.ajax({
                                type: 'POST',
                                url: '../api/school.php?a=set_assistant',
                                dataType: 'json',
                                data: {'user_id': uid, 'school_id': school_id, 'state': 1},
                                success: function (data) {
                                    $.hideLoading();
                                    $.toast("任命成功");
                                    location.reload();
                                },
                                error: function (xhr, type) {
                                    $.hideLoading();
                                    console.log('Ajax error!');
                                }
                            });

                        }
                        },
                        {
                            text: "老师", onClick: function () {
                            $.showLoading("任命中");
                            $.ajax({
                                type: 'POST',
                                url: '../api/school.php?a=set_teacher',
                                dataType: 'json',
                                data: {'user_id': uid, 'school_id': school_id, 'state': 1},
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

                        }
                        },
                        {text: "取消", className: "default"},
                    ]
                });
            })
        });
    </script>
</head>

<body ontouchstart style="background-color: #f8f8f8;">

<div class="weui-header bg-green">
    <div class="weui-header-left"><a href="yz_menu.php" class="icon icon-109 f-white">返回</a></div>
    <h1 class="weui-header-title">人事任命</h1>
    <div class="weui-header-right"></div>
</div>

<div class="weui_cells">
    <div class="weui_cells_title">管理员</div>
    <?php
    if (count($user) > 0) {
        foreach ($user as $u) {
            if ($u->is_assistant && $u->id!="") {
                ?>
                <div class="weui_cell">
                    <div class="weui_cell_hd"><img
                                src="<?= $u->headimgurl ?>"
                                alt="" style="width:20px;margin-right:5px;display:block"></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <p><?= $u->username ?></p>
                    </div>
                    <div class="weui_cell_ft">
                        <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_warn cancelA"
                           data-id="<?= $u->id ?>">取消</a>
                    </div>
                </div>
                <?php
            }
        }//end foreach
    }//end if
    ?>

</div>
<div class="weui_cells">
    <div class="weui_cells_title">老师</div>
    <?php
    if (count($user) > 0) {
        foreach ($user as $u) {
//            var_dump($u);
            if ($u->is_teacher && $u->id!="") {
                ?>
                <div class="weui_cell">
                    <div class="weui_cell_hd"><img
                                src="<?= $u->headimgurl ?>"
                                alt="" style="width:20px;margin-right:5px;display:block"></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <p><?= $u->username ?></p>
                    </div>
                    <div class="weui_cell_ft">
                        <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_warn cancelT"
                           data-id="<?= $u->id ?>">取消</a>
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
    if (count($user) > 0) {
        foreach ($user as $u) {
            if($u->id!="") {
                ?>
                <div class="weui_cell">
                    <div class="weui_cell_hd"><img
                                src="<?= $u->headimgurl ?>"
                                alt="" style="width:20px;margin-right:5px;display:block"></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <p><?= $u->username ?></p>
                    </div>
                    <div class="weui_cell_ft">
                        <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_primary"
                           data-id="<?= $u->id ?>">任命</a>
                    </div>
                </div>
                <?php
            }
        }//end foreach
    }//end if
    ?>
</div>


</body>
</html>
