<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";

$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];


if ($_GET['state']) {
    $_SESSION['state'] = $_GET['state'];
} else {
    echo "ID 错误";
    exit;
}
//检查用户是否已经登录
if (!isset($_SESSION['user']->openid)) {
    //未登录 微信登录
    $_SESSION['user'] = wx_userinfo($appid, $secret, $redirect_uri, $_SESSION['state']);
}
//检查用户并更新用户SESSION信息
$_SESSION['user'] = check_user($_SESSION['user']);

$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();


$sql = "select * from sc_question where id = " . $_GET['state'] . " limit 1";
$res = $db->query($sql);
$question = $res->fetch();

$sql = "select * from sc_user where id = " . $question['answer_user_id'] . " limit 1";
$res = $db->query($sql);
$user = $res->fetch();

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>回答</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="../public/style/weui.css"/>
    <link rel="stylesheet" href="../public/style/weui2.css"/>
    <link rel="stylesheet" href="../public/style/weui3.css?4"/>
    <script src="../public/zepto.min.js"></script>
    <script src="../public/jweixin-1.2.0.js"></script>
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
                'onVoiceRecordEnd'
            ]
        });

        wx.ready(function () {

            wx.onVoicePlayEnd({
                success: function (res) {
                    $.hideLoading();
                    playing = false;
                }
            });

            if (!localStorage.rainAllowRecord || localStorage.rainAllowRecord !== 'true') {
//                alert("预先录音");
                wx.startRecord({
                    success: function () {
                        localStorage.rainAllowRecord = 'true';
                        wx.stopRecord();
                    },
                    cancel: function () {
                        $.alert('用户拒绝授权录音');
                    }
                });
            }

        });


        $(function () {
            $.showLoading();
            setTimeout(function () {
                $.hideLoading();
            }, 300)

            var START = 0;
            var END = 0;
            var voice = {
                localId: '',
                serverId: ''
            };
            var recordTimer;

            $("#sendVoice").on("touchstart", function (event) {
                event.preventDefault();
            });

            $("#sendVoice").on("touchend", function (event) {
                event.preventDefault();
                if (voice.localId == "") {
                    $.alert("请先录音");
                    return true;
                }
                wx.uploadVoice({
                    localId: voice.localId, // 需要上传的音频的本地ID，由stopRecord接口获得
                    isShowProgressTips: 1, // 默认为1，显示进度提示
                    success: function (res) {


                        voice.serverId = res.serverId;
                        voice.user_id = <?=$_SESSION['user']->id?>;
                        voice.school_id = "";
                        voice.question_id = <?=$question['id']?>;

                        //把录音在微信服务器上的id（res.serverId）发送到自己的服务器供下载。
                        $.ajax({
                            url: '../api/qa.php?a=save_voice',
                            type: 'post',
                            data: voice,
                            dataType: "json",
                            success: function (data) {
                                $.alert('您的回答已经发送', function () {
                                    location.href = "g_my_answer.php";
                                });
                            },
                            error: function (xhr, errorType, error) {
                                $.alert("录音时间过短");
                            }
                        });
                    }
                });

            });

            $('#startRecord').on("touchstart", function (event) {
                event.preventDefault();
                $(this).addClass('r_btn_right_hover');
                $(this).find('img').attr('src', "../public/images/icon/mic_1.png");

                START = new Date().getTime();
                recordTimer = setTimeout(function () {

                    $("#voice_state").html("正在录音");
                    $("#voice_state2").html("松开结束");

                    wx.startRecord({
                        success: function () {
                            localStorage.rainAllowRecord = 'true';
                        },
                        cancel: function () {
                            $.alert('用户拒绝授权录音');
                        }
                    });
                }, 300);
                return true;
            });

            // 4.3 停止录音
            $('#startRecord').on("touchend", function (event) {
                event.preventDefault();
                $(this).removeClass('r_btn_right_hover');
                $(this).find('img').attr('src', "../public/images/icon/mic_2.png");

                END = new Date().getTime();
                if ((END - START) < 300) {
                    END = 0;
                    START = 0;
                    //小于300ms，不录音
                    clearTimeout(recordTimer);
                } else {
                    $("#voice_state").html("试听一下");
                    $("#voice_state2").html("按住说话");
                    wx.stopRecord({
                        success: function (res) {
                            voice.localId = res.localId;
                        },
                        fail: function (res) {
//                            $.alert(JSON.stringify(res));
                            $.alert("录音时间过短");
                        }
                    });
                }
            });

            $('#playVoice').on("touchstart", function (event) {
                event.preventDefault();
                $(this).addClass('r_btn_left_hover');
                $(this).find('img').attr('src', "../public/images/icon/try_1.png");
            });

            $('#playVoice').on("touchend", function (event) {
                event.preventDefault();
                $(this).find('img').attr('src', "../public/images/icon/try_2.png");
                $(this).removeClass('r_btn_left_hover');

                if (voice.localId == '') {
                    $.alert('请先录制一段声音');
                    return;
                }
                wx.playVoice({
                    localId: voice.localId
                });
                $.showLoading("播放中");
            });


            // 4.4 监听录音自动停止
            wx.onVoiceRecordEnd({
                complete: function (res) {
                    voice.localId = res.localId;
                    $.alert('录音时间已超过一分钟');
                }
            });

//            // 4.8 监听录音播放停止
//            wx.onVoicePlayEnd({
//                complete: function (res) {
//                    $.alert('录音（' + res.localId + '）播放结束');
//                }
//            });


        })


    </script>
    <style>
        .paragraphExtender {
            background-color: #35C535;
            color: white;
            padding: 8px;
            width: 87%;
            border-radius: 20px;
            padding-left: 20px;
            line-height: 22px;
            padding-right: 20px;
        }

        .paragraph {
            word-break: break-all;
        }
    </style>
