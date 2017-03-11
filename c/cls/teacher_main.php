<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
check_login();
//
$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();

if(!isset($_GET['tag'])){
    $_GET['tag']=0;
}

if(isset($_GET['cls_id'])){
    //切换 班级ID
    //设置默认的 cls_id 可以在用户信息里获得
    $_SESSION['cls_id'] = $_GET['cls_id'];
}


//var_dump($_SESSION);
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
    <script src="../public/updown.js"></script>
    <script src="../public/lazyimg.js"></script>
    <script>
        var cls_id = <?=$_SESSION['cls_id'] ?>;

        wx.config({
            debug: false,
            appId: '<?= $signPackage["appId"]; ?>',
            timestamp: <?= $signPackage["timestamp"];?>,
            nonceStr: '<?= $signPackage["nonceStr"]; ?>',
            signature:'<?= $signPackage["signature"]; ?>',
            jsApiList:
        [
            'checkJsApi',
            'chooseImage',
            'previewImage',
            'uploadImage'
        ]
        })
        ;
        wx.ready(function () {

        });


        function preview(em) {
            var imgs = [];
            $(em).parent().find("img").each(function (key, item) {
                imgs[imgs.length] = $(item).attr("src");
            });
            wx.previewImage({
                current: $(em).find("img").attr("src"), // 当前显示图片的http链接
                urls: imgs // 需要预览的图片http链接列表
            });
        }

        function del(id, em) {
            d = {};
            d.id = id;
            $.ajax({
                type: 'POST',
                data: d,
                url: '../api/api.php?a=del_cls_msg',
                dataType: 'json',
                success: function (data) {
                    $(em).parent().parent().fadeOut();
                    $.toast("删除成功");
                },
                error: function (xhr, type, e) {

                    alert(type);
                }
            });
        }

        function up(id, em) {
            d = {};
            d.id = id;
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
                url: '../api/api.php?a=up_cls_msg',
                dataType: 'json',
                success: function (data) {
                    var num = $(em).find("span").html();
                    if (d.type == "up") {
                        $(em).find("span").html((num * 1) + 1);
                    } else {
                        $(em).find("span").html((num * 1) - 1);
                    }
                },
                error: function (xhr, type, e) {

                    alert(type);
                }
            });
        }
        /**
         * 添加内容行
         * @param data
         * @param i
         * @returns {string}
         */
        function addCell(data, i) {
            var tag = "";
            if (data[i].tag == 0) {
                tag = "动态"
            } else if (data[i].tag == 1) {
                tag = "通知"
            } else if (data[i].tag == 2) {
                tag = "成长秀"
            }

            var div_img = "";
            if (typeof(data[i].imgs) != undefined) {
                $.each(data[i].imgs, function (i, img) {
                    div_img += ''
                        + '<div class="thumbnail" onclick="preview(this)" >'
                        + '<img src="' + img + '" style="height:100%;"/>'
                        + '</div>';
                })
            }

            var result = ''
                + '<!-- 普通的post -->'
                + '<div class="weui_cell moments__post">'
                + '<div class="weui_cell_hd">'
                + '<img src="' + data[i].user[0].headimgurl + '"/>'
                + '</div>'
                + '<div class="weui_cell_bd"  style="width: 100%;">'
                + '<!-- 删除链接 -->'
                + '<a class="title" href="javascript:;" onclick="del(' + data[i].id + ',this);" style="float: right;">'
                + '<span class="icon icon-26"></span>'
                + '</a>'
                + '<!-- 人名链接 -->'
                + '<a class="title" href="javascript:;">'
                + '<span>' + data[i].user[0].username + '</span>'
                + '<label class="weui-label-s" style="margin-left:10px;">' + tag + '</label>'
                + '</a>'
                + '<!-- post内容 -->'
                + '<p id="paragraph" class="paragraph">'
                + data[i].content
                + '</p>'
                + '<!-- 伸张链接 -->'
//                + '<a id="paragraphExtender" class="paragraphExtender">显示全文</a>'
                + '<!-- 相册 -->'
                + '<div class="thumbnails">'
                + div_img
                + '</div>'
                + '<!-- 资料条 -->'
                + '<div class="toolbar">'
                + '<p class="timestamp">' + data[i].c_time + '</p>'
                + '<span class="check" style="    margin-left: auto;"  onclick="up(' + data[i].id + ',this);" >'
                + '<i class="weui-comment-icon"></i>'
                + '<span class="weui-comment-num" style="padding-left: 4px;"> ' + data[i].up + '</span>'
                + '</span>'
                + '</div>'
                + '<!-- 赞／评论区 -->'
                + '</div>'
                + '<!-- 结束 post -->'
                + '</div>'
                + '<!-- 结束 朋友圈 -->';
            return result;
        }
        $(function () {
            var nowtime = new Date().getTime();
            console.log(nowtime);
            //开启时的时间戳 最好是从服务端获取的，可以在第一次请求时传回来
            var now = "20151209";
            //学校ID 其实慢成长也可以用这个模式，直接相当于群号码了
            var school_id = 0;
            //页数
            var page = 0;
            // 每页展示10个
            var size = 5;
            var dp;
            var tag = "<?= $_GET['tag'] ?>";

            var dropload = $('.weui_panel').dropload({
                scrollArea: window,
                autoLoad: true,//自动加载
                domDown: {//上拉
                    domClass: 'dropload-down',
                    domRefresh: '<div class="dropload-refresh f15 "><i class="icon icon-20"></i>上拉加载更多</div>',
                    domLoad: '<div class="dropload-load f15"><span class="weui-loading"></span>正在加载中...</div>',
                    domNoData: '<div class="dropload-noData">没有内容啦 >__< </div>'
                },
                domUp: {//下拉
                    domClass: 'dropload-up',
                    domRefresh: '<div class="dropload-refresh"><i class="icon icon-114"></i>下拉加载更多</div>',
                    domUpdate: '<div class="dropload-load f15"><i class="icon icon-20"></i>释放更新...</div>',
                    domLoad: '<div class="dropload-load f15"><span class="weui-loading"></span>正在加载中...</div>'
                },
                loadUpFn: function (me) {
                    if (tag != "") {
                        tag_str = "&tag=" + tag;
                    } else {
                        tag_str = "";
                    }
                    //刷新
                    $.ajax({
                        type: 'GET',
                        url: '../api/cls.php?a=get_new_msg_list' + tag_str,
                        dataType: 'json',
                        success: function (data) {
                            var result = '';
                            for (var i = 0; i < data.length; i++) {
                                result += addCell(data, i);
                            }
                            // 为了测试，延迟1秒加载
                            setTimeout(function () {
                                $('.weui_panel_bd').html(result);
                                var lazyloadImg = new LazyloadImg({
                                    el: '.weui-updown [data-img]', //匹配元素
                                    top: 50, //元素在顶部伸出长度触发加载机制
                                    right: 50, //元素在右边伸出长度触发加载机制
                                    bottom: 50, //元素在底部伸出长度触发加载机制
                                    left: 50, //元素在左边伸出长度触发加载机制
                                    qriginal: false, // true，自动将图片剪切成默认图片的宽高；false显示图片真实宽高
                                    load: function (el) {
                                        el.style.cssText += '-webkit-animation: fadeIn 01s ease 0.2s 1 both;animation: fadeIn 1s ease 0.2s 1 both;';
                                    },
                                    error: function (el) {
                                    }
                                });
                                // 每次数据加载完，必须重置
                                me.resetload();
                                dp = me;
                                // 重置索引值，重新拼接more.json数据
                                page = 1;
                                // 解锁
                                me.unlock();
                                me.noData(false);
                            }, 100);
                        },
                        error: function (xhr, type) {
                            console.log('Ajax error!');
                            // 即使加载出错，也得重置
                            me.resetload();
                        }
                    });
                },
                loadDownFn: function (me) {//加载更多
                    if (tag != "") {
                        tag_str = "&tag=" + tag;
                    } else {
                        tag_str = "";
                    }
                    dp = me;
                    page++;
                    window.history.pushState(null, document.title, window.location.href);
                    var result = '';
                    $.ajax({
                        type: 'POST',
                        url: '../api/cls.php?a=get_cls_msg_list&page=' + page + '&size=' + size + '&cls_id=' + cls_id + '&last_time=' + nowtime + tag_str,
                        dataType: 'json',
                        success: function (data) {
                                if(data.msg == "success"){
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
                                    // 为了测试，延迟1秒加载
                                    setTimeout(function () {
                                        $('.weui_panel_bd').append(result);
                                        // 每次数据加载完，必须重置
                                        me.resetload();
                                    }, 100);
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
    </style>
</head>

<body ontouchstart>
<div class="weui-header bg-green">
    <div class="weui-header-left">
        <a href="teacher_menu.php" class="icon icon-101 f-white" style="font-size: 26px;"></a>
    </div>
    <h1 class="weui-header-title">慢成长 <?=$_SESSION['cls']->name?></h1>
    <div class="weui-header-right">
        <a href="teacher_send_msg.php" class="icon icon-77 f-white" style="font-size: 26px;"></a>
    </div>
</div>

<div class="page-hd" style="padding: 0px">
    <div class="weui_tab" id="tab5" style="height:44px;">
        <div class="weui_navbar">
             <a href="pt_main.php?tag=0" class="weui_navbar_item <?= ($_GET['tag']==0)?"tab-green":"" ?>"> 动态 </a>
             <a href="pt_main.php?tag=1" class="weui_navbar_item <?= ($_GET['tag']==1)?"tab-green":"" ?>"> 通知 </a>
             <a href="pt_main.php?tag=2" class="weui_navbar_item <?= ($_GET['tag']==2)?"tab-green":"" ?>"> 成长秀 </a>
        </div>
    </div>
</div>

<div class="weui_panel weui_panel_access" style="     margin-top: 0px; ">
    <div class="weui_panel_bd weui_cells moments">
    </div>
</div>
</body>
</html>
