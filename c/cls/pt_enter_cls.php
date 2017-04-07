<?php
/*
慢成长入口 根据校园id 分库
*/

require_once '../config.php';
require_once '../lib/fun.php';
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

//交验唯一ID
$state = $_GET['state'];
if (!isset($_GET['state'])) {
    echo "ID错误";
    exit;
} else {
    $s = explode("-", $_GET['state']);

    $school_id = $s[0];
    $cls_id = $s[1];

    $_SESSION['state'] = $_GET['state'];
    $_SESSION['school_id'] = $school_id;
    $_SESSION['cls_id'] = $cls_id;
}
//交验校园
$school = get_school_info($_SESSION['school_id']);
if (!($school)) {
    //如果没有学校ID则报错
    echo "校区不存在";
    exit;
} else {
    $_SESSION['school'] = $school;
}


$state = $_SESSION['state'];
//检查用户是否已经登录
if (!isset($_SESSION['user']->openid)) {
    //未登录 微信登录
    $_SESSION['user'] = wx_userinfo($appid, $secret, $redirect_uri, $state);
}

//检查用户并更新用户SESSION信息
$_SESSION['user'] = check_user($_SESSION['user']);


if ($_SESSION['cls_id'] == "") {
    //检查用户有没有最终登录的班级ID如果有则
    //需要记录一下用户最后登录的那个学校 和 班级
//    if($_SESSION['user']->last_school_id==)
    v("./index_cls_list.php?school_id=" . $_SESSION['school_id']);
    exit;
}
//交验班级
$cls = get_cls_info($_SESSION['cls_id']);
if (!isset($cls)) {
    //如果没有学校ID则报错
    echo "班级ID不存在";
    exit;
} else {
    $_SESSION['cls'] = $cls;
}
//注册 tag
//var_dump($_SESSION);
$tags = get_tags($school->id, "cls");
if (!isset($tags)) {
    echo "获取系统tag错误";
    exit;
} else {
    $_SESSION['tags'] = $tags;
}


$sql = "select * from sc_cls where id='" . $_SESSION['cls_id'] . "';";
$res = $db->query($sql);
$cls = $res->fetch();


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
        $(function () {
//            var code = ["-","-","-","-"];
//            var index = 0;
//
//            $(".btn").on('click',function (){
//                if(index<4){
//                    code[index] = $(this).data('val');
//                    index++;
//                    reset_code(code);
//                }else{
//                }
//            });
//
//            $("#reset").click(function () {
//                code = ["-","-","-","-"];
//                index = 0;
//                reset_code(code);
//            })
//
//            function reset_code(code) {
//                $.each(code, function (i, c) {
//                    o = $("#code label").get(i);
//                    $(o).html(c);
//                })
//            }

            $("#enter").click(function () {
                var code = $("#code").val();
                d = {
                    'school_id': '<?= $_SESSION['school_id'] ?>',
                    'cls_id': '<?= $_SESSION['cls_id'] ?>',
                    'user_id': '<?= $_SESSION['user']->id ?>',
                    'code': code
                };
                $.ajax({
                    type: 'POST',
                    url: '../api/cls.php?a=check_inv_code',
                    dataType: 'json',
                    data: d,
                    success: function (data) {
                        if (data.msg == "success") {
                            $.alert("邀请码正确，确定后进入该班级", function () {
                                location.href = "index.php?state=<?=$_SESSION['school_id']?>-<?= $cls['id'] ?>";
                            });
                        } else {
                            $.alert("邀请码错误，请重新输入");
                            $("#code").val("");
                        }
                    },
                    error: function (xhr, type) {
                        console.log('Ajax error!');
                    }
                });
                return false;
            });
        });


    </script>
    <style>
        body {
            background-color: #f8f8f8;
        }

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


<div style="    text-align: center;">
    <img src="../public/images/0.jpeg" style="    width: 100px;padding: 50px;border-radius: 150px;"/>
    <h4 class="weui_msg_title">欢迎加入 <?= $cls['name'] ?> <br/>班级邀请码可以向班主任询问</h4>

</div>
<div class="weui_cells weui_cells_form">

    <div class="weui_cell">
        <div class="weui_cell_hd"><label class="weui_label">邀请码</label></div>
        <div class="weui_cell_bd weui_cell_primary">
            <input id="code" class="weui_input" type="tel" required="" pattern="[0-9]{4}" maxlength="4" placeholder="请输入班级邀请码" emptytips="请输入班级邀请码" notmatchtips="班级邀请码为4位纯数字">
        </div>
        <div class="weui_cell_ft">
            <i class="weui_icon_warn"></i>
        </div>
    </div>
</div>
<div class="weui_btn_area">
    <a id="enter" href="javascript:" class="weui_btn weui_btn_primary">进入班级</a>
</div>
<div class="weui_cells_tips"></div>

<div class="weui-footer weui-footer-fixed-bottom">
    <p class="weui-footer-links">
        <a href="javascript:;" class="weui-footer-link">千家师</a>
    </p>
    <p class="weui-footer__text">Copyright © 2008-2017 </p>
</div>
</body>
</html>