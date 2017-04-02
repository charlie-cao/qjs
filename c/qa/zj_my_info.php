<?php

require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";

$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();

$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

$state = $_GET['state'];
//检查用户是否已经登录
if (!isset($_SESSION['user']->openid)) {
    //未登录 微信登录
    $_SESSION['user'] = wx_userinfo($appid, $secret, $redirect_uri, $state);
}

//检查用户并更新用户SESSION信息
$_SESSION['user'] = check_user($_SESSION['user']);


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
                    stopWave();
                }
            });
        });

        function WXplayVoice(id, e) {
            //这里如果下载失败调用的是 fail回调函数
            wx.downloadVoice({
                serverId: id, // 需要下载的音频的服务器端ID，由uploadVoice接口获得
                isShowProgressTips: 1, // 默认为1，显示进度提示
                success: function (res) {
                    alert("直接下载");
                    wx.playVoice({
                        localId: res.localId
                    });
                },
                fail: function (res) {
                    //调用服务端上传远程数据到微信服务器并修改voice_id后重新调用playVoice
                    alert("重新上传");
                    var d = {'serverId': id, 'question_id': $(e).data("q_id")};

                    alert("question_id:" + $(e).data("q_id"));
                    alert("serverId:" + id);

                    var em = em;
                    $.ajax({
                        type: 'POST',
                        data: d,
                        url: '../api/qa.php?a=update_voice',
                        dataType: 'json',
                        success: function (data) {
                            alert("新ID" + data.data.mediaId);
                            alert($(e));

                            $(e).data("voice_id") = data.mediaId;

                            alert(data.data.mediaId);
                            alert($(e).data("voice_id"));

                            WXplayVoice(data.data.mediaId, e);
                        },
                        error: function (xhr, type, e) {
                            alert(type);
                        }
                    });
                }
            });
        }

        function playVoice(e) {
            var id = $(e).data("voice_id");
            WXplayVoice(id, e);
        }
    </script>
    <style>
        .paragraphExtender {
            background-color: #35C535;
            color: white;
            padding: 4px;
            /* width: 70%; */
            /* float: left; */
            border-radius: 20px;
            float: none;

            /* padding-left: 20px; */
            /* line-height: 22px; */
        }

        .weui_cells:before {
            top: 0;
            border-top: 0px solid #d9d9d9;
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
        }

        .weui_grid_icon2 {
            text-align: center;
        }
    </style>


</head>

<body ontouchstart="" style="background-color: #f8f8f8;">
<div class="weui-header bg-green">
    <div class="weui-header-left"><a href="zj_main.php" class="icon icon-109 f-white">返回</a></div>
    <h1 class="weui-header-title">个人面板</h1>
    <div class="weui-header-right"></div>
</div>

<?php if ($_SESSION['user']->is_expert) {

    $sql = "SELECT count(*) as c FROM `sc_question` WHERE `answer_user_id` = " . $_SESSION['user']->id;
    $res = $db->query($sql);
    $re = $res->fetch();
    $money_count = $re['c'];

    $sql = "SELECT count(*) as c FROM `sc_question` WHERE answer_content is not null and `answer_user_id` = " . $_SESSION['user']->id;
    $res = $db->query($sql);
    $re = $res->fetch();
    $answer_count = $re['c'];


    $sql = "SELECT sum(play_num) as c FROM `sc_question` WHERE answer_content is not null and `answer_user_id` = " . $_SESSION['user']->id;
    $res = $db->query($sql);
    $re = $res->fetch();
    $play_count = $re['c'] ? $re['c'] : 0;
//    var_dump($play_count);

    $sql = "SELECT sum(up_num) as c FROM `sc_question` WHERE answer_content is not null and `answer_user_id` = " . $_SESSION['user']->id;
    $res = $db->query($sql);
    $re = $res->fetch();
    $up_num_count = $re['c'] ? $re['c'] : 0;
//    var_dump($up_num_count);


    $jssdk = new JSSDK($appid, $secret);
    $signPackage = $jssdk->GetSignPackage();

    $sql = "select * from sc_question where question_user_id = " . $_SESSION['user']->id . " and is_del=0 order by c_time desc";
    $res = $db->query($sql);
    $questions = $res->fetchAll();

    $res = $db->query($sql);
    $questions = $res->fetchAll();

    ?>

    <div class="weui_cells_title">统计</div>
    <div class="weui_grids grids-small" style="background-color: #fff;">
        <a href="javascript:;" class="grid">
            <div class="weui_grid_icon2">
                <?= $money_count * 10 ?>
            </div>
            <p class="weui_grid_label">
                总收入
            </p>
        </a>
        <a href="javascript:;" class="grid">
            <div class="weui_grid_icon2">
                <?= $answer_count ?>
            </div>
            <p class="weui_grid_label">
                回答次数
            </p>
        </a>
        <a href="javascript:;" class="grid">
            <div class="weui_grid_icon2">
                <?= $play_count ?>
            </div>
            <p class="weui_grid_label">
                被偷听次数
            </p>
        </a>
        <a href="javascript:;" class="grid">
            <div class="weui_grid_icon2">
                <?= $up_num_count ?>
            </div>
            <p class="weui_grid_label">
                被点赞次数
            </p>
        </a>
    </div>
<?php } ?>

<div class="weui_cells_title">个人</div>
<div class="weui_cells weui_cells_access">
    <a class="weui_cell" href="./zj_change_info.php">
        <div class="weui_cell_hd"></div>
        <div class="weui_cell_bd weui_cell_primary">
            <p>个人信息</p>
        </div>
        <div class="weui_cell_ft"></div>
    </a>
    <a class="weui_cell" href="./zj_my_question.php">
        <div class="weui_cell_hd"></div>
        <div class="weui_cell_bd weui_cell_primary">
            <p>我的提问</p>
        </div>
        <div class="weui_cell_ft"></div>
    </a>
    <?php if ($_SESSION['user']->is_expert) {?>
        <a class="weui_cell" href="./zj_question_list.php">
            <div class="weui_cell_hd"></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p>我的回答</p>
            </div>
            <div class="weui_cell_ft"></div>
        </a>
    <?php } ?>

</div>
</div>

<?php if ($_SESSION['user']->is_expert) {
    $sql = "select * from sc_question where answer_user_id = " . $_SESSION['user']->id . " and answer_content is null and is_del=0 order by c_time desc";
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
    <div class="weui_cells_title">最新问题</div>
    <div class="weui_cells">
        <?php foreach ($questions as $q) { ?>
            <div class="weui_cell">
                <div class="weui_cell_hd">
                    <img src="<?= $q['question_user'][0]['headimgurl'] ?>" alt=""
                         style="width:20px;margin-right:5px;display:block"></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <p><?= $q['question_content'] ?></p>
                </div>
                <div class="weui_cell_ft">
                    <?php if ($q['answer_content'] == "") { ?>
                        <a href="zj_send_answer.php?id=<?= $q['id'] ?>&user_id=<?= $q['question_user_id'] ?>"
                           class="weui_btn weui_btn_mini weui_btn_primary">回答</a>
                    <?php } else { ?>
                        已回答
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php if (count($questions) == 0) { ?>
            <div class="weui_cell">
                还没有问题
            </div>
        <?php } ?>
    </div>

<?php } ?>


</body>

</html>