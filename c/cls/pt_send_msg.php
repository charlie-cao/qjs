<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
check_login();

$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();
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
            appId: '<?php echo $signPackage["appId"]; ?>',
            timestamp: <?php echo $signPackage["timestamp"]; ?>,
            nonceStr: '<?php echo $signPackage["nonceStr"]; ?>',
            signature: '<?php echo $signPackage["signature"]; ?>',
            jsApiList: [
                'checkJsApi',
                'chooseImage',
                'previewImage',
                'uploadImage'
            ]
        });

        wx.ready(function () {
            //hack 一下 第一次按按钮时没反应
            $.showLoading();
            setTimeout(function () {
                $.hideLoading();
            }, 300)
        });

        $(function () {
            var max = $('#count_max').text();
            $('#content').on('input', function () {
                var text = $(this).val();
                var len = text.length;
                $('#count').text(len);
                if (len > max) {
                    $(this).closest('.weui_cell').addClass('weui_cell_warn');
                    $(this).val($(this).val().substring(0, max));
                    $(this).focus();
                    $('#count').text(len - 1);
                } else {
                    $(this).closest('.weui_cell').removeClass('weui_cell_warn');
                }
            });

            $("#formSubmitBtn").on("click", function () {
                if ($("#content").val() == "") {
                    $.toast("内容不能为空", "forbidden");
                } else {
                    var d = $('#sendMsg').serializeArray();

                    d[d.length] = {name: "name", value: '<?=$_SESSION['user']->username?>'};
                    d[d.length] = {name: "headimg", value: '<?=$_SESSION['user']->headimgurl?>'};
                    d[d.length] = {name: "user_id", value: '<?=$_SESSION['user']->id ?>'};
                    d[d.length] = {name: "school_id", value: '<?=$_SESSION['school_id'] ?>'};

                    d[d.length] = {name: "cls_id", value: '<?=$_SESSION['cls_id'] ?>'};


                    $.ajax({
                        type: 'POST',
                        data: d,
                        url: '../api/cls.php?a=send_cls_msg',
                        dataType: 'json',
                        success: function (data) {
                            $.toast("成功");
                            location.href = "pt_main.php?cls_id=<?=$_SESSION['cls_id']?>";
                        },
                        error: function (xhr, type, e) {

                            alert(type);
                        }
                    });
                }
            });

            var weixinimg = [];
            var weixinsrc = [];

            //当前已经上传成功的所有图片
            var imgs = [];
            //当前还可以上传的图片数
            var imgcount = 9;
            //多图上传
            $('#selectimg').on('click', function () {
                wx.chooseImage({
                    count: imgcount,
                    success: function (res) {
                        $.each(res.localIds, function (index, item) {

                            imgcount--;
                            if (imgcount <= 0) {
                                imgcount = 0;
                            }
                        });
                        var localIds = res.localIds;
                        syncUpload(localIds);
                    }
                });
            });
            var syncUpload = function (localIds) {
                var localId = localIds.pop();
                wx.uploadImage({
                    localId: localId,
                    isShowProgressTips: 1,
                    success: function (res) {
                        var serverId = res.serverId; // 返回图片的服务器端ID
                        $.post("../api/cls.php?a=save_pic", {serverId: serverId}, function (data) {
                            $('#preview').append('<li class="weui_uploader_file p_img" style="background-image:url(' + data.src + ')"></li>');
                            $('#files').append('<input value="' + data.src + '" data-id="' + localId + '"  type="hidden"  name="files[]" />');

                            //重置图片索引
                            imgs = [];
                            $('#files input').each(function (index, item) {
//                                    alert($(item).value());
                                imgs[imgs.length] = $(item).data('id');
                            })


                            $('.p_img').unbind("click").click(function () {
                                var index = $('.p_img').index(this);

                                wx.previewImage({
                                    current: imgs[index], // 当前显示图片的http链接
                                    urls: imgs // 需要预览的图片http链接列表
                                });
                            });

                            $("#img_num").html(imgcount);
                            if (imgcount <= 0) {
                                $("#files").hide();
                            }

                            //异步发送图片，防止序列错误
                            if (localIds.length > 0) {
                                syncUpload(localIds);
                            }

                        }, 'json');

                    }
                });
            };
        });
    </script>
</head>

<body ontouchstart style="background-color: #f8f8f8;">

<div class="weui-header bg-green">
    <div class="weui-header-left"><a href="pt_main.php" class="icon icon-109 f-white">返回</a></div>
    <h1 class="weui-header-title">内容发布</h1>
    <div class="weui-header-right"></div>
</div>

<form id="sendMsg">
    <div class="weui_cells ">
        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary">
                <textarea id="content" name="content" class="weui_textarea" placeholder="输入内容" rows="3"></textarea>
                <div class="weui_textarea_counter"><span id='count'>0</span>/<span id='count_max'>120</span></div>
            </div>
        </div>

        <div class="weui_cell weui_cell_select weui_select_after">
            <div class="weui_cell_hd">
                <label for="" class="weui_label">内容类型</label>
            </div>
            <div class="weui_cell_bd weui_cell_primary">
                <select class="weui_select" name="tag" id="tag">
                    <option value="">动态</option>
                    <?php foreach ($_SESSION['cls_tags'] as $key => $val) { ?>
                        <option value="<?= $val['id'] ?>"><?= $val['name'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary">
                <div class="weui_uploader">
                    <div class="weui_uploader_hd weui_cell">
                        <div class="weui_cell_bd weui_cell_primary">还可以上传<span id="img_num">9</span>张照片</div>
                        <div class="weui_cell_ft"></div>
                    </div>

                    <div class="weui_uploader_bd">
                        <ul class="weui_uploader_files" id="preview">
                        </ul>

                        <div class="weui_uploader_input_wrp" id="files">
                            <span class="weui_uploader_input" id="selectimg"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="weui_btn_area">
        <a class="weui_btn weui_btn_primary" href="javascript:;" id="formSubmitBtn">发布</a>
    </div>
</form>
</body>
</html>

