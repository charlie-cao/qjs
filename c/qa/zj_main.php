<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";

check_login();
$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();

if (!isset($_GET['tag'])) {
    $_GET['tag'] = 2;
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title><?= $_SESSION['school']->name ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="../public/style/weui.css"/>
    <link rel="stylesheet" href="../public/style/weui2.css"/>
    <link rel="stylesheet" href="../public/style/weui3.css"/>
    <script src="../public/zepto.min.js"></script>
    <script src="../public/updown.js"></script>
    <script src="../public/lazyimg.js"></script>
    <script src="../public/jweixin-1.2.0.js"></script>
    <style>
        .paragraphExtender {
            background-color: #35C535;
            color: white;
            padding: 4px;
            width: 70%;
            float: left;
            border-radius: 20px;
            padding-left: 20px;
            line-height: 22px;

        }

        .paragraph {
            word-break: break-all;
        }
    </style>
    <script>
        wx.config({
            debug: false,
            appId: '<?= $signPackage["appId"]; ?>',
            timestamp: <?= $signPackage["timestamp"]; ?>,
            nonceStr: '<?= $signPackage["nonceStr"]; ?>',
            signature: '<?= $signPackage["signature"]; ?>',
            jsApiList: [
                'checkJsApi',
                'startRecord',
                'stopRecord',
                'playVoice',
                'pauseVoice',
                'onVoicePlayEnd',
                'uploadVoice',
                'downloadVoice',
                'translateVoice',
                'onVoiceRecordEnd'
            ]
        });

        wx.ready(function () {
            wx.onVoicePlayEnd({
                success: function (res) {
//                    alert(res.localId);
                    playing = false;
//                    stopWave();
                }
            });
        });

        //防止用户连续点击播放导致微信崩溃。
        var playing = false;

        function WXplayVoice(e) {
            if(playing==true){
                return true;
            }
            //这里如果下载失败调用的是 fail回调函数
            var voice_id = $(e).data("voice_id");
            var answer_user_id = $(e).data("answer_user_id");
            var q_id = $(e).data("q_id");
            wx.downloadVoice({
                serverId: voice_id, // 需要下载的音频的服务器端ID，由uploadVoice接口获得
                isShowProgressTips: 1, // 默认为1，显示进度提示
                success: function (res) {
//                    alert("直接下载");
//                    if(now_voice_id!=""){
//                        wx.stopVoice(now_voice_id);
//                    }
//                    now_voice_id = res.localId;
                    playing = true;
                    wx.playVoice({
                        localId: res.localId
                    });
                    // 增加播放计数
                    var d = {'voice_id': voice_id, 'question_id': q_id , 'answer_user_id':answer_user_id};
                    $.ajax({
                        type: 'POST',
                        data: d,
                        url: '../api/qa.php?a=add_play_num',
                        dataType: 'json',
                        success: function (data) {
//                            alert("计数成功");
                        },
                        error: function (xhr, type, e) {
                            alert(type);
                        }
                    });

                },
                fail: function (res) {
                    //调用服务端上传远程数据到微信服务器并修改voice_id后重新调用playVoice
                    alert("重新上传");
                    var d = {'serverId': voice_id, 'question_id': q_id};

//                    alert("question_id:" + q_id);
//                    alert("serverId:" + voice_id);
//                    alert(d.serverId);
//                    alert(d.question_id);

                    $.ajax({
                        type: 'POST',
                        data: d,
                        url: '../api/qa.php?a=update_voice',
                        dataType: 'json',
                        success: function (data) {
//                            alert("新ID" + data.data.mediaId);
//                            alert($(e));
                            $(e).data("voice_id", data.data.mediaId);
//                            alert($(e).data("voice_id"));
                            WXplayVoice(e);
                        },
                        error: function (xhr, type, e) {
                            alert(type);
                        }
                    });
                }
            });
        }

        function playVoice(e) {
            WXplayVoice(e);
        }

        function up(id, em) {
            d = {};
            d.id = id;
            d.user_id = '<?= $_SESSION['user']->id ?>';
            if ($(em).hasClass("checked")) {
                $(em).removeClass("checked");
                d.type = "down";
            } else {
                $(em).addClass("checked");
                d.type = "up";
            }

            $.ajax({
                type: 'POST',
                data: d,
                url: '../api/qa.php?a=up_qa',
                dataType: 'json',
                success: function (data) {
                    reset_up(id, data.data);
                    $('#actionMenu' + id).toggleClass('active');
                },
                error: function (xhr, type, e) {

                    alert(type);
                }
            });
        }

        function toggleMenu(e) {
            $('.actionMenu').removeClass('active');

            $('#actionMenu' + $(e).data('id')).toggleClass('active');
        }

        function is_uped(users) {

            var checked = "";
            if (typeof (users) != undefined) {
                if (users.length > 0) {
                    for (i in users) {
                        if (users[i].id == <?= $_SESSION['user']->id ?>) {
                            checked = "checked";
                        }
                    }
                }
            }
            return checked;
        }

        function reset_up(id, users, ret = false) {
            if (typeof (users) != undefined) {
                var up_user_html = ""
                console.log(users);
                if (users === null) {

                } else {
                    if (users.length > 0) {
                        up_user_html = '<p class="liketext" style="margin-top: 6px; padding-top:2px; padding-bottom:2px;border-bottom: 1px solid #e4e4e4;" >'
                        up_user_html += '<i class="icon icon-96" style="padding-right: 6px;padding-left: 6px;color: #5d6b85; font-size:14px"></i>'
                        for (i in users) {
                            if (i == users.length - 1) {
                                up_user_html += '<span class="nickname" style="font-size: 14px;">' + users[i].nickname + '</span> ';
                            } else {
                                up_user_html += '<span class="nickname" style="font-size: 14px;">' + users[i].nickname + '</span> ,';
                            }
                        }
                        up_user_html += '</p>';
                    }
                }

                if (ret) {
                    return up_user_html;
                } else {
                    $("#up_user" + id).html(up_user_html);
                }
            }
        }

        /**
         * 添 加内容行
         * @param data
         * @param i
         * @returns {string}
         */
        function addCell(data, i) {
            //生成点赞用户的html
            var div_users = reset_up(data[i].id, data[i].up_user, true);
            var checked = is_uped(data[i].up_user);
            if(<?=$_GET['tag']?>==2){
                //判定是否为校内专家
                var q_btn_html = '<a href="pt_send_question.php?id=' + data[i].answer_user[0].id + '" class="weui_btn weui_btn_mini weui_btn_primary"  style ="font-size: 12px;">提问</ a>';
            }else if (data[i].answer_user[0].is_expert == 1) {
                var q_btn_html = '<a href="pt_send_question.php?id=' + data[i].answer_user[0].id + '" class="weui_btn weui_btn_mini weui_btn_primary"  style ="font-size: 12px;">提问</ a>';
            } else {
                var q_btn_html = "";
            }
            var result = ''
                + '<!-- 普通的post -->'
                + '<div class="weui_cell moments__post">'

                + '<div class="weui_cell_hd weui-updown">'
                + '<img src="' + data[i].answer_user[0].headimgurl + '"/>'
                + q_btn_html
                + '</div>'

                + '<div class="weui_cell_bd"  style="width: 100%;">'
                + '<! --  删除链接 -->'

                + '<!- - 人名链接 -->'
                + '<a class="title" href="javascript:;">'
                + '<span>' + data[i].answer_user[0].username + '</span>'
                + '</a>'


                + '<!-- post内容 -->'
                + '<p id="paragraph" class="paragraph">'
                + "回答 : " + data[i].question_content
                + '</p>'
                + '<!-- 伸张链接 -->'
                + '<a id="paragraphExtender" class="paragraphExtender" style="color: white;" onclick="playVoice(this)" data-voice_id="' + data[i].answer_content + '" data-answer_user_id="' + data[i].answer_user_id + '" data-q_id="' + data[i].id + '"><span class="icon icon-53" style="padding-right:4px"></span> 免费 旁听 </a>'
                + '<!-- 相册 -->'
                + '<div class="thumbnails">'


                + '</div>'
                + '<!-- 资料条 -->'
                + '<div class="toolbar">'
                + '<p class="timestamp">' + data[i].c_time + '</p>'
                + '<span id="actionToggle" data-id="' + data[i].id + '" onclick="toggleMenu(this)" class="actionToggle" style="height: 12px;"><i class="icon icon-83" ></i></span>'
                + '<div>'

                + '<div id="actionMenu' + data[i].id + '" class="actionMenu slideIn">'
                + '<p class="actionBtn ' + checked + '"  onclick="up(' + data[i].id + ',this);"   style="font-size:14px"><i class="icon icon-96" style="font-size:14px"></i> 赞</p>'
                + '</div>'

                + '</div>'
                + '</div>'

                + '<!-- 赞／评论区 -->'
                + '<div id="up_user' + data[i].id + '" class="up_user">' + div_users + '</div>'

                + '</div>'
                + '<!-- 结束 post -->'
                + '</div>'
                + '<!-- 结束 朋友圈 -->';
            return result;
        }

        $(function () {
            var nowtime = new Date().getTime();
            //学校ID 其实慢成长也可以用这个模式，直接相当于群号码了
            //页数
            var page = 0;
            // 每页展示10个
            var size = 5;
            var dp;
            var dropload = $('.weui_panel').dropload({
                scrollArea: window,
                autoLoad: true,//自动加载
                domDown: {//上拉
                    domClass: 'dropload-down',
                    domRefresh: '<div class="dropload-refresh f15 "><i class="icon icon-20"></i>上拉加载更多</div>',
                    domLoad: '<div class="dropload-load f15"><span class="weui-loading"></span>正在加载中...</div>',
                    domNoData: '<div class="dropload-noData">没有更多数据了</div>'
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
                        url: '../api/qa.php?a=get_qa_list&page=' + page + '&size=' + size + '&last_time=' + nowtime + '&school_id=<?=$_SESSION['school']->id?>&tag=' +<?= $_GET['tag'] ?>,
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
                                // 如果没有数据
                                setTimeout(function () {
                                    $('.weui_panel_bd').append(result);
                                    me.resetload();
                                }, 300);
                            } else {
                                // 锁定
                                me.lock();
                                // 无数据
                                me.noData();
                                me.resetload();
                            }


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
        .icon {
            font-size: 20px;
        }

        .weui_cells:before {
            top: 0;
            border-top: 0px solid #d9d9d9;
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
            left: 0px;
        }

        .comment li {
            padding: 2px;
            padding-left: 6px;
        }

        .comment li span {
            color: #5d6b85;
        }

        .checked .icon {
            color: #ffa1a1;
        }
    </style>


</head>

<body ontouchstart>

<div class="weui-header bg-green">
    <div class="weui-header-left">
        <a href="zj_my_info.php" class="icon icon-85 f-white" style="font-size: 26px;"></a>
    </div>
    <h1 class="weui-header-title">千家师 专家</h1>
    <div class="weui-header-right">
        <a href="zj_expert_list.php" class="icon icon-99 f-white" style="font-size: 26px;"></a>
    </div>
</div>

<div class="page-hd" style="padding: 4px;height: 34px;
    background: white;">
    <div class="weui-flex">
        <div class="weui-flex-item">

        </div>

        <div class="weui-flex-item">
            <div class="weui_tab_nav">
                <a href="zj_main.php?tag=2"
                   class="weui_navbar_item weui_nav_green <?= ($_GET['tag'] == 2) ? "bg_green" : "" ?>"> 校内 </a>
                <a href="zj_main.php?tag=0"
                   class="weui_navbar_item weui_nav_green <?= ($_GET['tag'] == 0) ? "bg_green" : "" ?>"> 校外 </a>
            </div>
        </div>

        <div class="weui-flex-item">

        </div>
    </div>
</div>
<div class="weui_panel weui_panel_access" style="     margin-top: 0px; ">
    <div class="weui_panel_bd weui_cells moments">
    </div>
</div>
</body>
</html>