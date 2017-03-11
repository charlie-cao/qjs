<?php
require_once '../config.php';
require_once '../lib/fun.php';
check_login();


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
    <script src="../public/updown.js"></script>
    <script src="../public/lazyimg.js"></script>
    <script>
            function addCell(data, i) {
                var result = ''
                    + '<!-- 普通的post -->'
                    + '<div class="weui_media_box weui_media_appmsg">'

                    + '<div class="weui_media_hd">'
                    + '<img class="weui_media_appmsg_thumb" src="' + data[i].headimgurl + '"/>'
                    + '</div>'

                    + '<div class="weui_media_bd" >'
                    + '<h4 class="weui_media_title">' + data[i].username + '</h4>'

                    + '<p class="weui_media_desc">'
                    + data[i].memo
                    + '</p>'
                    + '</div>'
                    + '<div class="weui_media_fd">'
                    + '<a href="pt_send_question.php?id='+data[i].id+'" class="weui_btn weui_btn_mini weui_btn_primary" style="font-size: 12px;">提问</a>'
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
                        //刷新
                        $.ajax({
                            type: 'GET',
                            url: '../api/qa.php?a=get_new_expert_list',
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
                        dp = me;
                        page++;
                        window.history.pushState(null, document.title, window.location.href);
                        var result = '';
                        $.ajax({
                            type: 'GET',
//                            url: 'http://schoolcms.isqgame.com/api.php?m=Api&c=Index&a=Index&page=' + page + '&size=' + size + '&nowtime=' + nowtime + '&school_id=0&tag=' + tag,
                            url: '../api/qa.php?a=get_expert_list&page=' + page + '&size=' + size + '&last_time=' + nowtime ,
                            dataType: 'json',
                            success: function (data) {
                                console.log(data);
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
                                }


                                // 为了测试，延迟1秒加载
                                setTimeout(function () {
                                    $('.weui_panel_bd').append(result);

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

                function setCell(data) {

                }

                var tag = 0;
                $('#tab5').tab({
                    defaultIndex: 0, activeClass: "bg_green", onToggle: function (i) {
                        tag = i;
                        //                alert(tag);
                        dropload.lock('down');
                        dropload.noData();
                        dropload.resetload();
                        //                dp.resetload();
                    }
                });
            });
    </script>
</head>

<body ontouchstart>
<div class="weui-header bg-green">
    <div class="weui-header-left"> <a href="pt_main.php" class="icon icon-109 f-white">返回</a>  </div>
    <h1 class="weui-header-title">名师</h1>
    <div class="weui-header-right"></div>
</div>

    <div class="weui_panel weui_panel_access" style="     margin-top: 0px; ">
        <div class="weui_panel_bd weui_cells moments">
        </div>
    </div>

</body>
</html>
