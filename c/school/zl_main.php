<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";


check_login();
//
$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();

if (!isset($_GET['tag'])) {
    $_GET['tag'] = "";
}
$_SESSION['now_school_tag'] = $_GET['tag'];


?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $_SESSION['school']->name ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="../public/style/weui.css"/>
    <link rel="stylesheet" href="../public/style/weui2.css"/>
    <link rel="stylesheet" href="../public/style/weui3.css?1?1"/>
    <script src="../public/zepto.min.js"></script>
    <script src="../public/jweixin-1.2.0.js"></script>
    <script src="../public/updown.js"></script>
    <script src="../public/lazyimg.js"></script>
    <script>


        wx.config({
            debug: false,
            appId: '<?= $signPackage["appId"]; ?>',
            timestamp: <?= $signPackage["timestamp"];?>,
            nonceStr: '<?= $signPackage["nonceStr"]; ?>',
            signature: '<?= $signPackage["signature"]; ?>',
            jsApiList: [
                'checkJsApi',
                'chooseImage',
                'previewImage',
                'uploadImage',
                'onMenuShareAppMessage',
                'onMenuShareTimeline',
                'hideAllNonBaseMenuItem',
                'showMenuItems'
            ]
        });

        wx.ready(function () {
            wx.ready(function () {
                wx.hideAllNonBaseMenuItem();
                // 更新本分享链接
                wx.showMenuItems({
                    menuList: ["menuItem:share:appMessage","menuItem:share:timeline"]
                    // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
                });

                wx.onMenuShareAppMessage({
                    title: '<?=$_SESSION['user']->username?>邀请您加入<?=$_SESSION['school']->name?>', // 分享标题
                    desc: '<?=$_SESSION['school']->name?> 欢迎您', // 分享描述
                    link: '<?= $server_host ?>/c/school/index.php?state=<?=$_SESSION['school']->id?>', // 分享链接
                    imgUrl: '<?= $server_host ?>/c/public/images/wx_inv.jpg', // 分享图标
                    type: '', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

                wx.onMenuShareTimeline({
                    title: '<?=$_SESSION['user']->username?>邀请您加入<?=$_SESSION['school']->name?>', // 分享标题
                    link: '<?= $server_host ?>/c/school/index.php?state=<?=$_SESSION['school']->id?>', // 分享链接
                    imgUrl: '<?= $server_host ?>/c/public/images/wx_inv.jpg', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

            });

        });



        function preview(em) {
            var imgs = [];
            $(em).parent().find("div").each(function (key, item) {
                imgs[imgs.length] = $(item).data("imgurl");
            });
            wx.previewImage({
                current: $(em).data("imgurl"), // 当前显示图片的http链接
                urls: imgs // 需要预览的图片http链接列表
            });
        }

        function del(id, em) {
            $.actions({
                actions: [
                    {
                        text: "删除信息",
                        className: "bg-orange f-white",
                        onClick: function () {
                            d = {};
                            d.id = id;
                            $.ajax({
                                type: 'POST',
                                data: d,
                                url: '../api/school.php?a=del_school_msg',
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
                    }
                ]
            });
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
                url: '../api/school.php?a=up_school_msg',
                dataType: 'json',
                success: function (data) {
                    reset_up(id, data.data);
                    $('#actionMenu' + id).removeClass('active');
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
            if (typeof(users) != undefined) {
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
            if (typeof(users) != undefined) {
                var up_user_html = ""
                console.log(users);
                if (users === null) {

                } else {
                    if (users.length > 0) {
                        up_user_html = '<p class="liketext" style="margin-top: 6px; padding-top:2px; padding-bottom:2px;" >'
                        up_user_html += '<img src="../public/images/icon/love.png" style="width: 14px; padding: 2px; margin-right: 4px; margin-left: 4px;">'
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
         * 添加内容行
         * @param data
         * @param i
         * @returns {string}
         */
        function addCell(data, i) {

            var tag = "";
            if (data[i].tag_name === null) {
                tag = "动态"
            } else  {
                tag = data[i].tag_name;
            }

            var div_img = "";
            if (typeof(data[i].imgs) != undefined) {
                $.each(data[i].imgs, function (i, img) {
                    div_img += ''
                        + '<div class="thumbnail weui-updown" onclick="preview(this)" data-imgurl="<?=$server_host . "/c/cls/"?>' + img + '" >'
                        + '</div>';
                })
            }

            //生成点赞用户的html
            var div_users = reset_up(data[i].id, data[i].up_user, true);

            var checked = is_uped(data[i].up_user);

            var del_link = "";
//            if(data[i].user[0].id == <?=$_SESSION['user']->id?>){
            del_link += '<a class="title" href="javascript:;" onclick="del(' + data[i].id + ',this);" style="float: right;">';
            del_link += '<img src="../public/images/icon/del.png" style="width: 18px; height: 18px;">';
            del_link += '</a>';
//            }

            var result = ''
                + '<!-- 普通的post -->'
                + '<div class="weui_cell moments__post" id="msg' + data[i].id + '">'

                + '<div class="weui_cell_hd weui-updown">'
                + '<img src="' + data[i].user[0].headimgurl + '"/>'
                + '</div>'

                + '<div class="weui_cell_bd" style="width: 100%;">'
                + '<!-- 删除链接 -->'
                + del_link
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
                //              + '<a id="paragraphExtender" class="paragraphExtender">显示全文</a>'

                + '<!-- 相册 -->'
                + '<div class="thumbnails">'
                + div_img
                + '</div>'

                + '<!-- 资料条 -->'
                + '<div class="toolbar">'
                + '<p class="timestamp">' + data[i].c_time + '</p>'
                + '<span id="actionToggle" data-id="' + data[i].id + '" class="actionToggle" style="height: 12px;"><i class="icon icon-83" ></i></span>'
                + '<div>'

                + '<div id="actionMenu' + data[i].id + '" class="actionMenu slideIn">'
                + '<p class="actionBtn ' + checked + '"  onclick="up(' + data[i].id + ',this);"   style="font-size:14px"><i class="icon icon-96" style="font-size:14px"></i> 赞</p>'
                + '</div>'

                + '</div>'
                + '</div>'

                + '<!-- 赞／评论区 -->'
                + '<div id="up_user' + data[i].id + '" class="up_user">' + div_users + '</div>'


                + '</div>'
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
            var school_id = <?=$_SESSION['school_id']?>;
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
                    domNoData: '<div class="dropload-noData">-- 没有更多了 --</div>'
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
                        url: '../api/school.php?a=get_school_msg_list&page=' + page + '&size=' + size + '&school_id=' + school_id + '&last_time=' + nowtime + tag_str,
                        dataType: 'json',
                        success: function (data) {
                            if (data.msg == "success") {
                                data = data.data;
                                var arrLen = data.length;
                                if (arrLen > 0) {
                                    for (var i = 0; i < arrLen; i++) {
                                        result += addCell(data, i);
                                    }

                                }
                                // 如果没有数据
                                setTimeout(function () {
                                    $('.weui_panel_bd').append(result);
                                    //点击任意位置取消显示
                                    $('.actionToggle').off();
                                    $('.actionToggle').on("click",function (e) {
                                        e.preventDefault();
                                        $('.actionMenu').removeClass('active');
                                        $('#actionMenu' + $(this).data('id')).toggleClass('active');
                                        return false;
                                    })
                                    $(".weui_cell").off();
                                    $(".weui_cell").on("click",function (e) {
                                        console.log($(e));
                                        $('.actionMenu').removeClass('active');
                                    })


//                                    var lazyloadImg = new LazyloadImg({
//                                        el: '.weui-updown [data-img]', //匹配元素
//                                        top: 50, //元素在顶部伸出长度触发加载机制
//                                        right: 50, //元素在右边伸出长度触发加载机制
//                                        bottom: 50, //元素在底部伸出长度触发加载机制
//                                        left: 50, //元素在左边伸出长度触发加载机制
//                                        qriginal: false, // true，自动将图片剪切成默认图片的宽高；false显示图片真实宽高
//                                        load: function (el) {
//                                            el.style.cssText += '-webkit-animation: fadeIn 01s ease 0.2s 1 both;animation: fadeIn 1s ease 0.2s 1 both;';
//                                        },
//                                        error: function (el) {
//
//                                        }
//                                    });

                                    $(".thumbnails").each(function (e,m) {
                                        var imgs = $(m).find(".thumbnail");
                                        if(imgs.length==1){
                                            //设置大图
                                            $(imgs[0]).css("width","80%");
                                            $(imgs[0]).css("height","140px");
                                            $(imgs[0]).css("background-position","50%");
                                            $(imgs[0]).css("background-size","cover");
                                            $(imgs[0]).css("background-image","url("+$(imgs[0]).data('imgurl')+")");
                                        }else{
                                            imgs.each(function (e1,m1) {
                                                $(m1).css("background-position","50%");
                                                $(m1).css("background-size","cover");
                                                $(m1).css("background-image","url("+$(m1).data('imgurl')+")");
                                                $(m1).css("height", $(m1).css("width"));
                                            })
                                        }
                                    })

                                    // 每次数据加载完，必须重置
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

        .weui-comment {
            background-color: #f3f3f5;
        }

        .comment {
            font-size: 14px;
            background: #f3f3f5;
        / / border-top: 1 px solid white;
            list-style: none;
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
<div class="weui-header bg-green header">
    <div class="weui-header-left">
        <a href="zl_menu.php" class="title_icon" >
            <img src="../public/images/icon/setup.png" >
        </a>
    </div>
    <h1 class="weui-header-title">校园大家庭</h1>
    <div class="weui-header-right">
        <a href="zl_send_msg.php" class="title_icon">
            <img src="../public/images/icon/msg.png" >
        </a>
    </div>
</div>

<div class="page-hd" style="padding: 0px">
    <div class="weui_tab" id="tab5" style="height:44px;">
        <div class="weui_navbar">
            <a href="zl_main.php" class="weui_navbar_item <?php echo ($_GET['tag'] == "") ? " tab-green" : "" ?>">
                全部 </a>
            <?php foreach ($_SESSION['school_tags'] as $key => $val) { ?>
                <a href="zl_main.php?tag=<?= $val['id'] ?>"
                   class="weui_navbar_item <?php echo ($_GET['tag'] == $val['id']) ? " tab-green" : "" ?>">
                    <?= $val['name'] ?> </a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="weui_panel weui_panel_access" style="     margin-top: 0px; ">
    <div class="weui_panel_bd weui_cells moments">
    </div>
</div>
</body>
</html>