</head>

<body ontouchstart style="background-color: #f8f8f8;">
<?php

if ($question['answer_content'] == "2") {

    ?>
    <div class="weui_msg hide" id="msg1" style="display: block; opacity: 1;">
        <div class="weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">该问题已经回答</h2>
            <p class="weui_msg_desc"></p>
        </div>
        <div class="weui_opr_area">
            <p class="weui_btn_area">
            </p>
        </div>
        <div class="weui_extra_area">

        </div>
    </div>
    <?php
    exit;

}
?>

<div style="text-align: center; color: #333333">
    <img src="<?= $user['headimgurl'] ?>" style="width: 80px;
                 padding: 10px;
                 border-radius: 80px;">
    <div style="font-size: 16px;"><?= $user['username'] ?></div>
    <hr style="width: 40px;   border: 1px solid #18A5A1;  margin: 10px auto 10px auto">
    <span style="color: #999999">问题</span>
    <p>
        <?= $question['question_content'] ?>
    </p>
</div>


<div class="weui-flex  r_click_area">
    <div class="weui-flex-item">
        <a id="playVoice" class="r_btn r_btn_left">
            <img src="../public/images/icon/try_2.png" onclick="return false"/>
            <br>
            <span id="voice_state">试听一下</span>
        </a>

    </div>
    <div class="weui-flex-item">
        <a id="startRecord" class="r_btn r_btn_right">
            <img src="../public/images/icon/mic_2.png" onclick="return false"/>
            <br>
            <span id="voice_state2">按住说话</span>
        </a>
    </div>
</div>


<div style="text-align:center; margin-top: 10px; margin-left: 30px; margin-right: 30px; ">
    <a id="sendVoice" class="paragraphExtender" style="color: white; background-color:#FF6600">
        <span class="icon icon-71" style="padding-right:4px;"></span> <span style="">发送 回答</span>
    </a>
</div>

<div class="weui_cells ">
    <form id="sendMsg">
        <input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>">
        <input type="hidden" name="question_user_id" value="<?= $question['question_user_id'] ?>">
        <input type="hidden" name="answer_user_id" value="<?= $_SESSION['user']->id ?>">
    </form>
</div>


</body>
</html>
