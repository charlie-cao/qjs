<?php

require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";

$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();

$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

//这里由于是该用户的列表页所以不需要传入state了
//if($_GET['state']){
//    $_SESSION['state']=$_GET['state'];
//}else{
//    echo "ID 错误";
//    exit;
//}
//检查用户是否已经登录
if (!isset($_SESSION['user']->openid)) {
    //未登录 微信登录
    $_SESSION['user'] = wx_userinfo($appid, $secret, $redirect_uri, $_GET['state']);
}
//检查用户并更新用户SESSION信息
$_SESSION['user'] = check_user($_SESSION['user']);


?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>我的提问</title>
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
                    $.hideLoading();
                    playing = false;
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
//                    alert("重新上传");
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
                            if(data.data.mediaId===null){
                                $.hideLoading();
                                $.alert("这个回答出差去了月球，听听别的～");
                            }else{
                                $(e).data("voice_id", data.data.mediaId);
                                WXplayVoice(e);
                            }
                        },
                        error: function (xhr, type, e) {
                            alert(type);
                        }
                    });
                }
            });
        }

        function playVoice(e) {
            $.showLoading("回答播放中");
            WXplayVoice(e);
        }
    </script>
</head>

<body ontouchstart="" style="background-color: #f8f8f8;">
<?php

    $sql = "select * from sc_question where question_user_id = " . $_SESSION['user']->id . " and is_del=0 order by c_time desc";
    $res = $db->query($sql);
    $questions = $res->fetchAll();

    foreach ($questions as $key => $q) {
        $sql_u = "select * from sc_user where id=" . $q['question_user_id'] . "   ";
        $u_res = $db->query($sql_u);
        $questions[$key]['question_user'] = $u_res->fetchAll();

        $sql_u = "select * from sc_user where id=" . $q['answer_user_id'] . "   ";
        $u_res = $db->query($sql_u);
        $questions[$key]['answer_user'] = $u_res->fetchAll();

        $questions[$key]['c_time'] = formatTime($questions[$key]['c_time']);
    }

    ?>
    <div class="weui_cells" style="margin-top:0px;">
        <?php foreach ($questions as $q) { ?>
            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <p><?= $q['question_content'] ?></p>
                </div>
                <div class="weui_cell_hd">
                    <img src="<?= $q['answer_user'][0]['headimgurl'] ?>" alt=""
                         style="width:20px;margin-right:5px;display:block">
                </div>
                <div class="weui_cell_ft">
                    <?php if ($q['answer_content'] == "") { ?>

                        <a class="weui_btn weui_btn_mini weui_btn_warn ">
                            <img src="../public/images/icon/time.png" class="btn_icon btn_icon_sm" >
                            待回答
                        </a>
                    <?php } else { ?>
                        <a id="paragraphExtender" class="paragraphExtender" style="color: white;"
                           onclick="playVoice(this)"
                           data-voice_id="<?= $q['answer_content'] ?>"
                           data-answer_user_id="<?= $q['answer_user_id'] ?>"
                           data-q_id="<?= $q['id'] ?>">
                            <img src="../public/images/icon/play.png" class="btn_icon btn_icon_sm" >
                            听解答
                        </a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php if (count($questions) == 0) {
            ?>
            <div class="weui_cell">
                还没有问题
            </div>
        <?php } ?>
    </div>

</body>
</html>