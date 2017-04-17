<?php
require_once '../config.php';
require_once '../lib/fun.php';

$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

/**
 * 专家类型
 * 0 为校内
 * 1 为全局
 */
if ($_GET['tag'] == "") {
    $_GET['tag'] = 0;
}

$state = $_GET['state'];
//检查用户是否已经登录
if (!isset($_SESSION['user']->openid)) {
    //未登录 微信登录
    $_SESSION['user'] = wx_userinfo($appid, $secret, $redirect_uri, $state);
}

//检查用户并更新用户SESSION信息
$_SESSION['user'] = check_user($_SESSION['user']);

//var_dump($_SESSION['school']);
//exit;

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>教育家</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="../public/style/weui.css"/>
    <link rel="stylesheet" href="../public/style/weui2.css"/>
    <link rel="stylesheet" href="../public/style/weui3.css?1"/>
    <script src="../public/zepto.min.js"></script>
    <script src="../public/updown.js"></script>
    <script src="../public/lazyimg.js"></script>
    <script>
        function addCell(data, i) {
            if(data[i].small_memo=="" || data[i].small_memo===null){
                data[i].small_memo = "特约专家";
            }
            if (data[i].memo == "" || data[i].memo === null) {
                data[i].memo = "还没有填写备注";
            }

            function star_check(star_num, i) {
                if (star_num >= i) {
                    return "checked";
                } else {
                    return "";
                }
            }

            var title = "";
            if(data[i].is_leader=="1"){
                title = '<label class="weui-label-s">园长</label>';
            }else if(data[i].is_teacher=="1"){
                title = '<label  class="weui-label-s">教师</label>';
            }else if(data[i].is_assistant=="1"){
                title = '<label class="weui-label-s">助理</label>';
            }
            title = '<span class="weui-label-s" style="font-size: 8px; margin-left: 8px;">' + data[i].small_memo + '</span>';


            //更新用户星级
            var result = ''
                + '<div class="weui_media_box weui_media_appmsg">'
                + '<div class="weui_media_hd">'
                + '<img class="weui_media_appmsg_thumb" src="' + data[i].headimgurl + '"/>'
                + '</div>'

                + '<div class="weui_media_bd" >'
                + '<h4 class="weui_media_title">' + data[i].username +  title+'</h4>'

                + '    <div class="weui-rater">'
                + '      <a class="weui-rater-box ' + star_check(data[i].star_num, 1) + '"> <span class="weui-rater-inner">★</span> </a>'
                + '      <a class="weui-rater-box ' + star_check(data[i].star_num, 2) + '"> <span class="weui-rater-inner">★</span> </a>'
                + '      <a class="weui-rater-box ' + star_check(data[i].star_num, 3) + '"> <span class="weui-rater-inner">★</span> </a>'
                + '      <a class="weui-rater-box ' + star_check(data[i].star_num, 4) + '"> <span class="weui-rater-inner">★</span> </a>'
                + '      <a class="weui-rater-box ' + star_check(data[i].star_num, 5) + '"> <span class="weui-rater-inner">★</span> </a>'
                + '</div>'
                + '<p class="weui_media_desc">'
                + data[i].memo
                + '</p>'
                + '</div>'
                + '<div class="weui_media_fd">'
                + '<a href="pt_send_question.php?id=' + data[i].id + '" class="weui_btn weui_btn_mini weui_btn_primary" style="font-size: 12px;">提问</a>'
                + '</div>'
                + '</div>';
            return result;
        }

        $(function () {
            //开启时的时间戳 最好是从服务端获取的，可以在第一次请求时传回来
            var nowtime = new Date().getTime();
            //页数
            var page = 0;
            // 每页展示10个
            var size = 5;
            var dp;
            var dropload = $('.weui_panel').dropload({
                scrollArea: window,
                autoLoad: true, //自动加载
                domDown: {//上拉
                    domClass: 'dropload-down',
                    domRefresh: '<div class="dropload-refresh f15 "><i class="icon icon-20"></i>上拉加载更多</div>',
                    domLoad: '<div class="dropload-load f15"><span class="weui-loading"></span>正在加载中...</div>',
                    domNoData: '<div class="dropload-noData">没有更多专家了</div>'
                },
                domUp: {//下拉
                    domClass: 'dropload-up',
                    domRefresh: '<div class="dropload-refresh"><i class="icon icon-114"></i>下拉加载更多</div>',
                    domUpdate: '<div class="dropload-load f15"><i class="icon icon-20"></i>释放更新...</div>',
                    domLoad: '<div class="dropload-load f15"><span class="weui-loading"></span>正在加载中...</div>'
                },
                loadUpFn: function (me) {
                    me.resetload();
                },
                loadDownFn: function (me) {//加载更多
                    dp = me;
                    page++;
                    window.history.pushState(null, document.title, window.location.href);
                    var result = '';
                    $.ajax({
                        type: 'GET',
                        url: '../api/qa.php?a=school_get_expert_list' +
                        '&page=' + page + '&size=' + size + '&last_time=' + nowtime +
                        '&school_id=<?=$_SESSION['school']->id?>&tag=<?=$_GET['tag']?>',
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
                            if (data.msg == "success") {
                                data = data.data;
                                var arrLen = data.length;
                                if (arrLen > 0) {
                                    for (var i = 0; i < arrLen; i++) {
                                        result += addCell(data, i);
                                    }
                                    // 如果没有数据
                                } else {
                                    // 锁定
                                    me.lock();
                                    // 无数据
                                    me.noData();
                                }
                            } else {
                                // 锁定
                                me.lock();
                                // 无数据
                                me.noData();
                                me.resetload();
                            }
                            // 为了测试，延迟1秒加载
                            setTimeout(function () {
                                $('.weui_panel_bd').append(result);
                                // 每次数据加载完，必须重置
                                me.resetload();
                            }, 100);
                        },
                        error: function (xhr, type) {
                            console.log('Ajax error!');
                            // 即使加载出错，也得重置
                            me.resetload();
                        }
                    });
                }
            });
        });
    </script>
    <style>

        .weui_media_title {
            float: left;
            margin-right: 10px;
        }

        .weui-rater-box {
            position: relative;
            margin-right: 2px;
            font-size: 14px;
            width: 14px;
            height: 14px;
            color: rgb(255, 204, 102);
        }

        .weui_media_title {
            float: none;
        }

        .weui_cells {
            margin-top: 0px !important;
        }

        .weui_media_title {
            font-size: 16px !important;
        }

        .weui_media_box .weui_media_desc {
            font-size: 14px !important;
        }

        .weui_cells:before {
            top: 0;
            border-top: 0px solid #d9d9d9;
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
            left: 0px;
        }

        .weui-label-s{
            margin-left: 4px;
        }
    </style>
</head>

<body ontouchstart="" style="background-color: #f8f8f8;">
<div class="weui-header bg-green">
    <div class="weui-header-left"><a href="pt_main.php" class="icon icon-109 f-white">返回</a></div>
    <h1 class="weui-header-title">教育家</h1>
    <div class="weui-header-right"></div>
</div>
<div class="page-hd" style="padding: 4px;height: 34px; background: white;">
    <div class="weui-flex">
        <div class="weui-flex-item">
        </div>
        <div class="weui-flex-item">
            <div class="weui_tab_nav">
                <a href="pt_expert_list.php?tag=0"
                   class="weui_navbar_item weui_nav_green <?= ($_GET['tag'] == 0) ? "bg_green" : "" ?>"> 校内 </a>
                <a href="pt_expert_list.php?tag=1"
                   class="weui_navbar_item weui_nav_green <?= ($_GET['tag'] == 1) ? "bg_green" : "" ?>"> 校外 </a>
            </div>
        </div>
        <div class="weui-flex-item">
        </div>
    </div>
</div>
<div class="weui_panel weui_panel_access" style="margin-top: 0px; ">
    <div class="weui_panel_bd weui_cells moments">
    </div>
</div>
</body>
</html>
