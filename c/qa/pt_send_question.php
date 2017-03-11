<?php
require_once '../config.php';
require_once '../lib/fun.php';
check_login();

$sql = "select * from sc_user where id = ".$_REQUEST['id']." limit 1";
$res = $db->query($sql);
$user = $res->fetch();

//var_dump($user);

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
<script>
    $(function () {
        var max = $('#count_max').text();
        $('#content').on('input', function () {
            var text = $(this).val();
            var len = text.length;
            $('#count').text(len);
            if (len > max) {
                $(this).closest('.weui_cell').addClass('weui_cell_warn');
            }
            else {
                $(this).closest('.weui_cell').removeClass('weui_cell_warn');
            }
        });

    //    var $form = $("#form");
    //    $form.form();
        $("#formSubmitBtn").on("click", function () {
            if($("#content").val()==""){
                $.toast("内容不能为空", "forbidden");
            }else{
                var d = $('#sendMsg').serializeArray();
                $.ajax({
                    type: 'POST',
                    data: d,
                    url: '../api/qa.php?a=send_question',
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $.toast("成功");
//                        window.history.back(-1);
                    }
                });

            }
        });
    })

</script>
</head>

<body ontouchstart style="background-color: #f8f8f8;">
<div class="weui-header bg-green">
    <div class="weui-header-left"> <a href="pt_expert_list.php" class="icon icon-109 f-white">返回</a>  </div>
    <h1 class="weui-header-title">提问</h1>
    <div class="weui-header-right"></div>
</div>
<div style="text-align: center; padding: 10px;">
    <img class="" src="<?=$user['headimgurl']?>"  style="    width: 80px;
    padding: 20px;
    border-radius: 80px;">

    <div class="weui-loadmore weui-loadmore-line">
        <span class="weui-loadmore-tips"><?=$user['username']?></span>
    </div>
    <p class="weui_media_desc" style="padding: 20px;">
        <?=$user['memo']?>
    </p>
</div>



<div class="weui_cells ">

<form id="sendMsg">

    <input type="hidden" name="question_user_id" value="<?=$_SESSION['user']->id?>">
    <input type="hidden" name="answer_user_id" value="<?=$user['id']?>">
    <div class="weui_cell">
        <div class="weui_cell_bd weui_cell_primary">
            <textarea id="content" name="content" class="weui_textarea" placeholder="提问内容" rows="3"></textarea>
            <div class="weui_textarea_counter"><span id='count'>0</span>/<span id='count_max'>60</span></div>
        </div>
    </div>

</form>
</div>
<div class="weui_btn_area">
    <a class="weui_btn weui_btn_primary" href="javascript:" id="formSubmitBtn">提问</a>
</div>

</body>
</html>
