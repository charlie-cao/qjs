<?php
require_once '../config.php';
require_once '../lib/fun.php';
check_login();

if(!isset($_GET['tag'])){
$_GET['tag']=0;
}
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
    <style>
    .paragraphExtender{
        background-color: #35C535;
        color: white;
        padding: 4px;
        width: 70%;
        float: left;
        border-radius: 20px;
        padding-left: 20px;
        line-height: 22px;

    }
    .paragraph{
    word-break: break-all;
    }
    </style>
    <script>
        /**
        * 删除信息
        **/
        function delMsg(id) {
            $.toast("删除成功" + id);
        }

        /**
         * 点赞
         */
        function upMsg(id) {
            $.toast("点赞成功" + id);
        }

        /**
         * 添加内容行
         * @param data
         * @param i
         * @returns {string}
         */
        function addCell(data, i) {

            var result = ''
                + '<!-- 普通的post -->'
                + '<div class="weui_cell moments__post">'

                + '<div class="weui_cell_hd">'
                + '<img src="' + data[i].question_user[0].headimgurl + '"/>'
                + '</div>'

                + '<div class="weui_cell_bd"  style="width: 100%;">'
                + '<!-- 删除链接 -->'

                + '<!-- 人名链接 -->'
                + '<a class="title" href="javascript:;">'
                + '<span>' + data[i].question_user[0].username + '</span>'
                + '</a>'


                + '<!-- post内容 -->'
                + '<p id="paragraph" class="paragraph">'
                + "问题 : "+data[i].question_content
                + '</p>'
                + '专家回答 : <a href="pt_send_question.php?id='+data[i].answer_user[0].id+'">'+ data[i].answer_user[0].username +"</a>"
                + '<!-- 伸张链接 -->'
                + '<a id="paragraphExtender" class="paragraphExtender" style="color: white;"><span class="icon icon-44" style="padding-right:4px"></span> 免费偷听</a>'
                + '<!-- 相册 -->'
                + '<div class="thumbnails">'



                + '</div>'
                + '<!-- 资料条 -->'
                + '<div class="toolbar">'
                + '<p class="timestamp">' + data[i].c_time + '</p>'

                + '<span class="check checked " style="    margin-left: auto;"  onclick="upMsg(' + data[i].id + ');" >'
                + '<i class="weui-comment-icon"></i>'
                + '<span class="weui-comment-num" style="padding-left: 4px;"> ' + data[i].up_num + '</span>'
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
                            url: '../api/qa.php?a=get_new_qa_list'  ,
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
                            url: '../api/qa.php?a=get_qa_list&page=' + page + '&size=' + size + '&last_time=' + nowtime ,
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

    <style>
        .icon {
            font-size: 20px;
        }

        .weui_cells:before {
            top: 0;
            border-top: 0px solid #d9d9d9;
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
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
            <a href="zj_question_list.php" class="icon icon-80 f-white" style="font-size: 26px;"></a>
            <span class="weui-badge" style="position: absolute;top: -.4em;right: -.4em;">8</span>
        </div>
    </div>

    <div class="page-hd" style="padding: 4px;height: 34px;
    background: white;">
        <div class="weui-flex">
            <div class="weui-flex-item">

            </div>
            <div class="weui-flex-item">
                <div class="weui_tab_nav">
                    <a href="zj_main.php?tag=0" class="weui_navbar_item weui_nav_green <?=($_GET['tag']==0)?"bg_green":""?>"> 最值 </a>
                    <a href="zj_main.php?tag=1" class="weui_navbar_item weui_nav_green <?=($_GET['tag']==1)?"bg_green":""?>"> 最新 </a>
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


    <div class="weui-gallery" style="display: none">
        <span class="weui-gallery-img" style="background-image: url(../../yey/icon/1.png);"></span>
        <div class="weui-gallery-opr">
            <a href="javascript:" class="weui-gallery-del" onclick="$('.weui-gallery').fadeOut();">
                <i class="icon icon-26 f-gray">关闭</i>
            </a>
        </div>
    </div>

</body>

</html>