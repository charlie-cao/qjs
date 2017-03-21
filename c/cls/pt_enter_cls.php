<?php
/*
慢成长入口 根据校园id 分库
*/

require_once '../config.php';
require_once '../lib/fun.php';
$redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

//交验唯一ID
$state = $_GET['state'];
if(!isset($_GET['state'])){
    echo "ID错误";
    exit;
}else{
    $s = explode("-",$_GET['state']);

    $school_id = $s[0];
    $cls_id = $s[1];

    $_SESSION['state'] =  $_GET['state'];
    $_SESSION['school_id'] =  $school_id;
    $_SESSION['cls_id'] =  $cls_id;
}

//echo $cls_id;
//var_dump($_SESSION);
//exit;
//交验校园
$school = get_school_info($school_id);
if(!isset($school)){
    //如果没有学校ID则报错
    echo "校园ID不存在";
    exit;
}else{
    $_SESSION['school'] = $school;
}

//交验班级
$cls = get_cls_info($cls_id);
if(!isset($cls)){
    //如果没有学校ID则报错
    echo "班级ID不存在";
    exit;
}else{
    $_SESSION['cls'] = $cls;
}

//交验用户

$user = wx_userinfo($appid, $secret, $redirect_uri, $state);
//检查该用户是否已经入库，如果没有-》入库，如果已经入库-》更新最后登录时间，获取用户身份
$user = check_user($user);
if (!isset($user)) {
    //身份验证错误，禁止登录
        echo "该用户不存在";
        exit;
} else {
    //用户更新信息后需要再次访问入口文件 更新用户个人信息
    $_SESSION['user'] = $user;
}


//注册 tag

$tags = get_tags($school->id,"cls");
if(!isset($tags)){
    echo "获取系统tag错误";
    exit;
}else{
    $_SESSION['tags'] = $tags;
}


$sql = "select * from sc_cls where id='".$_SESSION['cls_id']."';";
$res = $db->query($sql);
$cls = $res->fetch();

//var_dump($_SESSION);

//var_dump($cls);
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
        $(function (){
            var code = [0,0,0,0];
            var index = 0;
            $(".btn").on('click',function (){
                if(index<4){
                    code[index] = $(this).data('val');
                    index++;
                    reset_code(code);
                }else{
                }
            });

            $("#reset").click(function (){
                code = [0,0,0,0];
                index = 0;
                reset_code(code);
            })

            function reset_code(code){
                $.each(code,function (i,c){
                    o = $("#code label").get(i);
                    $(o).html(c);
                })
            }

            $("#enter").click(function (){
                d={'school_id':'<?= $_SESSION['school_id'] ?>','cls_id':'<?= $_SESSION['cls_id'] ?>','user_id':'<?= $_SESSION['user']->id ?>','code':code.join('')};
                    $.ajax({
                            type: 'POST',
                            url: '../api/cls.php?a=check_inv_code',
                            dataType: 'json',
                            data: d,
                            success: function (data) {
                                if(data.msg=="success"){
                                    $.alert("验证码正确，确定后进入该班级",function (){
                                        location.href = "pt_main.php?cls_id=<?= $cls['id'] ?>";
                                    });
                                }else{
                                    $.alert("验证码错误，请重新输入");
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
        .icon{
            font-size: 20px;
        }
        .weui_cells:before {
            top: 0;
            border-top: 0px solid #d9d9d9;
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
            left:0px;
        }
        .lb {
            font-size: 38px;
            border: 1px solid;
            padding: 14px;
            color: green;
                background: #ccc;
        }
        .weui_grid_label{
            font-size:20px;
        }
    </style>
</head>
<body ontouchstart>
<div class="weui_msg" id="msg2" style="display: block; opacity: 1;padding-top:66px;">
        <div class="weui_text_area">
            <h2 class="weui_msg_title">欢迎加入 <?= $cls['name'] ?></h2>
            <p class="weui_msg_desc"></p>
        </div>
        <div class="weui_icon_area" id="code">
            <label class="lb">0</label>
            <label class="lb">0</label>
            <label class="lb">0</label>
            <label class="lb">0</label>
        </div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">请输入邀请码</h2>
            <p class="weui_msg_desc">班级邀请码可以向班主任询问</p>
        </div>
        <div class="weui_opr_area">
        <div class="weui_grids">
            <a href="javascript:;" class="weui_grid js_grid btn"  data-val="1">
                <p class="weui_grid_label ">
                    1
                </p>
            </a>
            <a href="javascript:;" class="weui_grid js_grid btn"  data-val="2">
                <p class="weui_grid_label ">
                    2
                </p>
            </a>
            <a href="javascript:;" class="weui_grid js_grid btn"  data-val="3">
                <p class="weui_grid_label ">
                    3
                </p>
            </a>
            <a href="javascript:;" class="weui_grid js_grid btn"  data-val="4">
                <p class="weui_grid_label ">
                    4
                </p>
            </a>
            <a href="javascript:;" class="weui_grid js_grid btn"  data-val="5">
                <p class="weui_grid_label ">
                    5
                </p>
            </a>
            <a href="javascript:;" class="weui_grid js_grid btn"  data-val="6">
                <p class="weui_grid_label ">
                    6
                </p>
            </a>
            <a href="javascript:;" class="weui_grid js_grid btn" data-val="7">
                <p class="weui_grid_label " >
                    7
                </p>
            </a>
            <a href="javascript:;" class="weui_grid js_grid btn" data-val="8">
                <p class="weui_grid_label " >
                    8
                </p>
            </a>
            <a href="javascript:;" class="weui_grid js_grid btn"  data-val="9">
                <p class="weui_grid_label " >
                    9
                </p>
            </a>
            <a href="javascript:;" class="weui_grid js_grid btn"  data-val="0">
                <p class="weui_grid_label ">
                    0
                </p>
            </a>
            <div id="enter"  class="weui_grid js_grid">
                <p class="weui_grid_label ">
                    进入班级
                </p>
            </div>
            <a id="reset"  href="javascript:;" class="weui_grid js_grid">
                <p class="weui_grid_label ">
                    重置
                </p>
            </a>
        </div>
        </div>
        <div class="weui_extra_area">

        </div>
    </div>
</body>
</html>